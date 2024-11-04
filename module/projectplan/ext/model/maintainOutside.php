<?php
/**
 *根据年度信息计划的关联(外部)项目/任务处理outsidePlan表的linkedPlan
 */
function maintainOutside($projectPlanId,$outsidePlans)
{
    $outsides= $this->dao->select('id,linkedPlan')->from(TABLE_OUTSIDEPLAN)->where('deleted')->eq(0)->fetchPairs();
    $outsideLinkedPlan='';
    foreach ($outsides as $id=>$linkedPlan)
    {
        if(strpos($outsidePlans,','.$id.',')!==false){
            $outsideLinkedPlan = str_replace(','.$projectPlanId,'',$linkedPlan);
            $outsideLinkedPlan = $outsideLinkedPlan.$projectPlanId.',';
            if(strpos($outsideLinkedPlan,',')!==false && strpos($outsideLinkedPlan,',')!=0)$outsideLinkedPlan=','.$outsideLinkedPlan;
        }else{
            $outsideLinkedPlan = str_replace(','.$projectPlanId,'',$linkedPlan);
        }
        $this->dao->update(TABLE_OUTSIDEPLAN)->set('linkedPlan="'.$outsideLinkedPlan.'"')->where('id')->eq($id)->exec();
    }
}

/**
 *根据外部信息化项目计划处理projectplan表的outsideProject
 */
function maintain($outsidePlanId,$planIds)
{
    $plans= $this->dao->select('id,outsideProject')->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->fetchPairs();
    $outsideProjects = '';
    foreach($plans as $id=>$outsideProject)
    {
        if(strpos($planIds,','.$id.',')!==false){
            $outsideProjects = str_replace(','.$outsidePlanId,'',$outsideProject);
            $outsideProjects = $outsideProjects.$outsidePlanId.',';
            if(strpos($outsideProjects,',')!==false && strpos($outsideProjects,',')!=0)$outsideProjects=','.$outsideProjects;
        }else{
            $outsideProjects = str_replace(','.$outsidePlanId,'',$outsideProject);
        }
        $this->dao->update(TABLE_PROJECTPLAN)->set('outsideProject="'.$outsideProjects.'"')->where('id')->eq($id)->exec();
    }
}
?>
