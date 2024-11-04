<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->problem->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->problem->abstract;?></th>
            <td><?php echo html::input('abstract', $problem->abstract, "class='form-control' $readOnly");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->problem->source;?></th>
            <td><?php echo html::select('source', $lang->problem->sourceList, $problem->source, "class='form-control chosen' $disabled");?></td>
          </tr>
          <!--          迭代35 需求收集3512 自建问题单去掉问题级别-->
<!--          <tr>-->
<!--            <th>--><?php //echo $lang->problem->severity;?><!--</th>-->
<!--            <td>--><?php //echo html::select('severity', $lang->problem->severityList, $problem->severity, "class='form-control chosen' $disabled");?><!--</td>-->
<!--          </tr>-->
          <tr>
            <th><?php echo $lang->problem->app;?></th>
            <td><?php echo html::select('app[]', $apps, $problem->app, "class='form-control chosen'  required $disabled");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->pri;?></th>
            <td><?php echo html::select('pri', $lang->problem->priList, $problem->pri, "class='form-control chosen' $disabled");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->occurDate;?></th>
              <?php if($problem->IssueId){ ?>
                  <td><?php echo html::input('occurDate', $problem->occurDate, "class='form-control' $readOnly");?></td>
              <?php } else {?>
                  <td><?php echo html::input('occurDate', $problem->occurDate, "class='form-control form-date'");?></td>
              <?php } ?>
            </tr>
         <!--<tr>
                 <th><?php /*echo $lang->problem->consumed;*/?></th>
                 <td><?php /*echo html::input('consumed', end($problem->consumed)->consumed, "class='form-control'");*/?></td>
         </tr>-->
          <tr>
            <th><?php echo $lang->problem->nextExecutive;?></th>
            <td><?php echo html::select('dealUser', ['' => ''] + $executives, $problem->dealUser, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->problem->desc;?></th>
              <?php if($problem->IssueId){ ?>
                  <td colspan='2'><div class="detail-content article-content"><textarea disabled class='form-control'><?php echo  $problem->desc ;?></textarea></div></td>
              <?php } else {?>
                  <td colspan='2'><?php echo html::textarea('desc', $problem->desc, "class='form-control' ");?></td>
              <?php } ?>
          </tr>
          <tr>
              <th><?php echo $lang->problem->filelist;?></th>

              <td>
                  <div class='detail'>
                      <div class='detail-content article-content'>
                          <?php
                          if($problem->files){
                              echo $this->fetch('file', 'printFiles', array('files' => $problem->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                          }else{
                              echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                          }
                          ?>
                      </div>
                  </div>
              </td>
          </tr>
          <?php if(!$problem->IssueId){ ?>
          <tr>
              <th><?php echo $lang->files;?></th>
              <td colspan='2'><?php echo $this->fetch('file', 'buildform');?></td>
          </tr>
          <?php }?>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
