<?php

namespace App\Models;

use CodeIgniter\Model;

class Upload_model extends Model
{
    protected $table = 'Uploads';

    public function upload($title, $filename, $user_id, $email)
    {
        $file = [
            'title' => $title,
            'filename' => $filename,
            'user_id' => $user_id,
            'email' => $email,
        ];
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        if ($builder->insert($file)) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllFiles()
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        return $builder->get()->getResultArray();
    }
}
