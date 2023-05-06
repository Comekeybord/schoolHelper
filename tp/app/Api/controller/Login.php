<?php


namespace app\Api\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Request;

use ouyangke\Ticket;
class Login
{
    public function login(){
        /**
         * 1. 判断账户和密码（如果账号密码为空，直接返回让其从新输入）
         * 2. 根据账号查找密码
         */
        $account = input('post.account');
        if(empty($account)){
            $this->returnCode(10000101,[]);
        }
        $password = input('post.password');
        if(empty($password)){
            $this->returnCode(10000102,[]);
        }

        $password = md5($password);
        $user = Db::table('admin_user')->where('account',$account)->where('password',$password)->find();
        if(empty($user)){
            $this->returnCode(10000103,[]);
        }

        $user['ticket'] = Ticket::create($user['uid'],'ouyangke');
        unset($user['password'],$user['uid']);
        $this->returnCode(200,$user);  // 将ticket值返回到后端

    }

    public function returnCode($code=0,$data=[]){
        /**
         * $code: 状态码
         * $data: 返回数据
         * $msg: 状态码所代表的意思
         */
        $code_msg = [
            100 => '请先登录',
            200 => '成功',
            10000101 => '账户不能为空',
            10000102 => '密码不能为空',
            10000103 => '账号或密码输入错误',
        ];

        $arr = [  // 重新封装
            'code' => $code,
            'msg' => $code_msg[$code],
            'data' => $data
        ];

        echo json_encode($arr);
        if($code != 0){
            exit;
        }
    }
}