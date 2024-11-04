<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class sectransferPush extends control
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
            $res['pushSectransfer'] = $this->loadModel('sectransfer')->getUnPushData();
            $res['pushSectransferJx'] = $this->loadModel('sectransfer')->getUnPushDataJx();
        } catch (Exception $e) {
            $res['pushSectransfer'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('modifyPush', 5); //锁定防止重复
$lock = getTimeLock('pushSectransfer', 5); //锁定防止重复
$push = new sectransferPush();
$data = $push->doPush();
saveLog($data,'pushSectransfer', 'doPush');
unlock($lock); //解除锁定
