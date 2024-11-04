<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>#tableCustomBtn+.dropdown-menu > li:last-child{display: none}</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php 
    $i = 0;
    foreach($labelList as $label => $labelName)
    {
        if(empty($label)) continue;
        $active = $browseType == $label ? 'btn-active-text' : '';
        echo html::a($this->createLink('demandcollection', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
        $i++;
        if($i >= 11) break;
    }
    if($i >= 11)
    {
        echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
        echo "<ul class='dropdown-menu'>";
        $i = 0;
        foreach($labelList as $label => $labelName)
        {
            $i++;
            if($i <= 11) continue;

            $active = $browseType == $label ? 'btn-active-text' : '';
            echo '<li>' . html::a($this->createLink('demandcollection', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'"). '</li>';
        }
        echo '</ul></div>';
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
      <?php
      $class = common::hasPriv('demandcollection', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('demandcollection', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";    
      $link  = common::hasPriv('demandcollection', 'export') ? $this->createLink('demandcollection', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->demandcollection->export, '', $misc) . "</li>";
      ?>
      </ul>
    </div>
    <?php if(common::hasPriv('demandcollection', 'create')) echo html::a($this->createLink('demandcollection', 'create'), "<i class='icon-plus'></i> {$lang->demandcollection->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='demandcollection'></div>
    <?php if(empty($demandcollections)):?>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
    </div>
    <?php else:?>
    <form class='main-table' id = 'demancollectionFrom' method='post' >
      <?php 
      $vars = "browseType=$browseType&param=0&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; 
      include '../../common/view/datatable.html.php';
      
      $setting = $this->datatable->getSetting('demandcollection');
      $widths  = $this->datatable->setFixedFieldWidth($setting);
      ?>
      <table class='table has-sort-head datatable' id='demandcollectionList'  data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>'>
        <thead>
          <tr>
            <?php
              foreach($setting as $value)
              {
                  if($value->id == 'actions'){
                      $value->width = 180;
                  }
                  if($value->show)
                  {
                      $this->datatable->printHead($value, $orderBy, $vars, false);
                  }
              }
            ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($demandcollections as $demand):?>
          <tr >
            <?php foreach($setting as $value) $this->demandcollection->printCell($value, $demand,$depts, $users, $plans);?>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class="table-footer">
          <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
              <label><?php echo $lang->demandcollection->checkAll?></label>
          </div>
          <div class="table-actions btn-toolbar">
              <?php echo html::commonButton($lang->demandcollection->batchSyncKanban, "class='btn btn-default' onclick='batchSyncKanban()'");?>
          </div>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<script>
    var batchAddModalTrigger = new $.zui.ModalTrigger(
        {
            width: '660px',
            // height:'420px',
            type: 'iframe',
            waittime: 3000
        });
    function batchSyncKanban() {
        var url = '<?php echo $this->createLink('demandcollection', 'selectspace', 'id=IDSTR', '', true);?>';
        var ids = ""
        $("[name='idList[]']").each(function (i) {
            if ($(this).is(":checked")) {
                ids += $(this).val() + ',';
            }
        });
        batchAddModalTrigger.show({url: url.replace('IDSTR', ids)})
    }

    //已同步过看板，不再弹窗直接修改
    function updateKanbancard(id) {
        var url = '<?php echo $this->createLink('kanban', 'ajaxeditCardBydemand');?>';
        $.post(url,{id:id,fromType:'demandcollection'},function (res) {
            var myMessage = new $.zui.Messager(res.message,{
                type:      'success',
                icon:      'bell',
                placement: 'center',
                time:       3000
            })
            myMessage.show();
        },'json')
    }

  

$(function(){$('#demancollectionFrom').table();})
</script>
<?php include '../../common/view/footer.html.php';?>
