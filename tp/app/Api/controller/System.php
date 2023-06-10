<?php


namespace app\Api\controller;
use app\Api\model\Feeds;
use think\worker\Server;
use Workerman\Lib\Timer;
use think\facade\Db;

class System extends Base  // 在同一个文件夹下的文件可以直接继承
{
    public function pushMsg()
    {
        $uid = $this->uid;
        $group_id = input('post.uGroupId');
        $content = input('post.content');
        $arr = [
            "uid"            =>  $uid,
            "push_group_id"  => $group_id,
            "data"           =>  date("Y-m-d H:i:s"),
            "feed"           =>  $content
        ];
        $result = Feeds::insert($arr);
        if ($result)
        {
            return $this->returnCode(200,[],'消息保存成功');
        }
        else
        {
            return $this->returnCode(201,[],'消息保存失败');
        }
    }
    public function config()
    {

    }
    public function safeSee()
    {
        /**
         *
         */
    }
    public function getUserFeeds()
    {
        $values = Feeds::order('data','DESC')->select();  // 所有的推送消息
        $total = [];  // 所有消息的几集合
        foreach ($values as $value)
        {
            $arr = [
                'uid'    =>  $value['uid'],
                "feed"   =>  $value['feed'],
                "data"   =>  $value['data']
            ];
           array_push($total,$arr);
        }
        return $this->returnCode(200,$total,'推送消息成功');
    }
    public function getLoginLog()
    {

    }
}