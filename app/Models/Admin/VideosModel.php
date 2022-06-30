<?php


namespace App\Models\Admin;


use App\Library\Base\BaseModel;

class VideosModel extends BaseModel
{
    const TABLE = 'videos';

    protected $table = self::TABLE;

    protected $tableColumn = [
        'id'                => 'int',
        'user_id'           => 'int',
        'file_name'         => 'string',
        'cover_url'         => 'string',
        'file_url'          => 'string',
        'file_type'         => 'string',
        'file_size'         => 'int',
        'label_ids'         => 'string',
        'download_count'    => 'int',
        'liked_count'       => 'int',
        'status'            => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

}