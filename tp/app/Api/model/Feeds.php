<?php


namespace app\Api\model;
use think\model;

class Feeds extends model
{
    public function getfeedAll()
    {
        /*
         * 获取用户反馈，把Feeds表中的uid，data，feed（信息）以数组的形式返回
         */
        $feeds = Feeds::select()->toArray();
        $all = [];
        foreach ($feeds as $feed)
        {
            $arr = [
                'uid'     =>  $feed['uid'],
                "data"    =>  $feed['data'],
                "feed"    =>  $feed['feed']
            ];
            array_push($all,$arr);
        }
        return $all;
    }
}