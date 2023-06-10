<?php


namespace app\Api\model;
use think\model;

class Menu extends model
{
    public function getAllMenu()
    {
        /*
         * 把菜单信息以数组的形式返回，（mid,parent_id,label,src,sort,status）
         */
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