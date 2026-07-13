<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' | ' : '' ?> Admin Panel</title>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        DASHBOARD
                    </a>
                    <a href="<?= base_url('admin/users') ?>"
                        class="admin-nav-item <?= ($this->uri->segment(2) == 'users') ? 'active' : '' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        MANAGEMENT
                    </a>
                    <a href="<?= base_url('admin/categories') ?>"
                        class="admin-nav-item <?= ($this->uri->segment(2) == 'categories') ? 'active' : '' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        CATEGORIES
                    </a>
                </div>

                <div class="admin-sidebar-footer">
                    <div class="d-flex align-items-center gap-3 mb-4 p-2 border border-2 border-dark bg-white shadow-sm">
                        <div
                            class="bg-pastel-yellow border border-2 border-dark p-2 d-flex align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                    </button>
                    <h1 class="h4 font-mono fw-bold text-uppercase m-0 d-none d-md-block">Finance App Admin</h1>
                </div>
                <div id="swup" class="transition-fade">
                <?php else: ?>
                    <div class="container py-5">
                    <?php endif; ?>