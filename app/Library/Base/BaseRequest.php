<?php


namespace App\Library\Base;


use App\Constants\ResponseCode;
use App\Library\Logger;
use App\Traits\ResponseAdapter;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    /**
     * @var Validator
     */
    protected $validator;

    use ResponseAdapter;

    protected function failedValidation(Validator $validator)
    {
        $error= $validator->errors()->all();
        throw new HttpResponseException(
            response()->json(
                ['code'=> ResponseCode::CODE_VALIDATE_FAIL,
                    'message'=>$error[0], 'data'=>[],
                    'trace_id' => Logger::getLoggerId()
                ], 200
            )
        );
    }
}