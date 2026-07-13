<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wallet</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <!-- jQuery (Loaded early for view scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>
</head>

<body class="bg-gray-50">
    <div class="app-container shadow-brutal-lg">
        <!-- Top Bar -->
        <nav class="navbar navbar-light bg-white border-bottom border-black border-2 px-3 sticky-top navbar-sticky">
            <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
                <span class="navbar-brand mb-0 h1 fw-bold font-mono text-uppercase tracking-wider">My Wallet</span>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($this->session->userdata('role') === 'admin'): ?>
                        <a href="<?= base_url('admin') ?>" class="btn btn-sm btn-dark border-brutal rounded-0 fw-bold px-3"
                            data-no-swup>
                            ADMIN
                        </a>
                    <?php endif; ?>
                    <span class="badge bg-pastel-yellow text-black border border-black rounded-0 fw-bold">
                        <?= htmlspecialchars($this->session->userdata('name'), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-logout"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </a>
                </div>
            </div>
        </nav>
        <div id="swup" class="content p-3 pb-5 mb-5 transition-fade">

            <!-- Loader Overlay -->
            <div id="loader-overlay" class="loader-overlay">
                <div class="card card-brutal p-4 bg-pastel-yellow text-center animate-bounce-custom">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-3 animate-spin-custom"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    <h4 class="font-mono fw-bold">PROCESSING DATA...</h4>
                    <p class="font-mono small text-muted mb-0">Please wait while we crunch the numbers.</p>
                </div>
            </div>
