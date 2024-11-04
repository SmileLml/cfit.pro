<?php
include '../../control.php';
class myHistory extends history
{
    public function updateReviewParentId(){
        $sql = "SELECT t1.id from zt_review t1
                LEFT JOIN zt_reviewnode t2 on t2.objectType = 'review' and t2.objectID = t1.id
                LEFT JOIN zt_reviewer t3 on t3.node = t2.id
                where 1 
                and t1.deleted = '0'
                and t1.`status` not in ('reviewpass', 'fail', 'drop', 'archive', 'baseline')
                and t2.nodeCode in ('firstReview','firstMainReview')
                and t3.parentId = 0
                GROUP BY t1.id";
        $data = $this->dao->query($sql)->fetchAll();
        if(empty($data)){
            echo '处理历史数据成功';
            return true;
        }
        $dealReviewIds = [];
        foreach ($data as $val) {
            $isDeal = false;
            $reviewId = $val->id;
            $sql2 = "SELECT t1.version,t2.reviewer,t2.id as userId, t3.dept  
                            from zt_reviewnode t1
                            LEFT JOIN zt_reviewer t2 on t2.node = t1.id
                            LEFT JOIN zt_user t3 on t2.reviewer = t3.account
                            where 1 
                            and t1.objectType = 'review'
                            and t1.objectID = '{$reviewId}'
                            and t1.nodeCode = ('firstAssignReviewer')";
            $assignReviewerList = $this->dao->query($sql2)->fetchAll();
            if (empty($assignReviewerList)) {
                continue;
            }

            $tempAssignList = [];
            foreach ($assignReviewerList as $assignUserInfo) {
                $version = $assignUserInfo->version;
                $tempAssignList[$version][] = $assignUserInfo;
            }
            foreach ($tempAssignList as $version => $currentAssignList) {
                $firstAssignInfo = $currentAssignList[0];
                foreach ($currentAssignList as $assignUserInfo) {
                    $userId = $assignUserInfo->userId;
                    $dept = $assignUserInfo->dept;
                    $sql3 = "SELECT t2.id from zt_reviewnode t1
                                LEFT JOIN zt_reviewer t2 on t2.node = t1.id
                                LEFT JOIN zt_user t3 on t2.reviewer = t3.account
                                where 1 
                                and t1.objectType = 'review'
                                and t1.objectID =  '{$reviewId}'
                                and t1.nodeCode in ('firstReview','firstMainReview')
                                and t1.version = '{$version}'
                                and t2.parentId = 0
                                and t3.dept = '{$dept}'";
                    $reviewerList = $this->dao->query($sql3)->fetchAll();
                    if (empty($reviewerList)) {
                        continue;
                    }
                    $reviewerIds = array_column($reviewerList, 'id');
                    $updateParams = new stdClass();
                    $updateParams->parentId = $userId;
                    $res = $this->dao->update(TABLE_REVIEWER)->data($updateParams)->where("id")->in($reviewerIds)->exec();
                    $isDeal = true;
                }
                //处理找不到同一部门的
                $sql3 = "SELECT t2.id from zt_reviewnode t1
                            LEFT JOIN zt_reviewer t2 on t2.node = t1.id
                            where 1 
                            and t1.objectType = 'review'
                            and t1.objectID =  '{$reviewId}'
                            and t1.nodeCode in ('firstReview','firstMainReview')
                            and t1.version = '{$version}'
                            and t2.parentId = 0";
                $reviewerList = $this->dao->query($sql3)->fetchAll();
                if(!empty($reviewerList)){
                    $reviewerIds = array_column($reviewerList, 'id');
                    $updateParams = new stdClass();
                    $updateParams->parentId = $firstAssignInfo->userId;
                    $res = $this->dao->update(TABLE_REVIEWER)->data($updateParams)->where("id")->in($reviewerIds)->exec();
                    $isDeal = true;
                }
            }
            if($isDeal){
                $dealReviewIds[] = $reviewId;
            }
        }
        $count = count(array_flip(array_flip($dealReviewIds)));
        echo "处理历史数据成功, 处理了". $count. '条数据';
    }
}