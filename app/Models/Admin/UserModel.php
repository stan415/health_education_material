<?php

namespace App\Models\Admin;

use App\Library\Base\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class UserModel extends BaseModel
{
    protected $table = 'user';

    protected $tableColumn = [
        'id' => 'int',
        'pid' => 'int',
        'username' => 'string',
        'password' => 'string',
        'nickname' => 'string',
        'phone_number' => 'int',
        'roles_id' => 'int',
        'status' => 'int',
    ];

    public function files()
    {
        return $this->hasMany(FilesModel::class);
    }
}
