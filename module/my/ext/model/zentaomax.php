<?php
public function getNcList($browseType, $orderBy, $pager)
{
    $account = $this->app->user->account;
    $ncs = $this->dao->select('*')->from(TABLE_NC)
        ->where('deleted')->eq(0)
        ->beginIF($browseType == 'assigendToMe')->andWhere('assignedTo')->eq($account)
        ->beginIF($browseType == 'createdByMe')->andWhere('createdBy')->eq($account)
        ->beginIF($browseType == 'resolvedByMe')->andWhere('resolvedBy')->eq($account)
        ->beginIF($browseType == 'closedByMe')->andWhere('closedBy')->eq($account)
        ->page($pager)
        ->fetchAll();

    return $ncs;
}

public function getProductPairs()
{
    return $this->dao->select('id, name')->from(TABLE_PRODUCT)
        ->where('deleted')->eq(0)
        ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->products)->fi()
        ->fetchPairs();
}

public function ajaxGetProject($browseType)
{
    $reviewProject = $this->loadModel('project')->getReviewProject();
    $projectName = array();
    foreach($reviewProject as $project)
    {
        $projectName[] = $project->name;
    }
    $projectPinYin = common::convert2Pinyin($projectName);

    $projectLink = helper::createLink('my', 'review', "program=%s&browseType=$browseType");
    $listLink    = '';
    foreach($reviewProject as $item)
    {
        $listLink .= html::a(sprintf($projectLink, $item->id), '<i class="icon icon-folder-outline"></i>' . $item->name, '', 'title="' . $item->name . '" data-key="' . zget($projectPinYin, $item->name) . '"');
    }

    $html  = '<div class="table-row"><div class="table-col col-left"><div class="list-group">' . $listLink . '</div>';
    $html .= '<div class="col-footer">';
    $html .= html::a(sprintf($projectLink, 0), '<i class="icon icon-cards-view muted"></i>' . $this->lang->exportTypeList['all'], '', 'class="not-list-item"');
    $html .= '</div></div>';
    $html .= '<div class="table-col col-right"><div class="list-group"></div>';

    return $html;
}

public function reviewPrintCell($col, $review, $users, $products = array())
{
    $canView = common::hasPriv('my', 'review');
    $canBatchAction = false;
    $account    = $this->app->user->account;
    $id = $col->id;
    if($col->show)
    {
        $class = "c-$id";
        $title = '';
        if($id == 'id') $class .= ' cell-id';
        if($id == 'status')
        {
            $class .= ' status-' . $review->status;
        }
        if($id == 'result')
        {
            $class .= ' status-' . $review->result;
        }
        if($id == 'title')
        {
            $class .= ' text-left';
            $title  = "title='{$review->title}'";
        }

        echo "<td class='" . $class . "' $title>";
        switch($id)
        {
        case 'id':
            if($canBatchAction)
            {
                echo html::checkbox('reviewIDList', array($review->id => '')) . html::a(helper::createLink('review', 'view', "reviewID=$review->id"), sprintf('%03d', $review->id));
            }
            else
            {
                printf('%03d', $review->id);
            }
            break;
        case 'title':
            echo html::a(helper::createLink('review', 'view', "reviewID=$review->id", '', '', $review->project), $review->title);
            break;
        case 'category':
            echo zget($this->lang->review->objectList, $review->category);
            break;
        case 'product':
            echo zget($products, $review->product);
            break;
        case 'version':
            echo $review->version;
            break;
        case 'status':
            echo zget($this->lang->review->statusList, $review->status);
            break;
        case 'reviewedBy':
            $reviewedBy = explode(',', $review->reviewedBy);
            foreach($reviewedBy as $account)
            {
                $account = trim($account);
                if(empty($account)) continue;
                echo zget($users, $account) . " &nbsp;";
            }
            break;
        case 'createdBy':
            echo zget($users, $review->createdBy);
            break;
        case 'createdDate':
            echo $review->createdDate;
            break;
        case 'deadline':
            echo $review->deadline;
            break;
        case 'lastReviewedDate':
            echo $review->lastReviewedDate;
            break;
        case 'lastAuditedDate':
            echo $review->lastAuditedDate;
            break;
        case 'result':
            echo zget($this->lang->review->resultList, $review->result);
            break;
        case 'auditResult':
            echo zget($this->lang->review->auditResultList, $review->auditResult);
            break;
        case 'actions':
            $params           = "reviewID=$review->id&from={$this->app->rawMethod}";
            $canAssess        = $this->loadModel('review')->judgeIfCanAssess($review, $this->app->user->account);
            if($canAssess)
            {
                common::printIcon('review', 'assess', $params, $review, 'list', 'glasses', '', '', '', "data-app='project'", '', $review->project);
            }
            else
            {
                common::printIcon('review', 'assess', $params, $review, 'list', 'glasses', '', 'disabled');
            }
            common::printIcon('review', 'result', "program=$review->project&" . $params, $review, 'list', 'list-alt', '', 'iframe', true, '', '', $review->project);
        }
        echo '</td>';
    }
}
