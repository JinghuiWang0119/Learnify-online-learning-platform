<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile</title>
</head>

<body>
    <div class="container">
        <div class="col-4 offset-4">
            <h2 class="text-center">Edit Profile</h2>
            <?= form_open(base_url('profile/update')); ?>
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="New Password">
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm New Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm New Password">
            </div>
            <div class="form-group">
                <?php if (isset($error)) echo $error; ?>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>

</body>

</html>
