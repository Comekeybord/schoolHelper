<?php


namespace app\api\ model;
use think\model;

class UserGroup extends model
{
    public function getAllGroup()
    {
     $groups = UserGroup::select()->toArray();
     $arr1 = [];
     foreach ($groups as $group)
     {
         $arr = [
             'group_id'           =>   $group['group_id'],
             'group_name'         =>   $group['group_name'],
             'status'             =>   $group['status'],
             'time_add'           =>   $group['time_add'],
             'rights'             =>   $group['rights']
         ];
         array_push($arr1, $arr);
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
}