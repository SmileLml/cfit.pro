<?php include '../../../common/view/header.html.php'?>
<?php js::set('mode', $mode);?>
<?php js::set('total', $pager->recTotal);?>
<?php js::set('rawMethod', $app->rawMethod);?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->my->myReviewList as $key => $type):?>
    <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
    <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
    <?php endforeach;?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='datamanagement'></div>
    <?php if(empty($reviewList)):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted"><?php echo $lang->noData;?></span>
        </p>
      </div>
    <?php else:?>
      <form class='main-table' method='post' id='myReviewForm'>
          <div class="table-header fixed-right">
              <nav class="btn-toolbar pull-right"></nav>
          </div>
          <?php
          $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
          ?>
        <table class='table has-sort-head table-fixed' id='datamanagement'>
          <thead>
          <tr>
              <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->datamanagement->code); ?></th>
              <th class='w-60px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->datamanagement->type); ?></th>
              <th class='w-80px'><?php common::printOrderLink('source', $orderBy, $vars, $lang->datamanagement->source); ?></th>
              <th class='w-60px'><?php common::printOrderLink('desensitizeType', $orderBy, $vars, $lang->datamanagement->desensitizeType); ?></th>
              <th class='w-100px'><?php common::printOrderLink('useDeadline', $orderBy, $vars, $lang->datamanagement->useDeadline); ?></th>
              <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->datamanagement->createdBy); ?></th>
              <th class='w-120px'><?php common::printOrderLink('infoCode', $orderBy, $vars, $lang->datamanagement->infoCode); ?></th>
              <th class='w-80px'><?php common::printOrderLink('desc', $orderBy, $vars, $lang->datamanagement->desc); ?></th>
              <th class='w-120px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->datamanagement->createdDate); ?></th>
              <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->datamanagement->status); ?></th>
              <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->datamanagement->dealUser); ?></th>
              <th class='text-center w-120px'><?php echo $lang->actions; ?></th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($reviewList as $data): ?>
              <?php $data->desc = htmlspecialchars_decode($data->desc);    //处理【数据获取摘要】中的图片和表格等
              $data->desc = str_replace("&nbsp;","",$data->desc);//将空格替换成空
              $data->desc = strip_tags($data->desc);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
              $data->desc = mb_substr($data->desc, 0, 100,"utf-8");?>
              <tr>
                  <td class='text-ellipsis' title="<?php echo $data->code;?>"><?php echo common::hasPriv('datamanagement', 'view') ? html::a($this->createLink('datamanagement', 'view', "datamanagementId=$data->id"), $data->code) : $data->code;?></td>
                  <td class='text-ellipsis' title="<?php echo zget($lang->datamanagement->typeList,$data->type) ?>"><?php echo zget($lang->datamanagement->typeList,$data->type);?></td>
                  <td class='text-ellipsis' title="<?php echo zget($lang->datamanagement->sourceList,$data->source) ?>"><?php echo zget($lang->datamanagement->sourceList,$data->source);?></td>
                  <td class='text-ellipsis' title="<?php echo zget($lang->datamanagement->desensitizeTypeList,$data->desensitizeType) ?>"><?php echo zget($lang->datamanagement->desensitizeTypeList,$data->desensitizeType);?></td>
                  <td class='text-ellipsis' title="<?php echo $data->useDeadline ?>"><?php
                      if($data->isDeadline == $lang->datamanagement->longTermUseFlag){
                          echo $lang->datamanagement->longTerm;
                      }else{
                          echo substr($data->useDeadline,0, 10);
                      } ?></td>
                  <td class='text-ellipsis' title="<?php echo zget($users,$data->createdBy) ?>"><?php echo zget($users,$data->createdBy);?></td>
                  <td class='text-ellipsis' title="<?php echo $data->infoCode ?>">
                      <?php echo html::a($this->createLink($data->source, 'view', 'id=' . $data->infoId, '', true), $data->infoCode, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") ?>
                  </td>
                  <td class='text-ellipsis' title="<?php echo $data->desc ?>"><?php echo $data->desc; ?></td>
                  <td class='text-ellipsis' title="<?php echo $data->createdDate ?>"><?php echo $data->createdDate;?></td>
                  <td class='text-ellipsis' title="<?php echo zget($lang->datamanagement->statusList,$data->status) ?>"><?php echo zget($lang->datamanagement->statusList,$data->status);?></td>
                  <?php
                  $dealUserTitle = '';
                  $dealUsersTitles = '';
                  if (!empty($data->dealUser)) {
                      foreach (explode(',', $data->dealUser) as $dealUser) {
                          if (!empty($dealUser)) $dealUserTitle .= zget($users, $dealUser, $dealUser) . ',';
                      }
                  }
                  $dealUsersTitles = trim($dealUserTitle, ',');
                  ?>
                  <td title='<?php echo $dealUsersTitles; ?>' class='text-ellipsis'>
                      <?php echo $dealUsersTitles; ?>
                  </td>
                  <td class='c-actions text-center' style="overflow:visible">
                      <?php
                      common::printIcon('datamanagement', 'readmessage', "datamanagementID=$data->id", $data, 'list', 'bullhorn','','iframe', true);
                      common::printIcon('datamanagement', 'delay', "datamanagementID=$data->id", $data, 'list', 'time','','iframe', true);
                      common::printIcon('datamanagement', 'review', "datamanagementID=$data->id&changeVersion=$data->changeVersion&reviewStage=$data->reviewStage", $data, 'list', 'glasses', '', 'iframe', true);
                      common::printIcon('datamanagement', 'destroyexecution', "datamanagementID=$data->id", $data, 'list', 'play','','iframe', true);
                      common::printIcon('datamanagement', 'destroy', "datamanagementId=$data->id", $data, 'list', 'close','','iframe', true);
                      ?>
                  </td>
              </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <div class="table-footer">
        </div>
      </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
