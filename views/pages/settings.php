<?php
// views/pages/settings.php

/**
 * This view displays all application settings within a single, tabbed interface.
 */
?>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <h5 class="card-header">تنظیمات پنل</h5>
            <div class="card-body">
                <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-settings-tab" data-bs-toggle="tab" data-bs-target="#general-settings" type="button" role="tab" aria-controls="general-settings" aria-selected="true">
                            تنظیمات عمومی
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="db-settings-tab" data-bs-toggle="tab" data-bs-target="#db-settings" type="button" role="tab" aria-controls="db-settings" aria-selected="false">
                            تنظیمات دیتابیس
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="panels-settings-tab" data-bs-toggle="tab" data-bs-target="#panels-settings" type="button" role="tab" aria-controls="panels-settings" aria-selected="false">
                            مدیریت پنل‌های X-UI
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="settingsTabsContent">
                    <div class="tab-pane fade show active" id="general-settings" role="tabpanel" aria-labelledby="general-settings-tab">
                        <div class="pt-3">
                            <form action="/settings/general" method="POST">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="telegram_support_url" class="form-label">لینک پشتیبانی تلگرام</label>
                                        <input class="form-control" type="url" id="telegram_support_url" name="telegram_support_url" value="<?= htmlspecialchars(setting('telegram_support_url', '')) ?>" placeholder="https://t.me/your_username" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="default_total_traffic_gb" class="form-label">ترافیک پیش‌فرض کاربران (GB)</label>
                                        <input class="form-control" type="number" id="default_total_traffic_gb" name="default_total_traffic_gb" value="<?= htmlspecialchars(setting('default_total_traffic_gb', 0)) ?>" min="0" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="fixed_expiry_date" class="form-label">تاریخ انقضای عمومی</label>
                                        <?php
                                            $expiryTimestamp = setting('fixed_expiry_timestamp');
                                            $expiryDate = $expiryTimestamp ? date('Y-m-d', $expiryTimestamp) : '';
                                        ?>
                                        <input class="form-control" type="date" id="fixed_expiry_date" name="fixed_expiry_date" value="<?= $expiryDate ?>" />
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">ذخیره تنظیمات عمومی</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="db-settings" role="tabpanel" aria-labelledby="db-settings-tab">
                        <div class="pt-3">
                            <form action="/settings/db" method="POST">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="db_host" class="form-label">هاست دیتابیس</label>
                                        <input class="form-control" type="text" id="db_host" name="db_host" value="<?= htmlspecialchars(setting('db_settings.db_host', 'localhost')) ?>" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="db_name" class="form-label">نام دیتابیس</label>
                                        <input class="form-control" type="text" id="db_name" name="db_name" value="<?= htmlspecialchars(setting('db_settings.db_name', '')) ?>" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="db_user" class="form-label">نام کاربری دیتابیس</label>
                                        <input class="form-control" type="text" id="db_user" name="db_user" value="<?= htmlspecialchars(setting('db_settings.db_user', '')) ?>" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="db_pass" class="form-label">رمز عبور دیتابیس</label>
                                        <input class="form-control" type="password" id="db_pass" name="db_pass" placeholder="برای تغییر، مقدار جدید را وارد کنید." />
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">ذخیره تنظیمات دیتابیس</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="panels-settings" role="tabpanel" aria-labelledby="panels-settings-tab">
                        <div class="pt-3">
                            <div class="table-responsive text-nowrap">
                                <?php if (empty($panels)): ?>
                                    <div class="alert alert-warning mx-4">هیچ پنلی یافت نشد.</div>
                                <?php else: ?>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>نام پنل</th>
                                                <th>آدرس API</th>
                                                <th>نام کاربری</th>
                                                <th>وضعیت</th>
                                                <th>عملیات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($panels as $panel): ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($panel['name']) ?></strong></td>
                                                    <td><?= htmlspecialchars($panel['api_url']) ?></td>
                                                    <td><?= htmlspecialchars($panel['username']) ?></td>
                                                    <td>
                                                        <span class="badge bg-label-<?= $panel['is_active'] ? 'success' : 'secondary' ?>">
                                                            <?= $panel['is_active'] ? 'فعال' : 'غیرفعال' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="/settings/panels/edit?id=<?= $panel['id'] ?>" class="btn btn-sm btn-info">ویرایش</a>
                                                        <a href="/settings/panels/delete?id=<?= $panel['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                            
                            <hr class="my-4">

                            <div>
                                 <?php if (isset($panelToEdit)): ?>
                                    <h5 class="mb-3">ویرایش پنل: <?= htmlspecialchars($panelToEdit['name']) ?></h5>
                                    <form action="/settings/panels/update" method="POST">
                                        <input type="hidden" name="id" value="<?= $panelToEdit['id'] ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-6"><label for="panel_name_edit" class="form-label">نام پنل</label><input class="form-control" type="text" id="panel_name_edit" name="name" value="<?= htmlspecialchars($panelToEdit['name']) ?>" required /></div>
                                            <div class="mb-3 col-md-6"><label for="panel_api_url_edit" class="form-label">آدرس API</label><input class="form-control" type="url" id="panel_api_url_edit" name="api_url" value="<?= htmlspecialchars($panelToEdit['api_url']) ?>" required /></div>
                                            <div class="mb-3 col-md-6"><label for="panel_username_edit" class="form-label">نام کاربری X-UI</label><input class="form-control" type="text" id="panel_username_edit" name="username" value="<?= htmlspecialchars($panelToEdit['username']) ?>" required /></div>
                                            <div class="mb-3 col-md-6"><label for="panel_password_edit" class="form-label">رمز عبور جدید</label><input class="form-control" type="password" id="panel_password_edit" name="password" placeholder="برای عدم تغییر، خالی بگذارید" /></div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="submit" class="btn btn-primary me-2">ذخیره تغییرات</button>
                                            <a href="/settings" class="btn btn-label-secondary">لغو</a>
                                        </div>
                                    </form>
                                 <?php else: ?>
                                    <h5 class="mb-3">افزودن پنل جدید</h5>
                                    <form action="/settings/panels/store" method="POST">
                                        <div class="row">
                                            <div class="mb-3 col-md-6"><label for="panel_name" class="form-label">نام پنل</label><input class="form-control" type="text" id="panel_name" name="name" required /></div>
                                            <div class="mb-3 col-md-6"><label for="panel_api_url" class="form-label">آدرس API</label><input class="form-control" type="url" id="panel_api_url" name="api_url" placeholder="https://domain.com/path/" required /></div>
                                            <div class="mb-3 col-md-6"><label for="panel_username" class="form-label">نام کاربری X-UI</label><input class="form-control" type="text" id="panel_username" name="username" required /></div>
                                            <div class="mb-3 col-md-6"><label for="panel_password" class="form-label">رمز عبور X-UI</label><input class="form-control" type="password" id="panel_password" name="password" required /></div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="submit" class="btn btn-primary me-2">افزودن پنل</button>
                                        </div>
                                    </form>
                                 <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>