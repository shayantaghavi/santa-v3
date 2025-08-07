<?php
// views/pages/users_edit.php

/**
 * This view displays the form for editing an existing user.
 * It receives the $pageTitle and $user variables from UserController.
 */
?>

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?= htmlspecialchars($pageTitle ?? 'ویرایش کاربر') ?></h5>
                <a href="/users" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> بازگشت به لیست
                </a>
            </div>
            <div class="card-body">
                <form action="/users/update" method="POST">
                    
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

                    <div class="mb-3">
                        <label class="form-label" for="remark">نام کاربری اصلی (Remark)</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="remark" 
                            name="remark" 
                            placeholder="مثال: Mostafa-01" 
                            value="<?= htmlspecialchars($user['remark']) ?>" 
                            required 
                        />
                        <div class="form-text">این نام کاربری باید منحصر به فرد باشد.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="uuid">UUID کاربر</label>
                        <div class="input-group">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="uuid" 
                                name="uuid" 
                                value="<?= htmlspecialchars($user['uuid']) ?>" 
                                readonly 
                            />
                        </div>
                         <div class="form-text">UUID در این فرم قابل ویرایش نیست.</div>
                    </div>
                     
                    <div class="mb-3">
                        <label class="form-label" for="status">وضعیت</label>
                        <select id="status" name="status" class="form-select">
                            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>
                                فعال
                            </option>
                            <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>
                                غیرفعال
                            </option>
                        </select>
                    </div>


                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                        <a href="/users" class="btn btn-label-secondary">لغو</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>