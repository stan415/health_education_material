<?php


namespace App\Models\Admin;


use App\Library\Base\BaseModel;

class DownloadModel extends BaseModel
{
    protected $table = 'download';

    protected $tableColumn = [
        'id'             => 'int',
        'user_id'        => 'int',
        'source_type'    => 'int',
        'source_id'      => 'int',
        'status'         => 'int'
    ];

    public function videos()
    {
        return $this->hasOne(VideosModel::class, 'id', 'source_id');
    }

    public function images()
    {
        return $this->hasOne(ImagesModel::class, 'id', 'source_id');
    }

    public function files()
    {
        return $this->hasOne(FilesModel::class, 'id', 'source_id');
    }

}