<?php
/**
 * Created by PhpStorm.
 * User: t_xiangyang
 * Date: 2023/1/9
 * Time: 13:23
 */
include '../../control.php';
class myReport extends report
{
    public function reviewFlowWorkload($projectID = 0){
        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('review');
        $this->loadModel('reviewmeeting');

        // 查询所有用户真实姓名
        $accounts = $this->dao->select('account,realname')->from(TABLE_USER)->fetchPairs();
        // 获取搜索条件。
        $begin    = $this->post->begin   ? $this->post->begin : '';
        $end      = $this->post->end     ? $this->post->end   : '';

        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $reviewList       = $this->report->getReviewListsSearch(TABLE_FLOWWORKLOAD, $projectID, $begin, $end, '', $group = 'reviewID', $fetch = 'reviewID');

        foreach($reviewList as $key => $review){
            $realName='';
            if(!empty($review->realExpert)){
                $expert = explode(',',$review->realExpert);
                foreach($expert as $name){
                    $realName .= $accounts[$name]?$accounts[$name].',':$name;
                }
            }
            $review->realExpert = $realName;
        }

        $this->view->title             = $this->lang->report->reviewFlowWorkload;
        $this->view->position[]        = $this->lang->report->reviewFlowWorkload;
        $this->view->submenu           = 'program';
        $this->view->projectID         = $projectID;
        $this->view->begin             = $begin;
        $this->view->end               = $end;
        $this->view->depts             = $deptMap;
        $this->view->stageList         = $this->lang->review->reviewStageNameOrder;
        $this->view->typeList          = $this->lang->review->typeList;
        $this->view->statusList        = $this->lang->review->statusLabelList;
        $this->view->reviewList        = $reviewList;
        $this->view->accounts          = $accounts;

        $param = json_encode(array('begin' => $begin, 'end' => $end));
        $this->view->param        = helper::safe64Encode($param);

        $this->display();
    }
}