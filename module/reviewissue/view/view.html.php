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
        <div class="detail-title"><?php echo $lang->reviewissue->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($issue->desc) ? $issue->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>

    <div class='cell'>
          <div class="detail">
              <div class="detail-title"><?php echo $lang->reviewissue->dealDesc;?></div>
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
        $browseLink = inlink('issue', $backParams);
        ?>
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
            if(!in_array($issue->review,$reviewIds)){
                js::set('confirmActive', $lang->reviewissue->confirmActive);
                js::set('confirmClose', $lang->reviewissue->confirmClose);
                $recTotal = $pager->recTotal;
                $recPerPage = $pager->recPerPage;
                $pageID = $pager->pageID;
                $params = "project=$projectID&issueID=$issue->id&source=detail&review=$reviewID&status=$status&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
                $resolvedURL   = $this->createLink('reviewissue', 'resolved',$params);
                $editURL   = $this->createLink('reviewissue', 'edit',$params);
                $delURL   = $this->createLink('reviewissue', 'delete',$params);
                echo html::a($resolvedURL, '<i class="icon-checked"></i>' . $lang->reviewissue->resolved, '', 'class="btn" data-size="sm"');
                echo html::a($editURL, '<i class="icon-edit"></i>' . $lang->reviewissue->edit, '', 'class="btn" data-size="sm"');
//                echo html::a($delURL, '<i class="icon-trash"></i>' . $lang->reviewissue->delete, '', 'class="btn" data-size="sm"');
                common::hasPriv('reviewissue', 'delete') ? common::printIcon('reviewissue', 'delete', $params, '', 'list', 'trash','', 'iframe', true, 'data-position="50px"', $lang->reviewissue->delete,0) : '';

            }
         ?>

      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->reviewissue->issueInfo;?></div>
        <div class="detail-content">
          <table class="table table-data">
            <tbody>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->code;?></th>
                <td><?php echo $issue->mark;?></td>
            </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->review;?></th>
                <td><?php echo html::a($this->createLink('review', 'view', "reviewID=$issue->review"), $issue->reviewTitle)?></td>
              </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->title;?></th>
                <td><?php echo $issue->title;?></td>
            </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->type;?></th>
                <td><?php echo zget($lang->reviewissue->typeList, $issue->type, '');?></td>
              </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->raiseBy;?></th>
                <td><?php echo zget($users, $issue->raiseBy);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->raiseDate;?></th>
                <td><?php echo $issue->raiseDate;?></td>
            </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->status;?></th>
                <td><?php echo zget($lang->reviewissue->statusList, $issue->status);?></td>
              </tr>

            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->resolutionBy;?></th>
                <td><?php echo zget($users, $issue->resolutionBy);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->resolutionDate;?></th>
                <td><?php echo $issue->resolutionDate;?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->dealDesc;?></th>
                <td><?php echo $issue->dealDesc;?></td>
            </tr>

            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->validation;?></th>
                <td><?php echo zget($users, $issue->validation);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->verifyDate;?></th>
                <td><?php echo $issue->verifyDate;?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->meetingCode;?></th>
                <td><?php echo $issue->meetingCode;?></td>
            </tr>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->editBy;?></th>
                <td><?php echo zget($users, $issue->editBy);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->editDate;?></th>
                <td><?php echo $issue->editDate;?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->dealOwner;?></th>
                <td><?php echo zget($users, $issue->dealOwner);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->dealDate;?></th>
                <td><?php echo $issue->dealDate;?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->dealUser;?></th>
                <td><?php echo zget($users, $issue->dealUser);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->createdBy;?></th>
                <td><?php echo zget($users, $issue->createdBy);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissue->createdDate;?></th>
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
