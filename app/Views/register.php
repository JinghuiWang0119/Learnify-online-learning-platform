<div class="container">
    <div class="col-4 offset-4">
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <form action="<?= base_url('login/register') ?>" method="post">
            <h2 class="text-center">Sign up</h2>
            <br>
            <p class="text-center">Dive into anything by just a few steps!</p>
            <br>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Username" required="required" name="username">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email" required="required" name="email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password" required="required" name="password">
            </div>
            <p><strong>Notice: Secret question and answer cannot be modified once created.</strong></p>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Secret Question" required="required" name="security_question">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Secret Answer" required="required" name="security_answer">
            </div>
            <p><strong>Notice: Your password should at least including one A-Z, one a-z, one 0-9 and one special characters.</strong></p>
            <br>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Sign up now</button>
            </div>
        </form>
    </div>
</div>
