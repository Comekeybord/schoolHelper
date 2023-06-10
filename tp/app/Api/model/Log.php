<?php


namespace app\Api\model;
use think\model;

class Log extends  model
{
    public function getAllLog()
    {
        /*
         * 获取用户登录日志，把Log表中的uid，data，addr以数组的形式进行返回
         */
        $totals = Log::select();
        $all = [];
        foreach ($totals as $item)
        {
            $arr = [
                'uid'    =>  $item['uid'],
                'data'   =>  $item['data'],
                'addr'   =>  $item['addr']
            ];
            array_push($all,$arr);
        }
        return $arr;
    }
}