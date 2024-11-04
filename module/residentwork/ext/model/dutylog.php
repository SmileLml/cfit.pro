<?php
public function createlog(){
    return $this->loadExtension('dutylog')->createlog();
}
public function getWorkList($browseType,$queryID, $orderBy, $pager=''){
    return $this->loadExtension('dutylog')->getWorkList($browseType,$queryID, $orderBy, $pager);
}
public function editlog($workId){
    return $this->loadExtension('dutylog')->editlog($workId);
}
