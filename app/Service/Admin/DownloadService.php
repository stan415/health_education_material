<?php


namespace App\Service\Admin;


use App\Library\Base\BaseService;
use App\Models\Admin\DownloadModel;
use ChunkUpload\OssUploadClient;
use Illuminate\Database\Eloquent\Builder;
use OSS\OssClient;

class DownloadService extends BaseService
{
    public function __construct()
    {
        $this->dao = new DownloadModel();
    }

    public function getList($conditions = [])
    {
        return $this->dao::query()
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

    public function donwload()
    {
        $client = new OssUploadClient();
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => "C:\Users\Administrator\Desktop\blog.mp4",
        );
        $res = $client->ossClient->getObject($client->bucket, 'material//628f3f5fed933.mp4', $options);
        var_dump($res);
    }
}