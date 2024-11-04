<?php
/**
 * The create of programplan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     programplan
 * @version     $Id: create.html.php 4903 2013-06-26 05:32:59Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<style>.icon-help{margin-left: 3px;}</style>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <span class='btn btn-link btn-active-text'>
      <?php
      $title = $programPlan->name . $lang->project->stage . '（' . $programPlan->begin . $lang->project->to . $programPlan->end . '）';
      echo "<span class='text'>{$title}</span>";
      ?>
    </span>
  </div>
</div>
<div id='mainContent' class='main-content'>
  <form class='main-form form-ajax' method='post' id='planForm' enctype='multipart/form-data'>
    <table class='table table-form'>
      <thead>
        <tr class='text-center'>
          <th class='required'><?php echo $lang->programplan->subStageName;;?></th>
          <th class='w-110px'><?php echo $lang->programplan->milestone;?></th>
          <th class='w-100px'><?php echo $lang->execution->code;?></th>
          <th class='w-110px'><?php echo $lang->stage->setType;?></th>
          <th class='w-110px required'><?php echo $lang->programplan->begin;?></th>
          <th class='w-130px required'><?php echo $lang->programplan->end;?></th>
          <th class='w-130px'><?php echo $lang->programplan->days?></th>
          <th class='w-200px'><?php echo $lang->programplan->resource?></th>
          <th class="w-70px text-center"> <?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 0;?>
        <?php for($j = 0; $j < 5; $j ++):?>
        <tr class='addedItem'>
          <td><input type='text' name='names[<?php echo $i;?>]' id='names<?php echo $i;?>' value='' class='form-control' /></td>
          <td><?php echo html::radio("milestone[$i]", $lang->programplan->milestoneList, 0);?></td>
          <td><input type='text' name='code[<?php echo $i;?>] ' id='code<?php echo $i;?>' value='' class='form-control' /></td>
          <td><?php echo html::select("attribute[$i]", $lang->stage->typeList, '', "class='form-control chosen'");?></td>
          <td><input type='text' name='begin[<?php echo $i;?>] ' id='begin<?php echo $i;?>' value='' class='form-control form-date' onchange='changeComputerBegin(this, <?php echo $i;?>)'/></td>
          <td><input type='text' name='end[<?php echo $i;?>]' id='end<?php echo $i;?>' value='' class='form-control form-date' onchange='changeComputerEnd(this, <?php echo $i;?>)'/></td>
          <td><input type='text' name='planDuration[<?php echo $i;?>] ' id='planDuration<?php echo $i;?>' value='' class='form-control' /></td>
          <td><input type='text' name='resource[<?php echo $i;?>]' id='resource<?php echo $i;?>' value='' class='form-control'/></td>
          <td class='c-actions text-left'>
            <a href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon-plus'></i></a>
            <a href='javascript:;' onclick='deleteItem(this)' class='btn btn-link'><i class='icon icon-close'></i></a>
          </td>
        </tr>
        <?php $i ++;?>
        <?php endfor;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='9' class='text-center form-actions'><?php echo html::submitButton() . ' ' . html::backButton(); ?></td>
        </tr>
      </tfoot>
    </table>
    <?php js::set('i', $i);?>
  </form>
</div>
<div>
  <table class='hidden'>
    <tr id='addItem' class='hidden'>
      <td><input type='text' name='names[%i%]' id='names%i%' class='form-control' /></td>
      <td><?php echo html::radio("milestone[%i%]", $lang->programplan->milestoneList, 0);?></td>
      <td><input type='text' name='code[%i%] ' id='code%i%' class='form-control' /></td>
      <td><?php echo html::select("attribute[%i%]", $lang->stage->typeList, '', "class='form-control chosen'");?></td>
      <td><input type='text' name='begin[%i%] ' id='begin%i%' class='form-control form-date' onchange='changeComputerBegin(this, %i%)'/></td>
      <td><input type='text' name='end[%i%]' id='end%i%' class='form-control form-date' onchange='changeComputerEnd(this, %i%)'/></td>
      <td><input type='text' name='planDuration[%i%] ' id='planDuration%i%' class='form-control' /></td>
      <td><input type='text' name='resource[%i%]' id='resource%i%' class='form-control' /></td>
      <td class='c-actions text-center'>
        <a href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon-plus'></i></a>
        <a href='javascript:;' onclick='deleteItem(this)' class='btn btn-link'><i class='icon icon-close'></i></a>
      </td>
    </tr>
  </table>
</div>
<script>
function changeComputerBegin(obj, key)
{
    var beginDate = $(obj).val();
    var endDate   = $('#end' + key).val();
    if(!beginDate || !endDate) return;

    var dateStart = new Date(beginDate);
    var dateEnd = new Date(endDate);
    var difValue = (dateEnd - dateStart) / (1000 * 60 * 60 * 24);
    $('#planDuration' + key).val(difValue ? difValue : 0);
}

function changeComputerEnd(obj, key)
{
    var beginDate = $('#begin' + key).val();
    var endDate   = $(obj).val();
    if(!beginDate || !endDate) return;

    var dateStart = new Date(beginDate);
    var dateEnd = new Date(endDate);
    var difValue = (dateEnd - dateStart) / (1000 * 60 * 60 * 24);
    $('#planDuration' + key).val(difValue ? difValue : 0);
}
</script>
<?php include '../../../common/view/footer.html.php';?>
