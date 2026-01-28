<div class="d-flex justify-content-between align-items-end mb-5">
    <div>
        <h2 class="font-mono fw-bold text-uppercase m-0">Category Management</h2>
        <p class="text-muted m-0">Manage global income and expense categories.</p>
    </div>
    <button type="button" class="btn btn-dark border-brutal rounded-0 fw-bold px-4 py-2" data-bs-toggle="modal"
        data-bs-target="#addCategoryModal">
        + ADD CATEGORY
    </button>
</div>

<?= validation_errors('<div class="alert alert-danger border-brutal" role="alert">', '</div>'); ?>
<?= $this->session->flashdata('message'); ?>

<div class="card border-brutal bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-gray-100 border-bottom border-black">
                <tr>
                    <th class="p-3 font-mono small fw-bold">NO</th>
                    <th class="p-3 font-mono small fw-bold">NAME</th>
                    <th class="p-3 font-mono small fw-bold">TYPE</th>
                    <!-- <th class="p-3 font-mono small fw-bold">OWNER</th> -->
                    <th class="p-3 font-mono small fw-bold text-end">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($categories as $cat): ?>
                    <tr class="align-middle">
                        <td class="p-3 fw-bold"><?= $no ?></td>
                        <td class="p-3"><?= $cat['name'] ?></td>
                        <td class="p-3">
                            <?php if ($cat['type'] == 'income'): ?>
                                <span
                                    class="badge bg-pastel-green text-black border border-black rounded-0 text-uppercase">INCOME</span>
                            <?php else: ?>
                                <span
                                    class="badge bg-pastel-red text-black border border-black rounded-0 text-uppercase">EXPENSE</span>
                            <?php endif; ?>
                        </td>
                        <!-- <td class="p-3">
                            <?php if (!$cat['user_id']): ?>
                                <span class="badge bg-dark text-white rounded-0">GLOBAL</span>
                            <?php else: ?>
                                <span class="badge border border-black text-black rounded-0 small">USER
                                    #<?= $cat['user_id'] ?></span>
                            <?php endif; ?>
                        </td> -->
                        <td class="p-3 text-end">
                            <?php if (!$cat['user_id']): ?>
                                <button class="btn btn-sm btn-outline-dark border-brutal rounded-0 fw-bold me-1 edit-cat-btn"
                                    data-id="<?= $cat['id'] ?>" data-name="<?= htmlspecialchars($cat['name']) ?>"
                                    data-type="<?= $cat['type'] ?>" data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                                    EDIT
                                </button>
                                <button class="btn btn-sm btn-outline-danger border-brutal rounded-0 fw-bold delete-cat-btn"
                                    data-id="<?= $cat['id'] ?>" data-name="<?= htmlspecialchars($cat['name']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">
                                    DELETE
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                    $no++;
                endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-brutal rounded-0">
            <div class="modal-header border-bottom border-black">
                <h5 class="modal-title font-mono fw-bold text-uppercase">Add Global Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/add_category') ?>" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">CATEGORY NAME</label>
                        <input type="text" name="name" class="form-control border-brutal rounded-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">TYPE</label>
                        <select name="type" class="form-select border-brutal rounded-0" required>
                            <option value="income">INCOME</option>
                            <option value="expense">EXPENSE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-black bg-gray-50">
                    <button type="button" class="btn btn-outline-dark border-brutal rounded-0 fw-bold"
                        data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-dark border-brutal rounded-0 fw-bold">SAVE CATEGORY</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-brutal rounded-0">
            <div class="modal-header border-bottom border-black">
                <h5 class="modal-title font-mono fw-bold text-uppercase">Edit Global Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" action="" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">CATEGORY NAME</label>
                        <input type="text" name="name" id="edit_cat_name" class="form-control border-brutal rounded-0"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">TYPE</label>
                        <select name="type" id="edit_cat_type" class="form-select border-brutal rounded-0" required>
                            <option value="income">INCOME</option>
                            <option value="expense">EXPENSE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-black bg-gray-50">
                    <button type="button" class="btn btn-outline-dark border-brutal rounded-0 fw-bold"
                        data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-dark border-brutal rounded-0 fw-bold">UPDATE CATEGORY</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-brutal rounded-0">
            <div class="modal-header border-bottom border-black bg-pastel-red">
                <h5 class="modal-title font-mono fw-bold text-uppercase">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="m-0">Are you sure you want to delete category "<span id="delete_cat_name_display"
                        class="fw-bold"></span>"? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-top border-black">
                <button type="button" class="btn btn-outline-dark border-brutal rounded-0 fw-bold"
                    data-bs-dismiss="modal">CANCEL</button>
                <a href="" id="confirmDeleteBtn" class="btn btn-danger border-brutal rounded-0 fw-bold">DELETE NOW</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Use delegated events for SWUP compatibility
        $(document).on('click', '.edit-cat-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const type = $(this).data('type');

            $('#editCategoryForm').attr('action', '<?= base_url('admin/edit_category/') ?>' + id);
            $('#edit_cat_name').val(name);
            $('#edit_cat_type').val(type);
        });

        $(document).on('click', '.delete-cat-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#delete_cat_name_display').text(name);
            $('#confirmDeleteBtn').attr('href', '<?= base_url('admin/delete_category/') ?>' + id);
        });

        // Ensure clicking the confirmation link actually works with SWUP
        $(document).on('click', '#confirmDeleteBtn', function (e) {
            const href = $(this).attr('href');
            if (href && href !== '') {
                // If using SWUP, we might want to use its API, but window.location is safer for deletions
                window.location.href = href;
            }
            e.preventDefault();
        });
    });
</script>