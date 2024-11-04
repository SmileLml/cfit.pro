<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php $browseLink = inlink('issue', "project=$projectID&reviewID=$issue->reviewID");?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::backButton('<i class="icon icon-back icon-sm"></i>' . $lang->goback , '','btn btn-secondary');?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $issueID;?></span>
      <span class="text"><?php echo $issue->title;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class='cell'>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->reviewproblem->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($issue->desc) ? $issue->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class='cell'>
          <div class="detail">
              <div class="detail-title"><?php echo $lang->reviewproblem->dealDesc;?></div>
              <div class="detail-content article-content">
                  <?php echo !empty($issue->dealDesc) ? $issue->dealDesc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
              </div>
          </div>
    </div>

  <?php if($issue->files):?>
      <div class="cell">
          <?php echo $this->fetch('file', 'printFiles', array('files' => $issue->files, 'fieldset' => 'true', 'object' => $issue));?>
      </div>
  <?php endif;?>

    <div class='cell'>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=reviewissue&objectID=$issue->id");?>
        <?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php
        $backParams = "project=$projectID"."&reviewId=$reviewID"."&status=$status"."&param=0"."&orderBy=$orderBy"."&recTotal=$pager->recTotal"."&recPerPage=$pager->recPerPage"."&pageID=$pager->pageID";
        $browseLink = $this->session->reviewproblemList ? $this->session->reviewproblemList : inlink('issue', $backParams);
        ?>
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
            if(!in_array($issue->review,$reviewIds)){
                js::set('confirmActive', $lang->reviewproblem->confirmActive);
                js::set('confirmClose', $lang->reviewproblem->confirmClose);
                $recTotal = $pager->recTotal;
                $recPerPage = $pager->recPerPage;
                $pageID = $pager->pageID;
                $params = "project=$projectID&issueID=$issue->id&source=detail&review=$reviewID&status=$status&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
                $resolvedURL   = $this->createLink('reviewproblem', 'resolved',$params);
                $editURL   = $this->createLink('reviewproblem', 'edit',$params);
                $delURL   = $this->createLink('reviewproblem', 'delete',$params);
                echo html::a($resolvedURL, '<i class="icon-checked"></i>' . $lang->reviewproblem->resolved, '', 'class="btn" data-size="sm"');
                echo html::a($editURL, '<i class="icon-edit"></i>' . $lang->reviewproblem->edit, '', 'class="btn" data-size="sm"');
//                echo html::a($delURL, '<i class="icon-trash"></i>' . $lang->reviewproblem->delete, '', 'class="btn" data-size="sm"');
                common::hasPriv('reviewproblem', 'delete') ? common::printIcon('reviewproblem', 'delete', $params, '', 'button', 'trash','', 'iframe', true, 'data-position="50px"', $lang->reviewproblem->delete) : '';

            }
         ?>

      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->reviewproblem->issueInfo;?></div>
        <div class="detail-content">
          <table class="table table-data">
            <tbody>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->code;?></th>
                <td><?php echo $issue->mark;?></td>
            </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->review;?></th>
                <td><?php echo html::a($this->createLink('reviewmanage', 'view', "reviewID=$issue->review"), $issue->reviewTitle)?></td>
              </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->title;?></th>
                <td><?php echo $issue->title;?></td>
            </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->type;?></th>
                <td><?php echo zget($lang->reviewproblem->typeList, $issue->type, '');?></td>
              </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->raiseBy;?></th>
                <td><?php echo zget($users, $issue->raiseBy);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->raiseDate;?></th>
                <td><?php echo $issue->raiseDate;?></td>
            </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->status;?></th>
                <td><?php echo zget($lang->reviewproblem->statusList, $issue->status);?></td>
              </tr>

            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->resolutionBy;?></th>
                <td><?php echo zget($users, $issue->resolutionBy);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->resolutionDate;?></th>
                <td><?php echo $issue->resolutionDate;?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->dealDesc;?></th>
                <td><?php echo $issue->dealDesc;?></td>
            </tr>

            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->validation;?></th>
                <td><?php echo zget($users, $issue->validation);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->verifyDate;?></th>
                <td><?php echo $issue->verifyDate;?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->meetingCode;?></th>
                <td><?php echo $issue->meetingCode;?></td>
            </tr>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->editBy;?></th>
                <td><?php echo zget($users, $issue->editBy);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->editDate;?></th>
                <td><?php echo $issue->editDate;?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->dealOwner;?></th>
                <td><?php echo zget($users, $issue->dealOwner);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->dealDate;?></th>
                <td><?php echo $issue->dealDate;?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->dealUser;?></th>
                <td><?php echo zget($users, $issue->dealUser);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->createdBy;?></th>
                <td><?php echo zget($users, $issue->createdBy);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewproblem->createdDate;?></th>
                <td><?php echo $issue->createdDate;?></td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php'?>
