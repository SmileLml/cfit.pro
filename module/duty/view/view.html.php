<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()) echo html::a(inlink('browse'), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $duty->id;?></span>
      <span class="text" title='<?php echo zget($lang->duty->typeList, $duty->type, '');?>'><?php echo zget($lang->duty->typeList, $duty->type, '');?></span>
      <?php echo $duty->importantTime ? "<span><i class='icon icon-flag red'></i></span>" : '';?>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->duty->user;?></div>
        <div class="detail-content">
          <?php if(empty($duty->user)) echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          <table class='table table-data'>
            <tbody>
              <?php foreach($userPhone as $user => $phone):?>
              <tr>
                <th class='w-90px' style="color: #3c4353"><?php echo zget($users, $user, '');?></th>
                <td><?php echo $phone;?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->duty->actualUser;?></div>
        <div class="detail-content">
          <?php if(empty($duty->actualUser)) echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
          <table class='table table-data'>
            <tbody>
              <?php foreach($actualUser as $user => $phone):?>
              <tr>
                <th class='w-90px' style="color: #3c4353"><?php echo zget($users, $user, '');?></th>
                <td><?php echo $phone;?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->duty->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($duty->desc) ? $duty->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=duty&objectID=$duty->id");?>
    </div>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack(inlink('browse'));?>
        <div class='divider'></div>
        <?php
          common::printIcon('duty', 'edit', "dutyID=$duty->id", $duty, 'button', '', '', "class='btn showinonlybody'");
          common::printIcon('duty', 'delete', "dutyID=$duty->id", $duty, 'button', 'trash', 'hiddenwin');
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->duty->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-90px'><?php echo $lang->duty->application;?></th>
                <td title='<?php echo zget($appList, $duty->application, '');?>'><?php echo zget($appList, $duty->application, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->duty->type;?></th>
                <td><?php echo zget($lang->duty->typeList, $duty->type, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->duty->importantTime;?></th>
                <td><?php echo zget($lang->duty->importantTimeList, $duty->importantTime, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->duty->planDate;?></th>
                <td><?php echo $duty->planDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->duty->actualDate;?></th>
                <td><?php echo $duty->actualDate;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->duty->createdBy;?></th>
                <td><?php echo zget($users, $duty->createdBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->duty->createdDate;?></th>
                <td><?php echo $duty->createdDate;?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
