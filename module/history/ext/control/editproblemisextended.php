<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 把问题单延期申请成功的单子【是否交付超期】改为否
     * @return void
     */
    public function editProblemIsExtended()
    {
        $field = 't2.delayStatus';
        $problems = $this->dao
            ->select("t1.*, $field")
            ->from(TABLE_DELAY)->alias('t2')
            ->leftJoin(TABLE_PROBLEM)->alias('t1')
            ->on("t1.id = t2.objectId and t2.objectType = 'problem'")
            ->where('delayStatus')->eq('success')
            ->fetchAll();

        $ids = [];
        foreach ($problems as $key => $problem){
            if(1 == $problem->isExtended) continue;
            $this->dao->update(TABLE_PROBLEM)->set('isExtended')->eq(1)->where('id')->eq($problem->id)->exec();
            $ids[] = $problem->id;
        }


        echo '修改了' . count($ids) . '条数据。';
        a($ids);
    }
}