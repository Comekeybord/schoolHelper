<?php
namespace app\api\controller;

use ouyangke\Ticket;

class Base{
    public $uid;
    public function __construct(){
        header("Access-Control-Allow-Origin:*");
        $post = input('post.');
        $this->uid = Ticket::get($post['ticket'],'ouyangke');  // 将ticket值进行反转，并获取用户id。

        if(empty($this->uid)){
            $this->returnCode(100,[]);  // 如果没有该用户id就
        }
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