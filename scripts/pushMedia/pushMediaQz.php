<?php
include_once('db.php');
include_once('pushBase.php');
class pushMediaQz extends pushBase
{
    public function run()
    {
        $time = date('Y-m-d H:i:s', strtotime('-10 hour'));
        $sql = "SELECT id FROM zt_release where pushStatusQz = 2 and pushTimeQz > '{$time}'";
        $result = DBbase::getInstance()->query($sql);
        if($result->num_rows >=5 ) return; //最多同时5个推送 (10小时内)
        //轮训 只处理待发送的
        $sql = "SELECT * FROM zt_release where pushStatusQz = 1 limit 1";
        $result = DBbase::getInstance()->query($sql);

        while ($row = $result->fetch_assoc()) {
            DBbase::getInstance()->query("update zt_release set pushStatusQz = 2, pushTimeQz = now() where id = {$row["id"]} and pushStatusQz = 1"); //状态改为发送中 锁定 其他进程不会处理
            $affected_rows = DBbase::getInstance()->affected_rows; //是否更新（锁定）成功
            if($affected_rows == 0) continue; //如果更新失败（有其他进程在处理），本进程不做处理
            $line = "time: " . date('Y-m-d H:i:s') ." id: " . $row["id"] . " - Name: " . $row["name"] . " start" . PHP_EOL;
            DBbase::saveLog($line); //记录处理内容
            $targetPath = date('/Y-m-d/');
            $fileInfoArr = explode('/', $row["path"]);
            $zipName = end($fileInfoArr);

            $remotePath = empty($row["remotePathQz"]) ? self::REMOTE_DIR . $targetPath . $zipName : $row["remotePathQz"]; //以前传过的
            $resCode = $this->doPushQz($row["path"], $remotePath, $row["id"]);//推送清总
            $line = "time: " . date('Y-m-d H:i:s') ." id: " . $row["id"] . " - Name: " . $row["name"] . " end" .$resCode. PHP_EOL;
            $addFail = '';

            if($resCode != 3){ //失败的
                $addFail = ', pushFailsQz = pushFailsQz +1 ';
            }
            $remotePathQz =  ', remotePathQz = "' . $remotePath . '" ';

            $sql = "SELECT * FROM zt_release where id = {$row["id"]}";
            $currentInfo =  DBbase::getInstance()->query($sql)->fetch_assoc();
            if($currentInfo['pushStatusQz']!=2) continue;//状态已被其他程序重置 这里不做更新处理 让其他程序正常执行
            DBbase::getInstance()->query("update zt_release set pushStatusQz = {$resCode} {$addFail} {$remotePathQz} where id = {$row["id"]}"); //更新处理结果
            DBbase::getInstance()->query("insert into zt_pushlog (`releaseId`,`type`,`pushTime`,`pushStatus`) VALUES({$row["id"]}, 1, now(), {$resCode})"); //更新处理结果历史记录
            DBbase::saveLog($line); //记录处理内容
        }
    }

}

$pushMedia = new pushMediaQz();
$pushMedia->run();

