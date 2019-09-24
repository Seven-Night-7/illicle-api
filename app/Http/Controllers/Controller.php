<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 常见响应码和响应信息
     * @var array
     */
    protected $commonCodeToMsg = [
        -1 => '请求失败',
        0 => '请求成功',
        10 => '新增成功',
        20 => '更新成功',
        30 => '删除成功',
    ];

    /**
     * 统一响应格式
     * @param int $code
     * @param array $data
     * @param string $msg
     * @return array
     */
    protected function response($code = 0, $data = [], $msg = '')
    {
        $msg = !empty($msg) ? $msg :
            (isset($this->commonCodeToMsg[$code]) ? $this->commonCodeToMsg[$code] : '未知信息');

        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
    }
}
