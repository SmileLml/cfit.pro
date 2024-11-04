<?php
public function getTestcases($applicationID = 0, $productID = 0, $projectID = 0)
{
    return $this->loadExtension('report')->getTestcases($applicationID, $productID, $projectID);
}

public function getBuildBugs($productID)
{
    return $this->loadExtension('report')->getBuildBugs($productID);
}

public function getWorkSummary($begin, $end, $dept, $type)
{
    return $this->loadExtension('report')->getWorkSummary($begin, $end, $dept, $type);
}

public function getRoadmaps($conditions = '')
{
    return $this->loadExtension('report')->getRoadmaps($conditions);
}

public function getBugSummary($dept, $begin, $end, $type)
{
    return $this->loadExtension('report')->getBugSummary($dept, $begin, $end, $type);
}

public function getCasesRun($applicationID = 0, $productID = 0, $projectID = 0)
{
    return $this->loadExtension('report')->getCasesRun($applicationID, $productID, $projectID);
}

public function getStoryBugs($moduleID)
{
    return $this->loadExtension('report')->getStoryBugs($moduleID);
}

public function getModule($productID)
{
    return $this->loadExtension('report')->getModule($productID);
}
public function getQualityGateBugSummaryOld($projectID = 0, $productId = 0, $productVersion = 0, $buildId = 0){
    return $this->loadExtension('report')->getQualityGateBugSummaryOld($projectID, $productId, $productVersion, $buildId);
}

 public function getQualityGateBugSummary($projectID = 0, $productId = 0, $productVersion = 0, $buildId = 0){
    return $this->loadExtension('report')->getQualityGateBugSummary($projectID, $productId, $productVersion, $buildId);
}

public function getQualityGateBugList($dataSource = 'bug', $projectID = 0, $productId = 0, $productVersion = 0,  $build = 0, $childType = '', $sourceType = '', $severity = 0, $orderBy = 'id_desc', $pager = null){
    return $this->loadExtension('report')->getQualityGateBugList($dataSource, $projectID, $productId, $productVersion, $build, $childType, $sourceType, $severity, $orderBy, $pager);
}