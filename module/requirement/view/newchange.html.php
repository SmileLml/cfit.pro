<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.read-info {padding: 5px 5px 5px 10px; background-color: rgba(0,0,0,.025); border: 1px solid #eee; word-wrap: break-word;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirement->change;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-150px'><?php echo $lang->requirement->name;?></th>
            <td><?php echo html::input('name', $requirement->name, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->desc;?></th>
            <td colspan='3'><?php echo html::textarea('desc', $requirement->desc, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->background;?></th>
            <td colspan='2'><div class='read-info'><?php echo $opinion->background;?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->opinion->overview;?></th>
            <td colspan='2'><div class='read-info'><?php echo $opinion->overview;?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->deadLine;?></th>
<!--            <td colspan='2'><div class='read-info'>--><?php //echo $requirement->deadLine;?><!--</div></td>-->
              <td colspan='2'><?php echo html::input('deadLine', $requirement->deadLine, "class='form-control' readonly");?></td>
          </tr>
<!--          <tr>-->
<!--              <th>--><?php //echo $lang->requirement->deadLine;?><!--</th>-->
<!--              <td colspan='2'><div class='read-info'>--><?php //echo $opinion->deadline;?><!--</div></td>-->
<!--          </tr>-->
          <?php if(!empty($requirement->entriesCode)):?>
          <tr>
            <th><?php echo $lang->requirement->product;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php echo html::select('product[]', $products, $requirement->product, "disabled='disabled' class='form-control chosen' multiple");?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('product', 'create', '', '', 1), $lang->requirement->createProduct, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->line;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php echo html::select('line[]', $lines, $requirement->line, "disabled='disabled' class='form-control chosen' multiple");?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('product', 'manageLine', '', '', 1), $lang->requirement->createLine, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <?php echo html::hidden('product', $requirement->product);?>
          <?php echo html::hidden('line', $requirement->line);?>
          <?php else:?>
          <tr>
            <th><?php echo $lang->requirement->product;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php echo html::select('product[]', $products, $requirement->product, "class='form-control chosen' multiple");?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('product', 'create', '', '', 1), $lang->requirement->createProduct, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->line;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php echo html::select('line[]', $lines, $requirement->line, "class='form-control chosen' multiple");?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('product', 'manageLine', '', '', 1), $lang->requirement->createLine, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->requirement->project;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php echo html::select('project', $projects, $requirement->project, "class='form-control chosen'");?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('projectplan', 'create', '', '', 1), $lang->requirement->createProject, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->app;?></th>
            <td colspan='2'>
              <div class='input-group'>
                <?php
                if($requirement->entriesCode)
                {
                    echo html::select('app', $apps, $requirement->app, "class='form-control chosen '");
                } else {
                    echo html::select('app[]', $apps, $requirement->app, "class='form-control chosen'multiple");
                }
                ?>
                <span class='input-group-addon'><?php echo html::a($this->createLink('application', 'create', 'programID=0', '', 1), $lang->requirement->createApp, '', "data-toggle='modal' data-type='iframe'");?></span>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->dept;?></th>
            <td><?php echo html::select('dept', $depts, $requirement->dept, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->planEnd;?></th>
            <td><?php echo html::input('planEnd', $requirement->planEnd == '0000-00-00' ? '' : $requirement->planEnd, "class='form-control form-date'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->owner;?></th>
            <td><?php echo html::select('owner', $users, $requirement->owner, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->contact;?></th>
            <td><?php echo html::input('contact', $requirement->contact, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->method;?></th>
            <td><?php echo html::select('method', $lang->requirement->methodList, $requirement->method, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->analysis;?></th>
            <td colspan='3'><?php echo html::textarea('analysis', $requirement->analysis, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->handling;?></th>
            <td colspan='3'><?php echo html::textarea('handling', $requirement->handling, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->implement;?></th>
            <td colspan='3'><?php echo html::textarea('implement', $requirement->implement, "class='form-control'");?></td>
          </tr>
          <!-- 非推送需求条目需要评审人。-->
          <?php if(empty($requirement->entriesCode)):?>
          <tr>
            <th><?php echo $lang->requirement->reviewer;?></th>
            <td colspan='3'><?php echo html::select('reviewer[]', $users, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirement->mailto;?></th>
            <td colspan='3'><?php echo html::select('mailto[]', $users, $requirement->mailto, "class='form-control chosen' multiple");?></td>
          </tr>
          <?php else:?>
            <?php echo html::hidden('reviewer[]', '');?>
            <?php echo html::hidden('mailto[]', '');?>
          <?php endif;?>
          <tr>
            <?php echo html::hidden('code', $requirement->code);?>
            <?php echo html::hidden('entriesCode', $requirement->entriesCode);?>
            <?php echo html::hidden('parentCode', $requirement->parentCode);?>
            <?php echo html::hidden('feedbackCode', $requirement->feedbackCode);?>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($lang->requirement->change) . html::backButton();?></td>
          </tr>
          <?php if(!empty($requirement->entriesCode)):?>
          <tr>
            <td class='form-actions text-center' colspan='4'><h4 style='color: #96c1c1;'><?php echo $lang->requirement->additionalTips;?></h4></td>
          </tr>
          <?php endif;?>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('prohibitFeedback', json_encode($config->requirement->prohibitFeedback));?>
<?php js::set('entriesCode', empty($requirement->entriesCode) ? 0 : 1);?>
<script>
if(entriesCode)
{
    //var prohibitFeedback = eval('(' + prohibitFeedback + ')');
    //for(var i in prohibitFeedback)
    //{
    //    $('#' + prohibitFeedback[i]).attr('disabled', 'disabled');
    //}

    $(function()
    {
        var $desc = editor['desc'];
        $desc.readonly(true);
    });
}
</script>
<?php include '../../common/view/footer.html.php';?>
