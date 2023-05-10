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
        foreach ($users as $user)
        {
            $arr=[  // 数据整理
                'uid'              =>   $user['uid'],
                'account'          =>   $user['account'],
                'name'             =>   $user['name'],
                'phone'            =>   $user['phone'],
                'qq'               =>   $user['qq'],
                'group_id'         =>   $user['group_id'],
                'status'           =>   $user['status']
            ];
            array_push($arr1, $arr);
        }
        return $arr1;
    }
    public function getStatusAttr($value)  // 获取器，在从数据库拿数据的时候触发
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
            0 => '男',
            1 => '女'
        ];
        return $arr[$value];
    }
    public function setStatusAttr($value)  // 修改器，数据存储到数据库的时候触发
    {
        $arr = [
            0  =>  '关闭',
            1  =>  '开启'
        ];
        return $arr[$value];
    }
}