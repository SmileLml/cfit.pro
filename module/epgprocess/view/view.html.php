<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a(inlink('browse'), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $epgprocess->id;?></span>
      <span class="text" title='<?php echo $epgprocess->name;?>'><?php echo $epgprocess->name;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->epgprocess->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($epgprocess->desc) ? $epgprocess->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php echo $this->fetch('file', 'printFiles', array('files' => $epgprocess->files, 'fieldset' => 'true', 'object' => $epgprocess));?>
      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=epgprocess&objectID=$epgprocess->id");?>
    </div>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack(inlink('browse'));?>
        <div class='divider'></div>
        <?php
          common::printIcon('epgprocess', 'edit', "processID=$epgprocess->id", $epgprocess, 'button');
          common::printIcon('epgprocess', 'delete', "processID=$epgprocess->id", $epgprocess, 'button', 'trash', 'hiddenwin');
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->epgprocess->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th><?php echo $lang->epgprocess->host;?></th>
                <td title="<?php echo $epgprocess->host;?>"><?php echo $epgprocess->host;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->epgprocess->createdBy;?></th>
                <td><?php echo zget($users, $epgprocess->createdBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->epgprocess->createdDate;?></th>
                <td><?php echo $epgprocess->createdDate;?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
