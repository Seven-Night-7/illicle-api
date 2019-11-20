<?php

namespace App\Http\Controllers\Api;

use App\Models\Detachment;
use App\Models\IllegalVehicle;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class IllegalVehicleController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        //  表单验证
        $validator = Validator::make($request->all(), [
            'detachment_name' => '',
            'nickname' => '',
            'start_at' => '',
            'end_at' => '',
        ]);
        if ($validator->fails()) {
            //  未通过验证
            return $this->response(-1, [], $validator->errors()->first());
        }

        $user = get_user_info();
        $data = $validator->validate();

        $illegalVehicles = IllegalVehicle::with(['detachments','users'])
            ->where('user_id', $user['id']);

        //  中队名筛选
        if (isset($data['detachment_name'])) {
            $detachmentName =  $data['detachment_name'];
            $illegalVehicles->whereHas('detachments', function ($query) use ($detachmentName) {
                $query->where('name', 'LIKE', '%'.$detachmentName.'%');
            });
        }
        //  用户名筛选
        if (isset($data['nickname'])) {
            $nickname =  $data['nickname'];
            $illegalVehicles->whereHas('users', function ($query) use ($nickname) {
                $query->where('nickname', 'LIKE', '%'.$nickname.'%');
            });
        }
        //  日期筛选
        if (isset($data['start_at']) && isset($data['end_at'])) {
            $illegalVehicles->whereBetween('created_at', [$data['start_at'], $data['end_at']]);
        }

        $illegalVehicles = $illegalVehicles->paginate(get_per_page())
            ->toArray();

        array_with($illegalVehicles, [
            ['detachments', 'name', 'detachment_name'],
            ['users', 'nickname', 'nickname']
        ], true);

        //  获取图片
        $imageIds = collect($illegalVehicles['data'])
            ->pluck('image_ids')
            ->map(function ($item) {
                return explode(',', $item);
            })
            ->collapse()
            ->unique()
            ->values()
            ->toArray();
        $images = Image::whereIn('id', $imageIds)
            ->pluck('path', 'id')
            ->toArray();
        array_walk($illegalVehicles['data'], function (&$item) use ($images) {
            $temp = explode(',', $item['image_ids']);
            $item['images'] = collect($temp)->map(function ($image_id) use ($images) {
                return isset($images[$image_id]) ? $images[$image_id] : '';
            });
        });

        return $this->response(0, $illegalVehicles);
    }

    /**
     * 新增
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        //  表单验证
        $validator = Validator::make($request->post(), [
            'detachment_id' => 'required|integer',
            'image_ids' => 'required',
            'image_ids.*' => 'required|integer',
        ]);
        if ($validator->fails()) {
            //  未通过验证
            return $this->response(-1, [], $validator->errors()->first());
        }

        $user = get_user_info();
        $data = $validator->validate();

        if (!Detachment::find($data['detachment_id'])) {
            return $this->response(-1, [], '中队不存在');
        }

        if (Image::whereIn('id', $data['image_ids'])->count() != count($data['image_ids'])) {
            return $this->response(-1, [], '图片不存在');
        }

        $illegalVehicle = new IllegalVehicle();
        set_model_data($illegalVehicle, [
            'user_id' => $user['id'],
            'detachment_id' => $data['detachment_id'],
            'image_ids' => implode(',', $data['image_ids']),
        ]);
        $illegalVehicle->save();

        return $this->response(0);
    }

    /**
     * 详情
     * @param $id
     * @return array
     */
    public function show($id)
    {
        $illegalVehicle = IllegalVehicle::with(['detachments','users'])
            ->find($id);
        if (!$illegalVehicle) {
            return $this->response(-1, [], '违章车辆不存在');
        }

        $illegalVehicle->detachement_name = '';
        if ($illegalVehicle->detachments != null) {
            $illegalVehicle->detachement_name = $illegalVehicle->detachments->name;
        }
        $illegalVehicle->nickname = '';
        if ($illegalVehicle->users != null) {
            $illegalVehicle->nickname = $illegalVehicle->users->nickname;
        }
        unset($illegalVehicle->detachments, $illegalVehicle->users);

        $illegalVehicle->images = Image::whereIn('id', explode(',', $illegalVehicle->image_ids))
            ->pluck('path')
            ->toArray();

        return $this->response(0, $illegalVehicle);
    }

    /**
     * 删除
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        $illegalVehicle = IllegalVehicle::find($id);
        if (!$illegalVehicle) {
            return $this->response(-1, [], '违章车辆不存在');
        }

        $illegalVehicle->delete();

        return $this->response(0);
    }
}
