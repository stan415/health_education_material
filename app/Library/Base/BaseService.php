<?php

namespace App\Library\Base;

use App\Models\Admin\UserModel;
use Illuminate\Support\Facades\Redis;

class BaseService
{
    const ACTION_ADD = 'add';
    const ACTION_EDIT = 'edit';

    protected $action_type = '';

    /**
     * @var BaseModel
     */
    protected $dao;

    public function fillParams($params, $action)
    {
        switch ($action) {
            case self::ACTION_ADD:
                break;
            case self::ACTION_EDIT:
                break;
        }

        return $params;
    }


    /**
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function addRow($params)
    {
        $params = $this->fillParams($params, self::ACTION_ADD);
        return $this->dao->addRow($params);
    }


    /**
     * @param $id
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function editRow($id, $params)
    {
        $params = $this->fillParams($params, self::ACTION_EDIT);

        return $this->dao->editRow($id, $params);
    }


    /**
     * @param $id
     * @return bool|null
     * @throws \Exception
     */
    public function delRow($id)
    {
        return $this->dao->delRow($id);
    }

    /**
     * @param $id
     * @param int $is_e
     * @return mixed
     * @throws \App\Exceptions\NotFoundException
     */
    public function getRowById($id, $is_e = 0)
    {
        return $this->dao->getRowByPrimaryKey($id, $is_e);
    }

    public function getAll($where = [])
    {
        return $this->dao->getList($where);
    }

    public function updateOrCreate($where, $data)
    {
        return $this->dao->updateOrCreateRows($where, $data);
    }

    /**
     * 获取下属机构id
     * @param $id
     * @param bool $self
     * @return array
     */
    public function getChildrenIds($id, $self = true)
    {
        $cKey = 'childrenIds:' . $id;
        $cids = Redis::get($cKey);
        if (!$cids) {
            $cids = $this->getChildrenIdsByid($id, $self);
            Redis::setex($cKey, 3600*24, json_encode($cids));
        } else {
            $cids = json_decode($cids, true);
        }
        return $cids;
    }

    /**
     * 获取下属机构id
     * @param $id
     * @param bool $self
     * @return array
     */
    public function getChildrenIdsByid($id, $self = true)
    {
        $cids = [];
        if ($self) {
            $cids[] = $id;
        }
        if ($id) {
            $ChildrenIds = UserModel::query()->select(['id'])->where('pid', $id)->get()->toArray();
            if ($ChildrenIds) {
                foreach ($ChildrenIds as $childrenId) {
                    $cids[] = $childrenId['id'];
                    $ids = $this->getChildrenIdsByid($childrenId['id']);
                    $cids = array_merge($cids, $ids);
                }
            }
        }
        return array_unique($cids);
    }

    /**
     * 所有上级机构id
     * @param $organ_id
     * @return array|false|string
     */
    public function getParentIds($id, $self = true)
    {
        $pKey = 'parentIds:' . $id;
        $pids = Redis::get($pKey);
        if (!$pids) {
            $pids = $this->getParentIdsById($id, $self);
            Redis::setex($pKey, 3600*24, json_encode($pids));
        } else {
            $pids = json_decode($pids, true);
        }
        return $pids;
    }

    /**
     * 获取上级机构id
     * @param $organ_id
     * @return array|false|string
     */
    public function getParentIdsById($id, $self = true)
    {
        $pids = [];
        if ($self) {
            $pids[] = $id;
        }
        if ($id) {
            $parent_id = UserModel::query()->select(['pid'])->where('id', $id)->first()->toArray();
            if ($parent_id  && $parent_id['pid']) {
                $pids[] = $parent_id['pid'];
                $npids = $this->getParentIdsById( $parent_id['pid'] );
                $pids = array_merge($pids, $npids);
            }
        }
        return $pids;
    }

}
