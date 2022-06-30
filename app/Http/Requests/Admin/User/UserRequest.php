<?php


namespace App\Http\Requests\Admin\User;


use App\Exceptions\ValidateException;
use App\Library\Base\BaseRequest;
use App\Library\Base\BaseService;
use App\Models\Admin\UserModel;
use Illuminate\Validation\Validator;

class UserRequest extends BaseRequest
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
            "user_id"       => "required",
            "id"            => "required",
        ];
    }


    public function messages()
    {
        return [
            "user_id.required"  => "当前登录用户user_id必传",
            "id.required"       => "操作用户id必传",
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            try {
                $params = request()->all();
                if (isset($params['id'])) {
                    $info = UserModel::getInfo($params['id']);
                    $service = new BaseService();
                    if (isset($params['id']) && isset($params['user_id']) && !in_array($info['id'], $service->getChildrenIds($params['user_id']))) {
                        throw new ValidateException('不可操作非子账号的数据');
                    }
                }

            } catch (ValidateException $e) {
                $validator->errors()->add('err', $e->getMessage());
            }
        });
    }
}