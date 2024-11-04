<?php
include_once('db.php');
include_once('pushBase.php');
class retryPushMediaQz extends pushBase
{
    public function run()
    {
        $time = date('Y-m-d H:i:s', strtotime('-10 hour'));
        $sql = "SELECT id FROM zt_release where pushStatusQz = 2 and pushTimeQz > '{$time}'";
        $result = DBbase::getInstance()->query($sql);
        if($result->num_rows >=5 ) return; //最多同时5个推送 (10小时内)

        $time = date('Y-m-d H:i:s', strtotime('-30 min'));
        $sql = "SELECT * FROM zt_release where pushStatusQz > 1 and pushStatusQz <> 3 and pushStatusQz <> 2 and pushFailsQz < 3 and pushTimeQz < '{$time}' limit 1"; //一次处理一个 取40分钟前 推送且未完成的 或者失败的 重新推。

        $result = DBbase::getInstance()->query($sql);

        while ($row = $result->fetch_assoc()) {
            DBbase::getInstance()->query("update zt_release set pushStatusQz = 2, pushTimeQz = now() where id = {$row["id"]}"); //记录处理时间 用于重试
            $line = "id: " . $row["id"] . " - Name: " . $row["name"] . " " . 'retryStart' . PHP_EOL;
            DBbase::saveLog($line); //记录处理内容
            $targetPath = date('/Y-m-d/');
            $fileInfoArr = explode('/', $row["path"]);
            $zipName = end($fileInfoArr);

            $remotePath = empty($row["remotePathQz"]) ? self::REMOTE_DIR . $targetPath . $zipName : $row["remotePathQz"]; //以前传过的
            $resCode = $this->doPushQz($row["path"], $remotePath, $row["id"]);//推送清总
            $line = "id: " . $row["id"] . " - Name: " . $row["name"] . " " . 'retryEnd:'. $resCode . PHP_EOL;
            $addFail = '';
            if($resCode != 3){
                $addFail = ', pushFailsQz = pushFailsQz +1 ';
            }
            $remotePathQz =  ', remotePathQz = "'.$remotePath.'" ';
            DBbase::getInstance()->query("update zt_release set pushStatusQz = {$resCode} {$addFail} {$remotePathQz} where id = {$row["id"]}"); //更新处理结果
            DBbase::getInstance()->query("insert into zt_pushlog (`releaseId`,`type`,`pushTime`,`pushStatus`) VALUES({$row["id"]}, 1, now(), {$resCode})"); //更新处理结果历史记录
            DBbase::saveLog($line); //记录处理内容
        }
    }


}
$pushMedia = new retryPushMediaQz();
$pushMedia->run();
