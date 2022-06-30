<?php
namespace App\Traits;

use App\Constants\ResponseCode;
use App\Exceptions\BusinessException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidateException;
use App\Library\Logger;
use Illuminate\Pagination\LengthAwarePaginator;

trait ResponseAdapter
{
    /**
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($msg = '', $data = [])
    {
        if ($data instanceof LengthAwarePaginator) {
            $tmp = [
                'data' => $data->items(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'page_size' => $data->perPage(),
                'total' => $data->total()
            ];

            $data = $tmp;
        }

        $return_data = [
            'code' => ResponseCode::CODE_OK,
            'message' => $msg,
            'data' => $data,
            'trace_id' => Logger::getLoggerId()
        ];

        return \response()->json($return_data);
    }



    /**
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($msg = '', $data = [])
    {
        $return_data = [
            'code' => ResponseCode::CODE_ERROR,
            'message' => $msg . "[编号：" . Logger::getLoggerId() . "】",
            'data'=>$data,
            'trace_id' => Logger::getLoggerId()
        ];
        return \response()->json($return_data);
    }

    public function  exception(\Throwable $e, $msg = '', $data = [], $code = 0)
    {
        empty($msg) && $msg = $e->getMessage();

        $headers = request()->headers->all();
        $headers['body'] = request()->all();

        $showMsg = $e->getMessage();
        if ($e instanceof NotFoundException) {
            $code = ResponseCode::CODE_NOT_FOUND;
        } elseif ($e instanceof ValidateException) {
            $code = ResponseCode::CODE_VALIDATE_FAIL;
        } elseif ($e instanceof UnauthorizedException) {
            $showMsg = '没有权限';
            $code = ResponseCode::CODE_PERMISSION_DENIED;
        } else {
//            $showMsg = '系统出现未知错误';
            $code = ResponseCode::CODE_ERROR;
        }

        Logger::logE($msg, $e, array_merge(
                $data, ['headers' => json_encode($headers, JSON_UNESCAPED_UNICODE)])
        );
        $return_data = [
            'code' => $code,
            'message' => $showMsg,
            'data' => $data,
            'trace_id' => Logger::getLoggerId()
        ];
        return \response()->json($return_data);
    }

    public function validateFail($e)
    {
        $data = $e->getMessageBag()->toArray();
        $return_data = [
            'code' => 422,
            'message' => implode(',', $e->getMessageBag()->all()) . "[编号：" . Logger::getLoggerId() . "】",
            'data'=> $data,
            'trace_id' => Logger::getLoggerId()
        ];
        return \response()->json($return_data);
    }


    /**
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorized($msg = '', $data = [], $code = 401)
    {
        $return_data = [
            'code' => $code,
            'message' => $msg,
            'data'=>$data,
            'trace_id' => Logger::getLoggerId()
        ];
        return \response()->json($return_data);
    }
}
