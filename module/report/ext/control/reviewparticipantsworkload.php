<?php
/**
 * Created by PhpStorm.
 * User: t_xiangyang
 * Date: 2023/1/9
 * Time: 13:24
 */
include '../../control.php';
class myReport extends report
{
    public function reviewParticipantsWorkload($projectID = 0)
    {
        $this->loadModel('project')->setMenu($projectID);
        $this->loadModel('review');
        $this->loadModel('reviewmeeting');

        // 查询所有用户真实姓名
        $accounts = $this->dao->select('account,realname')->from(TABLE_USER)->fetchPairs();
        // 获取搜索条件。
        $begin   = $this->post->begin   ? $this->post->begin : '';
        $end     = $this->post->end     ? $this->post->end   : '';
        $account = $this->post->account ? $this->post->account   : array();
        $list = [];$rowsCount = [];

        // 获取部门、评审、用户工作量数据。
        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $reviewList       = $this->report->getReviewListsSearch(TABLE_PARTICIPANTSWORKLOAD, $projectID, $begin, $end, $account, $group = 'reviewID, blockDept, blockMember', $fetch = '');

        foreach($reviewList as $key => $review){
            $realName='';
            if(!empty($review->realExpert)){
                $expert = explode(',',$review->realExpert);
                foreach($expert as $name){
                    $realName .= $accounts[$name]?$accounts[$name].',':$name;
                }
            }
            $review->realExpert = $realName;
            $list[$review->reviewID][$review->blockDept][] = $review;
            $rowsCount[$review->reviewID] ++;
        }

        $this->view->title      = $this->lang->report->reviewParticipantsWorkload;
        $this->view->position[] = $this->lang->report->reviewParticipantsWorkload;

        $this->view->submenu   = 'program';
        $this->view->projectID = $projectID;

        $this->view->begin          = $begin;
        $this->view->end            = $end;
        $this->view->depts          = $deptMap;
        $this->view->statusList     = $this->lang->review->statusLabelList;
        $this->view->typeList       = $this->lang->review->typeList;
        $this->view->reviewList     = $list;
        $this->view->accounts       = $accounts;
        $this->view->rowsCount      = $rowsCount;
        $this->view->account        = $account;

        $param = json_encode(array('begin' => $begin, 'end' => $end, 'account' => $account));
        $this->view->param        = helper::safe64Encode($param);

        $this->display();
    }
}
