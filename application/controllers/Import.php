<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class Import extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Transaction_model');
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    public function index()
    {
        $data['title'] = 'Import Data';
        $this->load->view('templates/header', $data);
        $this->load->view('import/index');
        $this->load->view('templates/main_footer');
    }

    public function upload()
    {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'csv|xls|xlsx';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        if (!is_dir('./uploads/')) {
            mkdir('./uploads/', 0777, TRUE);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $error = $this->upload->display_errors('', '');
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $error]);
                return;
            }
            $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">' . $error . '</div>');
            redirect('import');
        } else {
            $file_data = $this->upload->data();
            $file_path = './uploads/' . $file_data['file_name'];

            // Store file info in session to process in next step
            $this->session->set_userdata('import_file', $file_path);

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'redirect_url' => base_url('import/map')]);
                return;
            }
            redirect('import/map');
        }
    }

    public function map()
    {
        $file_path = $this->session->userdata('import_file');
        if (!$file_path || !file_exists($file_path)) {
            redirect('import');
        }

        try {
            $spreadsheet = IOFactory::load($file_path);
            $worksheet = $spreadsheet->getActiveSheet();

            // 1. Extract raw rows first
            $raw_rows = [];
            $limit = 20; // Check more rows for column detection
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }

                // Only keep rows that aren't entirely empty
                if (count(array_filter($cells)) > 0) {
                    $raw_rows[] = $cells;
                }

                if (count($raw_rows) >= $limit)
                    break;
            }

            if (empty($raw_rows)) {
                $this->session->set_flashdata('message', '<div class="alert alert-warning border-brutal" role="alert">The file seems to be empty.</div>');
                redirect('import');
            }

            // 2. Identify which columns have at least some data
            $active_column_indices = [];
            $num_cols = count($raw_rows[0]);
            for ($i = 0; $i < $num_cols; $i++) {
                $has_data = false;
                foreach ($raw_rows as $row) {
                    if (isset($row[$i]) && trim($row[$i]) !== "") {
                        $has_data = true;
                        break;
                    }
                }
                if ($has_data) {
                    $active_column_indices[] = $i;
                }
            }

            // 3. Filter rows to only include active columns
            $filtered_rows = [];
            foreach ($raw_rows as $row) {
                $filtered_cells = [];
                foreach ($active_column_indices as $idx) {
                    $filtered_cells[] = $row[$idx] ?? '';
                }
                $filtered_rows[] = $filtered_cells;
            }

            // Store active column indices in session for the preview step
            $this->session->set_userdata('import_active_cols', $active_column_indices);

            $data['title'] = 'Map Columns';
            $data['preview_rows'] = array_slice($filtered_rows, 0, 6);
            $data['total_cols'] = count($active_column_indices);

            $this->load->view('templates/header', $data);
            $this->load->view('import/map', $data);
            $this->load->view('templates/main_footer');
        } catch (Exception $e) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Error reading file: ' . $e->getMessage() . '</div>');
            redirect('import');
        }
    }

    public function preview()
    {
        $file_path = $this->session->userdata('import_file');
        $col_date = $this->input->post('col_date');
        $col_title = $this->input->post('col_title');
        $col_amount = $this->input->post('col_amount');
        $col_type = $this->input->post('col_type');
        $col_payee = $this->input->post('col_payee');
        $has_header = $this->input->post('has_header');

        if ($col_date === null || $col_title === null || $col_amount === null) {
            redirect('import/map');
        }

        try {
            $spreadsheet = IOFactory::load($file_path);
            $worksheet = $spreadsheet->getActiveSheet();
            $transactions = [];
            $user_id = $this->session->userdata('user_id');
            $active_cols = $this->session->userdata('import_active_cols');

            $is_first = true;
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }

                // Skip rows that are empty or header
                if (count(array_filter($cells)) === 0)
                    continue;
                if ($is_first && $has_header) {
                    $is_first = false;
                    continue;
                }

                // Map chosen indices (relative to filtered view) back to original cells
                $real_idx_date = $active_cols[$col_date] ?? null;
                $real_idx_title = $active_cols[$col_title] ?? null;
                $real_idx_amount = $active_cols[$col_amount] ?? null;
                $real_idx_type = ($col_type !== "") ? ($active_cols[$col_type] ?? null) : null;
                $real_idx_payee = ($col_payee !== "") ? ($active_cols[$col_payee] ?? null) : null;

                $raw_date = ($real_idx_date !== null) ? ($cells[$real_idx_date] ?? null) : null;
                $title = ($real_idx_title !== null) ? ($cells[$real_idx_title] ?? 'Untitled') : 'Untitled';
                $amount = ($real_idx_amount !== null) ? ($cells[$real_idx_amount] ?? 0) : 0;
                $type = ($real_idx_type !== null && isset($cells[$real_idx_type])) ? strtolower($cells[$real_idx_type]) : 'expense';
                $payee = ($real_idx_payee !== null) ? ($cells[$real_idx_payee] ?? '') : '';

                // Clean amount (remove Rp, dots, commas)
                $amount = preg_replace('/[^0-9]/', '', (string) $amount);

                // Basic AI Categorization logic here for preview
                $suggested_category = $this->_suggest_category($title, $type);

                if ($raw_date && $amount > 0) {
                    $transactions[] = [
                        'date' => $this->_parse_date($raw_date),
                        'title' => $title,
                        'amount' => $amount,
                        'type' => (strpos($type, 'in') !== false || strpos($type, 'masuk') !== false) ? 'income' : 'expense',
                        'category' => $suggested_category,
                        'payee' => $payee
                    ];
                }
            }

            $data['title'] = 'Review Import';
            $data['transactions'] = $transactions;
            $data['categories'] = $this->Transaction_model->get_categories_by_user(null, $user_id);

            $this->load->view('templates/header', $data);
            $this->load->view('import/preview', $data);
            $this->load->view('templates/main_footer');
        } catch (Exception $e) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Error processing preview: ' . $e->getMessage() . '</div>');
            redirect('import/map');
        }
    }

    public function process()
    {
        $titles = $this->input->post('titles');
        $dates = $this->input->post('dates');
        $amounts = $this->input->post('amounts');
        $types = $this->input->post('types');
        $categories = $this->input->post('categories');
        $payees = $this->input->post('payees');
        $user_id = $this->session->userdata('user_id');

        if (empty($titles)) {
            redirect('import');
        }

        $batch_data = [];
        foreach ($titles as $i => $title) {
            $cat_name = $categories[$i];

            // Auto-create category if it doesn't exist
            $final_category = $this->Transaction_model->get_or_create_category($cat_name, $types[$i], $user_id);

            $batch_data[] = [
                'user_id' => $user_id,
                'title' => $title,
                'amount' => $amounts[$i],
                'type' => $types[$i],
                'category' => $final_category,
                'payee' => $payees[$i] ?? '',
                'transaction_date' => $dates[$i],
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        if (!empty($batch_data)) {
            $this->Transaction_model->add_batch_transactions($batch_data);
            $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-green" role="alert">' . count($batch_data) . ' transactions imported successfully!</div>');
        }

        // Cleanup
        $file_path = $this->session->userdata('import_file');
        if (file_exists($file_path))
            unlink($file_path);
        $this->session->unset_userdata('import_file');

        redirect('dashboard/transactions');
    }

    private function _parse_date($raw)
    {
        if (!$raw)
            return date('Y-m-d');

        // If it's an Excel numeric date
        if (is_numeric($raw) && $raw < 1000000) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($raw)->format('Y-m-d');
            } catch (Exception $e) {
                // fall through
            }
        }

        // Unix timestamp?
        if (is_numeric($raw) && $raw > 10000000) {
            return date('Y-m-d', $raw);
        }

        // Try various string formats
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $f) {
            $d = DateTime::createFromFormat($f, $raw);
            if ($d)
                return $d->format('Y-m-d');
        }

        return date('Y-m-d'); // Fallback
    }

    private function _suggest_category($title, $type)
    {
        $title = strtolower($title);

        $map = [
            'food' => ['makan', 'minum', 'resto', 'cafe', 'starbucks', 'kopi', 'warung', 'grabfood', 'shopeefood', 'bakery', 'coffee'],
            'transport' => ['gojek', 'grab', 'shell', 'pertamina', 'parkir', 'pajak', 'toll', 'tiket', 'train', 'bus'],
            'bill' => ['listrik', 'pln', 'bpjs', 'internet', 'indihome', 'pulsa', 'telkom', 'pdam'],
            'shopping' => ['tokopedia', 'shopee', 'mall', 'uniqlo', 'alfamart', 'indomaret', 'supermarket', 'market', 'belanja'],
            'salary' => ['gaji', 'salary', 'bonus', 'payroll', 'deviden'],
            'investment' => ['saham', 'reksadana', 'crypto', 'bibit', 'ajaib'],
        ];

        foreach ($map as $cat => $keywords) {
            foreach ($keywords as $key) {
                if (strpos($title, $key) !== false) {
                    return ucfirst($cat);
                }
            }
        }

        return 'Other';
    }
}
