<?php

namespace App\Models;

use CodeIgniter\Model;

class User_model extends Model
{
    protected $table = 'users';

    public function login($username, $password) 
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('username', $username);
        $query = $builder->get();

        if ($user = $query->getRowArray()) {
            $hashed_password = $user["password"]; 
            return password_verify($password, $hashed_password);
        }
        
        return false;
    }

    public function signup($username, $email, $password, $security_question, $security_answer)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('username', $username);
        $builder->orWhere('email', $email);
        $query = $builder->get();

        if ($query->getRowArray()) {
            return false;
        }

        $user_info = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'security_question' => $security_question,
            'security_answer' => $security_answer,
        ];

        if ($builder->insert($user_info)) {
            return true;
        } else {
            return false;
        }
    }

    public function generateEmailVerificationToken($userId) {
        $token = bin2hex(random_bytes(50));
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('id', $userId);
        $builder->update(['email_verification_token' => $token]);
        return $token;
    }
    
    public function verifyEmail($token) {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $query = $builder->where('email_verification_token', $token)->get();
    
        $user = $query->getRowArray();
    
        if($user) {
            $builder->where('id', $user['id']);
            $builder->update(['is_email_verified' => true]);
            return true;
        }
        return false;
    }    

    public function getUserById($id)
    {
        return $this->asArray()
                    ->where(['id' => $id])
                    ->first();
    }

    public function getUserByUsername($username)
    {
        return $this->asArray()
                    ->where(['username' => $username])
                    ->first();
    }

    public function updateUser($id, $update_data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('id', $id);
        return $builder->update($update_data);
    }

    public function getCommentsByVideoId($video_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('comments');
        $builder->where('video_id', $video_id);
        $builder->orderBy('created_at', 'DESC');
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function addComment($video_id, $user_id, $content)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('comments');
        $data = [
            'video_id' => $video_id,
            'user_id' => $user_id,
            'content' => $content,
        ];

        return $builder->insert($data);
    }

    public function getUsernameById($id)
    {
        $user = $this->asArray()
                    ->where(['id' => $id])
                    ->first();
        return $user ? $user['username'] : null;
    }

    public function likeVideo($video_id, $user_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('video_likes');

        if ($this->hasLikedVideo($video_id, $user_id)) {
            return false; 
        }
        
        $data = [
            'video_id' => $video_id,
            'user_id' => $user_id,
        ];
        $builder->insert($data);
        return true;
    }

    public function hasLikedVideo($video_id, $user_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('video_likes');
        $builder->where('video_id', $video_id);
        $builder->where('user_id', $user_id);
        $query = $builder->get();

        return $query->getRowArray() !== null;
    }

    public function getTotalLikesByVideoId($video_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('video_likes');
        $builder->where('video_id', $video_id);
        $builder->selectCount('id', 'like_count');
        $query = $builder->get();
    
        return $query->getRowArray()['like_count'];
    }

    public function getAllUsersEmails()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('email');
        $query = $builder->get();

        $result = $query->getResultArray();
        $emails = array_map(function($row) { return $row['email']; }, $result);
        
        return $emails;
    }

}