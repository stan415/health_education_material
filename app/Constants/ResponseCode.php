<?php


namespace App\Constants;

class ResponseCode
{
    /**
     * 成功
     */
    const CODE_OK = 10000;

    /**
     * 系统出现未知错误
     */
    const CODE_ERROR = 10001;

    /**
     * 数据没有找到
     */
    const CODE_NOT_FOUND = 10002;

    /**
     * 验证不通过
     */
    const CODE_VALIDATE_FAIL = 10003;

    /**
     * 重复提交
     */
    const REPEAT_SUBMIT = 10004;


    /**
     * 没有权限
     */
    const CODE_PERMISSION_DENIED = 401;
}