<?php
// views/pages/users_create.php

/**
 * This view displays the form for creating a new user.
 * It receives the $pageTitle variable from UserController.
 */
?>

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?= htmlspecialchars($pageTitle ?? 'افزودن کاربر') ?></h5>
                <a href="/users" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> بازگشت به لیست
                </a>
            </div>
            <div class="card-body">
                <form action="/users/store" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label" for="remark">نام کاربری اصلی (Remark)</label>
                        <input type="text" class="form-control" id="remark" name="remark" placeholder="مثال: Mostafa-01" required />
                        <div class="form-text">این نام کاربری باید منحصر به فرد باشد.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="uuid">UUID کاربر</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="uuid" name="uuid" placeholder="یک UUID جدید تولید یا وارد کنید" required />
                            <button class="btn btn-outline-primary" type="button" onclick="generateAndSetUUID()">تولید UUID</button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary">ذخیره کاربر</button>
                        <a href="/users" class="btn btn-label-secondary">لغو</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
function generateAndSetUUID() {
    // A simple UUID v4 generator
    const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0,
            v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
    document.getElementById('uuid').value = uuid;
}

// Generate a UUID on page load for convenience
document.addEventListener('DOMContentLoaded', generateAndSetUUID);
</script>