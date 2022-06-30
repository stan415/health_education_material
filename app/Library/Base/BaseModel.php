<?php

namespace App\Library\Base;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidateException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Library\Base
 * @method updateWhere
 */
class BaseModel extends Model
{
    protected $guarded = ['id'];

    protected static $tableName = '';

    /**
     * 字段定义
     * @var array
     */
    protected $tableColumn = [];

    /**
     * 数据表字段默认值
     *
     * @var array
     */
    protected $tableColumnDefaultValue = [];

    /**
     * 返回不同类型的默认值
     *
     * @param string $cast
     * @return float|int|string|null
     */
    private function castDefaultColumn($cast)
    {
        $result = '';

        switch ($cast) {
            case 'int':
                $result = 0;
                break;
            case 'float':
            case 'double':
                $result = 0.00;
                break;
            case 'null':
                $result = null;
                break;
            case 'string':
            default:
                break;
        }

        return $result;
    }


    public function getCreatedAtAttribute()
    {
        return date('Y-m-d H:i:s', strtotime($this->attributes['created_at']));
    }


    /**
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|Model
     * @throws ValidateException
     */
    public function addRow($params)
    {
        $data = [];

        $this->filterTableColumn();
        foreach ($this->tableColumn as $column => $cast) {
            if (empty($params[$column])) {
                if (isset($this->tableColumnDefaultValue[$column])) {
                    $data[$column] = $this->tableColumnDefaultValue[$column];
                } else {
                    $data[$column] = $this->castDefaultColumn($cast);
                }
            } else {
                if (is_string($params[$column])) {
                    $data[$column] = trim($params[$column]);
                } else {
                    $data[$column] = $params[$column];
                }
            }
        }

        if (empty($data)) {
            throw new ValidateException('数据库无有效字段');
        } else {
            try {
                return self::query()->create($data);
            } catch (ValidateException $e) {
                throw new ValidateException('添加异常');
            }

        }
    }


    /**
     * @param $primaryKey
     * @param $param
     * @param array $extraWhere
     * @return int
     * @throws ValidateException
     */
    public function editRow($primaryKey, $param, $extraWhere = [])
    {
        $data = [];
        $this->filterTableColumn();

        foreach ($this->tableColumn as $column => $cast) {
            if (isset($param[$column])) {
                if (is_string($param[$column])) {
                    $data[$column] = trim($param[$column]);
                } else {
                    $data[$column] = $param[$column];
                }
            }
        }

        if (empty($data)) {
            throw new ValidateException('数据库无有效字段');
        } else {
            $where = array_merge(
                [$this->primaryKey => $primaryKey],
                $extraWhere
            );
            try {
                return self::query()->where($where)->update($data);
            } catch (ValidateException $e) {
                throw new ValidateException('更新异常');
            }

        }
    }


    public function delRow($primaryKey, $extraWhere = [])
    {
        $where = array_merge(
            [$this->primaryKey => $primaryKey],
            $extraWhere
        );

        return self::query()->where($where)->delete();
    }


    /**
     * 过滤掉不需要传参的表字段
     */
    private function filterTableColumn()
    {
        foreach ($this->guarded as $val) {
            unset($this->tableColumn[$val]);
        }

        $filterColumn = [
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        foreach ($filterColumn as $val) {
            unset($this->tableColumn[$val]);
        }
    }

    /**
     * @param $id
     * @param int $is_e
     * @return \Illuminate\Database\Eloquent\Builder|Model|object
     * @throws NotFoundException
     */
    public static function getInfo($id, $is_e = 1)
    {
        $info = self::query()->where('id', $id)->first();
        if (empty($info) && $is_e) {
            throw new NotFoundException(self::$tableName . '信息未找到');
        }

        return $info;
    }

    /**
     * @param $primaryKey
     * @param int $is_e
     * @param array $extraWhere
     * @param string $trashed
     * @return mixed
     * @throws NotFoundException
     */
    public function getRowByPrimaryKey($primaryKey, $is_e = 0, $extraWhere = [], $trashed = '')
    {
        $where = array_merge(
            [$this->primaryKey => $primaryKey],
            $extraWhere
        );

        $info = $this
            ->where($where)
            ->when(! empty($trashed), function ($query) use ($trashed) {
                return $query->$trashed();
            })
            ->first();

        if (empty($info) && $is_e) {
            throw new NotFoundException('数据未找到：' . $primaryKey);
        }

        return $info;
    }


    public function updateOrCreateRows($where, $data)
    {
        return $this->updateOrCreate($where, $data);
    }

    /**
     * 判断是否存在数据
     * @param $id
     * @param $where
     * @return bool
     */
    public static function isExitsData($id, $where = [])
    {
        return self::query()
            ->where($where)
            ->when(! empty($id), function (Builder $query) use ($id) {
                return $query->where('id', '!=', $id);
            })
            ->exists();
    }
}
