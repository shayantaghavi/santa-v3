<?php
// views/pages/dashboard.php
?>

<div class="row">
    <div class="col-lg-8 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">خوش آمدید, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>! 🎉</h5>
                        <p class="mb-4">
                            نمای کلی از وضعیت سیستم شما در دسترس است. می‌توانید از طریق منو به بخش‌های مختلف دسترسی داشته باشید.
                        </p>
                        <a href="/users" class="btn btn-sm btn-outline-primary">مشاهده کاربران</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="/assets/img/illustrations/man-with-laptop-light.png" height="140" alt="View Badge User" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-success rounded p-2"><i class="bx bx-server fs-4"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">پنل‌های متصل</span>
                        <h3 class="card-title mb-2"><?= htmlspecialchars($connectedPanels ?? 'N/A') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-info rounded p-2"><i class="bx bx-user fs-4"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">کاربران آنلاین</span>
                        <h3 class="card-title text-nowrap mb-2"><?= htmlspecialchars($onlineUsers ?? 'N/A') ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 order-2">
        <div class="row">
            <div class="col-md-6 col-lg-4 col-xl-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">وضعیت سیستم</h5>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-data"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">اتصال دیتابیس</h6>
                                        <small class="text-muted">وضعیت اتصال به MySQL</small>
                                    </div>
                                    <div class="user-progress">
                                        <?php if ($dbStatus === 'Successful'): ?>
                                            <span class="badge bg-success">موفق</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">ناموفق</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-info"><i class="bx bx-time"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">آخرین آپدیت ترافیک</h6>
                                        <small class="text-muted" dir="ltr"><?= htmlspecialchars($lastTrafficUpdate ?? 'N/A') ?></small>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-8 col-xl-8 mb-4">
                <div class="card h-100">
                    <h5 class="card-header">گزارش فعالیت‌های اخیر (Cron Jobs)</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>وظیفه</th>
                                    <th>وضعیت</th>
                                    <th>پیام</th>
                                    <th>زمان اجرا</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php if (empty($cronLogs)): ?>
                                    <tr><td colspan="4" class="text-center">هیچ گزارشی یافت نشد.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($cronLogs as $log): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($log['job_name']) ?></strong></td>
                                        <td>
                                            <span class="badge bg-label-<?= $log['status'] === 'success' ? 'success' : 'danger' ?>">
                                                <?= htmlspecialchars($log['status']) ?>
                                            </span>
                                        </td>
                                        <td style="white-space: normal; min-width: 250px;"><?= htmlspecialchars($log['message']) ?></td>
                                        <td><span dir="ltr"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($log['executed_at']))) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>