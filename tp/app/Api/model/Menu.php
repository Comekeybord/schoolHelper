<?php
namespace app\Api\model;
use think\model;

class Menu extends model
{
    public function getAllMenu()
    {
        $menus = Menu::select()->toArray();
        $arr = [];
        foreach ($menus as $menu)
        {
            $arr1 = [
                'mid'         =>  $menu['mid'],
                'parent_id'   =>  $menu['parent_id'],
                'label'       =>  $menu['label'],
                'src'         =>  $menu['src'],
                'sort'        =>  $menu['sort'],
                'status'      =>  $menu['status']==1?'开启':'关闭'
            ];
            array_push($arr,$arr1);
        }
        return $arr;
    }
}