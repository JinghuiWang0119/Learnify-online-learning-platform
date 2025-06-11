<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index()
    {

        $data['error'] = "";
        $data['username'] = "";
        $data['password'] = "";

        $usernameCookie = $this->request->getCookie("username");
        $passwordCookie = $this->request->getCookie("password");

        if ($usernameCookie && $passwordCookie) {
            $data['username'] = base64_decode($usernameCookie);
            $data['password'] = base64_decode($passwordCookie);
        }

        $session = session();
        $isLoggedIn = $session->get('isLoggedIn');

        if ($isLoggedIn) {
            echo view('template/header');
            echo view('welcome_message');
            echo view('template/footer');
        } else {
            echo view('template/header');
            echo view('login', $data);
            echo view('template/footer');
        }
    }

    public function check_login() 
    {
        $data['error'] = "<div class=\"alert alert-danger\" role=\"alert\"> Incorrect username or password!! </div> ";
        $session = session();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $model = model('App\Models\User_model');
        $user = $model->getUserByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $session->set('username', $username);
            $session->set('isLoggedIn', true);
            $session->set('user_id', $user['id']);
            if ($this->request->getPost('remember_me')) {
                $encryptedUsername = base64_encode($username);
                $encryptedPassword = base64_encode($password);
                $this->response->setCookie("username", $encryptedUsername, time() + (86400 * 30), "/");
                $this->response->setCookie("password", $encryptedPassword, time() + (86400 * 30), "/");
            }
            return redirect()->to(base_url('video'));
        } else {
            echo view('template/header');
            echo view('login', $data);
            echo view('template/footer');
        }
    }

    public function logout() 
    {
        $session = session();
        $session->destroy();

        $this->response->deleteCookie("username", "/");
        $this->response->deleteCookie("password", "/");

        return redirect()->to(base_url('login'));
    }

    public function register()
    {
        $data['error'] = "";
        if ($this->request->getMethod() == 'post') {
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $security_question = $this->request->getPost('security_question');
            $security_answer = $this->request->getPost('security_answer');

            if (!$this->is_password_strong($password)) {
                $data['error'] = "Password is not strong enough!";
                echo view('template/header');
                echo view('register', $data);
                echo view('template/footer');
                return;
            }

            $model = model('App\Models\User_model');
            $user = $model->getUserByUsername($username);

            if ($user) {
                $data['error'] = "Error registering the user! Username already exists.";
                echo view('template/header');
                echo view('register', $data);
                echo view('template/footer');
                return;
            }

            $signup = $model->signup($username, $email, $password, $security_question, $security_answer);

            if ($signup) {
                $user = $model->getUserByUsername($username);
                if($user) {
                    $token = $model->generateEmailVerificationToken($user['id']);
                    $this->sendVerificationEmail($email, $token);
                }
                return redirect()->to(base_url('login'));
            } else {
                $data['error'] = "Error registering the user! Please try again.";
                echo view('template/header');
                echo view('register', $data);
                echo view('template/footer');
            }
        } else {
            echo view('template/header');
            echo view('register', $data);
            echo view('template/footer');
        }
    }

    private function sendVerificationEmail($email, $token) {
        $curl = curl_init();
    
        $postData = array(
            "sender" => array(
                "name" => "Learnify",
                "email" => "wjh277027302@gmail.com"
            ),
            "to" => array(
                array(
                    "email" => $email
                )
            ),
            "subject" => "Email Verification",
            "htmlContent" => "Click this link to verify your email: ". base_url("login/verify_email/$token")
        );
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/smtp/email",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "api-key: SENDINBLUE_API_KEY",
                "content-type: application/json"
            ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if ($err) {
            log_message('error', $err);
            return false;
        } else {
            return true;
        }
    }    

    public function verify_email($token = '') {
        $model = model('App\Models\User_model');
        if ($model->verifyEmail($token)) {
            echo "Email verified successfully!";
        } else {
            echo "Invalid verification link or email already verified.";
        }
    }    

    private function is_password_strong($password)
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            return false;
        }

        return true;
    }

    public function forgot_password()
    {
        $data['error'] = '';
        $data['secret_question'] = '';
        $data['username'] = '';

        if ($this->request->getMethod() == 'post') {
            $username = $this->request->getPost('username');
            $model = model('App\Models\User_model');
            $user = $model->getUserByUsername($username);

            if ($user) {
                $security_answer = $this->request->getPost('security_answer');
                if (!empty($security_answer)) {
                    if ($security_answer === $user['security_answer']) {
                        return redirect()->to(base_url("login/change_password/$username"));
                    } else {
                        $data['error'] = "Incorrect username or answer!!";
                    }
                }
                $data['username'] = $username;
                $data['secret_question'] = $user['security_question'];
            } else {
                $data['error'] = "Incorrect username or answer!!";
            }
        }

        echo view('template/header');
        echo view('forgot_password', $data);
        echo view('template/footer');
    }

    public function change_password($username = '')
    {
        $data['error'] = '';
        $data['username'] = $username;

        if ($this->request->getMethod() == 'post') {
            $username = $this->request->getPost('username');
            $new_password = $this->request->getPost('new_password');
            $confirm_password = $this->request->getPost('confirm_password');

            if ($new_password !== $confirm_password) {
                $data['error'] = "Passwords do not match!";
            } else {
                $model = model('App\Models\User_model');
                $user = $model->getUserByUsername($username);

                if ($user) {
                    $update_data = [
                        'password' => password_hash($new_password, PASSWORD_DEFAULT),
                    ];
                    $model->updateUser($user['id'], $update_data);
                    return redirect()->to(base_url('login'));
                } else {
                    $data['error'] = "Error changing the password! Please try again.";
                }
            }
        }

        echo view('template/header');
        echo view('change_password', $data);
        echo view('template/footer');
    }


}