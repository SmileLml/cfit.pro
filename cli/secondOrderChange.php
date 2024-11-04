<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class secondOrderChange extends control
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
            $res['secondOrderChange'] = $this->loadModel('secondorder')->changestatus();
        } catch (Exception $e) {
            $res['secondOrderChange'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('modifyPush', 5); //锁定防止重复
$lock = getTimeLock('secondOrderChange', 5); //锁定防止重复
$push = new secondOrderChange();
$data = $push->doPush();
saveLog($data,'secondOrderChange', 'doPush');
unlock($lock); //解除锁定
