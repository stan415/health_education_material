<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\User\LikeRequest;
use App\Http\Requests\Admin\User\UpdateOrCreateRequest;
use App\Http\Requests\Admin\User\UserRequest;
use App\Library\Base\BaseAdminController;
use App\Service\Admin\UserService;
use Illuminate\Http\Request;

class UserController extends BaseAdminController
{
    public function __construct(UserService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * @api {get} /admin/user/index
     * @apiVersion 0.1.0
     *
     * @apiGroup user
     * @apiName index
     * @apiDescription 账号列表
     *
     * @apiParamExample {json} Request-Example:
        {
            "user_id": 1,              登录id|required
            "pid": 1,                   上级id|optional
            "nickname": "普通管理员",     后台院站昵称|optional
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
                        "id": 3,
                        "pid": 1,
                        "username": "test",
                        "nickname": "普通管理员",
                        "phone_number": 0,
                        "roles_id": 2,
                        "status": 1,
                        "created_at": "2022-05-17 09:21:33"
                    }
                ],
                "current_page": 1,
                "last_page": 1,
                "page_size": 10,
                "total": 1
                },
            "trace_id": "0517dbb96eb0826eb34c82d0a9dcca68cc65"
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
        $conditions = $request->all(['user_id', 'pid', 'nickname', 'page_size', 'status']);
        $list = $this->service->getList($conditions);
        return $this->success('获取成功', $list);
    }

    /**
     * @api {get} /admin/user/detail
     * @apiGroup user
     * @apiName detail
     * @apiDescription 管理员详情
     *
     * @apiParam {json} Request-Example:
        {
            "user_id": 1,             登录id|required
            "id": 1,                   操作id|optional
        }
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "",
            "data": {
                "id": 1,
                "pid": 1,
                "username": "xbwh",
                "password": "$2y$10$1Frlz8edtCiGnUO8BMAyUe5DqUsNjGOzPDrAqB9xXRVsD8i8o8IvK",
                "nickname": "超级管理员",
                "phone_number": 0,
                "roles_id": 1,
                "status": 1,
                "created_at": "2022-05-17 09:15:05",
                "updated_at": "2022-05-17T09:15:05.000000Z"
            },
            "trace_id": "05172326fbdebbbd49e40e0bc5290e351283"
        }
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(UserRequest $request)
    {
        $id = $request->get('id', '');
        $data = $this->service->getRowById($id, 1);
        return $this->success('获取成功', $data);
    }

    /**
     * @api {post} /admin/user/create
     * @apiGroup user
     * @apiName create
     * @apiDescription 创建账号
     *
     * @apiParamExample {json} Request-Example:
        {
            "user_id": 1,               登录账号id|required
            "pid": 1,                   上级id|required
            "username": "test",         登录账户名称|required
            "password": "123456",       登录密码|required
            "nickname": "普通管理员",     后台院站昵称|required
            "roles_id":2,               所属角色|required
            "status":1,                 是否启用|required
            "phone_number":14576452312  手机号码|optional
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
            "message": "登录账号已存在",
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
        $params['password'] = bcrypt($params['password']);
        $this->service->addRow($params);
        return $this->success('添加成功');
    }

    /**
     * @api {post} /admin/user/edit
     * @apiGroup user
     * @apiName edit
     * @apiDescription 修改账号信息
     *
     * @apiParamExample {json} Request-Example:
        {
            "user_id": 1,               登录账号id|required
            "id": 1,                    操作账号id|required
            "pid": 1,                   上级id|required
            "username": "test",         登录账户名称|required
            "password": "123456",       登录密码|required
            "nickname": "普通管理员",     后台院站昵称|required
            "roles_id":2,               所属角色|required
            "status":1,                 是否启用|required
            "phone_number":14576452312  手机号码|optional
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
            "message": "登录账号已存在",
            "data": [],
            "trace_id": "0517aa82abcb7997f7c7cd36abab8807b305"
        }
     * @param UpdateOrCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function edit(UpdateOrCreateRequest $request)
    {
        $params = $request->all();
        $params['password'] = bcrypt($params['password']);
        $this->service->editRow($params['id'], $params);
        return $this->success('修改成功');
    }

    /**
     * @api {get} /admin/user/del
     * @apiGroup user
     * @apiName del
     * @apiDescription 删除账号
     *
     * @apiParam {json} Request-Example:
        {
            "user_id": 1,              登录id|required
            "id": 1,                   操作id|required
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
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function del(UserRequest $request)
    {
        $id = $request->get('id');
        $data = $this->service->delRow($id);
        return $this->success('删除成功', $data);
    }

    /**
     * @api {get} /admin/user/like
     * @apiGroup user
     * @apiName like
     * @apiDescription 点赞
     *
     * @apiParam {json} Request-Example:
        {
            "user_id": 1,              登录id|required
            "source_id": 1,            点赞资源id|required
            "source_type": 1,          资源类型 1-视频videos 2-图片images 3-文件files|required
        }
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "点赞成功",
            "data": [],
            "trace_id": "0519db64b3246caff1bd343af960af9181f3"
        }
     * @apiErrorExample {json} Error-Response:
        {
            "code": 10003,
            "message": "您今天已对此资料点过赞了，请勿重复点赞！",
            "data": [],
            "trace_id": "05190e81b4e92619e9d55a4db2a38534790f"
        }
     * @param LikeRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ValidateException
     */
    public function like(LikeRequest $request)
    {
        $params = $request->all(['user_id', 'source_id', 'source_type']);
        $this->service->handleLike($params);
        return $this->success('点赞成功');
    }
}
