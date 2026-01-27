<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wallet</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="app-container shadow-brutal-lg">
        <!-- Top Bar -->
        <nav class="navbar navbar-light bg-white border-bottom border-black border-2 px-3 sticky-top"
            style="z-index: 1020;">
            <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
                <span class="navbar-brand mb-0 h1 fw-bold font-mono text-uppercase tracking-wider">My Wallet</span>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($this->session->userdata('role') === 'admin'): ?>
                        <a href="<?= base_url('admin') ?>" class="btn btn-sm btn-dark border-brutal rounded-0 fw-bold px-3">
                            ADMIN
                        </a>
                    <?php endif; ?>
                    <span class="badge bg-pastel-yellow text-black border border-black rounded-0 fw-bold">
                        <?= $this->session->userdata('name'); ?>
                    </span>
                    <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm border-0">
                        <span class="iconify" data-icon="lucide:log-out" style="width: 20px; height: 20px;"></span>
                    </a>
                </div>
            </div>
        </nav>
        <div id="swup" class="content p-3 pb-5 mb-5 transition-fade">