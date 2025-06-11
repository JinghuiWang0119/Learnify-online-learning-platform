<?php
$storedUsername = isset($username) ? $username : '';
$storedPassword = isset($password) ? $password : '';
?>

<div class="container">
    <div class="col-4 offset-4">
        <?= form_open(base_url('login/check_login')); ?>
        <h2 class="text-center">Log in</h2>
        <br>
        <p class="text-center"><strong>Dive into anything!</strong></p>
        <br>
        <div class="form-group">
            <input type="text" name="username" class="form-control" placeholder="Username" value="<?= $storedUsername ?>" required autofocus>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="password" id="password" placeholder="Password" value="<?= $storedPassword ?>">
        </div>
        <div class="form-group">
            <?= $error ?>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Log in</button>
        </div>
        <div class="clearfix">
            <div class="custom-control custom-checkbox float-left">
                <input type="checkbox" class="custom-control-input" id="remember_me" name="remember_me">
                <label class="custom-control-label" for="remember_me">Remember me</label>
            </div>
            <a href="<?= base_url('login/forgot_password'); ?>" class="float-right" style="line-height: 40px;">Forgot Password?</a>
        </div>
        <div class="clearfix">
            <a href="<?= base_url('login/register'); ?>" class="float-right">Haven't signed up?</a>
        </div>
        <?= form_close(); ?>
    </div>
</div>
