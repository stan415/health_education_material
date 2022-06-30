<?php


namespace App\Http\Requests\Admin\User;


use App\Exceptions\ValidateException;
use App\Library\Base\BaseRequest;
use App\Library\Base\BaseService;
use App\Models\Admin\UserModel;
use Illuminate\Validation\Validator;

class LikeRequest extends BaseRequest
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
            "source_id"     => "required",
            "source_type"   => "required",
        ];
    }


    public function messages()
    {
        return [
            "user_id.required"      => "当前登录用户user_id必传",
            "source_id.required"    => "资源id必传",
            "source_type.required"  => "资源类型必传",
        ];
    }
}