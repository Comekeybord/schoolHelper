<?php
namespace app\Api\controller;
use app\Api\model\Menu;
use app\Api\model\User as UserModel;
use app\Api\model\UserGroup;
use ouyangke\Ticket;
use think\facade\Db;


class User extends Base
{
    public function user_list()
    {
        /**
         *  获取uid，部门，账号，姓名，手机号，qq号，状态
         */
        $arr1 = [];
        $users = new UserModel();
        // 获取user表的['uid','account','name','phone','qq','status','group_id']信息
        $users = $users->getAllUser();
        foreach ($users as $user)  // 遍历
        {
            // 通过用户组ID获取用户组名字
            $group_name = UserGroup::where('group_id',$user['group_id'])->value('group_name');
            $arr=[
                'uid'              =>   $user['uid'],
                'account'          =>   $user['account'],
                'name'             =>   $user['name'],
                'phone'            =>   $user['phone'],
                'qq'               =>   $user['qq'],
                'group_name'       =>   $group_name,
                'status'           =>   $user['status']
            ];
            array_push($arr1,$arr);
        }
        $arr = [];
        $page = input('get.page');
        for($i = ($page-1)*10;$i<$page*10;$i++)
        {
            if($i<count($arr1))
            {
                array_push($arr,$arr1[$i]);
            }
        }
        return $this->returnCode(200,$arr);  // 如果需要对JSON数据进行操作和转换，应该使用thinkphp6中的json函数；如果只是需要将PHP变量转换为JSON格式的字符串，则可以使用json_encode()函数。
    }
    public function user_add()
    {
        /**
         * 1. 以post方式获取前端参数
         * 2. 判断数据是否正确
         *      2.1 账户是否不为空，为空返回失败数据并停止往下指向
         *      2.2 密码是否不为空，为空返回失败数据并停止往下指向(先判断uid的值，如果uid为0表示添加数据，uid为1表示修改数据)
         * 3. 根据用户ID来判断是更新数据还是插入数据
         *      3.1 用户ID为0表示插入数据
         *      3.2 用户ID>0表示更新数据
         */
        $data = input('post.');
        if(empty($data['account']))
        {
            return $this->returnCode( 201,[],'账号不能为空');
        }
        if(empty($data['password']))
        {
            return $this->returnCode( 201,[],'密码不能为空');
        }
        // 通过用户组名字获取用户组ID
        $group_id = UserGroup::where('group_name',$data['group_name'])->value('group_id');
        $gid = UserModel::where('uid',$this->uid)->value('group_id');  // 当前用户的用户组ID
        if($group_id < $gid)  // 当前用户ID如果大于添加用户组ID，说明权限不构
        {
            return $this->returnCode(201,[],'当前用户不能添加或修改该用户组下的用户');
        }
        $arr = [  // 数据整理
            "account"     => $data['account'],
            "password"    => md5($data['password']),
            "name"        => $data['name'],
            "phone"       => $data['phone'],
            "qq"          => $data['qq'],
            "sex"         => $data['sex'],
            "group_id"     => $group_id,
            "times_login"  => 1,
            "status"       => $data['status'] == '开启' ? 1 : 0,
            "time_add"     => time(),
            "time_last"    => time(),
        ];
        if($data['uid'] == 0) // 插入数据
        {
            $result = UserModel::insert($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[],'数据插入成功');
            }
            else{
                return $this->returnCode(201,[],'数据插入失败');
            }
        }
        else  // 修改数据
        {
            $result = UserModel::where('uid', $data['uid'])->update($arr);  // 根据用户ID修改数据
            if($result == 1)
            {
                return $this->returnCode(200,[],'数据更改成功');
            }
            else{
                return $this->returnCode(201,[],'数据更改失败');
            }
        }
    }

    public function user_del()
    {
        $uid = input('post.uid');
        $g1 = UserModel::where('uid',$this->uid)->value('group_id'); // 删除者的uid
        $g2 = UserModel::where('uid',$uid)->value('group_id');  // 被删除者的uid
        if($g2 < $g1)
        {
            return $this->returnCode(201,[],'用户权限不够,不能够删除该用户');
        }
        $result = UserModel::where('uid',$uid)->delete();
        if($result == 1)
        {
            return $this->returnCode(200,[],'用户删除成功');
        }
        else{
            return $this->returnCode(201,[],'用户删除失败');
        }
    }
}