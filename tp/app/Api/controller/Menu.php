<?php


namespace app\Api\controller;
use app\Api\model\Menu as MenuModel;
use app\Api\model\User;
use app\Api\model\UserGroup;
use think\Model;


class Menu extends Base
{
    public function menu_list()
    {
        // mid，pid,label，src，sort，status,
        $menus = new MenuModel();
        $menus = $menus->getAllMenu();
        $arr = [];
        $page = input('get.page'); // 接收参数表示是第几页
        for($i = ($page-1)*10;$i<$page*10;$i++) // 每页有10个
        {
            if($i<count($menus))
            {
                array_push($arr,$menus[$i]);
            }
        }
        return $this->returnCode(200,$arr);
    }
    public function menu_add()
    {
        $menus = input('post.');  // 获取所有的参数
        if(empty($menus['parent_id']) | empty($menus['label']) | empty($menus['src']) | empty($menus['sort']) | empty('status'))
        {
            return $this->returnCode(201,[],'菜单信息填写不全');
        }
        $mid = $menus['mid']; // 菜单ID为0表示添加菜单，否则表示修改菜单
        $pid = MenuModel::column('mid');  // 获取所有的菜单ID，保证填写的ID都是菜单ID
        // 数据整理
        $idArr = [];
       foreach ($pid as $id)
       {
           array_push($idArr,$id);
       }
       array_push($idArr,0);
       if(!in_array($mid,$idArr)) // 如果填写的ID不是菜单ID直接返回
       {
           return $this->returnCode(201,[],'父ID填写错误');
       }
        $arr =   // 数据整理
            [
                'parent_id'          =>  $menus['parent_id'],
                'label'              =>  $menus['label'],
                'src'                =>  $menus['src'],
                'sort'               =>  $menus['sort'],
                'status'             =>  $menus['status']=='开启'?1:0
            ];
        if($mid == 0)
        {
            $result = MenuModel::insert($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[],'菜单插入成功');
            }
            else
            {
                return $this->returnCode(201,[],'菜单插入失败');
            }
        }
        else
        {
            $result = MenuModel::where('mid',$mid)->update($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[],'菜单更新成功');
            }
            else
            {
                return $this->returnCode(201,[],'菜单更新失败');
            }
        }
    }
    public function menu_del()
    {
        // mid,根据菜单ID进行删除
        $mid = input('post.mid');
        // 根据登录用户的group_id，查看用户权限（uid->group_id->rights）
        $g1 = User::where('uid',$this->uid)->value('group_id'); // 删除者的group_id
        $rights = UserGroup::where('group_id',$g1)->value('rights');
        $rights = explode(",", $rights);
        $arr  = []; // 用户权限数组
        foreach ($rights as $right)
        {
            array_push($arr,$right);
        }
        if(!in_array($mid,$arr)) // 如果该用户没有权限就不能删除
        {
            return $this->returnCode(201,[],'当前用户没有删除该菜单的权限');
        }
        $result = MenuModel::where('mid',$mid)->delete();
        if($result == 1)
        {
            return $this->returnCode(200,[],'菜单删除成功');
        }
        else
        {
            return $this->returnCode(201,[],'菜单删除失败');
        }
    }
}