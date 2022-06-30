<?php


namespace App\Http\Requests\Admin\Upload;


use App\Library\Base\BaseRequest;

class UploadCoverRequest extends BaseRequest
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
            "content"   => "required",
        ];
    }


    public function messages()
    {
        return [
            "content.required"  => "内容必传",
        ];
    }
}