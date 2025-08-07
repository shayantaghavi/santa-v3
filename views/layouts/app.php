<?php
// views/layouts/app.php
?>
<!DOCTYPE html>
<html lang="fa" class="light-style layout-menu-fixed" dir="rtl" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= htmlspecialchars($pageTitle ?? 'پنل مدیریت') ?></title>

    <meta name="description" content="پنل مدیریت اشتراک" />

    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.css">

    <link rel="stylesheet" href="/assets/vendor/fonts/boxicons.css" />

    <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/assets/css/demo.css" />

    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/js/config.js"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="/" class="app-brand-link">
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">پنل مدیریت</span>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <li class="menu-item active">
                        <a href="/dashboard" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div>داشبورد</div>
                        </a>
                    </li>
                     <li class="menu-item">
                        <a href="/users" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div>مدیریت کاربران</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/templates" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-layer"></i>
                            <div>مدیریت قالب‌ها</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/settings" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-cog"></i>
                            <div>تنظیمات</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <div class="layout-page">
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                           <li class="nav-item">
                               <a class="nav-link" href="/logout">
                                   <i class="bx bx-power-off me-2"></i>
                                   <span class="align-middle">خروج</span>
                               </a>
                           </li>
                           </ul>
                    </div>
                </nav>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        
                        <?php if (isset($_SESSION['flash_message'])): ?>
                            <?php $message = $_SESSION['flash_message']; ?>
                            <div class="alert alert-<?= htmlspecialchars($message['type']) ?> alert-dismissible" role="alert">
                                <?= htmlspecialchars($message['text']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['flash_message']); ?>
                        <?php endif; ?>
                        <?php require $contentFile; ?>
                        
                    </div>
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ساخته شده با ❤️
                            </div>
                        </div>
                    </footer>
                    <div class="content-backdrop fade"></div>
                </div>
                </div>
            </div>
    </div>
    <script src="/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="/assets/vendor/libs/popper/popper.js"></script>
    <script src="/assets/vendor/js/bootstrap.js"></script>
    <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/assets/vendor/js/menu.js"></script>
    
    <script src="/assets/js/main.js"></script>

</body>
</html>