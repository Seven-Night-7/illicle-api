<?php

//  设置模型数据
function set_model_data($model, $array)
{
    array_walk($array, function ($value, $key) use ($model) {
        $model->$key = $value;
    });
}

//  遍历数组加入关联模型的某个字段
//  场景：实现在Eloquent模型中使用paginate()方法后关联模型的数据转换
function array_with(&$array, array $params, $isUnset = false)
{
    if (!$params || !$array || !isset($array['data'])) {
        return false;
    }

    //  $params 值如：
    //  [
    //      ['user', 'account', 'user_account', 0],
    //      ['detachment', 'name'],
    //  ]
    array_walk($params, function (&$param) {
        //  第三个参数不传，则默认为 第一个参数和第二个参数用下划线'_'拼接
        $param[2] = !empty($param[2]) ? $param[2] : $param[0] . '_' . $param[1];
        //  第四个参数表示当键值不存在时的默认值
        $param[3] = isset($param[3]) ? $param[3] : '';
    });

    array_walk($array['data'], function (&$value) use ($params, $isUnset) {
        foreach ($params as $param) {
            $value[$param[2]] = isset($value[$param[0]]) ? $value[$param[0]][$param[1]] : $param[3];
        }

        if ($isUnset) {
            $relationNames = array_unique(array_column($params, 0));
            foreach ($relationNames as $relationName) {
                //  是否释放关联模型对应的数据
                if (isset($relationName)) {
                    unset($value[$relationName]);
                }
            }
        }
    });

    return true;
}