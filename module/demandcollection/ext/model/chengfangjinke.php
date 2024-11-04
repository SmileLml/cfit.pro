<?php

public function syncCreated(){
    return $this->loadExtension('demand')->syncCreated();
}

public function syncUpdate($demandId){
    return $this->loadExtension('demand')->syncUpdate($demandId);
}

/**
 * 获取需求意向下拉框
 * @return mixed
 */
public function getPairsByOpinion(){
    return $this->loadExtension('demand')->getPairsByOpinion();
}

/**
 * 获取需求意向下拉框
 * @param $opinionId
 * @return mixed
 */
public function getPairsByRequirement($opinionId = 0){
    return $this->loadExtension('demand')->getPairsByRequirement($opinionId);
}

/**
 * 获取需求条目下拉框
 * @return mixed
 */
public function getPairsByDemand($status = ['deleted', 'suspend', 'closed', 'onlinesuccess']){
    return $this->loadExtension('demand')->getPairsByDemand($status);
}

/**
 * 需求收集状态联动
 * @param $demandId
 * @return mixed
 */
public function statusChange($demandId){
    return $this->loadExtension('demand')->statusChange($demandId);
}

/**
 * 解除同步需求条目
 * @param $demand
 * @param $collectionId
 * @return mixed
 */
public function updateCollection($demand, $collectionId){
    return $this->loadExtension('demand')->updateCollection($demand, $collectionId);
}

