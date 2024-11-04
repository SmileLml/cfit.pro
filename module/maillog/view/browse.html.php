<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->search;?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = "class=disabled";
                $misc  =  "class=disabled";
                $link  =  '#';
                if(common::hasPriv('maillog', 'export')){
                    $class = "";
                    $misc =  "data-toggle='modal' data-type='iframe' class='export'" ;
                    $link =  $this->createLink('maillog', 'export', "orderBy=$orderBy&browseType=$browseType");
                }
                echo "<li $class>" . html::a($link, $lang->maillog->export, '', $misc) . "</li>";
                ?>
            </ul>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module="maillog"></div>
    <form class="main-table" data-ride="table" method="post" id="maillogForm">
      <table class="table has-sort-head">
        <thead>
          <tr>
            <th class='w-10px'><?php echo $lang->maillog->id;?></th>
            <th class='w-100px'><?php echo $lang->maillog->title;?></th>
            <th class='w-60px'><?php echo $lang->maillog->objectType;?></th>
            <th class='w-60px'><?php echo $lang->maillog->createdBy;?></th>
            <th class='w-60px'><?php echo $lang->maillog->toList;?></th>
            <th class='w-40px'><?php echo $lang->maillog->ccList;?></th>
            <th class='w-40px'><?php echo $lang->maillog->status;?></th>
            <th class='w-30px'><?php echo $lang->maillog->content;?></th>
            <th class='w-30px'><?php echo $lang->maillog->userinfo;?></th>
            <th class='w-50px'><?php echo $lang->maillog->createdDate;?></th>
            <th class='w-40px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($logList as $log):?>
          <tr>
            <td><?php echo $log->id;?></td>
            <td class="text-ellipsis" title="<?php echo $log->title;?>"><?php echo $log->title;?></td>
            <td><?php echo zget($lang->maillog->objectTypeList, $log->objectType, $log->objectType);?></td>
            <td><?php echo zget($users, $log->createdBy,'');?></td>
            <td>
                <?php if ($log->toList != ''){
                    $tolist = [];
                    foreach (explode(',',$log->toList) as $item) {
                        if ($item != '') $tolist[] = zget($users,$item,'');
                    }
                    echo implode(',',$tolist);
                }?>
            </td>
            <td>
                <?php if ($log->ccList != ''){
                    $cclist = [];
                    foreach (explode(',',$log->ccList) as $item) {
                        if ($item != '') $cclist[] = zget($users,$item,'');
                    }
                    echo implode(',',$cclist);
                }?>
            </td>
            <td><?php echo zget($lang->maillog->statusList, $log->status, $log->status);?></td>
            <td><?php echo html::commonButton($lang->maillog->details, 'data-type="ajax" data-title="' . $lang->maillog->content . '" data-remote="' . $this->createLink('maillog', 'ajaxGetContent', 'id=' . $log->id) . '" data-toggle="modal"', 'btn btn-mini btn-primary triggerButton');?></td>
            <td><?php echo html::commonButton($lang->maillog->details, 'data-type="ajax" data-title="' . $lang->maillog->userinfo . '" data-remote="' . $this->createLink('maillog', 'ajaxGetUserInfo', 'id=' . $log->id) . '" data-toggle="modal"', 'btn btn-mini btn-primary triggerButton');?></td>
            <td><?php echo $log->createdDate;?></td>
            <td>
                <?php
                    if ($log->status == 2){
                        echo html::commonButton($lang->maillog->errorinfo, 'data-type="ajax" data-title="' . $lang->maillog->errorinfo . '" data-remote="' . $this->createLink('maillog', 'ajaxGetError', 'id=' . $log->id) . '" data-toggle="modal"', 'btn btn-mini btn-primary triggerButton');
                    }else{
                        echo $lang->noData;
                    }
                ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </form>
    <div class='table-footer'><?php echo $pager->show('right', 'pagerjs');?></div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
