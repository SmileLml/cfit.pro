<?php
class sdk extends control
{
    public function getUserList()
    {
        include_once('../../sdk/php/zentao.php');
        $zentao      = new \zentao();
        $params      = array('deptID' => 1);    // 请求参数
        $extraFields = array('title', 'users');    // 自定义返回字段
        $result      = $zentao->getUserList($params, $extraFields);
        echo $result;
    }
}
