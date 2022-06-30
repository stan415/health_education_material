<?php


namespace App\Http\Requests\Admin\Login;

use App\Library\Base\BaseRequest;

class LoginRequest extends BaseRequest
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
            'username'  => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            "username.required"    => "账号必填",
            "username.string"      => "账号类型不正确",
            "password.required"    => "密码必填",
            "password.string"      => "密码类型不正确",
        ];
    }
}