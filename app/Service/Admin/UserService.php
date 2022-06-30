<?php


namespace App\Service\Admin;


use App\Exceptions\ValidateException;
use App\Library\Base\BaseService;
use App\Models\Admin\FilesModel;
use App\Models\Admin\ImagesModel;
use App\Models\Admin\UserModel;
use App\Models\Admin\VideosModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Redis;

class UserService extends BaseService
{
    public function __construct()
    {
        $this->dao = new UserModel();
    }

    public function getList($conditions = [])
    {
        return UserModel::query()
            ->select(['id', 'pid', 'username', 'nickname', 'phone_number', 'roles_id', 'status', 'created_at'])
            ->when(! empty($conditions['pid'] ?? null), function (Builder $query) use ($conditions) {
                return $query->where('pid', '=', $conditions['pid']);
            })
            ->when(! empty($conditions['nickname'] ?? null), function (Builder $query) use ($conditions) {
                return $query->where('nickname', 'like', '%'.$conditions['nickname'].'%');
            })
            ->when(is_numeric($conditions['status'] ?? null), function (Builder $query) use ($conditions) {
                return $query->where('status', '=', $conditions['status']);
            })->when(! empty($conditions['user_id'] ?? null), function (Builder $query) use ($conditions) {
                return $query->whereIn('id', $this->getChildrenIds($conditions['user_id']));
            })
            ->paginate($conditions['page_size'] ?? 10);
    }

    public function handleLike($params = [])
    {
        //每日单个用户只可对每个资源点赞一次
        $likeCacheKey = "like_key:u=" . $params['user_id'] . "t=" . $params['source_type'] . "id=" . $params['source_id'];
        if (Redis::get($likeCacheKey)) {
            throw new ValidateException('您今天已对此资料点过赞了，请勿重复点赞！');
        }
        $model = '';
        switch ($params['source_type']) {
            case 1:
                $model = VideosModel::query();
                break;
            case 2:
                $model = ImagesModel::query();
                break;
            case 3:
                $model = FilesModel::query();
                break;
        }
        if(!$model->where('id', $params['source_id'])->first()) {
            throw new ValidateException('当前资源不存在！');
        }
        $model->increment('liked_count', 1);
        $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
        Redis::setex($likeCacheKey, $expireTime, 1);
    }

}