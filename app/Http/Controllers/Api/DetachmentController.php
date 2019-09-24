<?php

namespace App\Http\Controllers\Api;

use App\Models\Detachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DetachmentController extends Controller
{
    /**
     * 新增
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        //  表单验证
        $validator = Validator::make($request->post(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            //  未通过验证
            return $this->response(-1, [], $validator->errors()->first());
        }

        $data = $validator->validate();

        $detachment = new Detachment();
        set_model_data($detachment, $data);
        $detachment->save();

        return $this->response(10);
    }

    /**
     * 下拉列表
     * @return array
     */
    public function terse()
    {
        return $this->response(0, Detachment::select('id','name')->get()->toArray());
    }
}
