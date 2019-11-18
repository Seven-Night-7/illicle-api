<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    /**
     * 新增/上传图片
     * @param Request $request
     * @param ImageUploadHandler $uploader
     * @return array
     */
    public function store(Request $request, ImageUploadHandler $uploader)
    {
        //  表单验证
        $validator = Validator::make($request->file(), [
            'image' => 'required',
        ]);
        if ($validator->fails()) {
            //  未通过验证
            return $this->response(-1, [], $validator->errors()->first());
        }

        $user = get_user_info();
        $data = $validator->validate();

        $image = $uploader->save($data['image'], 'default', $user['id']);

        return $this->response(0, $image, '上传成功');
    }
}
