<?php
namespace App\Controllers;

use CodeIgniter\Files\File;

class Upload extends BaseController
{
	public function index() {
        $data['errors'] = "";
        $model = model('App\Models\Upload_model');
        $data['files'] = $model->getAllFiles();
        echo view('template/header');
        echo view('upload_form', $data);
        echo view('template/footer');
    }

    public function upload_file() {
        $session = session();
        $user_id = $session->get('user_id');

        if (!$user_id) {
            return redirect()->to(base_url('upload'));
        }
    
        $data['errors'] = "";
        $title = $this->request->getPost('title');
        $email = $this->request->getPost('email');
        $files = $this->request->getFiles();
        $uploadModel = model('App\Models\Upload_model');
        $userModel = model('App\Models\User_model');
        $success = true;
    
        $user = $userModel->getUserById($user_id);
        $email = $user['email'];
        
        if(isset($files['userfile'])){
            foreach ($files['userfile'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $filename = $file->getName();
                    $ext = $file->getExtension();
                
                    if($ext == 'jpg' || $ext == 'png') {
                        $filename = $this->process_image($file, $filename, $ext);
                    } else {
                        $file->move(FCPATH . 'uploads');
                    }
                    $check = $uploadModel->upload($title, $filename, $user_id, $email);
                    if (!$check) {
                        $success = false;
                        break;
                    }
    
                    $this->sendReceiptEmail($email, $filename);
    
                }
            }
        }
    
        if ($success) {
            echo view('template/header');
            echo "upload_success!";
            echo view('template/footer');
        } else {
            $data['errors'] = "<div class=\"alert alert-danger\" role=\"alert\"> Upload failed!! </div> ";
            echo view('template/header');
            echo view('upload_form', $data);
            echo view('template/footer');
        }
    }

    private function process_image(File $file, $originalName, $ext) {
        $newName = str_replace('.' . $ext, '_edited.' . $ext, $originalName);
    
        $imageService = \Config\Services::image()
            ->withFile($file)
            ->fit(500, 500, 'center');
    
        $imageService->save(FCPATH . 'uploads/' . $newName, 80);
    
        return $newName;
    }
    
    
    public function download_file($filename) {
        $path = FCPATH . 'uploads/' . $filename;
    
        if (file_exists($path)) {
            return $this->response->download($path, null);
        } else {
            return redirect()->back()->with('error', 'File not found');
        }
    }

    public function sendReceiptEmail($email, $filename)
    {
        $curl = curl_init();
        $submissionTime = date("Y-m-d H:i:s");
    
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
            "subject" => "Receipt for your file upload",
            "htmlContent" => "You have successfully uploaded the file: ". $filename . "<br>Submitted at: " . $submissionTime
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
                "api-key: YOUR_SENDINBLUE_API_KEY",
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
    
}