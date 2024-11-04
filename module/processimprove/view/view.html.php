<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a(inlink('browse'), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $processimprove->id;?></span>
      <span class="text" title='<?php echo '';?>'><?php echo '';?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->processimprove->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($processimprove->desc) ? $processimprove->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->processimprove->judge;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($processimprove->judge) ? $processimprove->judge : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php echo $this->fetch('file', 'printFiles', array('files' => $processimprove->files, 'fieldset' => 'true', 'object' => $processimprove));?>
      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=processimprove&objectID=$processimprove->id");?>
    </div>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack(inlink('browse'));?>
        <div class='divider'></div>
        <?php
          if(common::hasPriv('processimprove', 'feedback')) common::printIcon('processimprove', 'feedback', "processID=$processimprove->id", $processimprove, 'button', 'feedback', '', 'iframe', true);
          if(common::hasPriv('processimprove', 'close')) common::printIcon('processimprove', 'close', "processID=$processimprove->id", $processimprove, 'button', 'off', '', 'iframe', true);
          echo "<div class='divider'></div>";
          if(common::hasPriv('processimprove', 'edit')) common::printIcon('processimprove', 'edit', "processID=$processimprove->id", $processimprove, 'button');
          if(common::hasPriv('processimprove', 'delete')) common::printIcon('processimprove', 'delete', "processID=$processimprove->id", $processimprove, 'button', 'trash', 'hiddenwin');
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->processimprove->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->processimprove->process;?></th>
                <td><?php echo zget($lang->processimprove->processList, $processimprove->process, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->involved;?></th>
                <td><?php echo zget($lang->processimprove->involvedList, $processimprove->involved, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->source;?></th>
                <td><?php echo zget($lang->processimprove->sourceList, $processimprove->source, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->createdDept;?></th>
                <td><?php echo zget($depts, $processimprove->createdDept, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->createdBy;?></th>
                <td><?php echo zget($users, $processimprove->createdBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->createdDate;?></th>
                <td><?php echo $processimprove->createdDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->judgedBy;?></th>
                <td><?php echo zget($users, $processimprove->judgedBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->judgedDate;?></th>
                <td><?php echo $processimprove->judgedDate == '0000-00-00' ? '' : $processimprove->judgedDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->isAccept;?></th>
                <td><?php echo zget($lang->processimprove->isAcceptList, $processimprove->isAccept, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->pri;?></th>
                <td><?php echo zget($lang->processimprove->priorityList, $processimprove->pri, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->isDeploy;?></th>
                <td><?php echo zget($lang->processimprove->isAcceptList, $processimprove->isDeploy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->deployDate;?></th>
                <td><?php echo $processimprove->deployDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->processimprove->reviewedBy;?></th>
                <td><?php $reviewedBy = explode(',', str_replace(' ', '', $processimprove->reviewedBy)); foreach($reviewedBy as $account) echo ' ' . zget($users, $account); ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
