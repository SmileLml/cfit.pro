<?php

/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class removeLogDir extends control
{
    public function remove()
    {
        $month = date('Ym', strtotime('-2 month'));
        $logPath = dirname(dirname(__FILE__)).'/www/data/log/'.$month.'/';
        $files = scandir($logPath);
        foreach ($files as $file){
            if($file == '.' || $file == '..') continue;
            unlink($logPath.$file);
        }
        @rmdir($logPath);
    }
}

$push = new removeLogDir();
$push->remove();
