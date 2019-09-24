<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
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

        $user_session = [
            'id' => $user->id,
            'account' => $user->account,
            'type' => $user->type,
        ];

        //  缓存用户数据
        session([
            'user' => $user_session
        ]);

        return $this->response(0, [], '登陆成功');
    }
}
