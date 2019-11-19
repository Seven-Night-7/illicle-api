<?php

namespace App\Http\Controllers\Api;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OfficialAccountController extends Controller
{
    public function test()
    {
        $config = config('wechat.official_account.default');

        $officialAccount = Factory::officialAccount($config);

        return $officialAccount->server->serve();
    }
}
