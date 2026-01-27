<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' | ' : '' ?> Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <!-- jQuery (Loaded early for view scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Force Sidebar Layout */
        .admin-layout {
            display: flex !important;
            min-height: 100vh !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            border: none !important;
            overflow-x: hidden;
            background-color: #f8fafc;
        }

        .admin-sidebar {
            width: 280px !important;
            min-width: 280px !important;
            background: #ffffff !important;
            border-right: 4px solid #000 !important;
            display: flex !important;
            flex-direction: column !important;
            position: sticky !important;
            top: 0 !important;
            height: 100vh !important;
            z-index: 1060;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-sidebar.collapsed {
            transform: translateX(-280px);
            margin-right: -280px;
        }

        .admin-brand {
            padding: 1.5rem !important;
            border-bottom: 4px solid #000 !important;
            background: #000 !important;
            color: #fff !important;
            text-decoration: none !important;
        }

        .admin-main {
            flex: 1 !important;
            padding: 2.5rem !important;
            min-width: 0 !important;
            background-color: #f8fafc !important;
        }

        .admin-nav-group {
            padding: 1rem 0;
            flex-grow: 1;
        }

        .admin-nav-item {
            display: flex !important;
            align-items: center !important;
            padding: 1rem 1.5rem !important;
            color: #000 !important;
            text-decoration: none !important;
            font-family: 'Space Mono', monospace !important;
            font-weight: 700 !important;
            border-bottom: 2px solid #000 !important;
            transition: all 0.2s;
            font-size: 0.85rem !important;
        }

        .admin-nav-item:hover {
            background: #bfdbfe !important;
            /* Pastel Blue */
        }

        .admin-nav-item.active {
            background: #fef9c3 !important;
            /* Pastel Yellow */
        }

        .admin-nav-item .iconify {
            margin-right: 1rem !important;
            width: 22px !important;
            height: 22px !important;
        }

        .admin-sidebar-footer {
            margin-top: auto;
            padding: 1.5rem;
            border-top: 4px solid #000;
            background: #fff;
        }

        .admin-top-bar {
            display: flex;
            align-items: center;
            margin-bottom: 2.5rem;
            gap: 1.5rem;
        }

        .sidebar-toggle-btn {
            background: #fff !important;
            border: 3px solid #000 !important;
            box-shadow: 4px 4px 0px 0px #000 !important;
            padding: 10px !important;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.1s;
        }

        .sidebar-toggle-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 0px 0px 0px 0px #000 !important;
        }

        @media (max-width: 992px) {
            .admin-sidebar {
                position: fixed !important;
                left: 0;
                transform: translateX(-100%);
                z-index: 2000 !important;
                box-shadow: none !important;
            }
            .admin-sidebar.show-mobile {
                transform: translateX(0);
                box-shadow: 10px 0px 50px rgba(0,0,0,0.3) !important;
            }
            .admin-main {
                padding: 1.5rem !important;
                width: 100% !important;
            }
            .admin-layout {
                flex-direction: column;
            }
            /* Overlay Backdrop */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1999;
                backdrop-filter: blur(2px);
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
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
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-pastel-yellow text-black border-2 border-dark rounded-0 fw-bold px-3 py-2">
                            <?= $this->session->userdata('username'); ?>
                        </span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('dashboard') ?>"
                            class="btn btn-outline-dark border-brutal rounded-0 fw-bold py-2">
                            APP VIEW
                        </a>
                        <a href="<?= base_url('admin/logout') ?>" class="btn btn-dark border-brutal rounded-0 fw-bold py-2">
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