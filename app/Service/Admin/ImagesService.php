<?php


namespace App\Service\Admin;


use App\Library\Base\BaseService;
use App\Models\Admin\ImagesModel;
use Illuminate\Database\Eloquent\Builder;

class ImagesService extends BaseService
{
    public function __construct()
    {
        $this->dao = new ImagesModel();
    }

    public function getList($conditions = [])
    {
        return ImagesModel::query()
            ->with(['user:id,nickname'])
            ->when(! empty($conditions['nickname'] ?? null), function (Builder $query) use ($conditions) {
                return $query->whereHas('user', function (Builder $query) use ($conditions) {
                    return $query->where('nickname', 'like', '%'.$conditions['nickname'].'%');
                });
            })
            ->when(! empty($conditions['file_name'] ?? null), function (Builder $query) use ($conditions) {
                return $query->where('file_name', 'like', '%'.$conditions['file_name'].'%');
            })
            ->when(is_numeric($conditions['status'] ?? null), function (Builder $query) use ($conditions) {
                return $query->where('status', '=', $conditions['status']);
            })
            ->when(! empty($conditions['label_ids'] ?? null), function (Builder $query) use ($conditions) {
                return $query->where('label_ids', 'like', '%'.$conditions['label_ids'].'%');
            })
            ->when(empty($conditions['get_type'] ?? null), function (Builder $query) use ($conditions) {
                return $query->where('user_id', '=', $conditions['user_id']);
            })
            ->when(! empty($conditions['get_type'] ?? null), function (Builder $query) use ($conditions) {
                return $query->whereIn('user_id', $this->getChildrenIds($conditions['user_id']));
            })
            ->paginate($conditions['page_size'] ?? 10);
    }
}