<?php


namespace app\api\model;
use think\model;

class UserGroup extends model
{
    public function getAllGroup()
    {
     $groups = UserGroup::select()->toArray();
     $arr1 = [];
     foreach ($groups as $group)
     {
         $arr2 = [];
         foreach ($group as $value)
         {
             array_push($arr2,$value);
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
}