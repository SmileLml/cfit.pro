<?php
include '../../control.php';
class myReport extends report
{
    public function stageParticipantWorkload($projectID = 0)
    {
        $this->app->loadLang('execution');
        $this->loadModel('project')->setMenu($projectID);

        // 获取搜索条件。
        $begin   = $this->post->begin   ? $this->post->begin : '';
        $end     = $this->post->end     ? $this->post->end   : '';
        $account = $this->post->account ? $this->post->account   : array();
        $stage   = $this->post->stage   ? $this->post->stage   : array();

        $queryStages = $this->project->getConditionStagePairsByProject($projectID);

        $sortStages = $this->project->getReportStageOrderByProject($projectID, $stage);
        $stageWorkloadList = $this->report->getChildrenStagePersonnelWorkload($sortStages, $begin, $end, $account,$projectID);
        $deptMap = $this->loadModel('dept')->getOptionMenu();

        $this->view->title      = $this->lang->report->stageparticipantWorkload;
        $this->view->position[] = $this->lang->report->stageparticipantWorkload;

        $this->view->submenu   = 'program';
        $this->view->projectID = $projectID;

        $this->view->begin   = $begin;
        $this->view->end     = $end;
        $this->view->account = $account;
        $this->view->stage   = $stage;

        $this->view->queryStages = $queryStages;
        $this->view->deptMap     = $deptMap;

        $this->view->participants  = $this->report->getReportWorkloadUserPairs($projectID);
        $this->view->workloadList  = $stageWorkloadList['stageWorkloadList'];
        $this->view->rowspanParent = $stageWorkloadList['rowspanParent'];
        $this->view->rowspanChild  = $stageWorkloadList['rowspanChild'];

        foreach ($sortStages as $sortStage){
            if($sortStage->parent == 0){
                $totalUser = 0;
                $perMonth = 0;
                if(isset($sortStage->childres)){
                    foreach ($sortStage->childres as $children){
                        foreach($stageWorkloadList['stageWorkloadList'][$children->id] as $deptID => $users){
                            foreach($users as $index => $user){
                                $totalUser += $user->total;
                                $perMonth += $user->perMonth;
                            }
                        }
                    }
                }

                if($totalUser != 0){
                    $sortStage->total = $totalUser;
                }
                if($perMonth != 0){
                    $sortStage->perMonth = $perMonth;
                }
            }
        }
        $this->view->stages      = $sortStages;


        $param = json_encode(array('begin' => $begin, 'end' => $end, 'account' => $account, 'stage' => $stage));
        $this->view->param        = helper::safe64Encode($param);

        $this->display();
    }
}
