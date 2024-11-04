<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class modifyPush extends control
{
    /**
     * User: TongYanQi
     * Date: 2022/8/29
     * 清总问题反馈失败重试
     */
    public function doPush(): array
    {
        $this->config->debug = 2; //启动报错
        try {
            $res['pushModify'] = $this->loadModel('modify')->getUnPushedAndPush();
        } catch (Exception $e) {
            $res['pushModify'] = $e;
        }
        try {
            $res['pushCancelModify'] = $this->loadModel('modify')->getCancelUnPushedAndPush();
        } catch (Exception $e) {
            $res['pushCancelModify'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('modifyPush', 5); //锁定防止重复
$lock = getTimeLock('modifyPush', 5); //锁定防止重复
$push = new modifyPush();
$data = $push->doPush();
saveLog($data,'modifyPush', 'doPush');
unlock($lock); //解除锁定
