<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 修改交付管理退回审批节点记录数据结构
     * @return void
     */
    public function editReviewFailReason(){
        $modifyData = $this->dao
            ->select('id, reviewFailReason')
            ->from(TABLE_MODIFY)
            ->where('reviewFailReason')->ne('')
            //->andWhere('id')->eq(1429)
            ->fetchAll();
        $ids = [];
        foreach ($modifyData as $key => $modify){
            $reviewFailReason = json_decode($modify->reviewFailReason, true);
            if(empty($reviewFailReason)){
                continue;
            }
            $data = [];
            foreach ($reviewFailReason as $version => $value){
                $arr = [];
                foreach ($value as $node => $item){
                    if(is_array($item) && count($item) == count($item, COUNT_RECURSIVE)){
                        $arr[$node] = $item;
                    }else{
                        $data[$version][] = $item;
                    }
                }
                if(!empty($arr)) $data[$version][] = $arr;
            }
            $this->dao->update(TABLE_MODIFY)->set('reviewFailReason')->eq(json_encode($data))->where('id')->eq($modify->id)->exec();
            $ids[] = $modify->id;
        }

        echo '金信修改了' . count($ids) . '条数据。';
        a($ids);

        $outwardDeliveryData = $this->dao
            ->select('id, reviewFailReason')
            ->from(TABLE_OUTWARDDELIVERY)
            ->where('reviewFailReason')->ne('')
//            ->andWhere('id')->eq(272)
            ->fetchAll();
        $ids = [];
        foreach ($outwardDeliveryData as $key => $outwardDelivery){
            $reviewFailReason = json_decode($outwardDelivery->reviewFailReason, true);
            if(empty($reviewFailReason)){
                continue;
            }
            $data = [];
            foreach ($reviewFailReason as $version => $value){
                $arr = [];
                foreach ($value as $node => $item){
                    if(is_array($item) && count($item) == count($item, COUNT_RECURSIVE)){
                        $arr[$node] = $item;
                    }else{
                        $data[$version][] = $item;
                    }
                }
                if(!empty($arr)) $data[$version][] = $arr;
            }

            $this->dao->update(TABLE_OUTWARDDELIVERY)->set('reviewFailReason')->eq(json_encode($data))->where('id')->eq($outwardDelivery->id)->exec();
            $ids[] = $outwardDelivery->id;
        }

        echo '清总修改了' . count($ids) . '条数据。';
        a($ids);
    }
}