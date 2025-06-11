<?php

namespace App\Models;

use CodeIgniter\Model;

class Video_model extends Model
{
    protected $table = 'videos';

    public function getVideos()
    {
        return $this->findAll();
    }

    public function getVideoById($id)
    {
        return $this->where('id', $id)->first();
    }

    public function getYoutubeVideoId($url)
    {
        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $matches)) {
            return $matches[1];
        } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $matches)) {
            return $matches[1];
        } elseif (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $matches)) {
            return $matches[1];
        } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }

    public function addCourse($data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('videos');
        return $builder->insert($data);
    }

    public function searchVideos($query)
    {
        return $this->like('title', $query)->findAll();
    }

}
