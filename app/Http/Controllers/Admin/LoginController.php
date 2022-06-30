<?php


namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Login\ChangePwdRequest;
use App\Http\Requests\Admin\Login\LoginRequest;
use App\Library\Base\BaseAdminController;
use App\Service\Admin\LoginService;
use Illuminate\Http\Request;

class LoginController extends BaseAdminController
{
    public $service;

    public function __construct(LoginService $loginService)
    {
        parent::__construct();
        $this->service = $loginService;
    }

    /**
     * @api {post} /admin/login/login
     * @apiVersion 0.1.0
     * @apiGroup login
     * @apiName login
     * @apiPermission none
     *
     * @apiDescription 登录
     *
     * @apiParamExample {json} Request-Example:
        {
            "username":"test",
            "password":"123456"
        }
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "登录成功",
            "data": {
                "user_id": 1,
                "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9"
            },
            "trace_id": "051915d52e7f3c2b7b4d50d8ed664f0635c2"
        }
     * @apiErrorExample {json} Error-Response:
     * {
            "code": 10003,
            "message": "用户名或者密码不正确",
            "data": [],
            "trace_id": "05190dc1554ce163afc4ca7e7ccdce624c6c"
        }
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\ValidateException
     */
    public function login(LoginRequest $request)
    {
        $params = $request->all(['username', 'password']);
        $data = $this->service->handleLogin($params['username'], $params['password']);
        return $this->success('登录成功', $data);
    }

    /**
     * @api {post} /admin/login/logout
     * @apiVersion 0.1.0
     * @apiGroup login
     * @apiName logout
     * @apiPermission none
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "退出成功",
            "data": [],
            "trace_id": "051915d52e7f3c2b7b4d50d8ed664f0635c2"
        }
     * @apiErrorExample {json} Error-Response:
        {
            "code": 401,
            "message": "token无效",
            "data": [],
            "trace_id": "05195771f66d63d44ec7bc3b547a2b50b972"
        }
     *
     * @apiDescription 退出登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $token = $request->header('token');
        $this->service->handLogout($token);
        return $this->success('退出成功');
    }

    /**
     * @api {post} /admin/login/change
     * @apiVersion 0.1.0
     * @apiGroup login
     * @apiName change
     * @apiPermission none
     *
     * @apiDescription 修改密码
     *
     * @apiParamExample {json} Request-Example:
        {
            "user_id":1,
            "password":"123456"
        }
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "修改成功",
            "data": [],
            "trace_id": "051915d52e7f3c2b7b4d50d8ed664f0635c2"
        }
     * @apiErrorExample {json} Error-Response:
        {
            "code": 10003,
            "message": "修改失败",
            "data": [],
            "trace_id": "05190dc1554ce163afc4ca7e7ccdce624c6c"
        }
     *
     * @param ChangePwdRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function change(ChangePwdRequest $request)
    {
        $params = $request->all(['user_id', 'password']);
        $this->service->handleChangePwd($params);
        return $this->success('修改成功');
    }
}