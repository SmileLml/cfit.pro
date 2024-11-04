<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datepicker.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
.task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
.task-toggle .icon{display: inline-block; transform: rotate(90deg);}
.more-tips{display: none;}
.close-tips{display: none}
</style>
<?php $urlParams = "projectId=$build->project&productId=$build->product&productVersion=$build->version"; ?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->build->deal;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
        <?php /*if($build->status == 'waittest'):*/?><!--
          <tr>
            <th class='w-140px'></th>
            <td colspan="10" style="color:red;"><?php /*echo $lang->build->notice;;*/?></td>
          </tr>
        --><?php /*endif;*/?>
          <tr>
                <th class='w-140px'><?php echo $lang->build->name;?></th>
                <td colspan="10"><?php echo html::input('name', $build->name, "class='form-control ' ");?>
                </td>
<!--              <input type="hidden" value="<?php /*echo $statusList;*/?>" name="status" id = 'status'/>
-->        </tr>
        <?php if($build->status == 'waittest' && $isQualityGate):?>
            <tr>
                <th class='w-140px'><?php echo $lang->qualitygate->severityTest;?></th>
                <td colspan="10">
                    <?php echo $this->qualitygate->diffColorStatus($severityTestResult); ?>
                </td>
            </tr>

            <?php if($severityTestResult == $lang->qualitygate->statusArray['finish']):?>
                <tr>
                    <th class='w-140px'><?php echo $lang->qualitygate->qualitygate;?></th>
                    <td colspan="10">
                        <?php echo $this->qualitygate->diffSeverityGateResult($severityGateResult);?>
                        <span style="margin-left: 40px;">
                            <?php echo html::a($this->createLink('report', 'qualityGateCheckResult', $urlParams, '', true).'#app=project', '点击查看详情',   '_blank', "style='color: #0c60e1;' id='qualityGateResultDetail'");?>
                        </span>
                    </td>
                </tr>
            <?php endif;?>
        <?php endif;?>

        <?php if($build->status == 'waitdeptmanager'):?>
            <tr>
                <th class='w-140px'><?php echo $lang->qualitygate->qualitygate;?></th>
                <td colspan="10">

                    <?php echo $this->qualitygate->diffSeverityGateResult($severityGateResult);?>
                    <span style="margin-left: 40px;">
                            <?php echo html::a($this->createLink('report', 'qualityGateCheckResult', $urlParams, '', true).'#app=project', '点击查看详情',  '_blank', "style='color: #0c60e1;' id='qualityGateResultDetail'");?>
                        </span>
                </td>
            </tr>

            <tr>
                <th class='w-140px'><?php echo $lang->build->specialPassReason;?></th>
                <td colspan="10">
                  <?php echo $build->specialPassReason;?>
                </td>
            </tr>
        <?php endif;?>

          <tr class="hidden dev">
              <th class='w-140px'><?php echo $build->status != 'waitverifyapprove' ? $lang->build->result : $lang->build->approveResult;?></th>
              <td colspan="10"><?php echo html::select('status', $statusList, '',"class='form-control chosen ' required onchange=changeStaus()");?></td>
              <input type="hidden" value="<?php echo $build->status;?>" name='oldstatus' id = 'oldstatus'/>
          </tr>
          <tr class="hidden dev2">
              <th class='w-140px'><?php echo $lang->build->releaseName;?></th>
              <td colspan="10"><?php echo html::input('releaseName',  $build->name,"class='form-control  ' ");?></td>
          </tr>
          <tr class="hidden dev2">
              <th class='w-140px'><?php echo $lang->build->releasePath;?></th>
              <td colspan="10"><?php echo html::input('releasePath', '',"class='form-control  ' ");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->build->filePath;?></th>
            <td colspan="10"><?php echo html::input('filePath', '', "class='form-control '  placeholder='{$lang->build->placeholder->filePathTip}'");?></td>
          </tr>
        <!--报工改造去掉-->
          <!--<tr>
              <th class='w-140px'><?php /*echo $lang->build->consumed;*/?></th>
              <td colspan="10"><?php /*echo html::input('consumed', '', "class='form-control ' required");*/?>
              <span style="color: lightslategray" colspan='10'><?php /*echo $this->lang->build->dealTip */?></span>
              </td>
          </tr>-->
        <tr class="hidden dev3">
            <th class='w-140px'><?php echo $lang->build->actualVerifyUser;?></th>
            <td colspan="10"><?php echo html::select('actualVerifyUser[]', $users, $build->actualVerifyUser,"class='form-control chosen ' required multiple");?></td>
        </tr>

        <?php if($build->status == "waittest" || $build->status == "testsuccess") :?>
        <?php $relevantUser = $build->status == "waittest" ? $build->testRelevantUser :$build->verifyRelevantUser;
        if(!$relevantUser) :?>
          <tr id='relevantDept1'>
            <th class='w-140px'><?php echo $build->status == "waittest" ? $lang->build->testRelevantUser :$lang->build->verifyRelevantUser ;?></th>
            <td colspan="10">
              <div class='table-row'>
                <div class='table-col'>
