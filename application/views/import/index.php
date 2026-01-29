<div class="animate-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-mono fw-bold m-0 text-uppercase">ADD TRANSACTION</h4>
        <a href="<?= base_url('dashboard/transactions') ?>"
            class="btn btn-sm border-brutal bg-white font-mono fw-bold">BACK</a>
    </div>

    <!-- Choice Card -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <a href="<?= base_url('dashboard/add') ?>" class="text-decoration-none text-black">
                <div class="card card-brutal bg-white h-100 p-3 text-center hover-lift">
                    <span class="iconify mb-1" data-icon="lucide:keyboard" data-width="24"></span>
                    <small class="d-block font-mono fw-bold">MANUAL</small>
                </div>
            </a>
        </div>
        <div class="col-6">
            <div class="card card-brutal bg-pastel-yellow h-100 p-3 text-center active-choice border-4">
                <span class="iconify mb-1" data-icon="lucide:file-spreadsheet" data-width="24"></span>
                <small class="d-block font-mono fw-bold">VIA EXCEL</small>
            </div>
        </div>
    </div>

    <?= $this->session->flashdata('message'); ?>

    <div class="card card-brutal p-5 text-center bg-white mb-4">
        <form action="<?= base_url('import/upload') ?>" method="post" enctype="multipart/form-data" id="uploadForm"
            data-no-swup>
            <div class="mb-4">
                <div class="bg-pastel-blue border-brutal p-5 rounded-0 d-inline-block mb-3">
                    <span class="iconify" data-icon="lucide:upload-cloud" data-width="64"></span>
                </div>
                <h4 class="fw-bold">Click to upload or drag and drop</h4>
                <p class="text-muted font-mono small">Supported formats: .XLS, .XLSX, .CSV (Max 2MB)</p>
            </div>

            <input type="file" name="file" id="fileInput" class="d-none" accept=".csv, .xls, .xlsx">

            <button type="button" id="btnChooseFile" class="btn btn-primary-brutal btn-lg px-5 py-3">
                CHOOSE FILE
            </button>
        </form>
    </div>

    <div class="card card-brutal bg-pastel-yellow p-4">
        <h5 class="fw-bold font-mono text-uppercase mb-3"><span class="iconify me-2" data-icon="lucide:info"></span>HOW
            IT WORKS</h5>
        <ol class="font-mono small mb-0 ps-3">
            <li class="mb-2">Upload your file (any format from your bank/app).</li>
            <li class="mb-2">Tell us which columns are the <b>Date, Title, and Amount</b>.</li>
            <li class="mb-2">Our AI will suggest <b>Categories</b> for each transaction.</li>
            <li>Review and fix any details before saving to your wallet.</li>
        </ol>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Handle button click to reset input and trigger picker
        $('#btnChooseFile').on('click', function () {
            $('#fileInput').val('').click();
        });

        // Trigger upload on file selection
        $('#fileInput').on('change', function () {
            if ($(this).val()) {
                $('#loader-overlay').addClass('active');
                $('#uploadForm').submit();
            }
        });
    });
</script>