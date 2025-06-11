<div class="container">
    <div class="col-4 offset-4">
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <?= form_open(base_url('login/forgot_password')); ?>
        <h2 class="text-center">Forgot your password?</h2>
        <br>
        <p>Don't worry!</p>
        <p>Type your username so we can proceed:</p>
        <?php if (!empty($secret_question)): ?>
            <p><strong>Username checked!</strong></p>
            <p>Answer the security question to proceed:</p>
            <p><strong><?= $secret_question ?></strong></p>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Your Answer" required="required" name="security_answer">
                <input type="hidden" name="username" value="<?= $username ?>">
            </div>
        <?php else: ?>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Username" required="required" name="username">
            </div>
        <?php endif; ?>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </div>
        <?= form_close(); ?>
    </div>
</div>
