<?php
namespace app\Api\controller;

use ouyangke\Ticket;
use think\Exception;

class Base{
    public $uid; // $uid身份认证标识
    public function __construct(){
        header("Access-Control-Allow-Origin:*");
        $post = input('post.');
        try {  // 抛出异常，如果抛出异常就代表token值不正确
            $this->uid = Ticket::get($post['token'],'ouyangke');  // 将ticket值进行反转，并获取用户id。
        } catch(Exception $e) {  // 捕获异常，
            $this->returnCode(201,[],'Token值不正确');
        }
        if(empty($this->uid)){
            $this->returnCode(201,[],'请先登录');  // 如果没有该用户id就
        }
    }

    public function returnCode($code=0,$data=[],$msg=''){
        /**
         * $code: 状态码
         * $data: 返回数据
         * $msg: 状态码所代表的意思
         */;
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