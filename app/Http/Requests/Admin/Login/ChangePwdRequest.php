<?php


namespace App\Http\Requests\Admin\Login;

use App\Library\Base\BaseRequest;

class ChangePwdRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|string',
            'user_id'  => 'required',
        ];
    }

    public function messages()
    {
        return [
            "password.required"    => "新密码必填",
            "password.string"      => "新密码类型不正确",
            "user_id.required"     => "当前登录用户user_id必传",
        ];
    }
}