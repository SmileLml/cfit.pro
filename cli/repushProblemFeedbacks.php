<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class repushProblemFeedbacks extends control
{
    /**
     * User: TongYanQi
     * Date: 2022/8/29
     * 清总问题反馈失败重试
     */
    public function doPush(): array
    {
        $this->config->debug = 2; //启动报错
        $res[] = $this->loadModel('problem')->rePushFeedBacks();
        return $res;
    }
}
//$lock = getLock('repushProblemFeedbacks', 5); //锁定防止重复
$lock = getTimeLock('repushProblemFeedbacks', 5); //锁定防止重复
$push = new repushProblemFeedbacks();
$data = $push->doPush();
saveLog($data,'repushProblemFeedbacks', 'doPush');
unlock($lock); //解除锁定
