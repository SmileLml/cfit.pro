<?php
include_once('db.php');
include_once('pushBase.php');
include_once('pushFileJx.php');
class putProductionMediaJx extends pushFileJx
{
    const LOCAL_DIR        = '/ftpdatas';                          //文件服务器目录
    const REMOTE_DIR       = "";                               //清总服务器目录
    #const PUSH_FILE_URL = "http://172.23.10.123/zuul/api-service-provider/process/attachment";
    const PUSH_FILE_URL = "https://172.22.7.4/zuul/api-service-provider/process/attachment";
    public function run()
    {
        //1.查询需要遍历的单子
        try {
            $sql = "SELECT * FROM zt_putproduction where openFile = 'true'";
            $result = DBbase::getInstance()->query($sql);
            while ($row = $result->fetch_assoc()) {
                $resultFileArray = array();
                $sftpArray = json_decode($row["sftpPath"]);
                foreach ($sftpArray as $filePath){
                    //如果没有/ftpdatas开头，就自动拼接
                    if(!(strpos($filePath,'/ftpdatas') === 0)) {
                        $filePath = '/ftpdatas/'.$filePath;
                    }
                    $cmd = 'zipinfo -1 '.$filePath;
                    $output = array();
                    exec($cmd, $output, $return_code);
                    array_push($resultFileArray, implode(',',$output));
                }
                $fileStr = implode(',',$resultFileArray);
                DBbase::getInstance()->query("update zt_putproduction set openFile = 'false',remoteFileList = '{$fileStr}' where id = {$row["id"]}"); //更新处
            }
        } catch (Exception $e) {
            $line = "time: " . date('Y-m-d H:i:s') ." get zipinfo error " .$e. PHP_EOL;
            DBbase::saveLog($line,'putproduction');
        }

        //推送文件
        try{
            //查找推送的单子
            $sql = "SELECT * FROM zt_putproduction where status = 'waitdelivery' and pushStatus = '0'";
            $result = DBbase::getInstance()->query($sql);
            while ($row = $result->fetch_assoc()) {
                DBbase::getInstance()->query("update zt_putproduction set pushStatus = '3', pushDate = now() where id = {$row["id"]} and pushStatus = '0'"); //状态改为发送中 锁定 其他进程不会处理
                $affected_rows = DBbase::getInstance()->affected_rows; //是否更新（锁定）成功
                if($affected_rows == 0) continue; //如果更新失败（有其他进程在处理），本进程不做处理
                $line = "time: " . date('Y-m-d H:i:s') ." id: " . $row["id"] . " start" . PHP_EOL;
                DBbase::saveLog($line,'putproduction'); //记录处理内容
                if($row["stage"] == '1'){
                    //推送sftp地址
                    $sftpList = json_decode($row["sftpPath"]);
                }else{
                    //通过介质查找sftp
                    $sftpList = array();
                    $releaseIds = explode(',',trim($row["releaseId"],','));
                    foreach ($releaseIds as $releaseId){
                        $sql = "SELECT * FROM zt_release where id = '{$releaseId}'";
                        $releaseResult = DBbase::getInstance()->query($sql);
                        while ($releaseObj = $releaseResult->fetch_assoc()) {
                            array_push($sftpList, $releaseObj["path"]);
                        }
                    }
                }
                $fileIds = array();
                foreach ($sftpList as $sftp){
                    $paramArray = array();
                    $paramArray['idUnique'] = $row["code"];
                    $paramArray['processType'] = 'production';
                    $resCode = $this->doPushFileJx($sftp, self::PUSH_FILE_URL, $paramArray);//推送清总
                    if($resCode['status'] != 'success'){ //失败的
                        $pushStatus = '5';
                        if($resCode['msg'] == 'noFile'){
                            $putFileFailReason = '文件不存在';
                        }else if($resCode['msg'] == 'md5Err'){
                            $putFileFailReason = 'md5值错误';
                        }else if($resCode['msg'] == 'networkErr'){
                            $putFileFailReason = '网络错误';
                        }else if($resCode['msg'] == 'transErr'){
                            $putFileFailReason = 'md5验证失败';
                        }else{
                            $putFileFailReason = $resCode['msg'];
                        }
                        DBbase::getInstance()->query("update zt_putproduction set pushStatus='{$pushStatus}' , putFileFailReason='{$putFileFailReason}' where id = {$row['id']}");
                        return ;
                    }else if($resCode['status'] == 'success'){
                        if(!empty($resCode['fileId'])){
                            array_push($fileIds, $resCode['fileId']);
                        }
                    }
                }
                $fileIdsStr = implode(',',$fileIds);
                DBbase::getInstance()->query("update zt_putproduction set pushStatus='4', jxfileId='{$fileIdsStr}' where id = {$row['id']}");
            }
        }catch (Exception $e){
            $line = "time: " . date('Y-m-d H:i:s') ." push file error " .$e. PHP_EOL;
            DBbase::saveLog($line,'putproduction');
        }
    }
}

$putProductionMediaJx = new putProductionMediaJx();
$putProductionMediaJx->run();
