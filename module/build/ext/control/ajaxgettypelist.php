<?php
include '../../control.php';
class myBuild extends build
{
    const NO_PRODUCT   = 99999; //产品  无
    const NO_VERSION   = 1; //版本   无
    /**
     * 获取版本关联的单子（问题需求二线）
     * @param $product
     * @param $version
     * @param $app
     * @param $type
     */
    public function ajaxGetTypeList($app,$project,$product,$version,$type,$value = null)
    {
        $where = '';
        $value = $value ? $value : '';
        $list = $this->loadModel('task')->getVersionNumber($app,$project,$product,$version,$type);

        $type = ($type == 'demand' || $type == 'demandinside') ? 'demandid' : ( $type == 'problem' ? 'problemid' : 'sendlineId');
        //产品 无  版本 无
       if($type != 'sendlineId' && ($product != self::NO_PRODUCT && $version != self::NO_VERSION)) {
           $where = 'disabled';
           $value = implode(',',array_filter(array_keys($list)));
       }
        echo html::select("$type".'[]', $list, $value, "class='form-control chosen' multiple $where onchange=change$type()");
    }
}