<?php include '../../../common/view/header.html.php'?>
<?php js::set('mode', $mode);?>
<?php js::set('rawMethod', $app->rawMethod);?>
<?php js::set('browseType', $browseType);?>
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
    <?php if(empty($showDatas)):?>
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
      <table class='table has-sort-head' id='auditList'>
        <thead>
          <tr>
            <?php foreach($showFields as $field):?>
            <?php if($field->field == 'actions'):?>
            <th><?php echo $lang->actions;?></th>
            <?php else:?>
            <th><?php echo zget($titleList, $field->field, $field->field);?></th>
            <?php endif;?>
            <?php endforeach;?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($showDatas as $data):?>
          <tr>
          <?php foreach($showFields as $field):?>
          <?php $filedKey = $field->field;?>
          <?php if($filedKey == 'actions'):?>
          <?php continue;?>
          <?php elseif($field->field == $flowList[$browseType]['flowView']):?>
          <?php $flowApp = $flowList[$browseType]['flowApp'];?>
            <td><?php echo html::a($this->createLink($browseType, 'view', array('id' => $data->{$filedKey})), $data->{$filedKey}, '', $flowApp);?></td>
          <?php else:?>
            <!--处理特殊的多选字段-->
            <?php
            $printValue   = $data->{$filedKey};
            $filedControl = zget($controlList, $filedKey, '');
            if(in_array($filedControl, $queryControl))
            {
                $printValue  = '';
                $filedOption = zget($optionList, $filedKey, '');
                $fieldValue  = explode(',', $data->{$filedKey});
                foreach($fieldValue as $value)
                {
                    $printValue .= zget($filedOption, $value, $value) . '&nbsp;';
                }
            }
            ?>
            <td class='text-ellipsis'><?php echo $printValue;?></td>
          <?php endif;?>
          <?php endforeach;?>
          <td>
              <?php echo html::a($this->createLink($browseType, 'view', array('id' => $data->id)), $lang->my->see, '', $flowApp);?>
              <?php if($browseType == 'support'):?>
                  <?php echo html::a($this->createLink($browseType, 'assign', array('id' => $data->id)), $lang->my->confirm, '', 'data-toggle="modal"');?>
              <?php endif;?>
          </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class='table-footer'></div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
