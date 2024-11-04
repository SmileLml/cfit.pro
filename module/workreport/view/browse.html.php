<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
      <?php
      $i = 0;
      foreach ($lang->workreport->labelList as $label => $labelName) {
          $active = $browseType == $label ? 'btn-active-text' : '';
          echo   html::a($this->createLink('workreport', 'browse', "browseType=$label&param=0&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
          $i++;
          if ($i >= 1) break;
      }
      $i = count($allYear);
      if ($i >= 0) {
          echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->workreport->year}<span class='caret'></span></a>";
          echo "<ul class='dropdown-menu'>";
          $i = 0;
          foreach ($allYear as $label => $labelName) {
              $i++;
              $active = ($browseType == 'all' && $labelName->year == date('Y')) || $browseType == $labelName->year ? 'btn-active-text' : '';
              echo '<li>' . html::a($this->createLink('workreport', 'browse', "browseType={$labelName->year}&param=0&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName->year}</span>", '', "class='btn btn-link $active'") . '</li>';
          }
          echo '</ul></div>';
      }
      ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    <a class="btn btn-link" > <?php $act = $browseType == 'history' ? 'btn-active-text' : ''; echo   html::a($this->createLink('workreport', 'history', "param=0&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $lang->workreport->history . '</span>', '', "class='btn btn-link $act'  ");
          ;?></a>
  
  </div>
  <div class="btn-toolbar pull-right">
   <div class='w-300px' style="display: flex;float: left;">
            <?php echo html::input('date', $begin, "class='form-control form-date' ;' placeholder='{$lang->workreport->begin}' onchange = checkBegin() readonly");?>
            <span class='input-group-addon' style="border-radius: 0px;display: block;text-align: center;width: 40px;line-height: 20px;"><?php echo $lang->workreport->to;?></span>
            <?php
            echo html::input('enddate', $end, "class='form-control form-date' ;'  placeholder='{$lang->workreport->end}' onchange = checkDate() readonly");
            ?>
    </div>
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
        <ul class="dropdown-menu" id='exportActionMenu'>
            <?php
            $class = common::hasPriv('workreport', 'export') ? '' : "class=disabled";
            $misc = common::hasPriv('workreport', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
            $link = common::hasPriv('workreport', 'export') ? $this->createLink('workreport', 'export', "browseType=$browseType") : '#';
            echo "<li $class>" . html::a($link, $lang->workreport->export, '', $misc) . "</li>";

            ?>
        </ul>
    </div>
<!--      --><?php /*if(common::hasPriv('workreport', 'create')) echo html::a($this->createLink('workreport', 'create'), "<i class='icon-plus'></i> {$lang->workreport->create}", '', "class='btn btn-primary iframe' data-app='my' data-width='90%'");*/?>
      <?php  common::hasPriv('workreport','create') ? common::printIcon('workreport', 'create', "", $workReport, 'button', 'plus', '', ' btn-primary  iframe', true,'data-app="my" data-width="100%" id="workreport"  style="background-color:#0c64eb;color:#fff"') : '';?>
      <?php common::hasPriv('workreport','supplementParent') ? common::printIcon('workreport', 'supplementParent', "", $workReport, 'button', 'plus', '', 'iframe', true,'data-app="my" data-width="100%" id="workreport"  style="background-color:#0c64eb;color:#fff"') : '';?>

  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='workreport'></div>

    <form class='main-table' id='workreportForm' method='post' >
<!--      --><?php /*$vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";*/?>
      <table>
          <tr>
              <?php if($browseType != 'bysearch' && empty($workreportYear) && $browseType != 'date') :?>
                  <th class='w-120px '><?php echo $lang->workreport->mondthTotal;?></th>
                  <td class='w-120px  text-center'>
                      <?php
                      $total = 0 ;/*foreach (array_column($workReport,'total') as $item) {
                          $total +=  array_sum($item);
                      };*/
                      $total = $workReport['total'];
                      echo sprintf($this->lang->workreport->totalDesc,  $total);?>
                  </td>
              <?php else :?>
                  <th class='w-120px '><?php echo  (!empty($workreportYear)  && $browseType != 'bysearch') ? ($browseType == date('Y') ? $lang->workreport->yearTotal : $browseType.$lang->workreport->yearTotalOne) : $lang->workreport->total;?></th>
                  <td class='w-120px  text-center'>
                      <?php echo sprintf($this->lang->workreport->totalDesc, $workReport['total']);?>
                  </td>
              <?php endif;?>
              <th></th>
          </tr>
      </table>
      <table class='table table-fixed  table-bordered ' id='secondorders'>
        <thead>
          <tr>
            <th class=' w-90px text-center'><?php echo $lang->workreport->weeklyNum;?></th>
             <th class='w-70px text-center'><?php echo $lang->workreport->week;?></th>
            <th class='w-60px text-center'><?php echo $lang->workreport->effortSum ;?></th>
          <!--<th ><?php /*echo $lang->workreport->workReportInfo ;*/?></th>-->
              <th  class='w-300px text-center'><?php echo $this->lang->workreport->projectSpace ;?></th>
              <th class='w-140px text-center'><?php echo $this->lang->workreport->activity ;?></th>
              <th class='w-200px text-center'><?php echo $this->lang->workreport->stage ;?></th>
              <th class='text-center'><?php echo $this->lang->workreport->object ;?></th>
          <!--<th class='w-100px text-center'><?php /* echo $this->lang->workreport->beginDate ;*/?></th>-->
              <th  class='w-60px text-center'><?php echo $this->lang->workreport->consumed ;?></th>
              <th  class='w-110px text-center'><?php echo $this->lang->workreport->workType ;?></th>
              <th  class='w-50px text-center'><?php echo $this->lang->workreport->append ;?></th>
              <th class='text-center w-150px' ><?php echo $lang->actions;?></th>

          </tr>
        </thead>
        <tbody>
          <?php unset($workReport['total']);foreach($workReport as $weekly): ?>
          <?php if(!$weekly['effort']) continue;?>
          <tr>
            <td  class=' '><?php echo $weekly['weekNum'];?></td>
            <td ><?php echo  $this->lang->todo->dayNames[date('w',strtotime($weekly['weekNum']))] ;?></td>
            <td <?php if(helper::isWorkDay($weekly['weekNum']) && array_sum(array_column($weekly['effort'],'consumed')) < 8) echo "style='color:red'" ?> class='text-center'><?php echo array_sum(array_column($weekly['effort'],'consumed'));?></td>
            <td   class='text-ellipsis'>
                <?php foreach($weekly['effort'] as $week):?>
                        <ul class='text-ellipsis' title="<?php echo zget($projects,$week->project) ;?>"> <?php echo zget($projects,$week->project) ;?></ul>
                <?php endforeach;?>
            </td>
              <td  >
                  <?php foreach($weekly['effort'] as $week):?>
                      <ul class='text-ellipsis' title="<?php echo zget($stages,$week->activity ) ;?>"><?php echo zget($stages,$week->activity );?> </ul>
                  <?php endforeach;?>
              </td>
              <td  >
                  <?php foreach($weekly['effort'] as $week):?>
                      <ul class='text-ellipsis' title="<?php echo  zget($apps,$week->apps) ;?>"><?php echo  zget($apps,$week->apps)  ;?> </ul>
                  <?php endforeach;?>
              </td>
              <td  >
                  <?php foreach($weekly['effort'] as $week):?>
                      <ul class='text-ellipsis' title="<?php echo zget($tasks,$week->objects) ;?>">
<!--                          --><?php /*echo common::hasPriv('task', 'view') && $week->objects ? html::a( $this->createLink('task','view',"taskID=$week->objects"),zget($tasks,$week->objects), '','style=color:darkblue') :  zget($tasks,$week->objects) */?>
                          <?php echo common::hasPriv('task', 'view') && $week->objects ?  html::a('javascript:void(0)',zget($tasks,$week->objects), '', 'data-app="project" style=color:darkblue onclick="seturl('.$week->project.','.$week->objects.')"') : zget($tasks,$week->objects)?>
                          <p class="hidden"><?php echo html::a('','','','data-app="project"  id="worktaskurl"')?></p>
                      </ul>

              <?php endforeach;?>
              </td>
              <!-- <td  >
                  <?php /*foreach($weekly['effort'] as $week):*/?>
                      <ul > <?php /*echo  $week->beginDate;*/?> </ul>
                  <?php /*endforeach;*/?>
              </td>-->
              <td  >
                  <?php foreach($weekly['effort'] as $week):?>
                      <ul class='text-ellipsis'> <?php echo  $week->consumed;?> </ul>
                  <?php endforeach;?>
              </td>
              <td  >
                  <?php foreach($weekly['effort'] as $week):?>
                      <ul class='text-ellipsis'> <?php echo  zget($this->lang->task->typeList,$week->workType);?> </ul>
                  <?php endforeach;?>
              </td>
              <td  >
                  <?php foreach($weekly['effort'] as $week):?>
                      <ul class='text-ellipsis'> <?php echo  zget($this->lang->workreport->appendList,$week->append,'');?> </ul>
                  <?php endforeach;?>
              </td>
              <td    class='c-actions text-center'>
                  <?php foreach($weekly['effort'] as $week):?>
                      <ul class='text-ellipsis'>
                          <?php
                             if($week->project){
                                 echo '<button type="button" class="disabled btn" title="' . $week->workContent . '"><i class="icon-common-file-text icon-file-text"></i><span class="text">&nbsp</span></button>';
                                 if($week->canEditAndDelete && strpos($week->taskName->name,'已') == false){
                                       common::hasPriv('workreport','edit') ? common::printIcon('workreport', 'edit', "workID=$week->id", $week, 'list','edit', '', 'iframe', true,'data-width="90%"') : '';
                                       common::hasPriv('workreport','delete') ? common::printIcon('workreport', 'delete', "workID=$week->id", $week, 'list', 'trash', '', 'iframe', true) : '';
                                  }else{
                                       echo '<button type="button" class="disabled btn" title="' . $lang->workreport->edit . '"><i class="icon-common-edit icon-edit"></i><span class="text">&nbsp' . $lang->workreport->edit .'</span></button>';
                                       if(strpos($week->taskName->name,'已') !== false){
                                           common::hasPriv('workreport','delete') ? common::printIcon('workreport', 'delete', "workID=$week->id", $week, 'list', 'trash', '', 'iframe', true) : '';
                                       }else{
                                           echo '<button type="button" class="disabled btn" title="' . $lang->workreport->delete . '"><i class="icon-common-delete icon-trash"></i><span class="text">&nbsp' . $lang->workreport->delete .'</span></button>';
                                       }                                  }
                                  if($this->app->user->account =='admin' || common::hasPriv('workreport','correct')){
                                        common::printIcon('workreport', 'correct', "workID=$week->id", $week, 'list','change', '', 'iframe', true,'data-width="90%"') ;
                                  }
                              }
                          ?>
                      </ul>
                  <?php endforeach;?>
              </td>
          </tr>
          <?php endforeach;?>
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
        $(".form-date").datetimepicker('setEndDate', '<?php echo date(DT_DATE1)?>');
    })
</script>
<?php include '../../common/view/footer.html.php';?>
