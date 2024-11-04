<?php
include_once('db.php');
include_once('pushBase.php');
class pushProtransferMediaQz extends pushBase
{

    const PROJECT_DIR        = 'PROJECT_';

    const TASK_DIR        = 'TASK_';

    public function run()
    {
        //1.查询需要遍历的单子
        try {
            $sql = "SELECT * FROM zt_sectransfer where openFile = 'true'";
            $result = DBbase::getInstance()->query($sql);
            while ($row = $result->fetch_assoc()) {
                $code = $this->getOrderUser($row);
                if($code != 1){
                    continue;
                }
                //如果没有/ftpdatas开头，就自动拼接
                $filePath = $row["sftpPath"];
                if(!(strpos($filePath,'/ftpdatas') === 0)) {
                    $filePath = '/ftpdatas/'.$filePath;
                }
                $cmd = 'zipinfo -1 '.$filePath;
                $output = array();
                exec($cmd, $output, $return_code);
                $fileStr = implode(',',$output);
                DBbase::getInstance()->query("update zt_sectransfer set openFile = 'false',remoteFileList = '{$fileStr}' where id = {$row["id"]}"); //更新处
            }
        } catch (Exception $e) {
            $line = "time: " . date('Y-m-d H:i:s') ." get zipinfo error " .$e. PHP_EOL;
            DBbase::saveLog($line,'sectransfer');
        }

        //1.查找对外移交表待同步介质状态的数据
        $time = date('Y-m-d H:i:s', strtotime('-10 hour'));
        $sql = "SELECT id FROM zt_sectransfer where pushStatus = 'mediaPending' and pushMediaTime > '{$time}'";
        $result = DBbase::getInstance()->query($sql);
        if($result->num_rows >=5 ) return; //最多同时5个推送 (10小时内)

        //2.找到移交表-移交发布区
        //轮训 只处理待发送的
        $sql = "SELECT * FROM zt_sectransfer where pushStatus = 'tosend' and status = 'waitDeliver' limit 1";
        $result = DBbase::getInstance()->query($sql);
        while ($row = $result->fetch_assoc()) {
            $code = $this->getOrderUser($row);
            if($code != 1){
                continue;
            }
            DBbase::getInstance()->query("update zt_sectransfer set pushStatus = 'mediaPending', pushTime = now() where id = {$row["id"]} and pushStatus = 'tosend'"); //状态改为发送中 锁定 其他进程不会处理
            $affected_rows = DBbase::getInstance()->affected_rows; //是否更新（锁定）成功
            if($affected_rows == 0) continue; //如果更新失败（有其他进程在处理），本进程不做处理
            $line = "time: " . date('Y-m-d H:i:s') ." id: " . $row["id"] . " start" . PHP_EOL;
            DBbase::saveLog($line,'sectransfer'); //记录处理内容
            $targetPath = date('/Y-m-d/');
            $fileInfoArr = explode('/', $row["sftpPath"]);
            $zipName = end($fileInfoArr);

            //3.将文件打包
           /* $zip = new ZipArchive();
            if($row["jftype"] == '1'){
                $zip_filename =  $row["sftpPath"].'/'.self::PROJECT_DIR.$zipName.time().'.zip';
                $md5_filename =  $row["sftpPath"].'/'.self::PROJECT_DIR.$zipName.time().'.md5';
                $file_name = self::PROJECT_DIR.$zipName.time().'.zip';
            }else{
                $zip_filename =  $row["sftpPath"].'/'.self::TASK_DIR.$zipName.time().'.zip';
                $md5_filename =  $row["sftpPath"].'/'.self::TASK_DIR.$zipName.time().'.md5';
                $file_name = self::TASK_DIR.$zipName.time().'.zip';
            }

            try{
                if($zip->open($zip_filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true){
                    $this->addFileToZip($row["sftpPath"], $zip);
                    $zip->close();
                }
            } catch (Exception $exception){
                $line = "time: " . date('Y-m-d H:i:s') ." id: " . $row["id"] . " zipErr" . PHP_EOL;
                DBbase::saveLog($line,'sectransfer'); //记录处理内容
                $resValue = 'zipErr';
                $sendReason = '压缩zip失败';
                DBbase::getInstance()->query("update zt_sectransfer set pushStatusQz = {$resValue},sendFailReason = {$sendReason}  where id = {$row["id"]}"); //更新处理结果
                return ;
            }
            //4.生成md5文件
            $md5 = md5_file($zip_filename);

            $md5file = fopen($md5_filename, "w");
            fwrite($md5file, $md5);
            fclose($md5file);*/



            $remotePath = self::REMOTE_DIR . $targetPath . $zipName; //以前传过的
            DBbase::getInstance()->query("insert into zt_action (`objectType`,`objectID`,`actor`,`action`,`date`,`product`,`project`,`execution`, `comment`, `read`, `efforted`) VALUES('sectransfer', {$row["id"]}, 'guestjk', 'pushmedia' ,now(), '0', 0, 0, '', '0', 0)"); //更新处理结果历史记录
            $resCode = $this->doPushProtransferQz($row["sftpPath"], $remotePath, $row["id"]);//推送清总
            //5.得到结果，修改数据状态
            $line = "time: " . date('Y-m-d H:i:s') ." id: " . $row["id"] . " end " .$resCode. PHP_EOL;
            $addFail = '';
            $statusChange = '';
            $comment = '';
            if($resCode != 'success'){ //失败的
                $resValue = 'mediaFail';
                $statusChange =  ', status = "askCenterFailed" ';
                $actionValue = 'pushmediafail';
                if($resCode == 'noFile'){
                    $comment = '文件不存在';
                }else if($resCode == 'md5Err'){
                    $comment = 'md5值错误';
                }else if($resCode == 'networkErr'){
                    $comment = '网络错误';
                }else if($resCode == 'transErr'){
                    $comment = 'md5验证失败';
                }
                //修改审批记录
                $sqlreview = "SELECT * FROM zt_reviewnode where objectType = 'sectransfer' and objectID = {$row["id"]} and version = '{$row['version']}' and stage = '6'";
                $reviewInfo =  DBbase::getInstance()->query($sqlreview)->fetch_assoc();
                DBbase::getInstance()->query("update zt_reviewnode set status = 'syncfail' where id = {$reviewInfo["id"]}");
                DBbase::getInstance()->query("update zt_reviewer set status = 'syncfail' , comment = '{$comment}' ,reviewTime = now() where node = {$reviewInfo["id"]}");
                //状态流转
                DBbase::getInstance()->query("insert into zt_consumed (`projectId`,`objectType`,`objectID`,`consumed`, `deptId`,`account`,`reviewStage`,`before`,`after`,`extra`,`createdBy`,`createdDate`,`version`,`deleted`) 
                            VALUES('0', 'sectransfer', {$row["id"]}, 0, 0,'guestjk','','{$row["status"]}','askCenterFailed','','guestjk',now(),0,'0')"); //更新处理结果历史记录
            }else{
                $resValue = 'mediaSuccess';
                $actionValue = 'pushmediasuccess';
                $comment = '介质推送成功';
            }
            $remotePathQz =  ', remotePath = "' . $remotePath . '" ';

            $sql = "SELECT * FROM zt_sectransfer where id = {$row["id"]}";
            $currentInfo =  DBbase::getInstance()->query($sql)->fetch_assoc();
            if($currentInfo['pushStatus']!='mediaPending') continue;//状态已被其他程序重置 这里不做更新处理 让其他程序正常执行
            DBbase::getInstance()->query("update zt_sectransfer set pushStatus = '{$resValue}',sendFailReason = '{$resCode}' {$remotePathQz}{$statusChange} where id = {$row["id"]}"); //更新处理结果
            DBbase::getInstance()->query("insert into zt_pushlog (`releaseId`,`type`,`pushTime`,`pushStatus`) VALUES({$row["id"]}, 3, now(), {$resCode})"); //更新处理结果历史记录
            DBbase::getInstance()->query("insert into zt_action (`objectType`,`objectID`,`actor`,`action`,`date`,`product`,`project`,`execution`, `comment`, `read`, `efforted`) VALUES('sectransfer', {$row["id"]}, 'guestjk', '{$actionValue}' ,now(), '0', 0, 0, '{$comment}', '0', 0)"); //更新处理结果历史记录
            DBbase::saveLog($line); //记录处理内容
        }
    }

    /**
     * 压缩文件
     * @param $path
     * @param $zip
     * @return void
     */
    public function addFileToZip($path, $zip){
        $handler = opendir($path);
        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != "..") {//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                if (is_dir($path . "/" . $filename)) {// 如果读取的某个对象是文件夹，则递归
                    $this->addFileToZip($path . "/" . $filename, $zip);
                } else { //将文件加入zip对象
                    //过滤之前的压缩包
                    if((substr($filename, 0,7) == 'PROJECT' or substr($filename, 0,4) == 'Task') and (substr($filename,-3) == 'zip' or substr($filename,-3) == 'md5')){
                        continue;
                    }
                    $zip->addFile($path . "/" . $filename);
                }
            }
        }
        closedir($handler);
    }

    /**
     * shixuyang 2023/5/12
     * 推送介质到清总
     */
    public function doPushProtransferQz($localFile, $remotePath, $id)
    {

        //如果没有/ftpdatas开头，就自动拼接
        if(!(strpos($localFile,'/ftpdatas') === 0)) {
            $localFile = '/ftpdatas/'.$localFile;
        }

        $sftp           =  DBbase::getSftpQz(self::$_reconnect); //打开sftp
        self::$_reconnect = 0; //只有链接失败时重新链接
        if(DBbase::$_errCode) {
            return  DBbase::$_errCode;
        }

        //$localRealFile  = iconv('UTF-8','GB2312',$localFile);  //centos 本地文件地址
        //$fileInfoArr = explode('/', $localFile);
        //$zipName = end($fileInfoArr);
        $localRealFile = $localFile;


        if (!file_exists($localRealFile) || empty($localFile)) {
            return "noFile"; //'文件不存在'
        } //检查下载文件是否存在

        $md5value = $this->checkLocalMd5_sc($localRealFile);
        echo($md5value);
        if($md5value === 0 or $md5value < 0) {
            return "md5Err";
        }

        $targetPath = dirname($remotePath);
        $res = ssh2_sftp_mkdir($sftp, $targetPath, 0777, true); //如果目录不存在 创建目录

        $resource = "ssh2.sftp://".intval($sftp) . $remotePath;   //远程文件地址 /2022/11/11/a.zip

        $res = copy($localRealFile, $resource);   //将文件复制到远程
        if(!$res){
            self::$_reconnect = 1; //传输失败 重新链接
            return "networkErr";
        }
        if($this->checkMd5Qz_sc($remotePath, $md5value) != 1){
            return "transErr"; //对方验证MD5失败
        }
        DBbase::getInstance()->query("update zt_sectransfer set mediaMd5 = '".$md5value."'  where id = {$id}"); //md5
        return "success"; //成功
    }

    /**
     * 检查本地md5文件是否正确
     * @param $localFile
     */
    public function checkLocalMd5_sc($localFile)
    {
        $localFileMd5   = $this->getMd5FileName_sc($localFile);            ///aaa.zip 转成 aaa.md5
        $localRealFileMd5   = $localFileMd5;          //本地文件地址MD5
        if (!file_exists($localRealFileMd5)) {
            $localFileMd5   = $this->getMd5OrgName_sc($localFile);         ///aaa.zip 转成 aaa.org
            $localRealFileMd5   = $localFileMd5;      //本地临时文件地址MD5
            if (!file_exists($localRealFileMd5)) {
                $localFileMd5   = $this->getMd5OrgFile($localFile);     //将文件名转成md5
                $localRealFileMd5 = $localFileMd5;
                if(!file_exists($localRealFileMd5)){
                    return 0;                                               //'md5文件不存在';
                }
            }
        }
        if (!file_exists($localRealFileMd5)) {
            return self::$_statusList['md5Err']; //'md5文件不存在'
        } //检查下载文件是否存在

        $md5value = $this->getFileMd5_sc($localRealFileMd5);                   //文件里的md5值
        $myMd5value = md5_file($localFile);   //现算的md5值
        if($md5value != $myMd5value) {
            return 0;                                                   //md5 不对
        }
        return $md5value;      //                                          //没问题返回md5值
    }
    //把后缀变成MD5
    public function getMd5FileName_sc($filename)
    {
        $arr = explode('.', $filename);
        $ext = end($arr);
        $extLen = strlen($ext);
        return substr($filename, 0, -$extLen) . 'md5';
    }

    //把后缀变成org
    public function getMd5OrgName_sc($filename)
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
    public function getFileMd5_sc($filename)
    {
        $info = file_get_contents($filename);
        $md5 = substr($info, 0,32);
        return $md5;
    }

    public function checkMd5Qz_sc($filePath, $md5)
    {
        $data['filePath'] = $filePath;
        $data['md5'] = $md5;
        $res = $this->curl(self::QZ_CHECK_MD5_URL, $data);
        $info = json_decode($res, 2);
        return $info['result'] ?? 0;
    }

    /**
     * 判断工单所属用户为清总还是金信
     * @param $data
     * @return int
     */
    public function getOrderUser($data)
    {
        if ('1' == $data['jftype']) {//项目移交
            if(2 == $data['externalRecipient']){//清总
                return 1;
            }elseif (37 == $data['externalRecipient']){//金信
                return 2;
            }
        }else{
            $sql = "SELECT * FROM zt_secondorder where id = {$data['secondorderId']}";
            $info = DBbase::getInstance()->query($sql)->fetch_assoc();
            if(!empty($info) && 'guestcn' == $info['createdBy']){
                return 1;
            }elseif (!empty($info) && 'guestjx' == $info['createdBy']){
                return 2;
            }
        }

        return 0;
    }

}

$pushProtransferMediaQz = new pushProtransferMediaQz();
$pushProtransferMediaQz->run();