<!--                  --><?php //echo html::select('relevantUser[]', $users, '', "class='form-control chosen'");?>
                  <?php echo html::select('relevantUser[]', $users, '', "class='form-control chosen' multiple");?>
                </div>
               <!-- <div class='table-col'>
                  <div class='input-group'>
                    <span class='input-group-addon fix-border'><?php /*echo $lang->build->workload;*/?></span>
                    <?php /*echo html::input('workload[]', '', "class='form-control'");*/?>
                  </div>
                </div>-->
              </div>
            </td>
<!--            <td class="c-actions">-->
<!--              <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>-->
<!--            </td>-->
          </tr>
          <?php else:?>
            <?php  $relevantUserArr = $build->status == "waittest" ? explode(',',$build->testRelevantUser) :explode(',',$build->verifyRelevantUser) ;
            foreach($relevantUserArr as $item) :?>
            <tr id='relevantDept1'>
            <th class='w-140px'><?php echo $build->status == "waittest" ? $lang->build->testRelevantUser :$lang->build->verifyRelevantUser ;?></th>
            <td colspan="10">
              <div class='table-row'>
                <div class='table-col'>
<!--                  --><?php //echo html::select('relevantUser[]', $users, $item, "class='form-control chosen'");?>
                  <?php echo html::select('relevantUser[]', $users, '', "class='form-control chosen' multiple");?>
                </div>
               <!-- <div class='table-col'>
                  <div class='input-group'>
                    <span class='input-group-addon fix-border'><?php /*echo $lang->build->workload;*/?></span>
                    <?php /*echo html::input('workload[]', '', "class='form-control'");*/?>
                  </div>
                </div>-->
              </div>
            </td>
<!--            <td class="c-actions">-->
<!--              <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>-->
<!--            </td>-->
          </tr>
          <?php endforeach;?>
        <?php endif;?>
        <?php else:?>
            <?php if ($build->status != "waitdeptmanager"):?>
                <tr id='relevantDept1'>
                <th class='w-140px'><?php echo $lang->build->relevantDept ;?></th>
                <td colspan="10">
                    <div class='table-row'>
                        <div class='table-col'>
<!--                            --><?php //echo html::select('relevantUser[]', $users, '', "class='form-control chosen'");?>
                            <?php echo html::select('relevantUser[]', $users, '', "class='form-control chosen' multiple");?>
                        </div>
                        <!-- <div class='table-col'>
                  <div class='input-group'>
                    <span class='input-group-addon fix-border'><?php /*echo $lang->build->workload;*/?></span>
                    <?php /*echo html::input('workload[]', '', "class='form-control'");*/?>
                  </div>
                </div>-->
                    </div>
                </td>
