<?php
// views/pages/templates.php

/**
 * This view displays the UI for managing subscription link templates.
 * It receives $pageTitle, $templateLinks, and $globallyDisabledTemplates from the controller.
 */
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <h5 class="card-header">افزودن قالب جدید</h5>
            <div class="card-body">
                <form action="/templates/store" method="POST">
                    <div class="mb-3">
                        <label class="form-label" for="template_name">نام قالب (Key)</label>
                        <input type="text" class="form-control" id="template_name" name="name" placeholder="مثال: VLESS-WS" required />
                        <div class="form-text">یک نام کوتاه و منحصر به فرد به انگلیسی وارد کنید.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="template_link">لینک قالب</label>
                        <input type="text" class="form-control" id="template_link" name="link" placeholder="vless://UUID@domain.com:port?..." dir="ltr" required />
                        <div class="form-text">
                            از `UUID` برای شناسه کاربر و از `#USER` برای نام کاربری (remark) در لینک استفاده کنید.
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary">ذخیره قالب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <h5 class="card-header">لیست قالب‌های موجود</h5>
            <div class="card-body">
                <?php if (empty($templateLinks)): ?>
                    <div class="alert alert-warning" role="alert">
                        هیچ قالبی یافت نشد. برای شروع، یک قالب جدید اضافه کنید.
                    </div>
                <?php else: ?>
                    <form action="/templates/status-update" method="POST">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>فعال/غیرفعال</th>
                                        <th>نام قالب</th>
                                        <th>لینک قالب</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($templateLinks as $name => $link): ?>
                                        <?php $isDisabled = in_array($name, $globallyDisabledTemplates); ?>
                                        <tr>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="active_templates[]" value="<?= htmlspecialchars($name) ?>" <?= !$isDisabled ? 'checked' : '' ?>>
                                                </div>
                                            </td>
                                            <td><strong><?= htmlspecialchars($name) ?></strong></td>
                                            <td style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" dir="ltr">
                                                <?= htmlspecialchars($link) ?>
                                            </td>
                                            <td>
                                                <a href="/templates/edit?name=<?= urlencode($name) ?>" class="btn btn-sm btn-info">ویرایش</a>
                                                <a href="/templates/delete?name=<?= urlencode($name) ?>" class="btn btn-sm btn-danger" onclick="return confirm('آیا از حذف این قالب مطمئن هستید؟')">حذف</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-secondary">ذخیره وضعیت فعال/غیرفعال</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>