<div class="container">
    <div class="col-4 offset-4">
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <form action="<?= base_url("login/change_password/$username") ?>" method="post">
            <input type="hidden" name="username" value="<?= $username ?>">
            <h2 class="text-center">Change my password</h2>
            <br>
            <p><strong>Please reset your password:</strong></p>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="New Password" required="required" name="new_password">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Confirm New Password" required="required" name="confirm_password">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Change Password</button>
            </div>
        </form>
    </div>
</div>
