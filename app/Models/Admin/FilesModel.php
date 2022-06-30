<?php


namespace App\Models\Admin;


use App\Library\Base\BaseModel;

class FilesModel extends BaseModel
{
    const TABLE = 'files';

    protected $table = self::TABLE;

    protected $tableColumn = [
        'id'                => 'int',
        'user_id'           => 'int',
        'file_name'         => 'string',
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