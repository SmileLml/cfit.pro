<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class getgitlabauthorize extends control
{
    public function doPush(): array
    {
        $this->config->debug = 2; //启动报错
        try {
            $res['getauthorize'] = $this->loadModel('myauthority')->getGitlabAuthorityModel();
        } catch (Exception $e) {
            $res['getauthorize'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('modifyPush', 5); //锁定防止重复
$lock = getTimeLock('getgitlabauthorize', 60); //锁定防止重复
$push = new getgitlabauthorize();
$data = $push->doPush();
saveLog($data,'getgitlabauthorize', 'doPush');
unlock($lock); //解除锁定
