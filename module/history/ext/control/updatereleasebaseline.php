<?php
include '../../control.php';
class myHistory extends history
{
    public function updateReleaseBaseLine(){
        $sql = "SELECT zr.id, zr.`status`, zr.`version`, zr.baseLineUser,zr.baseLineTime,
                za.actor,za.date
                from zt_release zr
                LEFT JOIN zt_action za on za.objectType = 'release' and za.objectID = zr.id
                LEFT JOIN zt_history zh on zh.action =za.id
                where zr.deleted = '0'
                and (zr.version > 1 OR (zr.version = 1 and zr.`status` != 'waitBaseline'))
                and za.objectType = 'release' 
                and za.action = 'deal' 
                and zh.field = 'status' 
                and zh.old = 'waitBaseline'
                and zh.new = 'waitCmConfirm'
            GROUP BY zr.id";

        $data = $this->dao->query($sql)->fetchAll();
        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }
        $updateReleaseIds = [];
        foreach ($data as $val){
            $releaseId = $val->id;
            $baseLineList = $this->loadModel('projectrelease')->getBaseLineLogList($releaseId, '*', 'version ASC');
            if(empty($baseLineList)){
                continue;
            }
            $baseLineData = [];
            foreach ($baseLineList as $baseLine){
                $version = $baseLine->version;
                $baseLineData[$version][] = $baseLine;
            }

            $actionSql = "SELECT za.* from zt_action za
                        LEFT JOIN zt_history zh on zh.action =za.id
                        where 1
                        and za.objectType = 'release'
                        and za.objectID = '{$releaseId}'
                        and za.action = 'deal'
                        and zh.field = 'status'
                        and zh.old = 'waitBaseline'
                        and zh.new = 'waitCmConfirm'";
            $actionList = $this->dao->query($actionSql)->fetchAll();
            if(empty($actionList)){
                continue;
            }

            foreach ($baseLineData as $key => $baseLines){
                $ids = array_column($baseLines, 'id');
                if(isset($actionList[$key])){
                    $actionInfo = $actionList[$key];
                    if(!empty($actionInfo)){
                        $updateParams = new stdClass();
                        $updateParams->baseLineUser = $actionInfo->actor;
                        $updateParams->baseLineTime = $actionInfo->date;
                        $res = $this->dao->update(TABLE_RELEASE_BASELINE_LOG)->data($updateParams)->where("id")->in($ids)->exec();
                        $updateReleaseIds[] = $releaseId;
                    }
                }
            }
            //获取最后一条记录
            $countNum = count($actionList);
            $lastActionInfo = $actionList[$countNum - 1];
            $updateParams = new stdClass();
            $updateParams->baseLineUser = $lastActionInfo->actor;
            $updateParams->baseLineTime = $lastActionInfo->date;
            $res = $this->dao->update(TABLE_RELEASE)->data($updateParams)->where("id")->eq($releaseId)->exec();
        }
        $updateReleaseIds = array_flip(array_flip($updateReleaseIds));
        $total = count($updateReleaseIds);
        echo "处理了{$total}条历史数据成功,发布单id:".implode(',', $updateReleaseIds);
        return true;
    }
}