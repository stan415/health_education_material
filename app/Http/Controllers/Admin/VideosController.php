<?php


namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Videos\VideosRequest;
use App\Http\Requests\Admin\Videos\UpdateOrCreateRequest;
use App\Library\Base\BaseAdminController;
use App\Service\Admin\VideosService;
use Illuminate\Http\Request;

class VideosController extends BaseAdminController
{
    public function __construct(VideosService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * @api {get} /admin/Videos/index
     * @apiVersion 0.1.0
     *
     * @apiGroup Videos
     * @apiName index
     * @apiDescription 文件列表
     *
     * @apiParamExample {json} Request-Example:
        {
            "user_id": 1,              登录id|required
            "get_type": 1,              获取类型默认0：查询当前用户上传的数据，1：查询所有|optional
            "nickname": "普通管理员",     上传人昵称|模糊搜索|optional
            "file_name":1,              文件名称|模糊搜索|optional
            "label_ids":1,              标签id|模糊搜索|optional
            "status":1,                 是否启用|optional
        }
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "获取成功",
            "data": {
                "data": [
                    {
                        "id": 1,
                        "user_id": 1,
                        "file_name": "111",
                        "file_url": "222",
                        "file_type": "3",
                        "file_size": 444,
                        "label_ids": "5,6",
                        "download_count": 6,
                        "liked_count": 0,
                        "status": 1,
                        "created_at": "1970-01-01 00:00:00",
                        "updated_at": null,
                        "user": {
                            "id": 1,
                            "nickname": "超级管理员"
                        }
                    }
                ],
                "current_page": 1,
                "last_page": 1,
                "page_size": 10,
                "total": 1
            },
            "trace_id": "0518a7dfb37bd49ce0bb811c314fe87b3c24"
        }
     * @apiErrorExample {json} Error-Response:
     *     {
     *       "code": 1,
     *       "message": "fail",
     *       "data": []
     *     }
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $conditions = $request->all(['user_id', 'nickname', 'label_ids', 'file_name', 'page_size', 'status', 'get_type']);
        $list = $this->service->getList($conditions);
        return $this->success('获取成功', $list);
    }

    /**
     * @api {post} /admin/Videos/create
     * @apiGroup Videos
     * @apiName create
     * @apiDescription 创建文件
     *
     * @apiParamExample {json} Request-Example:
        {
            "user_id": 1,               登录账号id|required
            "file_name": "test",        文件名称|required
            "cover_url": "test",        封面地址|required
            "file_url": "test",         文件地址|required
            "file_type": "Videos",      文件类型|required
            "file_size": 1111,          文件大小|required
            "label_ids": "1,2,3",       标签ids|required
            "status": 1,                是否展示 1:展示 0-不展示|required
        }
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "添加成功",
            "data": [],
            "trace_id": "05174c3af01b3fb827bca3641b408450b179"
        }
     * @apiErrorExample {json} Error-Response:
        {
            "code": 10003,
            "message": "文件名称file_name必填",
            "data": [],
            "trace_id": "0517aa82abcb7997f7c7cd36abab8807b305"
        }
     * @param UpdateOrCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(UpdateOrCreateRequest $request)
    {
        $params = $request->all();
        $this->service->addRow($params);
        return $this->success('添加成功');
    }

    /**
     * @api {post} /admin/Videos/edit
     * @apiGroup Videos
     * @apiName edit
     * @apiDescription 修改文件信息
     *
     * @apiParamExample {json} Request-Example:
        {
            "user_id": 1,               登录账号id|required
            "id": 1,                    文件id|required
            "file_name": "test",        文件名称|required
            "cover_url": "test",        封面地址|required
            "file_url": "test",         文件地址|required
            "file_type": "Videos",      文件类型|required
            "file_size": 1111,          文件大小|required
            "label_ids": "1,2,3",       标签ids|required
            "status": 1,                是否展示 1:展示 0-不展示|required
        }
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "修改成功",
            "data": [],
            "trace_id": "05174c3af01b3fb827bca3641b408450b179"
        }
     * @apiErrorExample {json} Error-Response:
        {
            "code": 10003,
            "message": "不可修改非当前账号的数据",
            "data": [],
            "trace_id": "051955481d6b27e37a4db121d81a7bf71775"
        }
     * @param UpdateOrCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function edit(UpdateOrCreateRequest $request)
    {
        $params = $request->all();
        $this->service->editRow($params['id'], $params);
        return $this->success('修改成功');
    }

    /**
     * @api {get} /admin/Videos/detail
     * @apiGroup Videos
     * @apiName detail
     * @apiDescription 获取文件详情信息
     *
     * @apiParam {json} Request-Example:
        {
            "user_id": 1,             登录id|required
            "id": 1,                  文件id|optional
        }
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "获取成功",
            "data": {
                "id": 1,
                "user_id": 1,
                "file_name": "测试",
                "cover_url": "www.test.url",
                "file_url": "www.test.url",
                "file_type": "file",
                "file_size": 1111,
                "label_ids": "4,5",
                "download_count": 6,
                "liked_count": 0,
                "status": 1,
                "created_at": "1970-01-01 00:00:00",
                "updated_at": "2022-05-19T02:22:18.000000Z"
            },
            "trace_id": "0519f15c0326641cb59c59156ef25b39a5ad"
        }
     * @apiErrorExample {json} Error-Response:
        {
            "code": 10002,
            "message": "信息未找到[编号：051997a6d77624bac2f441420c058bc5537d】",
            "data": [],
            "trace_id": "051997a6d77624bac2f441420c058bc5537d"
        }
     * @param VideosRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NotFoundException
     */
    public function detail(VideosRequest $request)
    {
        $id = $request->get('id', '');
        $data = $this->service->getRowById($id, 1);
        return $this->success('获取成功', $data);
    }

    /**
     * @api {get} /admin/Videos/del
     * @apiGroup Videos
     * @apiName del
     * @apiDescription 删除文件
     *
     * @apiParam {json} Request-Example:
        {
            "user_id": 1,              登录id|required
            "id": 1,                   操作id|optional
        }
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "删除成功",
            "data": 0,
            "trace_id": "051782b90ed6ed655ad6b11ba134f36f7589"
        }
     * @apiErrorExample {json} Error-Response:
        {
            "code": 10002,
            "message": "信息未找到[编号：051997a6d77624bac2f441420c058bc5537d】",
            "data": [],
            "trace_id": "051997a6d77624bac2f441420c058bc5537d"
        }
     * @param VideosRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function del(VideosRequest $request)
    {
        $id = $request->get('id');
        $data = $this->service->delRow($id);
        return $this->success('删除成功', $data);
    }
}