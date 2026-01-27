<div class="mb-5">
    <h2 class="font-mono fw-bold text-uppercase m-0">User Management</h2>
    <p class="text-muted">View and manage all registered users.</p>
</div>

<?= $this->session->flashdata('message'); ?>

<div class="card border-brutal bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-gray-100 border-bottom border-black">
                <tr>
                    <th class="p-3 font-mono small fw-bold">ID</th>
                    <th class="p-3 font-mono small fw-bold">NAME</th>
                    <th class="p-3 font-mono small fw-bold">USERNAME</th>
                    <th class="p-3 font-mono small fw-bold">ROLE</th>
                    <th class="p-3 font-mono small fw-bold">CREATED AT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="align-middle">
                        <td class="p-3 fw-bold"><?= $user['id'] ?></td>
                        <td class="p-3"><?= $user['name'] ?></td>
                        <td class="p-3"><span
                                class="badge bg-pastel-blue text-black border border-black rounded-0"><?= $user['username'] ?></span>
                        </td>
                        <td class="p-3">
                            <?php if ($user['role'] == 'admin'): ?>
                                <span class="badge bg-pastel-yellow text-black border border-black rounded-0">ADMIN</span>
                            <?php else: ?>
                                <span class="badge border border-black text-black rounded-0">USER</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 small"><?= date('d M Y, H:i', strtotime($user['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>