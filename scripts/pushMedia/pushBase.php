<?php

class pushBase extends DBbase
{
    const QZ_CHECK_MD5_URL = "http://172.22.140.219/checkFileMd5";
    const LOCAL_DIR        = '/ftpdatas';                          //文件服务器目录
//    const LOCAL_DIR        = '/home/monitor/scripts/pushMedia/';                          //文件服务器目录
//    const LOCAL_DIR        = '/www/scripts/pushMedia/';                          //文件服务器目录
//    const REMOTE_DIR       = "/jinke";                               //清总服务器目录
    const REMOTE_DIR       = "";                               //清总服务器目录

    static $_reconnect = 0;
    /**
     * TongYanQi 2022/11/3
     * 推送介质到清总
     */
    public function doPushQz($localFile, $remotePath, $releaseId)
    {

        $sftp           =  DBbase::getSftpQz(self::$_reconnect); //打开sftp
        self::$_reconnect = 0; //只有链接失败时重新链接
        if(DBbase::$_errCode) {
            return  DBbase::$_errCode;
        }

        $localRealFile  = self::LOCAL_DIR . $localFile;  //centos 本地文件地址

        if (!file_exists($localRealFile) || empty($localFile)) {
            return self::$_statusList['noFile']; //'文件不存在'
        } //检查下载文件是否存在


        $md5value = $this->checkLocalMd5($localFile);
        if($md5value === 0) {
            return self::$_statusList["md5Err"];
        }

        if($this->checkMd5Qz($remotePath, $md5value) == 1){ //如果有完整文件不再传
            DBbase::getInstance()->query("update zt_release set md5 = '".$md5value."'  where id = {$releaseId}"); //md5
            return self::$_statusList["success"]; //对方验证MD5失败
        }

//        $targetPath = self::REMOTE_DIR .  dirname($localFile); //远程保存目录地址
        $targetPath = dirname($remotePath);
        $res = ssh2_sftp_mkdir($sftp, $targetPath, 0777, true); //如果目录不存在 创建目录

        $resource = "ssh2.sftp://".intval($sftp) . $remotePath;   //远程文件地址 /2022/11/11/a.zip

        $res = copy($localRealFile, $resource);   //将文件复制到远程
        if(!$res){
            self::$_reconnect = 1; //传输失败 重新链接
            return self::$_statusList["networkErr"];
        }
        if($this->checkMd5Qz($remotePath, $md5value) != 1){
            return self::$_statusList["transErr"]; //对方验证MD5失败
        }
        DBbase::getInstance()->query("update zt_release set md5 = '".$md5value."'  where id = {$releaseId}"); //md5

        return self::$_statusList['success']; //成功
    }

    /**
     * 检查本地md5文件是否正确
     * @param $localFile
     */
    public function checkLocalMd5($localFile)
    {
        $localFileMd5   = $this->getMd5FileName($localFile);            ///aaa.zip 转成 aaa.md5
        $localRealFileMd5   = self::LOCAL_DIR . $localFileMd5;          //本地文件地址MD5
        if (!file_exists($localRealFileMd5)) {
            $localFileMd5   = $this->getMd5OrgName($localFile);         ///aaa.zip 转成 aaa.org
            $localRealFileMd5   = self::LOCAL_DIR . $localFileMd5;      //本地临时文件地址MD5
            if (!file_exists($localRealFileMd5)) {
                $localFileMd5   = $this->getMd5OrgFile($localFile);     //将文件名转成md5
                $localRealFileMd5 = self::LOCAL_DIR . $localFileMd5;
                if(!file_exists($localRealFileMd5)){
                    return 0;                                               //'md5文件不存在';
                }
            }
        }
        if (!file_exists($localRealFileMd5)) {
            return self::$_statusList['md5Err']; //'md5文件不存在'
        } //检查下载文件是否存在

        $md5value = $this->getFileMd5($localRealFileMd5);                   //文件里的md5值
        $myMd5value = md5_file(self::LOCAL_DIR . $localFile);   //现算的md5值
        if($md5value != $myMd5value) {
            return 0;                                                   //md5 不对
        }
        return $md5value;      //                                          //没问题返回md5值
    }
    //把后缀变成MD5
    public function getMd5FileName($filename)
    {
        $arr = explode('.', $filename);
        $ext = end($arr);
        $extLen = strlen($ext);
        return substr($filename, 0, -$extLen) . 'md5';
    }

    //把后缀变成org
    public function getMd5OrgName($filename)
    {
        $arr = explode('.', $filename);
        $ext = end($arr);
        $extLen = strlen($ext);
        return substr($filename, 0, -$extLen) . 'org';
    }

    //把文件名变成md5.org
    public function getMd5OrgFile($filename)
    {
        $arr = explode('/', $filename);
        $arr[sizeof($arr)-1]='md5.org';
        return rtrim(implode('/',$arr),'/');
    }

    //读MD5文件中的MD5值
    public function getFileMd5($filename)
    {
        $info = file_get_contents($filename);
        $md5 = substr($info, 0,32);
        return $md5;
    }

    public function checkMd5Qz($filePath, $md5)
    {
        $data['filePath'] = $filePath;
        $data['md5'] = $md5;
        $res = $this->curl(self::QZ_CHECK_MD5_URL, $data);
        $info = json_decode($res, 2);
        return $info['result'] ?? 0;
    }
}