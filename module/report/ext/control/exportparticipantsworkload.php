<?php
/**
 * Created by PhpStorm.
 * User: t_xiangyang
 * Date: 2023/1/28
 * Time: 18:17
 */
include '../../control.php';
class myReport extends report
{
    public function exportParticipantsWorkload($projectID = 0, $param = '')
    {
        $param = helper::safe64Decode($param);
        $param = json_decode($param, true);
        $project = $this->dao->select('name')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch();

        if($_POST)
        {
            $this->loadModel('project');
            $this->loadModel('review');
            $this->loadModel('reviewmeeting');

            // 获取搜索条件。
            $begin   = $param['begin'];
            $end     = $param['end'];
            $account = $param['account'];

            // 定义导出的表头。
            $fields                             = array();
            $fields['name']                     = $this->lang->project->name;
            $fields['code']                     = $this->lang->project->code;
            $fields['projectId']                = $this->lang->project->projectId;
            $fields['reviewID']                 = $this->lang->review->reviewID;
            $fields['title']                    = $this->lang->review->title;
            $fields['reviewStatus']             = $this->lang->review->reviewStatus;
            $fields['createdBy']                = $this->lang->reviewmeet->createdBy;
            $fields['createdDept']              = $this->lang->reviewmeet->createdDept;
            $fields['type']                     = $this->lang->review->type;
            $fields['blockDeptName']            = $this->lang->project->blockDeptName;
            $fields['blockMember']              = $this->lang->project->blockMember;
            $fields['blockTotal']               = $this->lang->project->blockTotal;
            $fields['blockPerMonth']            = $this->lang->project->blockPerMonth;
            $fields['trialDept']                = $this->lang->review->trialDept;
            $fields['trialDeptLiasisonOfficer'] = $this->lang->review->trialDeptLiasisonOfficer;
            $fields['trialAdjudicatingOfficer'] = $this->lang->review->trialAdjudicatingOfficer;
            $fields['trialJoinOfficer']         = $this->lang->review->trialJoinOfficer;
            $fields['reviewOwner']              = $this->lang->review->reviewOwner;
            $fields['qa']                       = $this->lang->review->qa;
            $fields['cm']                       = $this->lang->review->cm;
            $fields['onLineExpert']             = $this->lang->review->onLineExpert;
            $fields['expert']                   = $this->lang->review->realExpert;
            $fields['verifier']                 = $this->lang->review->verifier;
            $fields['createdDate']              = $this->lang->review->createdDate;
            $fields['firstPreReviewDate']       = $this->lang->review->firstPreReviewDate;
            $fields['closeTime']                = $this->lang->review->closeTime;
            $fields['baselineDate']             = $this->lang->review->baselineDate;
            $fields['suspendTime']              = $this->lang->review->suspendTime;
            $fields['renewTime']                = $this->lang->review->renewTime;

            // 获取部门、评审、用户工作量数据。
            // 查询所有用户真实姓名
            $accounts = $this->dao->select('account,realname')->from(TABLE_USER)->fetchPairs();
            $rowspanCount = [];$lists = [];

            // 获取部门、评审、用户工作量数据。
            $depts = $this->loadModel('dept')->getOptionMenu();
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
                $lists[$review->reviewID][$review->blockDept][] = $review;
                $rowspanCount[$review->reviewID] ++;
            }
            $typeList          = $this->lang->review->typeList;
            $statusList        = $this->lang->review->statusLabelList;

            $i = 0;
            foreach($lists as $reviewID => $list)
            {
                sort($list);
                foreach($list as $index => $value){
                    foreach($value as $key => $info){
                        $data[$i]                           = new stdclass();
                        $data[$i]->name                     = $info->projectName;
                        $data[$i]->code                     = $info->projectMark;
                        $data[$i]->projectId                = $info->projectCode;
                        if(empty($key) && empty($index)){
                            $rowspan[$i]['rows']['reviewID'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['title'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['reviewStatus'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['createdBy'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['createdDept'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['type'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['trialDept'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['trialDeptLiasisonOfficer'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['trialAdjudicatingOfficer'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['trialJoinOfficer'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['reviewOwner'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['qa'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['cm'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['onLineExpert'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['expert'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['verifier'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['createdDate'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['firstPreReviewDate'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['closeTime'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['baselineDate'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['suspendTime'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['renewTime'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['reviewDays'] = $rowspanCount[$reviewID];
                            $rowspan[$i]['rows']['preReviewDays'] = $rowspanCount[$reviewID];
                        }
                        $data[$i]->reviewID                 = $info->reviewID;
                        $data[$i]->title                    = $info->reviewName;
                        $data[$i]->reviewStatus             = $statusList[$info->status];
                        $data[$i]->createdBy                = $accounts[$info->createdBy];
                        $data[$i]->createdDept              = $depts[$info->createdDept];
                        $data[$i]->type                     = $typeList[$info->type];
                        if(empty($key)){
                            $rowspan[$i]['rows']['blockDeptName'] = count($value);
                        }
                        $data[$i]->blockDeptName            = $depts[$info->blockDept];
                        $data[$i]->blockMember              = $accounts[$info->blockMember];
                        $data[$i]->blockTotal               = $info->blockTotal;
                        $data[$i]->blockPerMonth            = $info->blockPerMonth;
                        $data[$i]->trialDept                = $info->trialDept;
                        $data[$i]->trialDeptLiasisonOfficer = $info->trialDeptLiasisonOfficer;
                        $data[$i]->trialAdjudicatingOfficer = $info->trialAdjudicatingOfficer;
                        $data[$i]->trialJoinOfficer         = $info->trialJoinOfficer;
                        $data[$i]->reviewOwner              = $accounts[$info->owner];
                        $data[$i]->qa                       = $accounts[$info->qa];
                        $data[$i]->cm                       = $accounts[$info->qualityCm];
                        $data[$i]->onLineExpert             = $info->onLineExpert;
                        $data[$i]->expert                   = $info->realExpert;
                        $data[$i]->verifier                 = $info->verifier;
                        $data[$i]->createdDate              = $info->createdDate != '0000-00-00 00:00:00' ? $info->createdDate : '';
                        $data[$i]->firstPreReviewDate       = $info->firstPreReviewDate != '0000-00-00 00:00:00' ? $info->firstPreReviewDate : '';
                        $data[$i]->closeTime                = $info->closeTime != '0000-00-00 00:00:00' ? $info->closeTime : '';
                        $data[$i]->baselineDate             = $info->baselineDate != '0000-00-00 00:00:00' ? $info->baselineDate : '';
                        $data[$i]->suspendTime              = $info->suspendTime != '0000-00-00 00:00:00' ? $info->suspendTime : '';
                        $data[$i]->renewTime                = $info->renewTime != '0000-00-00 00:00:00' ? $info->renewTime : '';
                        $i ++;
                    }
                }
            }
            if(isset($rowspan)) $this->post->set('rowspan', $rowspan);
            $this->post->set('fields', $fields);
            if(isset($data)) $this->post->set('rows', $data);

            if(empty($_POST['fileName']))  $this->post->set('fileName', 'null');
            $this->post->set('kind', 'sheet1');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
            die();
        }

        $this->view->projectID = $projectID;
        $this->view->fileName  = $project->name . '_' . $this->lang->report->reviewParticipantsWorkload;

        $this->display();
    }
}