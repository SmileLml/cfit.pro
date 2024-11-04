<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php $browseLink = inlink('issue', "reviewID=$issue->reviewID");?>
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
        <div class="detail-title"><?php echo $lang->reviewissueqz->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($issue->desc) ? $issue->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class='cell'>
          <div class="detail">
              <div class="detail-title"><?php echo $lang->reviewissueqz->verifyContent;?></div>
              <div class="detail-content article-content">
                  <?php echo !empty($issue->verifyContent) ? $issue->verifyContent : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
              </div>
          </div>
    </div>
    <div class='cell'>
      <div class="detail">
          <div class="detail-title"><?php echo $lang->reviewissueqz->opinionReply;?></div>
          <div class="detail-content article-content">
              <?php echo !empty($issue->opinionReply) ? $issue->opinionReply : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          </div>
      </div>
    </div>
    <div class='cell'>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=reviewissueqz&objectID=$issue->id");?>
        <?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php
        $backParams = "reviewId=$reviewID"."&status=$status"."&param=0"."&orderBy=$orderBy"."&recTotal=$pager->recTotal"."&recPerPage=$pager->recPerPage"."&pageID=$pager->pageID";
        $browseLink = inlink('issue', $backParams);
        ?>
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php

            $recTotal = $pager->recTotal;
            $recPerPage = $pager->recPerPage;
            $pageID = $pager->pageID;
            $params = "issueID=$issue->id&source=detail&review=$reviewID&status=$status&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
            common::hasPriv('reviewissueqz', 'edit') ?   common::printIcon('reviewissueqz', 'edit', $params, $issue, 'list', '','', '', '', '', $lang->reviewissueqz->edit) : '';
            common::hasPriv('reviewissueqz', 'delete') ? common::printIcon('reviewissueqz', 'delete', $params, $issue, 'list', 'trash','', 'iframe', true, 'data-position="50px"', $lang->reviewissueqz->delete) : '';

         ?>

      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->reviewissueqz->view;?></div>
        <div class="detail-content">
          <table class="table table-data">
            <tbody>

              <tr>
                <th class="w-100px"><?php echo $lang->reviewissueqz->review;?></th>
                <td><?php echo html::a($this->createLink('reviewqz', 'view', "reviewID=$issue->reviewId"), $issue->reviewTitle)?></td>
              </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissueqz->title;?></th>
                <td><?php echo $issue->title;?></td>
            </tr>

            <tr>
                <th class="w-100px"><?php echo $lang->reviewissueqz->raiseBy;?></th>
                <td><?php echo zget($users, $issue->raiseBy);?></td>
            </tr>
            <tr>
                <th class="w-100px"><?php echo $lang->reviewissueqz->raiseDate;?></th>
                <td><?php echo $issue->raiseDate;?></td>
            </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->reviewissueqz->status;?></th>
                <td><?php echo zget($lang->reviewissueqz->statusLabelList, $issue->status);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->createBy;?></th>
                  <td><?php echo zget($users, $issue->createBy);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->createTime;?></th>
                  <td><?php echo $issue->createTime;?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->dealUser;?></th>
                  <td><?php echo zget($users, $issue->dealUser);?></td>
              </tr>

              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->type;?></th>
                  <td><?php echo zget($lang->reviewissueqz->typeList, $issue->type);?></td>
              </tr>

              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->resolutionBy;?></th>
                  <td><?php echo zget($users, $issue->resolutionBy);?></td>
              </tr>

              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->resolutionDate;?></th>
                  <td><?php echo $issue->resolutionDate;?></td>
              </tr>

              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->validation;?></th>
                  <td><?php echo $issue->validation;?></td>
              </tr>

              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->verifyDate;?></th>
                  <td><?php echo $issue->verifyDate;?></td>
              </tr>

              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->content;?></th>
                  <td><?php echo $issue->content;?></td>
              </tr>

              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->accept;?></th>
                  <td><?php echo zget($lang->reviewissueqz->acceptList, $issue->accept);?></td>
              </tr>

              <tr>
                  <th class="w-100px"><?php echo $lang->reviewissueqz->proposalType;?></th>
                  <td><?php echo zget($lang->reviewissueqz->proposalTypeList, $issue->proposalType);?></td>
              </tr>


            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php'?>
