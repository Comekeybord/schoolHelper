<?php
namespace app\Api\controller;
use app\Api\model\Menu;
use app\Api\model\User as UserModel;
use app\Api\model\UserGroup as GroupModel;
use think\Model;

class UserGroup extends Base
{
    public function group_list()
    {
        // 展示group_id group_name status
        $groups = new GroupModel();
        $groups = $groups->getAllGroup();
        $arr1 = [];
        foreach ($groups as $group)  // 获取所有的数据
        {
            $arr = [
                'group_id'           =>   $group['group_id'],
                'group_name'         =>   $group['group_name'],
                'status'             =>   $group['status'] == 1?'开启':'关闭'
            ];
            array_push($arr1,$arr);
        }
        $arr = [];
        $arr2 = [];
        $limit = input('post.limit');
        for($i = 0;$i<count($arr1);$i++)  // 根据每页的数据limit进行数据整理
        {
            if($i<count($arr1))
            {
                array_push($arr2,$arr1[$i]);
            }else
            {
                break;
            }
            if(($i+1) % $limit === 0)
            {
                array_push($arr,$arr2);
                $arr2 = [];
            }
        }
        if(!empty($arr2))
        {
            array_push($arr,$arr2);
        }
        return $this->returnCode(200,$arr);
    }

    public function group_add()
    {
        /**
         * 1. group_id判断是添加还是修改
         *      group_id为0表示添加用户组
         *      group_id为其他表示修改
         * 2. 添加：group_id,group_name,status,(time_add),rights:数组
         * 2. 修改：group_id,group_name,status,(time_add),rights:数组
         */

        $groups = input('post.');  // 获取参数
        if(empty($groups['group_name']))
        {
            return $this->returnCode(201,[],'用户组名字不能为空');
        }
        $arr = explode(",", $groups['rights']);   // 用户填写的的权限，
        $gid = UserModel::where('uid',$this->uid)->value('group_id');  // 根据当前用户ID查看当前用户组ID
        $rights = GroupModel::where('group_id',$gid)->value('rights');  // 查看当前用户的用户组，用户只能够添加自己用户组有的权限,
        $totalRight = explode(',',$rights);
        foreach ($arr as $right)  // 查看用户填写的权限
        {
            if(!in_array($right,$totalRight)) // 判断用户填写的权限是否在
            {
                return $this->returnCode(201,[],'权限填写不正确，没有该权限');
            }
        }
        $arr = [  // 数据整理
            'group_name'         =>    $groups['group_name'],
            'status'             =>    $groups['status'] == '开启'?1:0,
            'time_add'           =>    time(),
            'rights'             =>    $groups['rights']
        ];

        if($groups['group_id'] == 0)
        {
            $result = GroupModel::insert($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[],'用户组插入成功');
            }
            else
            {
                return $this->returnCode(201,[],'用户组插入失败');
            }
        }
        else
        {
            $result = GroupModel::where('group_id',$groups['group_id'])->update($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[],'用户组更改成功');
            }
            else
            {
                return $this->returnCode(201,[],'用户组更改失败');
            }
        }

    }
    public function group_del()
    {
        // group_id,根据group_id进行删除用户组操作
        $gid = input('post.group_id'); // 被删除者的ID
        $uid = $this->uid;
        $g1 = UserModel::where('uid',$uid)->value('group_id'); // 删除者的uid
        if($gid < $g1)
        {
            return $this->returnCode(201,[],'用户权限不够,不能够删除该用户');
        }
        $result = GroupModel::where('group_id',$gid)->delete();
        if($result == 1)
        {
            return $this->returnCode(200,[],'用户删除成功');
        }
        else
        {
            return $this->returnCode(201,[],'用户删除失败');
        }
    }

}