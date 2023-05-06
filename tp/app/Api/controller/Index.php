<?php


namespace app\Api\controller;
use app\Api\model\User;
use app\Api\model\UserGroup;


class Index extends Base
{
    public function user_list()
    {
        /**
         *  获取uid，部门，账号，姓名，手机号，qq号，状态
         */
        $arr1 = [];
        $users = new User();
        $users = $users->getAllUser();  // 获取user表的['uid','account','name','phone','qq','status','group_id']信息
        foreach ($users as $user)  // 遍历
        {
            $arr2 = [];
            $group_id = '';
             for($i=0;$i<7;$i++)
             {
                 if($i == 5)
                 {
                     $group_id = $user[$i];
                 }
                 else {
                     array_push($arr2, $user[$i]);
                 }
             }
            $group_name = UserGroup::where('group_id',$group_id)->value('group_name');
            array_push($arr2,$group_name);
            array_push($arr1,$arr2);
        }
        return $this->returnCode(200,$arr1);  // 如果需要对JSON数据进行操作和转换，应该使用thinkphp6中的json函数；如果只是需要将PHP变量转换为JSON格式的字符串，则可以使用json_encode()函数。
    }
    public function user_add()
    {
        /**
         * 1. 以post方式获取前端参数
         * 2. 判断数据是否正确
         *      2.1 账户是否不为空，为空返回失败数据并停止往下指向
         *      2.2 密码是否不为空，为空返回失败数据并停止往下指向(先判断uid的值，如果uid为0表示添加数据，uid为1表示修改数据)
         * 3. 进行数据整理
         *      密码进行md5进行加密
         * 4. 插入数据和修改数据
         *      如果插入数据和修改数据成功就返回成功的消息，否则返回失败的消息
         */
        $data = input('post.');
        if(empty($data['account']))
        {
            return $this->returnCode( 10000101,[]);
        }
        if(empty($data['password']))
        {
            return $this->returnCode( 10000102,[]);
        }
        if($data['uid'] == 0)
        {
             $group_id = UserGroup::where('group_name',$data['group_name'])->value('group_id');
             $arr = [
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
             $result = User::insert($arr);
             if($result == 1)
             {
                 return $this->returnCode(200,[]);
             }
             else{
                 return $this->returnCode(404,[]);
             }
        }
        else
        {
            $group_id = UserGroup::where('group_name',$data['group_name'])->value('group_id');
            $arr = [
                "account"      => $data['account'],
                "password"     => md5($data['password']),
                "name"         => $data['name'],
                "phone"        => $data['phone'],
                "qq"           => $data['qq'],
                "sex"          => $data['sex'],
                "group_id"     => $group_id,
                "times_login"  => 1,
                "status"       => $data['status'] == '开启' ? 1 : 0,
                "time_add"     => time(),
                "time_last"    => time(),
            ];
            $result = User::where('uid', $data['uid'])->update($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[]);
            }
            else{
                return $this->returnCode(404,[]);
            }
        }
    }
    public function user_del()
    {
        $uid = input('post.uid');
        $result = User::where('uid',$uid)->delete();
        if($result == 1)
        {
            return $this->returnCode(200,[]);
        }
        else{
            return $this->returnCode(404,[]);
        }
    }
}