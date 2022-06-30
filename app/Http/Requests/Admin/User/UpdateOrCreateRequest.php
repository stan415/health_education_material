<?php


namespace App\Http\Requests\Admin\User;


use App\Exceptions\ValidateException;
use App\Library\Base\BaseRequest;
use App\Library\Base\BaseService;
use App\Models\Admin\UserModel;
use Illuminate\Validation\Validator;

class UpdateOrCreateRequest extends BaseRequest
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
            "user_id"      =>  "required",
            "pid"          => "required",
            "username"     => "required|string",
            "password"     => "required|string",
            "nickname"     => "required|string",
            "roles_id"     => "required",
            "status"       => "required",
        ];
    }


    public function messages()
    {
        return [
            "user_id.required"  => "当前登录用户user_id必传",
            "pid.required"       => "所属上级必填",
            "username.required"  => "登录名称必填",
            "username.string"    => "登录类型必填",
            "password.required"  => "密码必填",
            "password.string"    => "密码类型不正确",
            "nickname.required"  => "院站名称必填",
            "roles_id.required"  => "所属角色必填",
            "status.required"    => "启用状态必填",
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            try {
                $params = request()->all();
                $service = new BaseService();
                if (isset($params['id']) && isset($params['user_id']) && !in_array($params['id'], $service->getChildrenIds($params['user_id']))) {
                    throw new ValidateException('不可操作非子账号的数据');
                }
                if (isset($params['pid']) && isset($params['user_id']) && !in_array($params['id'], $service->getChildrenIds($params['user_id']))) {
                    throw new ValidateException('不可选择非子账号的上级');
                }
                if (isset($params['username']) && UserModel::isExitsData($params['id'] ?? 0, ['username' => $params['username']])) {
                    throw new ValidateException('登录账号已存在');
                }
                if (isset($params['nickname']) && UserModel::isExitsData($params['id'] ?? 0, ['nickname' => $params['nickname']])) {
                    throw new ValidateException('院站名称已经被占用');
                }
            } catch (ValidateException $e) {
                $validator->errors()->add('err', $e->getMessage());
            }
        });
    }

}