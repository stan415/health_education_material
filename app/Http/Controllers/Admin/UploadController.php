<?php


namespace App\Http\Controllers\Admin;


use App\Http\Requests\Admin\Upload\UploadCoverRequest;
use App\Http\Requests\Admin\Upload\UploadListRequest;
use App\Library\Base\BaseAdminController;
use App\Service\Admin\UploadService;

class UploadController extends BaseAdminController
{
    public function __construct(UploadService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * @api {get} /admin/upload/list
     * @apiVersion 0.1.0
     *
     * @apiGroup upload
     * @apiName list
     * @apiDescription 获取上传列表
     *
     * @apiParamExample {json} Request-Example:
        {
            "user_id": 1,               登录id|required
            "source_type": 3,           资源类型 1:视频 2-图片 3-文件|required
            "status":1,                 上传状态 0-上传中 1-上传完成 2-上传异常|optional
        }
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "获取成功",
            "data": {
                "data": [{
                        "id": 3,
                        "user_id": 1,
                        "source_type": 3,
                        "source_id": 1,
                        "status": 1,
                        "created_at": "1970-01-01 00:00:00",
                        "updated_at": null,
                        "files": {              //传1 => videos, 2=> images, 3=>files
                                "id": 1,
                                "user_id": 1,
                                "file_name": "测试",
                                "file_url": "www.test.url",
                                "file_type": "file",
                                "file_size": 1111,
                                "label_ids": "4,5",
                                "download_count": 6,
                                "liked_count": 1,
                                "status": 1,
                                "created_at": "1970-01-01 00:00:00",
                                "updated_at": "2022-05-19T08:35:53.000000Z"
                            }
                        }
                    ],
                    "current_page": 1,
                    "last_page": 1,
                    "page_size": 10,
                    "total": 1
                },
            "trace_id": "0527314f7fd652c55cacf9c7e49a34b444e1"
        }
     * @apiErrorExample {json} Error-Response:
        {
            "code": 10003,
            "message": "资源类型必传",
            "data": [],
            "trace_id": "0527e397cbe5723252f1302a57b98c6b7d48"
        }
     *
     * @param UploadListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(UploadListRequest $request)
    {
        $conditions = $request->all(['user_id', 'source_type', 'status']);
        $list = $this->service->getList($conditions);
        return $this->success('获取成功', $list);
    }

    /**
     * @api {get} /admin/upload/cover
     * @apiVersion 0.1.0
     *
     * @apiGroup upload
     * @apiName cover
     * @apiDescription 创建封面图
     *
     * @apiParamExample {json} Request-Example:
        {
            "user_id": 1,               登录id|required
            "content": 3,               文本内容|required
            "size":1,                   文字大小|optional
        }
     *
     * @apiUse Return
     * @apiSuccessExample {json} Success-Response:
        {
            "code": 10000,
            "message": "创建成功",
            "data": "http://xbjk.oss-cn-shenzhen.aliyuncs.com/material/6291d197f2878.png",
            "trace_id": "052883dbac97ae5322ce7da1a7fa05250e65"
        }
     * @apiErrorExample {json} Error-Response:
        {
            "code": 10003,
            "message": "内容必传",
            "data": [],
            "trace_id": "0527e397cbe5723252f1302a57b98c6b7d48"
        }
     *
     * @param UploadCoverRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ValidateException
     */
    public function cover(UploadCoverRequest $request)
    {
        $content = $request->get('content');
        $size = $request->get('size', '');
        $res = $this->service->makeImgWithStr( $content, $size);
        return $this->success('创建成功', $res);
    }
}