<?php


namespace App\Http\Requests\Admin\Download;


use App\Library\Base\BaseRequest;

class DownloadListRequest extends BaseRequest
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
            "source_type"   => "required",
        ];
    }


    public function messages()
    {
        return [
            "user_id.required"      => "当前登录用户user_id必传",
            "source_type.required"  => "资源类型必传",
        ];
    }
}