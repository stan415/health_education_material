<?php
namespace App\Library\Base;

use App\Library\Base\BaseController;
use App\Traits\ResponseAdapter;
use App\Library\Logger;

class BaseAdminController extends BaseController
{
    use ResponseAdapter;

    public $service;

    public function __construct()
    {
        parent::__construct();
        $headers = request()->headers->all();
        $headers['body'] = request()->all();

        Logger::debug('request', ['request' => 'request', 'headers' => json_encode($headers, JSON_UNESCAPED_UNICODE)]);
    }
}