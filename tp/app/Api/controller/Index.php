<?php


namespace app\Api\controller;
use app\Api\model\Menu;
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
        return $this->returnCode(200,$arr1);  // 如果需要对JSON数据进行操作和转换，应该使用thinkphp6中的json函数；如果只是需要将PHP变量转换为JSON格式的字符串，则可以使用json_encode()函数。
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
            return $this->returnCode( 10000101,[]);
        }
        if(empty($data['password']))
        {
            return $this->returnCode( 10000102,[]);
        }
        // 通过用户组名字获取用户组ID
        $group_id = UserGroup::where('group_name',$data['group_name'])->value('group_id');
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
             $result = User::insert($arr);
             if($result == 1)
             {
                 return $this->returnCode(200,[]);
             }
             else{
                 return $this->returnCode(404,[]);
             }
        }
        else  // 修改数据
        {
            $result = User::where('uid', $data['uid'])->update($arr);  // 根据用户ID修改数据
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

    public function group_list()
    {
        // 展示group_id group_name status
        $groups = new UserGroup();
        $groups = $groups->getAllGroup();
        $arr1 = [];
        foreach ($groups as $group)
        {
            $arr = [
                'group_id'           =>   $group['group_id'],
                'group_name'         =>   $group['group_name'],
                'status'             =>   $group['status'] == 1?'开启':'关闭'
            ];
            array_push($arr1,$arr);
        }
        return $this->returnCode(200,$arr1);
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
            return $this->returnCode(10000104,[]);
        }
        $arr = [  // 数据整理
            'group_name'         =>    $groups['group_name'],
            'status'             =>    $groups['status'] == '开启'?1:0,
            'time_add'           =>    time(),
            'rights'             =>    $groups['rights']
        ];
        if($groups['group_id'] == 0)
        {
            $result = UserGroup::insert($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[]);
            }
            else
            {
                return $this->returnCode(404,[]);
            }
        }
        else
        {
            $result = UserGroup::where('group_id',$groups['group_id'])->update($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[]);
            }
            else
            {
                return $this->returnCode(404,[]);
            }
        }

    }
    public function group_del()
    {
        // group_id,根据group_id进行删除用户组操作
        $gid = input('post.group_id');
        $result = UserGroup::where('group_id',$gid)->delete();
        if($result == 1)
        {
            return $this->returnCode(200,[]);
        }
        else
        {
            return $this->returnCode(404,[]);
        }
    }

    public function menu_list()
    {
        // mid，pid,label，src，sort，status,
        $menus = new Menu();
        $menus = $menus->getAllMenu();
        return $this->returnCode(200,$menus);
    }
    public function menu_add()
    {
        $menus = input('post.');  // 获取所有的参数
        if(empty($menus['parent_id']) | empty($menus['label']) | empty($menus['src']) | empty($menus['sort']) | empty('status'))
        {
            return $this->returnCode(10000104,[]);
        }
        $mid = $menus['mid'];
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
            $result = Menu::insert($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[]);
            }
            else
            {
                return $this->returnCode(404,[]);
            }
        }
        else
        {
            $result = Menu::where('mid',$mid)->update($arr);
            if($result == 1)
            {
                return $this->returnCode(200,[]);
            }
            else
            {
                return $this->returnCode(404,[]);
            }
        }
    }
    public function menu_del()
    {
        // mid,根据菜单ID进行删除
        $mid = input('post.mid');
        $result = Menu::where('mid',$mid)->delete();
        if($result == 1)
        {
            return $this->returnCode(200,[]);
        }
        else
        {
            return $this->returnCode(404,[]);
        }
    }
    public function getUserFeeds()
    {

    }
}