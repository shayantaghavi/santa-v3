<?php
// views/pages/users.php

/**
 * This view displays the user management table.
 * It now includes a button to sync users from X-UI panels.
 */
?>

<div class="card">
    <h5 class="card-header d-flex justify-content-between align-items-center">
        <?= htmlspecialchars($pageTitle ?? 'مدیریت کاربران') ?>
        <div>
            <a href="/users/sync" class="btn btn-secondary">
                <i class="bx bx-sync me-1"></i> همگام‌سازی با X-UI
            </a>
            <a href="/users/create" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> افزودن کاربر جدید
            </a>
        </div>
    </h5>
    <div class="table-responsive text-nowrap">
        <?php if (empty($users)): ?>
            <div class="alert alert-warning mx-4" role="alert">
                هیچ کاربری یافت نشد. برای شروع، یک کاربر جدید اضافه کنید.
            </div>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>نام کاربری (Remark)</th>
                        <th>UUID</th>
                        <th>وضعیت آنلاین</th>
                        <th>ترافیک مصرفی</th>
                        <th>تاریخ انقضا (X-UI)</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($user['remark']) ?></strong>
                            </td>
                            <td>
                                <span dir="ltr"><?= htmlspecialchars($user['uuid']) ?></span>
                            </td>
                            <td>
                                <?php if ($user['is_online']): ?>
                                    <?php if ($user['xui_status']): ?>
                                        <span class="badge bg-label-success me-1">آنلاین</span>
                                    <?php else: ?>
                                        <span class="badge bg-label-warning me-1">غیرفعال در X-UI</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-label-danger me-1">آفلاین / یافت نشد</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $consumed = $user['up'] + $user['down'];
                                    echo htmlspecialchars(formatBytes($consumed));
                                    
                                    if ($user['total'] > 0) {
                                        echo ' / ' . htmlspecialchars(formatBytes($user['total']));
                                    }
                                ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(formatTimestamp($user['expiryTime'])) ?>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/users/edit?id=<?= $user['id'] ?>">
                                            <i class="bx bx-edit-alt me-1"></i> ویرایش
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <i class="bx bx-sync me-1"></i> همگام‌سازی با X-UI
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="/users/delete?id=<?= $user['id'] ?>" onclick="return confirm('آیا از حذف این کاربر مطمئن هستید؟')">
                                            <i class="bx bx-trash me-1"></i> حذف
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>