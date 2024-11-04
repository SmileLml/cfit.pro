<?php

include_once 'db.php';
include_once 'pushBase.php';
include_once 'pushFileJx.php';
class pushSectransferMediaJx extends pushFileJx
{
    #public const PUT_FILE_URL = 'http://172.23.10.123/zuul/api-service-provider/process/attachment';
    public const PUT_FILE_URL = 'https://172.22.7.4/zuul/api-service-provider/process/attachment';

    public function run()
    {
        //1.查询需要遍历的单子
        try {
            $sql    = "SELECT * FROM zt_sectransfer where openFile = 'true'";
            $result = DBbase::getInstance()->query($sql);
            while ($row = $result->fetch_assoc()) {
                $code = $this->getOrderUser($row);
                if($code != 2){
                    continue;
                }
                //如果没有/ftpdatas开头，就自动拼接
                $filePath = $row['sftpPath'];
                if (!(0 === strpos($filePath, '/ftpdatas'))) {
                    $filePath = '/ftpdatas/' . $filePath;
                }
                $cmd    = 'zipinfo -1 ' . $filePath;
                $output = [];
                exec($cmd, $output, $return_code);
                $fileStr = implode(',', $output);
                DBbase::getInstance()->query("update zt_sectransfer set openFile = 'false',remoteFileList = '{$fileStr}' where id = {$row['id']}"); //更新处
            }
        } catch (Exception $e) {
            $line = 'time: ' . date('Y-m-d H:i:s') . ' get zipinfo error ' . $e . PHP_EOL;
            DBbase::saveLog($line, 'sectransfer');
        }

        //1.查找对外移交表待同步介质状态的数据
        $time   = date('Y-m-d H:i:s', strtotime('-10 hour'));
        $sql    = "SELECT id FROM zt_sectransfer where pushStatus = 'mediaPending' and pushMediaTime > '{$time}'";
        $result = DBbase::getInstance()->query($sql);
        if ($result->num_rows >= 5) {
            return;
        } //最多同时5个推送 (10小时内)

        //2.找到移交表-移交发布区
        //轮训 只处理待发送的
        $sql    = "SELECT * FROM zt_sectransfer where pushStatus = 'tosend' and status = 'waitDeliver' limit 1";
        $result = DBbase::getInstance()->query($sql);
        while ($row = $result->fetch_assoc()) {
            $code = $this->getOrderUser($row);
            if($code != 2 || empty($row['secondorderId'])){
                continue;
            }
            DBbase::getInstance()->query("update zt_sectransfer set pushStatus = 'mediaPending', pushTime = now() where id = {$row['id']} and pushStatus = 'tosend'"); //状态改为发送中 锁定 其他进程不会处理
            $affected_rows = DBbase::getInstance()->affected_rows; //是否更新（锁定）成功
            if (0 == $affected_rows) {
                continue;
            } //如果更新失败（有其他进程在处理），本进程不做处理
            $sql = "SELECT id,externalCode FROM zt_secondorder where id = {$row['secondorderId']}";
            $secondInfo = DBbase::getInstance()->query($sql)->fetch_assoc();

            $pushData = [//数据体
                'processType' => 'work_order',
                'idUnique' => $secondInfo['externalCode'],
                'type' => '',
            ];
            $resCode = $this->doPushFileJx($row['sftpPath'], self::PUT_FILE_URL, $pushData);
            //5.得到结果，修改数据状态
            $line         = 'time: ' . date('Y-m-d H:i:s') . ' id: ' . $row['id'] . ' end ' . json_encode($resCode) . PHP_EOL;
            $statusChange = '';
            $comment      = '';
            if ('success' != $resCode['status']) { //失败的
                $resValue     = 'mediaFail';
                $statusChange = ', status = "askCenterFailed" ';
                $actionValue  = 'pushmediafail';
                if ('noFile' == $resCode['msg']) {
                    $comment = '文件不存在';
                } elseif ('md5Err' == $resCode['msg']) {
                    $comment = 'md5值错误';
                } elseif ('networkErr' == $resCode['msg']) {
                    $comment = '网络错误';
                } elseif ('transErr' == $resCode['msg']) {
                    $comment = 'md5验证失败';
                }
                //修改审批记录
                $sqlreview  = "SELECT * FROM zt_reviewnode where objectType = 'sectransfer' and objectID = {$row['id']} and version = '{$row['version']}' and stage = '6'";
                $reviewInfo = DBbase::getInstance()->query($sqlreview)->fetch_assoc();
                DBbase::getInstance()->query("update zt_reviewnode set status = 'syncfail' where id = {$reviewInfo['id']}");
                DBbase::getInstance()->query("update zt_reviewer set status = 'syncfail' , comment = '{$comment}' ,reviewTime = now() where node = {$reviewInfo['id']}");
                //状态流转
                DBbase::getInstance()->query("insert into zt_consumed (`projectId`,`objectType`,`objectID`,`consumed`, `deptId`,`account`,`reviewStage`,`before`,`after`,`extra`,`createdBy`,`createdDate`,`version`,`deleted`)
                            VALUES('0', 'sectransfer', {$row['id']}, 0, 0,'guestjk','','{$row['status']}','askCenterFailed','','guestjk',now(),0,'0')"); //更新处理结果历史记录
            } else {
                $resValue    = 'mediaSuccess';
                $actionValue = 'pushmediasuccess';
                $comment     = '介质推送成功';
            }

            $sql         = "SELECT * FROM zt_sectransfer where id = {$row['id']}";
            $currentInfo = DBbase::getInstance()->query($sql)->fetch_assoc();
            if ('mediaPending' != $currentInfo['pushStatus']) {
                continue;
            }//状态已被其他程序重置 这里不做更新处理 让其他程序正常执行
            DBbase::getInstance()->query("update zt_sectransfer set pushStatus = '{$resValue}',sendFailReason = '{$resCode['msg']}' {$statusChange} where id = {$row['id']}"); //更新处理结果
            DBbase::getInstance()->query("insert into zt_pushlog (`releaseId`,`type`,`pushTime`,`pushStatus`) VALUES({$row['id']}, 3, now(), {$resCode['msg']})"); //更新处理结果历史记录
            DBbase::getInstance()->query("insert into zt_action (`objectType`,`objectID`,`actor`,`action`,`date`,`product`,`project`,`execution`, `comment`, `read`, `efforted`) VALUES('sectransfer', {$row['id']}, 'guestjk', '{$actionValue}' ,now(), '0', 0, 0, '{$comment}', '0', 0)"); //更新处理结果历史记录
            DBbase::saveLog($line); //记录处理内容
        }
    }

    /**
     * 检查本地md5文件是否正确
     * @param $localFile
     */
    public function checkLocalMd5_sc($localFile)
    {
        $localFileMd5     = $this->getMd5FileName_sc($localFile);            ///aaa.zip 转成 aaa.md5
        $localRealFileMd5 = $localFileMd5;          //本地文件地址MD5
        if (!file_exists($localRealFileMd5)) {
            $localFileMd5     = $this->getMd5OrgName_sc($localFile);         ///aaa.zip 转成 aaa.org
            $localRealFileMd5 = $localFileMd5;      //本地临时文件地址MD5
            if (!file_exists($localRealFileMd5)) {
                $localFileMd5     = $this->getMd5OrgFile($localFile);     //将文件名转成md5
                $localRealFileMd5 = $localFileMd5;
                if (!file_exists($localRealFileMd5)) {
                    return 0;                                               //'md5文件不存在';
                }
            }
        }
        if (!file_exists($localRealFileMd5)) {
            return self::$_statusList['md5Err']; //'md5文件不存在'
        } //检查下载文件是否存在

        $md5value   = $this->getFileMd5_sc($localRealFileMd5);                   //文件里的md5值
        $myMd5value = md5_file($localFile);   //现算的md5值
        if ($md5value != $myMd5value) {
            return 0;                                                   //md5 不对
        }

        return $md5value;      //                                          //没问题返回md5值
    }

    //把后缀变成MD5
    public function getMd5FileName_sc($filename)
    {
        $arr    = explode('.', $filename);
        $ext    = end($arr);
        $extLen = strlen($ext);

        return substr($filename, 0, -$extLen) . 'md5';
    }

    //把后缀变成org
    public function getMd5OrgName_sc($filename)
    {
        $arr    = explode('.', $filename);
        $ext    = end($arr);
        $extLen = strlen($ext);

        return substr($filename, 0, -$extLen) . 'org';
    }

    //把文件名变成md5.org
    public function getMd5OrgFile($filename)
    {
        $arr                 = explode('/', $filename);
        $arr[count($arr) - 1] = 'md5.org';

        return rtrim(implode('/', $arr), '/');
    }

    //读MD5文件中的MD5值
    public function getFileMd5_sc($filename)
    {
        $info = file_get_contents($filename);

        return substr($info, 0, 32);
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
            $sql = "SELECT createdBy FROM zt_secondorder where id = {$data['secondorderId']}";
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

$pushSectransferMediaJx = new pushSectransferMediaJx();
$pushSectransferMediaJx->run();
