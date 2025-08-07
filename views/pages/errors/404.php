<?php
// views/pages/errors/404.php
?>
<!DOCTYPE html>

<html lang="fa" class="light-style" dir="rtl" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= htmlspecialchars($pageTitle ?? 'خطای 404') ?></title>

    <meta name="description" content="" />

    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.css">

    <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    
    <link rel="stylesheet" href="/assets/vendor/css/pages/page-misc.css" />
    
    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/js/config.js"></script>
</head>

<body>
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
            <h2 class="mb-2 mx-2">صفحه یافت نشد :(</h2>
            <p class="mb-4 mx-2">متاسفانه آدرسی که به دنبال آن بودید در این سیاره وجود ندارد.</p>
            <a href="/" class="btn btn-primary">بازگشت به خانه</a>
            <div class="mt-3">
                <img src="/assets/img/illustrations/page-misc-error-light.png" alt="page-misc-error-light" width="500" class="img-fluid" data-app-dark-img="illustrations/page-misc-error-dark.png" data-app-light-img="illustrations/page-misc-error-light.png" />
            </div>
        </div>
    </div>
    </body>
</html>