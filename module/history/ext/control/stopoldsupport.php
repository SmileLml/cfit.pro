<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 旧的数据支持迁到新的现场支持表
     * @return void
     */
    public function stopOldSupport($flowCode =  'support', $flowEnable = 'disable')
    {
        $isUpdate = false;
        $sql = "select  *  from zt_config zc where 1 and `section`  = 'global' and `key` = 'flowPending';";
        $data = $this->dao->query($sql)->fetch();
        if($data){
            $id = $data->id;
            $customList = json_decode($data->value, true);
            if(!empty($customList)){
                foreach ($customList as $key => $val){
                    if($val['flowCode'] == $flowCode && $val['flowEnable'] != $flowEnable){
                        $val['flowEnable'] = $flowEnable;
                        $customList[$key] = $val;
                        $isUpdate = true;
                        break;
                    }
                }
                $customList = json_encode($customList);
                $ret = $this->dao->update(TABLE_CONFIG)->set('value')->eq($customList)->where('id')->eq( $id)->exec();
            }
        }
        $op = $isUpdate ? '处理成功': '无需处理';
        echo $op;
        exit();

    }
}