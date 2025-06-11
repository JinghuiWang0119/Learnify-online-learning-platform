<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Profile</title>
</head>

<body>
    <div class="container">
        <h2 class="text-center">User Profile</h2>
        <table class="table table-bordered">
        <tr>
            <td>ID:</td>
            <td><?= $user['id'] ?></td>
        </tr>
        <tr>
            <td>Username:</td>
            <td><?= $user['username'] ?></td>
        </tr>
        <tr>
            <td>Email Verification Status:</td>
            <td><?= $user['is_email_verified'] == 1 ? 'Yes' : 'No' ?></td>
        </tr>
        <tr>
            <td>Password:</td>
            <td><?= $user['password'] ?></td>
        </tr>
        <tr>
            <td>Security Question:</td>
            <td><?= $user['security_question'] ?></td>
        </tr>
        <tr>
            <td>Security Answer:</td>
            <td><?= $user['security_answer'] ?></td>
        </tr>
        </table>
    </div>
    <div class="container d-flex justify-content-center">
        <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary">Edit Profile</a>
    </div>

</body>

</html>
