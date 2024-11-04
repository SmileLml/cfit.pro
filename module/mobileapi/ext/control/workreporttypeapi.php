<?php
include '../../control.php';
include '../../../../vendor/autoload.php';
use Firebase\JWT\JWT;
class myMobileApi extends mobileapi
{

    /**
     * 类型接口
     */
    public function workreportTypeApi()
    {
        $this->app->loadLang('task');
        unset($this->lang->task->typeList[array_search('现场支持',$this->lang->task->typeList)]);
        $workType = $this->lang->task->typeList;

        $res = array();
        foreach ($workType as $key => $item) {
            $pro = new stdClass();
            $pro->key  = $key;
            $pro->text = $item;
            $res[] = $pro;
        }

        $this->loadModel('mobileapi')->response('success', '', array('workType'=>$res) ,  0, 200,'workreportTypeApi');
    }
}
