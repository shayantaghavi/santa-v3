<?php
// views/auth/login.php
?>
<!DOCTYPE html>
<html lang="fa" class="light-style customizer-hide" dir="rtl" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= htmlspecialchars($pageTitle ?? 'ورود به پنل') ?></title>

    <meta name="description" content="" />

    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.css">

    <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/assets/css/demo.css" />

    <link rel="stylesheet" href="/assets/vendor/css/pages/page-auth.css" />

    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/js/config.js"></script>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center">
                            <a href="index.html" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-body fw-bolder">پنل مدیریت</span>
                            </a>
                        </div>
                        <h4 class="mb-2 text-center">به پنل خود خوش آمدید! 👋</h4>
                        <p class="mb-4 text-center">لطفا برای دسترسی به حساب خود وارد شوید</p>
                        
                        <?php if (isset($_SESSION['flash_message'])): ?>
                            <?php $message = $_SESSION['flash_message']; ?>
                            <div class="alert alert-<?= htmlspecialchars($message['type']) ?>" role="alert">
                                <?= htmlspecialchars($message['text']) ?>
                            </div>
                            <?php unset($_SESSION['flash_message']); ?>
                        <?php endif; ?>

                        <form id="formAuthentication" class="mb-3" action="/login/handle" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">نام کاربری</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="نام کاربری خود را وارد کنید" autofocus required />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">رمز عبور</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" placeholder="············" aria-describedby="password" required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">ورود</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
        </div>
    </div>
    <script src="/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="/assets/vendor/libs/popper/popper.js"></script>
    <script src="/assets/vendor/js/bootstrap.js"></script>
</body>
</html>