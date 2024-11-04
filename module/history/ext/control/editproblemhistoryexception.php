<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 修改问题单历史操作记录中得备注
     * 把【变更异常】修改为【上线异常】
     * @return void
     */
    public function editProblemHistoryException(){
        $actions = $this->dao
            ->select('`id`, `objectID`, `comment`')
            ->from(TABLE_ACTION)
            ->where('objectType')->eq('problem')
            ->andWhere('actor')->eq('guestjk')
            ->andWhere('comment')->like("%变更异常%")
            ->fetchAll('id');

        $ids = [];
        foreach ($actions as $key => $action){
            $comment = str_replace('变更异常', '上线异常', $action->comment);

            $this->dao->update(TABLE_ACTION)->set('comment')->eq($comment)->where('id')->eq($action->id)->exec();

            $ids[] = $action->objectID;
        }


        echo '修改了' . count($ids) . '条数据。';
        a($ids);
    }
}