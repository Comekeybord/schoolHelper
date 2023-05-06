<?php


namespace app\Api\model;
use think\model;

class User extends model
{
    public function getAllUser()
    {
        // 获取uid，部门，账号，姓名，手机号，qq号，状态
        $users = User::select()->toArray();
        $arr1 = [];
        $arr = ['uid','account','name','phone','qq','group_id','status'];
        foreach ($users as $user)
        {
            $arr2 = [];
            foreach ($user as $key => $value)
            {
                if(in_array($key,$arr))
                {
                    array_push($arr2,$value);
                }
            }
            array_push($arr1,$arr2);
        }
        return $arr1;
    }
    public function getStatusAttr($value)
    {
        $arr = [
            0  =>  '关闭',
            1  =>  '开启'
        ];
        return $arr[$value];
    }
    public function getSexAttr($value)
    {
        $arr = [
            1 => '男',
            2 => '女'
        ];
        return $arr[$value];
    }
    public function setStatusAttr($value)
    {
        $arr = [
            0  =>  '关闭',
            1  =>  '开启'
        ];
        return $arr[$value];
    }
}