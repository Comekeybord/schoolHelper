<?php
namespace app\Api\controller;

use app\Api\model\User as UserModel;
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
        // 判断账号和密码不能为空
        $account = input('post.account');
        $password = input('post.password');
        $password = md5($password);
        if(empty($account)){
            $this->returnCode(201,[],'账号不能为空');
        }
        if(empty($password)){
            $this->returnCode(201,[],'密码不能为空');
        }
        // 通过账号和密码查看用户是否存在
        $user = UserModel::where('account',$account)->where('password',$password)->find();
        if(empty($user)){
            $this->returnCode(201,[],'账号或密码错误');
        }
        $user['token'] = Ticket::create($user['uid'],'ouyangke'); // 用户存在生成token值
        unset($user['password'],$user['uid']);
        $this->returnCode(200,$user,'登录成功');  // 将token值返回到后端
    }
    public function returnCode($code=0,$data=[],$msg){
        /**
         * $code: 状态码
         * $data: 返回数据
         * $msg: 状态码所代表的意思
         */
        $arr = [  // 重新封装
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        echo json_encode($arr);
        if($code != 0){
            exit;
        }
    }
}