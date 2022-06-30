<?php


namespace App\Service\Admin;


use App\Exceptions\ValidateException;
use App\Library\Base\BaseService;
use App\Models\Admin\UploadModel;
use ChunkUpload\OssUploadClient;
use Illuminate\Database\Eloquent\Builder;

class UploadService extends BaseService
{
    public function __construct()
    {
        $this->dao = new UploadModel();
    }

    public function getList($conditions = [])
    {
        return UploadModel::query()
            ->when(! empty($conditions['source_type'] ?? null), function (Builder $query) use ($conditions) {
                switch ($conditions['source_type']) {
                    case 1:
                        $query->with('videos');
                        break;
                    case 2:
                        $query->with('images');
                        break;
                    case 3:
                        $query->with('files');
                        break;
                }
                return $query->where('source_type', '=', $conditions['source_type']);
            })
            ->where('user_id', '=', $conditions['user_id'])
            ->when(is_numeric($conditions['status'] ?? null), function (Builder $query) use ($conditions) {
                return $query->where('status', '=', $conditions['status']);
            })
            ->paginate($conditions['page_size'] ?? 10);
    }

    /**
     * 创建封面图
     * @param $text
     * @param int $font_size
     * @return mixed
     * @throws ValidateException
     */
    public function makeImgWithStr($text, $font_size=20)
    {
        try {
            $filename = public_path() .  DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . uniqid() .".png";
            $font = public_path() . DIRECTORY_SEPARATOR . 'simsun.ttc';
            //图片尺寸
            $width = 480;
            $height = 300;
            $font_size = $font_size ? $font_size : 20;
            $im = imagecreatetruecolor($width, $height);
            //背景色
            $white = imagecolorallocate($im, 255, 255, 255);
            //字体颜色
            $black = imagecolorallocate($im, 0, 0, 0);

            imagefilledrectangle($im, 0, 0, $width, 300, $white);
            $txt_max_width = intval(0.8 * $width);
            $content = "";
            for ($i = 0; $i < mb_strlen($text); $i++) {
                $letter[] = mb_substr($text, $i, 1);
            }
            foreach ($letter as $l) {
                $test_str = $content . " " . $l;
                $test_box = imagettfbbox($font_size, 0, $font, $test_str);
                // 判断拼接后的字符串是否超过预设的宽度。超出宽度添加换行
                if (($test_box[2] > $txt_max_width) && ($content !== "")) {
                    $content .= "\n";
                }
                $content .= $l;
            }

            $txt_width = $test_box[2] - $test_box[0];

            $y = $height / 2; // 文字从何处的高度开始
            $x = ($width - $txt_width) / 2; //文字居中
            //文字写入
            imagettftext($im, $font_size, 0, $x, $y, $black, $font, $content); //写 TTF 文字到图中
            //图片保存
            if (imagepng($im, $filename)) {
                $client = new OssUploadClient();
                $res = $client->ossClient->uploadFile($client->bucket, config('chunk_upload.upload_path')  . uniqid() .".png", $filename);
                if ($res) {
                    @unlink($filename);
                    return $res['info']['url'];
                }
            }
        } catch (ValidateException $e) {
            throw new ValidateException('创建封面图异常');
        }
    }
}