<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' | ' : '' ?> Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM"
          crossorigin="anonymous">
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
    <?php if ($this->session->userdata('admin_authorized')): ?>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <div class="admin-layout">
            <!-- Sidebar -->
            <aside class="admin-sidebar" id="adminSidebar">
                <a href="<?= base_url('admin') ?>" class="admin-brand">
                    <span class="h4 fw-bold font-mono text-uppercase tracking-wider m-0">Admin Portal</span>
                </a>

                <div class="admin-nav-group">
                    <a href="<?= base_url('admin') ?>"
                        class="admin-nav-item <?= ($this->uri->segment(2) == '') ? 'active' : '' ?>">
                        <span class="iconify" data-icon="lucide:layout-dashboard"></span>
                        DASHBOARD
                    </a>
                    <a href="<?= base_url('admin/users') ?>"
                        class="admin-nav-item <?= ($this->uri->segment(2) == 'users') ? 'active' : '' ?>">
                        <span class="iconify" data-icon="lucide:users"></span>
                        MANAGEMENT
                    </a>
                    <a href="<?= base_url('admin/categories') ?>"
                        class="admin-nav-item <?= ($this->uri->segment(2) == 'categories') ? 'active' : '' ?>">
                        <span class="iconify" data-icon="lucide:tags"></span>
                        CATEGORIES
                    </a>
                </div>

                <div class="admin-sidebar-footer">
                    <div class="d-flex align-items-center gap-3 mb-4 p-2 border border-2 border-dark bg-white shadow-sm">
                        <div
                            class="bg-pastel-yellow border border-2 border-dark p-2 d-flex align-items-center justify-content-center">
                            <span class="iconify" data-icon="lucide:user" data-width="20"></span>
                        </div>
                        <div class="overflow-hidden">
                            <small class="d-block text-muted fw-bold font-mono admin-session-label">ADMIN SESSION</small>
                            <span class="d-block fw-bold text-uppercase text-truncate font-mono admin-session-username">
                                <?= htmlspecialchars($this->session->userdata('username'), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <!-- <a href="<?= base_url('dashboard') ?>"
                            class="btn btn-outline-dark border-brutal rounded-0 fw-bold py-2">
                            APP VIEW
                        </a> -->
                        <a href="<?= base_url('admin/logout') ?>" class="btn btn-dark border-brutal rounded-0 fw-bold py-2"
                            data-no-swup>
                            LOGOUT
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="admin-main" id="adminMain">
                <div class="admin-top-bar">
                    <button id="toggleSidebar" class="sidebar-toggle-btn">
                        <span class="iconify" data-icon="lucide:panel-left" data-width="24"></span>
                    </button>
                    <h1 class="h4 font-mono fw-bold text-uppercase m-0 d-none d-md-block">Finance App Admin</h1>
                </div>
                <div id="swup" class="transition-fade">
                <?php else: ?>
                    <div class="container py-5">
                    <?php endif; ?>