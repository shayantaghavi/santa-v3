<?php
// views/pages/subscription_info.php

/**
 * This is the public-facing view for users who open their subscription link in a browser.
 * It's a standalone page and does not use the admin panel layout.
 */

// Calculate traffic stats
$consumedBytes = ($traffic['up'] ?? 0) + ($traffic['down'] ?? 0);
$totalBytes = ($totalGB ?? 0) * 1024 * 1024 * 1024;
$consumedPercent = ($totalBytes > 0) ? round(($consumedBytes / $totalBytes) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'وضعیت اشتراک') ?></title>
    
    <link rel="stylesheet" href="/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.css">
    
    <style>
        body {
            background-color: #f5f5f9;
            font-family: 'Vazirmatn', sans-serif;
        }
        .subscription-card {
            max-width: 500px;
            margin: 50px auto;
        }
        .qr-code {
            max-width: 200px;
            margin: 1rem auto;
            display: block;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card subscription-card">
        <div class="card-header text-center">
            <h5 class="card-title mb-0">وضعیت اشتراک</h5>
            <p class="text-primary mb-0"><?= htmlspecialchars($user['remark']) ?></p>
        </div>
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($subscriptionLink) ?>" 
                     alt="QR Code" 
                     class="qr-code img-thumbnail">
                <p class="text-muted small">برای افزودن به کلاینت، کد QR را اسکن کنید</p>
            </div>

            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>حجم مصرفی:</span>
                    <strong><?= formatBytes($consumedBytes) ?> / <?= $totalGB > 0 ? formatBytes($totalBytes) : 'نامحدود' ?></strong>
                </li>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>وضعیت ترافیک:</span>
                        <span>%<?= $consumedPercent ?></span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: <?= $consumedPercent ?>%;" aria-valuenow="<?= $consumedPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>تاریخ انقضا:</span>
                    <strong><?= formatTimestamp($expiryTimestamp) ?></strong>
                </li>
            </ul>

            <div class="d-grid gap-2 mt-4">
                <button class="btn btn-primary" id="copyLinkBtn">
                    <i class="bx bx-copy"></i> کپی کردن لینک اشتراک
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('copyLinkBtn').addEventListener('click', function() {
        const linkToCopy = "<?= htmlspecialchars($subscriptionLink, ENT_QUOTES, 'UTF-8') ?>";
        const btn = this;

        navigator.clipboard.writeText(linkToCopy).then(function() {
            const originalText = btn.innerHTML;
            btn.innerHTML = 'کپی شد!';
            btn.classList.add('btn-success');
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
            }, 2000);
        }, function(err) {
            alert('خطا در کپی کردن لینک: ', err);
        });
    });
</script>

</body>
</html>