<?php


namespace App\Http\Requests\Admin\Images;


use App\Exceptions\ValidateException;
use App\Library\Base\BaseRequest;
use App\Library\Base\BaseService;
use App\Models\Admin\ImagesModel;
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
            "user_id"      => "required",
            "file_name"    => "required|string",
            "file_url"     => "required|string",
            "file_type"    => "required|string",
            "file_size"    => "required",
            "label_ids"    => "required",
            "status"       => "required",
        ];
    }


    public function messages()
    {
        return [
            "user_id.required"    => "当前登录用户user_id必传",
            "file_name.required"  => "文件名称file_name必填",
            "file_url.required"   => "文件地址file_url必填",
            "file_type.required"  => "文件类型file_type必填",
            "file_size.required"  => "文件大小file_size必填",
            "label_ids.required"  => "标签label_ids必填",
            "status.required"     => "公开状态status必填",
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            try {
                $params = request()->all();
                if (isset($params['id']) && isset($params['user_id']) && ImagesModel::isExitsData($params['id'], ['user_id' => $params['user_id']])) {
                    throw new ValidateException('不可修改非当前账号的数据');
                }
            } catch (ValidateException $e) {
                $validator->errors()->add('err', $e->getMessage());
            }
        });
    }
}