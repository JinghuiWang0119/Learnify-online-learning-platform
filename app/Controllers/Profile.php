<?php

namespace App\Controllers;

use App\Models\User_model;
use CodeIgniter\Controller;

class Profile extends Controller
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $model = new User_model();
        $user = $model->getUserById($session->get('user_id'));

        if (!$user) {
            return redirect()->to(base_url('login'));
        }

        $data['user'] = $user;

        if ($this->request->getMethod() == 'post') {
            $model->updateEmailById($session->get('user_id'), $this->request->getVar('email'));
            $data['user']['email'] = $this->request->getVar('email');
        }

        echo view('template/header');
        echo view('profile', $data);
        echo view('template/footer');
    }

    public function edit()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $model = new User_model();
        $user = $model->getUserByUsername($session->get('username'));

        if (!$user) {
            return redirect()->to(base_url('login'));
        }

        $data['user'] = $user;

        echo view('template/header');
        echo view('edit_profile', $data);
        echo view('template/footer');
    }

    public function update()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $model = new User_model();

        $id = $this->request->getPost('id');
        $email = $this->request->getPost('email');
        $password = trim($this->request->getPost('password'));
        $confirm_password = trim($this->request->getPost('confirm_password'));

        if (!empty($password) && $password !== $confirm_password) {
            echo "New passwords do not match!";
            return;
        }

        $update_data = ['email' => $email];

        if ($password) {
            $update_data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($model->updateUser($id, $update_data)) {
            return redirect()->to(base_url('profile'));
        } else {
            echo "Error updating the user profile!";
        }
    }


}
