<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class checkNfs extends control
{
    public function check()
    {
        $this->config->debug = 2; //启动报错
        try {
            $filePath = '/var/www/zentao/www/data';
            if(!is_dir($filePath)){
                $nfsIp = $this->dao->select("*")->from(TABLE_LANG)->where("module")->eq('api')->andWhere('section')->eq('nfsList')->andWhere('`key`')->eq('url')->fetch();
                if(!empty($nfsIp->value)){
                    $umountCmd = 'umount /var/www/zentao/www/data';
                    $mountCmd = 'mount -t nfs '.$nfsIp->value.':/redis/cfit_pms_data /var/www/zentao/www/data';
                    $out = shell_exec($umountCmd);
                    $out1 = shell_exec($mountCmd);
                    echo 'success';
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }
}
$checkNfs = new checkNfs();
$data = $checkNfs->check();

