<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 把问题单延期申请成功的单子【是否交付超期】改为否
     * @return void
     */
    public function editSectransferDept()
    {
        $data = $this->dao
            ->select('t1.id,t1.apply,t2.dept')
            ->from(TABLE_SECTRANSFER)->alias('t1')
            ->innerJoin(TABLE_USER)->alias('t2')
            ->on('t1.apply = t2.account')
            ->fetchAll();

        $ids = [];
        foreach ($data as $key => $value){
            $this->dao->update(TABLE_SECTRANSFER)->set('dept')->eq($value->dept)->where('id')->eq($value->id)->exec();
            $ids[] = $value->id;
        }


        echo '修改了' . count($ids) . '条数据。';
        a($ids);
    }
}