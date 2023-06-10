<?php
namespace app\Api\controller;
use app\Api\model\Menu;
use app\Api\model\User as UserModel;
use app\Api\model\UserGroup;
use ouyangke\Ticket;
use think\facade\Db;
use think\response\Json;


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
                'sex'              =>   $user['sex']===1?'男':'女',
                'group_name'       =>   $group_name,
                'status'           =>   $user['status']
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
        return $this->returnCode(200,$arr); // 如果需要对JSON数据进行操作和转换，应该使用thinkphp6中的json函数；如果只是需要将PHP变量转换为JSON格式的字符串，则可以使用json_encode()函数。
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
            if($group_id < $gid)  // 当前用户ID如果大于添加用户组ID，说明权限不构(可以添加同权限或者比自己权限低的用户)
            {
                return $this->returnCode(201,[],'当前用户不能添加或修改该用户组下的用户');
            }
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
            if($group_id <= $gid)  // 当前用户ID如果大于添加用户组ID，说明权限不构（不能够修改和自己一样或者比自己权限大的用户）
            {
                return $this->returnCode(201,[],'当前用户不能添加或修改该用户组下的用户');
            }
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
    public function user_search()
    {
        $value = input('post.');
        $key = $value['keyword'];
        $account = UserModel::select(); // $account为所有的信息
        $userList = [];  // 符合搜索条件的信息
        $total = 0;   // 搜索的条数
        foreach ($account as $item)
        {
            $group = UserGroup::where('group_id',$item['group_id'])->value('group_name');
            if($item['account'] === $key | $item['name'] === $key | $group === $key)  // 只要account,name用户组有一个符合搜索条件，就把整条数据push到$userList中
            {
                $arr = [  // 数据整理
                    "uid"        =>  $item['uid'],
                    "account"    =>  $item['account'],
                    "name"       =>  $item['name'],
                    "phone"      =>  $item['phone'],
                    "qq"         =>  $item['qq'],
                    "group_id"   =>  $group,
                    "status"     =>  $item['status']==1?'开启':'关闭',
                ];
                array_push($userList, $arr);
                $total = $total + 1;
            }
        }
        $arr = [];  // 分页
        $limit = $value['limit'];  // 每个页面显示的条数
        $page = $value['page'];  // 第几页
        for($i = ($page-1)*$limit;$i<$page*$limit;$i++)
        {
            if($i<count($userList))
            {
                array_push($arr,$userList[$i]);
            }
        }
        if(!empty($userList))
        {
            return json_encode([
                "code" => 200,
                "msg"  => '请求成功',
                "data" => [
                    "userList" => $arr,
                    "total"    => $total>count($arr)?count($arr):$total
                ]
            ]);
        }
        else
        {
            return json_encode([
            "code" => 201,
            "msg"  => '请求失败',
            "data" => []
            ]);
        }

    }
}