<!--                <td class="c-actions">-->
<!--                    <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>-->
<!--                </td>-->
            </tr>
            <?php endif;?>
        <?php endif;?>
          <tr class="hidden dev2">
              <th class='w-140px'><?php echo $lang->build->plateName;?></th>
              <td colspan='10'><?php echo html::textarea('plateName', '', "rows='10' class='form-control kindeditor' hidefocus='true'  placeholder='{$lang->build->placeholder->plateTip}'");?></td>
          </tr>
          <tr class="hidden dev2">
              <th><?php echo $lang->files;?></th>
              <td colspan='10'><?php echo $this->fetch('file', 'buildform');?></td>
          </tr>
        <tr class="hidden dev3">
            <th class='w-140px'><?php echo $lang->build->actualVerifyDate;?></th>
            <td colspan="10"><?php echo html::input('actualVerifyDate', strpos($build->actualVerifyDate,'0000-00-00') === false ? $build->actualVerifyDate :'', "class='form-control form-date ' required readonly");?></td>
        </tr>

        <?php if($build->files):?>
        <tr class="hidden dev3">
            <th><?php echo $lang->build->fileList ;?></th>
            <td colspan='10'>
                 <div class='detail'>
                        <?php   $canOperate = in_array($build->status, $this->lang->build->fileCanOperateList) && ($this->app->user->account == 'admin' || $this->app->user->account == $build->verifyUser)  ?  true: false;
                        ?>
                        <div class='detail-content article-content'>
                            <?php
                            if($build->files){
                                echo $this->fetch('file', 'printFiles', array('files' => $build->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => $canOperate));
                            }else{
                                echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                            }
                            ?>
                        </div>
                 </div>
            </td>
        </tr>
        <?php endif;?>
        <tr class="hidden dev3">
            <th><?php echo $lang->files;?></th>
            <td colspan='10' class="required"><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.9&filesName=verifyFiles');?>
                <div style="color: lightslategray"><span > <?php echo sprintf($lang->review->fileOverSize, $this->config->review->fileSize->fileSize);?></span></div>
            </td>
        </tr>
          <tr>
              <th class='w-140px'><?php echo $build->status != 'waitverifyapprove' ? $lang->build->desc : $lang->build->approveOpinion;?></th>
              <td colspan='10' class="required"><?php echo html::textarea('comment', '', "rows='10' class='form-control kindeditor' hidefocus='true'");?></td>
          </tr>
          <tr>
              <td class='form-actions text-center' colspan='11'>
                  <input type="hidden" name="isWarn"  id="isWarn" value="yes">
                  <?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>


<table class='hidden'>
  <tbody id="relevantDeptTable">
    <tr id='relevantDept0'>
      <th class='w-140px'><?php echo  $build->status == "waittest" ? $lang->build->testRelevantUser : ($build->status == "testsuccess" ? $lang->build->verifyRelevantUser  : $lang->build->relevantDept) ;?></th>
      <td colspan='10'>
        <div class='table-row'>
          <div class='table-col'>
<!--            --><?php //echo html::select('relevantUser[]', $users, '', "class='form-control' id='relevantUser0'");?>
              <?php echo html::select('relevantUser[]', $users, '', "class='form-control chosen' multiple");?>
          </div>
          <!--<div class='table-col'>
            <div class='input-group'>
              <span class='input-group-addon fix-border'><?php /*echo $lang->build->workload;*/?></span>
              <?php /*echo html::input('workload[]', '', "class='form-control'");*/?>
            </div>
          </div>-->
        </div>
      </td>
<!--      <td class="c-actions">-->
<!--        <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='0' id='codePlus0' class="btn btn-link"><i class="icon-plus"></i></a>-->
<!--        <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='0' id='codeClose0' class="btn btn-link"><i class="icon-close"></i></a>-->
<!--      </td>-->
    </tr>
  </tbody>
</table>
<?php echo js::set('status', $build->status);?>
<script>
    $(function () {
        $(".form-date").datetimepicker('setEndDate','<?php echo date(DT_DATE1)?>')
    })
</script>
<?php include '../../../common/view/footer.html.php';?>
