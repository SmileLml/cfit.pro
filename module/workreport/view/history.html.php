<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $goback = inlink('browse'); ?>
            <?php echo html::a($goback, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
            <div class="page-title">
                <span class="text"><?php echo$lang->workreport->history ?></span>

            </div>
        <?php endif; ?>
    </div>
  <div class="btn-toolbar pull-right">
   <div class='w-300px' style="display: flex;float: left;">
            <?php echo html::input('date', $begin, "class='form-control form-date' ;' placeholder='{$lang->workreport->begin}' onchange = checkBegin() readonly");?>
            <span class='input-group-addon' style="border-radius: 0px;display: block;text-align: center;width: 40px;line-height: 20px;"><?php echo $lang->workreport->to;?></span>
            <?php
            echo html::input('enddate', $end, "class='form-control form-date' ;'  placeholder='{$lang->workreport->end}' onchange = checkDate() readonly");
            ?>
    </div>

  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>


    <form class='main-table' id='workreportForm' method='post' >

      <table class='table table-fixed  table-bordered ' id='secondorders'>
        <thead>
          <tr>
            <th class=' w-120px text-center'><?php echo $lang->workreport->weeklyNum;?></th>
              <th  class='w-300px text-center'><?php echo $this->lang->workreport->projectSpace ;?></th>
              <th class='w-300px text-center'><?php echo $this->lang->workreport->stage ;?></th>
              <th class=' w-200px text-center'><?php echo $this->lang->workreport->object ;?></th>
               <th  class='w-60px text-center'><?php echo $this->lang->workreport->consumed ;?></th>
              <th  class='w-70px text-center'><?php echo $this->lang->workreport->workType ;?></th>
              <th  class='w-200px text-center'><?php echo $this->lang->workreport->workContent ;?></th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($history as $item):   ?>
          <tr>
              <td  class='text-ellipsis' ><?php echo $item->date;?></td>
              <td  class='text-ellipsis' title="<?php echo $item->project;?>"><?php echo $item->project;?></td>
              <td  class='text-ellipsis' title="<?php echo $item->execution;?>"><?php echo $item->execution;?></td>
              <td  class='text-ellipsis' title="<?php echo $item->taskName;?>"><?php echo $item->taskName;?></td>
              <td  class='text-ellipsis' title="<?php echo $item->consumed;?>"><?php echo $item->consumed;?></td>
              <td  class='text-ellipsis' title="<?php echo zget($this->lang->task->typeList,$item->type);?>"><?php echo zget($this->lang->workreport->typeList,$item->type);?></td>
              <td  class='text-ellipsis' title="<?php echo $item->work;?>"><?php echo $item->work;?></td>
          </tr>
         <?php endforeach;;?>
        </tbody>
      </table>
      <div class="table-footer">
      <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
  </div>
</div>
<?php
js::set('param',$param);
js::set('recTotal',$pager->recTotal);
js::set('recPerPage',$pager->recPerPage);
js::set('pageID',$pager->pageID);
?>
<script>
    $(function() {
        $(".form-date").datetimepicker('setStartDate', '<?php echo date('Y-m-d',strtotime('2023-01-01'))?>');
        $(".form-date").datetimepicker('setEndDate', '<?php echo date('Y-m-d',strtotime('2023-09-08'))?>');
    })
</script>
<?php include '../../common/view/footer.html.php';?>
