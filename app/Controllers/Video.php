<?php

namespace App\Controllers;

use App\Models\Video_model;
use CodeIgniter\Controller;

class Video extends Controller
{
    public function index()
    {
        $model = new Video_model();
        $videos = $model->getVideos();

        $data['videos'] = $videos;
        $data['video_id'] = $videos ? $model->getYoutubeVideoId($videos[0]['url']) : '';

        $data['totalVideos'] = count($videos);
        $data['query'] = '';

        echo view('template/header');
        echo view('video', $data);
        echo view('template/footer');
    }

    public function play($id)
    {
        $model = new Video_model();
        $video = $model->getVideoById($id);

        if (!$video) {
            return redirect()->to(base_url('video'));
        }

        $data['video'] = $video;
        $data['video_id'] = $model->getYoutubeVideoId($video['url']);
        $userModel = model('App\Models\User_model');
        $data['comments'] = $userModel->getCommentsByVideoId($id);
        $data['usernames'] = [];

        foreach ($data['comments'] as $comment) {
            $data['usernames'][$comment['user_id']] = $userModel->getUsernameById($comment['user_id']);
        }
        $data['videos'] = $model->getVideos();

        echo view('template/header');
        echo view('play_video', $data);
        echo view('template/footer');
    }

    public function submit_comment()
    {
        $video_id = $this->request->getPost('video_id');
        $content = $this->request->getPost('content');
        $session = session();
        $user_id = $session->get('user_id');

        $model = model('App\Models\User_model');
        $model->addComment($video_id, $user_id, $content);

        $subject = "New comment added";
        $message = "A new comment has been added to video id: {$video_id}. Comment: {$content}. Check it out at " . base_url("video/play/{$video_id}");
        $this->sendEmailToUsers($subject, $message);

        return redirect()->to(base_url("video/play/{$video_id}"));
    }

    public function get_comments()
    {
        $video_id = $this->request->getGet('video_id');
        $model = model('App\Models\User_model');
        $comments = $model->getCommentsByVideoId($video_id);

        $usernames = [];
        foreach ($comments as $comment) {
            $usernames[$comment['user_id']] = $model->getUsernameById($comment['user_id']);
        }

        $formattedComments = [];
        foreach ($comments as $comment) {
            $formattedComments[] = [
                'username' => $usernames[$comment['user_id']],
                'content' => $comment['content'],
                'created_at' => date('F j, Y, g:i a', strtotime($comment['created_at']))
            ];
        }

        echo json_encode($formattedComments);
    }

    public function like_video()
    {
        $video_id = $this->request->getPost('video_id');
        $session = session();
        $user_id = $session->get('user_id');

        if (!$user_id) {
            echo json_encode(['success' => false, 'error' => 'Please log in to like the video.']);
            return;
        }

        $model = model('App\Models\User_model');
        $success = $model->likeVideo($video_id, $user_id);

        if ($success) {
            $total_likes = $model->getTotalLikesByVideoId($video_id);
            $this->sendEmailToUsers("A Video Has Been Liked", "A video has been liked. Check it out at " . base_url("video/play/{$video_id}"));
            echo json_encode(['success' => true, 'total_likes' => $total_likes]);
        } else {
            echo json_encode(['success' => false, 'error' => 'You have already liked this video.']);
        }
    }

    public function get_likes()
    {
        $video_id = $this->request->getGet('video_id');
        $model = model('App\Models\User_model');
        $total_likes = $model->getTotalLikesByVideoId($video_id);
        echo json_encode($total_likes);
    }

    public function add_course()
    {
        $session = session();
        $username = $session->get('username');
        if ($username !== 'admin') {
            echo 'You are not authorized to add a course.';
            return;
        }

        $data = [
            'id' => $this->request->getPost('id'),
            'title' => $this->request->getPost('title'),
            'filename' => $this->request->getPost('filename'),
            'url' => $this->request->getPost('url'),
            'description1' => $this->request->getPost('description1'),
            'description2' => $this->request->getPost('description2'),
        ];
        $model = new Video_model();
        if ($model->addCourse($data)) {
            $this->sendEmailToUsers("New Video Added", "A new video has been added. Check it out at " . base_url("video/play/{$data['id']}"));
            return redirect()->to(base_url('video'));
        } else {
            $this->session->setFlashdata('error', 'Failed to add the new course.');
            return redirect()->back();
        }
    }

    public function search()
    {
        $query = $this->request->getGet('query');

        $model = new Video_model();
        $videos = $model->searchVideos($query);
        $totalVideos = count($videos);

        $data['videos'] = $videos;
        $data['video_id'] = $videos ? $model->getYoutubeVideoId($videos[0]['url']) : '';
        $data['model'] = $model;
        $data['query'] = $query;
        $data['totalVideos'] = $totalVideos;

        echo view('template/header');
        echo view('video', $data);
        echo view('template/footer');
    }

    private function sendEmailToUsers($subject, $message) {
        $model = model('App\Models\User_model');
        $userEmails = $model->getAllUsersEmails();
    
        $curl = curl_init();
    
        $postData = array(
            "sender" => array(
                "name" => "Learnify",
                "email" => "wjh277027302@gmail.com"
            ),
            "to" => array_map(function($email) { return array("email" => $email); }, $userEmails),
            "subject" => $subject,
            "htmlContent" => $message
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