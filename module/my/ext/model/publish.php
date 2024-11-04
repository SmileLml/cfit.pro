<?php
public function queryPublish($userid)
{   
    $publishs = $this->dao->select('*')->from(TABLE_FLOW_PUBLISH)
        ->where('status')->eq(2)
        ->andWhere('deleted')->eq(0)
        ->andWhere('sdate')->le(date('Y-m-d H:i:s'))
        ->andWhere('endDate')->gt(date('Y-m-d H:i:s'))
        ->fetchAll('id');

    $result=array();
    if(empty($publishs)){
        return $result;
    }else{
        foreach($publishs as $publish){
            $publish = $this->loadModel('file')->replaceImgURL($publish, 'content');
            $record = $this->dao->select('publishid')->from(TABLE_PUBLISHRECORD)
                ->where('userid')->eq($userid)
                ->andWhere('publishid')->eq($publish->id)
                ->fetchAll('publishid');
            if(empty($record)){
                array_push($result,$publish);
            }
        } 
    }
    return $result;
}   

?>