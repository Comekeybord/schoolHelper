<?php


namespace app\Api\controller;
use network\Request;

class test
{
    public function test()
    {
        $request = new Request('https://rest.ipw.cn/api/ip/queryThird',array(['ip'   =>  '61.136.204.136']));
         return $request->post();
    }
}