<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $i = 0;
    foreach($lang->secondorder->labelList as $label => $labelName)
    {
        $active = $browseType == $label ? 'btn-active-text' : ''; 
        echo html::a($this->createLink('secondorder', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

        $i++;
        if($i >= 13) break;
    }
    if($i>=14)
    {
        echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
        echo "<ul class='dropdown-menu'>";
        $i = 0;
        foreach($lang->secondorder->labelList as $label => $labelName)
        {
            $i++;
            if($i <= 13) continue;
            $active = $browseType == $label ? 'btn-active-text' : ''; 
            echo '<li>' . html::a($this->createLink('secondorder', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
        }
        echo '</ul></div>';
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
        <?php $this->app->loadLang('progress'); ?>
        <?php if (common::hasPriv('secondorder', 'importByQA')) echo html::a($this->createLink('secondorder', 'importByQA', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->progress->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
        <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
      <?php
      $class = common::hasPriv('secondorder', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('secondorder', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";    
      $link  = common::hasPriv('secondorder', 'export') ? $this->createLink('secondorder', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->secondorder->export, '', $misc) . "</li>";
      ?>
      </ul>
    </div>
    <?php if(common::hasPriv('secondorder', 'create')) echo html::a($this->createLink('secondorder', 'create'), "<i class='icon-plus'></i> {$lang->secondorder->create}", '', "class='btn btn-primary'");?>
  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='secondorder'></div>
    <?php if(empty($secondorders)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='secondorderForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head' id='secondorders'>
        <thead>
          <tr>
            <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->secondorder->code);?></th>
            <th class='w-150px'><?php common::printOrderLink('summary', $orderBy, $vars, $lang->secondorder->summary);?></th>
            <th class='w-60px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->secondorder->type);?></th>
            <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->secondorder->app);?></th>
            <th class='w-60px'><?php common::printOrderLink('source', $orderBy, $vars, $lang->secondorder->source);?></th>
            <th class='w-80px'><?php common::printOrderLink('team', $orderBy, $vars, $lang->secondorder->team);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->secondorder->createdUser);?></th>
            <th class='w-80px'><?php common::printOrderLink('exceptDoneDate', $orderBy, $vars, $lang->secondorder->exceptDoneDate);?></th>
            <th class='w-60px'><?php common::printOrderLink('ifAccept', $orderBy, $vars, $lang->secondorder->ifAccept);?></th>
            <th class='w-50px'><?php common::printOrderLink('acceptDept', $orderBy, $vars, $lang->secondorder->acceptDept);?></th>
            <th class='w-50px'><?php common::printOrderLink('acceptUser', $orderBy, $vars, $lang->secondorder->acceptUser);?></th>
            <th class='w-50px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->secondorder->status);?></th>
            <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->secondorder->dealUser);?></th>
            <th class='text-center w-150px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($secondorders as $secondorder):?>
          <tr>
            <td title="<?php echo $secondorder->code;?>" class='text-ellipsis'><?php echo common::hasPriv('secondorder', 'view') ? html::a(inlink('view', "secondorderID=$secondorder->id"), $secondorder->code) : $secondorder->code;?></td>
            <td title="<?php echo $secondorder->summary;?>" class='text-ellipsis'><?php echo $secondorder->summary;?></td>
            <td><?php echo zget($lang->secondorder->typeList, $secondorder->type);?></td>
            <td title="<?php echo zget($apps,$secondorder->app);?>" class='text-ellipsis'><?php echo zget($apps,$secondorder->app);?></td>
            <td><?php echo zget($lang->secondorder->sourceList, $secondorder->source);?></td>
            <td><?php echo zget($lang->application->teamList, $secondorder->team);?></td>
            <td><?php echo zget($users, $secondorder->createdBy); ?></td>
            <td><?php echo $secondorder->exceptDoneDate  != '0000-00-00' ? $secondorder->exceptDoneDate : '';;?></td>
            <td><?php
                if(!empty($secondorder->ifAccept) || $secondorder->ifAccept === '0'){
                    echo zget($lang->secondorder->ifAcceptList, $secondorder->ifAccept, '');
                }elseif (!empty($secondorder->ifReceived)){
                    echo zget($lang->secondorder->ifReceivedList, $secondorder->ifReceived, '');
                }else{
                    echo '';
                }
                ?></td>
            <td title="<?php echo zget($depts, $secondorder->acceptDept);?>" class='text-ellipsis'><?php echo zget($depts, $secondorder->acceptDept, '');?></td>
            <td title="<?php echo zget($users, $secondorder->acceptUser);?>" class='text-ellipsis'><?php echo zget($users, $secondorder->acceptUser, '');?></td>
            <td title="<?php echo zget($lang->secondorder->statusList, $secondorder->status);?> ">
                <?php echo zget($lang->secondorder->statusList, $secondorder->status);?>
            </td>
<!--            <td title="--><?php //echo zget($users, $secondorder->dealUser);?><!--" class='text-ellipsis'>--><?php //echo zget($users, $secondorder->dealUser, '');?><!--</td>-->
            <td title="<?php echo zmget($users, $secondorder->dealUser);?>" class='text-ellipsis'><?php echo $this->loadModel('secondorder')->printAssignedHtml($secondorder, $users);?></td>
            <td class='c-actions text-center'>
              <?php
              $closeflag = $this->loadModel('secondorder')->isClickable($secondorder, 'close');
              common::printIcon('secondorder', 'edit', "secondorderID=$secondorder->id", $secondorder, 'list');
              common::printIcon('secondorder', 'confirmed', "secondorderID=$secondorder->id", $secondorder, 'list', 'checked', '', 'iframe', true);
              common::printIcon('secondorder', 'deal', "secondorderID=$secondorder->id", $secondorder, 'list', 'time', '', 'iframe', true);
              common::printIcon('secondorder', 'returned', "secondorderID=$secondorder->id", $secondorder, 'list', 'back', '', 'iframe', true);
              common::printIcon('secondorder', 'copy', "secondorderID=$secondorder->id", $secondorder, 'list');
              if(common::hasPriv('secondorder', 'close'))
              {
                  if($closeflag)
                  {
                      echo "<a  href='javascript:;' onclick='closeCheck(".$secondorder->finallyHandOver.",".$secondorder->id.")' class='btn ' title='{$this->lang->secondorder->close}'><i class='icon-secondorder-close icon-off'></i></a>";
                  }
                  else
                  {
                      common::printIcon('secondorder', 'close', "secondorderID=$secondorder->id", $secondorder, 'list','off', '', 'iframe ', true," disabled");

                  }
              }

/*              common::printIcon('secondorder', 'close', "secondorderID=$secondorder->id", $secondorder, 'list','off', '', 'iframe ', true,"id='closed$secondorder->id' display ='none' href='#'");*/
              common::printIcon('secondorder', 'delete', "secondorderID=$secondorder->id", $secondorder, 'list', 'trash', '', 'iframe', true);
              ?>
            <a  data-app="secondorder" href="<?php echo $this->createLink('secondorder','close',"secondorderID=$secondorder->id").'?onlybody=yes';?>" id="closed<?php echo $secondorder->id?>"   class="btn iframe hidden " ></a>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class="table-footer">
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<!--<style>-->
<!--    .modal-header{width:32px;height:32px;position: absolute !important;top: 12px;right: 10px;font-size: 32px;color: #838a9d;text-shadow: 0 1px 0 rgb(255 255 255 / 85%);cursor: pointer}-->
<!--    .modal-header:hover{color:#3c4353}-->
<!--    .close{display:none}]-->
<!--    .modal-header:before{content:"#"}-->
<!--    .modal-header:after{content:"×"}-->
<!--</style>-->
<script>
    $("body").delegate("#triggerModal",'hide.zui.modal',function(){
        var isres = $('#iframe-triggerModal').contents().find("#reload").hasClass("reload");
        if(isres){
            parent.location.reload();
        }
    })
    //关闭时检查工单是否最终移交
    function closeCheck(flag,id){
      if(flag == '2'){
          alert('工单没有完成全部移交，不能关闭！');
          return true;
      }else{
         $('#closed'+id).click();
      }
    }
</script>
<?php include '../../common/view/footer.html.php';?>
