<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * 登录
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        //  表单验证
        $validator = Validator::make($request->post(), [
            'account' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            //  未通过验证
            return $this->response(-1, [], $validator->errors()->first());
        }

        $data = $validator->validate();

        //  账号检测、密码校验
        $account = $data['account'];
        $password = $data['password'];

        $user = User::where('account', $account)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            return $this->response(-1, [], '账号或密码不正确');
        }

        $userInfo = [
            'id' => $user->id,
            'account' => $user->account,
            'type' => $user->type,
        ];

        //  缓存用户数据
        session([
            'user' => $userInfo
        ]);

        return $this->response(0, [], '登陆成功');
    }

    /**
     * 登出
     * @param Request $request
     * @return array
     */
    public function logout(Request $request)
    {
        if (session('user')) {
            $request->session()->forget('user');
        }

        return $this->response(0, [], '注销成功');
    }

    //  生成假用户
    public function storeDemo()
    {
        $user = User::firstOrNew(['account' => 'zhonghang']);
        $user->account = 'zhonghang';
        $user->nickname = '钟航';
        $user->password = Hash::make('123456');
        $user->type = 1;
        $user->save();

        return $this->response(0);
    }
}
