<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php js::set('status', $status);?>
<style>
.execution {display: flex;}
.execution .name {width: 85%; overflow: hidden;}
.execution .c-progress {width: 15%; padding-left: 5px;}
.expand, .more a {color: #0c60e1; font-size: 11px;}
.expand-demandinside, .more a {color: #0c60e1; font-size: 11px;}
.expand .icon, .more a .icon {font-size: 14px; transform: rotate( 90deg); display: inline-block;}
.expand-demandinside .icon, .more a .icon {font-size: 14px; transform: rotate( 90deg); display: inline-block;}
.pullback.icon {color: #0c60e1; font-size: 14px; transform: rotate( -90deg); display: inline-block; float: right; margin-top: 8px;}
.pullback-demandinside.icon {color: #0c60e1; font-size: 14px; transform: rotate( -90deg); display: inline-block; float: right; margin-top: 8px;}
.more {color: #0c60e1; font-size: 11px; text-align: center;}
.c-progress { display: inline-block; float: right; width: auto;}
#opinionName{width: 88%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block;}
.requirement-name{width: 88%;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;display: inline-block;}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a($this->createLink('requirement', 'matrix', "status=noclosed"), '<span class="text">' . $lang->requirementinside->noClosed . '</span>', '', "class='btn btn-link noclosedTab' data-app='platform'");?>
    <?php echo html::a($this->createLink('requirement', 'matrix', "status=closed"), '<span class="text">' . $lang->requirementinside->closed . '</span>', '', "class='btn btn-link closedTab' data-app='platform'");?>
    <div class='input-group input-group-sm' style='width: 400px' id='conditions'>
      <span class='input-group-addon'><?php echo $lang->requirementinside->beginAndEnd;?></span>
      <div class='datepicker-wrapper datepicker-date'><?php echo html::input('begin', $begin, "class='form-control form-date' style='padding-right:10px' onchange='changeParams(this)'");?></div>
      <span class='input-group-addon fix-border'><?php echo $lang->requirementinside->to;?></span>
      <div class='datepicker-wrapper datepicker-date'><?php echo html::input('end', $end, "class='form-control form-date' style='padding-right:10px' onchange='changeParams(this)'");?></div>
    </div>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
      <?php
      $class = common::hasPriv('requirement', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('requirement', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";    
      $link  = common::hasPriv('requirement', 'export') ? $this->createLink('requirement', 'export', "status=$status&begin=$begin_export&end=$end_export") : '#';
      echo "<li $class>" . html::a($link, $lang->requirementinside->export, '', $misc) . "</li>";
      ?>
      </ul>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class="main-col">
    <?php if(empty($opinions)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <div class='main-table' data-ride="table">
      <table class='table table-bordered' id="trackList">
        <thead>
          <tr class='text-center'>
            <th><?php echo $lang->opinion->common;?></th>
            <th><?php echo $lang->requirementinside->common;?></th>
            <th><?php echo $lang->demandinside->common;?></th>
            <th><?php echo $lang->application->common;?></th>
            <th class='w-120px'><?php echo $lang->product->line;?></th>
            <th><?php echo $lang->requirementinside->productCommon;?></th>
            <th><?php echo $lang->product->code;?></th>
            <th><?php echo $lang->requirementinside->projectCommon;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($opinions as $opinion):?>
          <tr>
            <td id="o-<?php echo $opinion->id;?>" title="<?php echo $opinion->name;?>" rowspan="1" class='opinion'>
                <?php echo html::a($this->createLink('opinion', 'view', "opinionID=$opinion->id"), $opinion->name, '', 'id="opinionName"');?>
                <i class="pullback icon icon-angle-double-right hidden"></i>
            </td>
            <td class="expand h-<?php echo $opinion->id;?>" id="<?php echo $opinion->id;?>" count="<?php echo count($opinion->children);?>">
              <?php if(isset($opinion->children)):?>
                <?php echo sprintf($this->lang->requirement->childrenCount, count($opinion->children));?>
                <a class="task-toggle collapsed"><i class="icon icon-angle-double-right"></i></a>
              <?php endif;?>
            </td>
            <td class="h-<?php echo $opinion->id;?>"></td>
            <td class="h-<?php echo $opinion->id;?>"></td>
            <td class="h-<?php echo $opinion->id;?>"></td>
            <td class="h-<?php echo $opinion->id;?>"></td>
            <td class="h-<?php echo $opinion->id;?>"></td>
            <td class="h-<?php echo $opinion->id;?>"></td>
          </tr>
            <?php if(isset($opinion->children) and count($opinion->children) != 0):?>
            <?php foreach($opinion->children as $requirement):?>
            <tr class="hidden r-<?php echo $opinion->id;?>">
              <td id="r-<?php echo $requirement->id;?>" oid="<?php echo $opinion->id;?>" count="<?php echo count($requirement->children);?>" class="text-ellipsis" title="<?php echo $requirement->name;?>">
                <?php echo html::a($this->createLink('requirement', 'view', "opinionID=$requirement->id"), $requirement->name, '','class="requirement-name"');?>
                <i class="pullback-demandinside icon icon-angle-double-right hidden"></i>
              </td>
              <td class="expand-demandinside hd-<?php echo $requirement->id;?>" oid="<?php echo $opinion->id;?>" r-id="<?php echo $requirement->id;?>" >
                <?php if(!empty($requirement->children)):?>
                  <?php echo sprintf($this->lang->requirement->demandinsideCount, count($requirement->children));?>
                  <a class="task-toggle collapsed"><i class="icon icon-angle-double-right"></i></a>
                <?php endif;?>
              </td>
              <td class="text-ellipsis hd-<?php echo $requirement->id;?>">
              <?php
              $appList = explode(',', str_replace(' ', '', $requirement->app));
              foreach($appList as $appID) 
              {   
                  if($appID) echo '<div class="text-ellipsis" title="' . zget($apps, $appID, '') . '">' . html::a($this->createLink('application', 'view', "appID=$appID"), zget($apps, $appID)). '</div>';
              }
              ?>
              </td>
              <td class="hd-<?php echo $requirement->id;?>">
              <?php
              $lineList = explode(',', str_replace(' ', '', $requirement->line)); 
              foreach($lineList as $lineID) 
              {   
                  if($lineID) echo '<div class="text-ellipsis" title="' . zget($lines, $lineID, '') . '">' . zget($lines, $lineID, '') . '</div>';
              }
              ?>
              </td>
              <td class="hd-<?php echo $requirement->id;?>">
              <?php 
              $linkedProducts = explode(',', str_replace(' ', '', $requirement->product));
              $i = 0;
              foreach($linkedProducts as $productID)
              {
                  if($i == 2) echo '<div class="more">More<a class="task-toggle collapsed"><i class="icon icon-angle-double-right"></i></a></div>';

                  $name = zget($products, $productID, '');
                  $hidden = $i > 1 ? "hidden" : '';
                  echo $productID ? "<div title='$name' class='text-ellipsis $hidden'>" . html::a($this->createLink('product', 'view', "productID=$productID"), $name) . '</div>' : ''; 

                  $i++;
              }
              ?>
              </td>
              <td class="hd-<?php echo $requirement->id;?>">
              <?php
              $codeList = zget($codes, $requirement->project, array());
              $i = 0;
              foreach($codeList as $code)
              {
                  if($i == 2) echo '<div class="more">More<a class="task-toggle collapsed"><i class="icon icon-angle-double-right"></i></a></div>';

                  $hidden = $i > 1 ? "hidden" : '';
                  echo $code ? "<div title='$code' class='text-ellipsis $hidden'>" . html::a($this->createLink('product', 'view', "productID=$productID"), $code) . '</div>' : '';

                  $i++;
              }
              ?>
              </td>
              <?php $name = zget($projects, $requirement->project, '');?>
              <td class="hd-<?php echo $requirement->id;?>" title="<?php echo $name;?>">
                <?php
                $projectProgress = isset($progress[$requirement->project]) ? $progress[$requirement->project] : 0;
                echo "<div class='c-progress'>";
                echo "<div class='progress-pie' data-doughnut-size='90' data-color='#00da88' data-value='{$projectProgress}' data-width='24' data-height='24' data-back-color='#e8edf3'>";
                echo "<div>{$projectProgress}%</div>";
                echo "</div>";
                echo "</div>";
                echo $requirement->project ? html::a($this->createLink('projectplan', 'view', "planID=$requirement->project"), $name) : '';
                ?>
              </td>
            </tr>
              <?php foreach($requirement->children as $demandinside): ?>
                <tr class="hidden d-<?php echo $requirement->id;?>">
                  <td title="<?php echo $requirement->title;?>"><?php echo html::a($this->createLink('demandinside', 'view', "opinionID=$demandinside->id"), $demandinside->title, '');?></td>
                  <td>
                  <?php
                  $appList = explode(',', str_replace(' ', '', $demandinside->app));
                  foreach($appList as $appID) 
                  {   
                      if($appID) echo '<div class="text-ellipsis" title="' . zget($apps, $appID, '') . '">' . html::a($this->createLink('application', 'view', "appID=$appID"), zget($apps, $appID)). '</div>';
                  }
                  ?>
                  </td>
                  <td></td>
                  <td>
                  <?php 
                    $linkedProducts = explode(',', str_replace(' ', '', $demandinside->product));
                    $i = 0;
                    foreach($linkedProducts as $productID)
                    {
                        if($i == 2) echo '<div class="more">More<a class="task-toggle collapsed"><i class="icon icon-angle-double-right"></i></a></div>';
                        $name = zget($products, $productID, '');
                        $hidden = $i > 1 ? "hidden" : '';
                        echo $productID ? "<div title='$name' class='text-ellipsis $hidden'>" . html::a($this->createLink('product', 'view', "productID=$productID"), $name) . '</div>' : ''; 
                        $i++;
                    }
                    ?>
                  </td>
                  <td>
                  <?php
                  $codeList = zget($codes, $demandinside->project, array());
                  $i = 0;
                  foreach($codeList as $code)
                  {
                      if($i == 2) echo '<div class="more">More<a class="task-toggle collapsed"><i class="icon icon-angle-double-right"></i></a></div>';

                      $hidden = $i > 1 ? "hidden" : '';
                      echo $code ? "<div title='$code' class='text-ellipsis $hidden'>" . html::a($this->createLink('product', 'view', "productID=$productID"), $code) . '</div>' : '';

                      $i++;
                  }
                  ?>
                  </td>
                  <td>
                  <?php
                  $projectProgress = isset($progress[$demandinside->project]) ? $progress[$demandinside->project] : 0;
                  echo "<div class='c-progress'>";
                  echo "<div class='progress-pie' data-doughnut-size='90' data-color='#00da88' data-value='{$projectProgress}' data-width='24' data-height='24' data-back-color='#e8edf3'>";
                  echo "<div>{$projectProgress}%</div>";
                  echo "</div>";
                  echo "</div>";
                  echo $demandinside->project ? html::a($this->createLink('projectplan', 'view', "planID=$demandinside->project"), $name) : '';
                  ?>
                  </td>
                </tr>
              <?php endforeach;?>
            <?php endforeach;?>
            <?php endif;?>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <?php endif;?>
  </div>
</div>
<script>
$('#navbar li[data-id="requirement"]').removeClass('active');
$('.' + status + 'Tab').addClass('btn-active-text');
function changeParams(obj)
{
    var begin   = $('#conditions').find('#begin').val();
    var end     = $('#conditions').find('#end').val();

    if(begin.indexOf('-') != -1) 
    {   
        var beginarray = begin.split("-");
        var begin = ''; 
        for(i = 0; i < beginarray.length; i++) begin = begin + beginarray[i];
    }   
    if(end.indexOf('-') != -1) 
    {   
        var endarray = end.split("-");
        var end = ''; 
        for(i = 0 ; i < endarray.length ; i++) end = end + endarray[i];
    }   

    var link = createLink('requirement', 'matrix', 'status=' + status + '&begin=' + begin + '&end=' + end);
    location.href=link;
}
$('.expand').click(function() {
    var id = $(this).attr('id');
    var count = $('#' + id).attr('count');
    $('.r-' + id).removeClass('hidden');//打开二级
    $('#o-' + id).attr('rowspan',Number(count)+1);//扩展第一级
    $('#o-' + id + ' .pullback').removeClass('hidden');//一级关闭二级按钮展示
    $('.h-' + id).addClass('hidden');//二级第一排隐藏
})

$('.more').click(function() {
    $(this).nextAll('div').removeClass('hidden');
    $(this).hide();
})

$('.pullback').click(function() {
    $(this).addClass('hidden');
    var id = $(this).parent('td').attr('id').split('-')[1];
    console.log($('.r-' + id + ' .expand-demandinside'))
    $('.r-' + id + ' .pullback-demandinside').trigger('click');
    $('.h-' + id).removeClass('hidden');
    $('.r-' + id).addClass('hidden');
    $(this).parent('td').attr('rowspan', 1);
})

$('.expand-demandinside').click(function(){
  var id = $(this).attr('r-id');
  var oid = $(this).attr('oid');
  var rowspan = $('#o-' + oid).attr('rowspan');
  var count = $('#r-' + id).attr('count');
  $('.d-'+ id).removeClass('hidden');//打开三级
  $('#r-' + id).attr('rowspan', Number(count)+1);//扩展二级
  $('#o-' + oid).attr('rowspan',Number(rowspan) + Number(count));//扩展一级别
  $('#r-' + id + ' .pullback-demandinside').removeClass('hidden');//一级关闭二级按钮展示
  $('.hd-' + id).addClass('hidden');//二级第一排隐藏
})
$('.pullback-demandinside').click(function(){
  $(this).addClass('hidden');
  var id = $(this).parent('td').attr('id').split('-')[1];
  var oid = $(this).parent('td').attr('oid');
  var rowspan = $('#o-' + oid).attr('rowspan');
  var count = $('#r-' + id).attr('count');
  $('.d-' + id).addClass('hidden');
  $('#r-' + id).attr('rowspan', 1);//扩展二级
  $('#o-' + oid).attr('rowspan',Number(rowspan) - Number(count));//扩展一级别
  $('#r-' + id + ' .pullback-demandinside').addClass('hidden');//一级关闭二级按钮展示
  $('.hd-' + id).removeClass('hidden');//二级第一排隐藏
})
</script>
<?php include '../../common/view/footer.html.php';?>
