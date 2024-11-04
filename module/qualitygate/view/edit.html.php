<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.input-group-addon{min-width: 150px;}
.input-group{margin-bottom: 6px;}
.panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
.panel{border-color: #ddd;}
</style>
<div id="mainContent" class="main-content fade" style="min-height: 380px;">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo  $title;?></h2>
    </div>
      <?php if(!$checkRes['result']):?>
          <div class="tipMsg red">
              <span><?php echo $checkRes['message']; ?></span>
          </div>
      <?php else:?>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
          <table class="table table-form" id="">
              <tbody>
                  <tr>
                      <th class='w-120px'><?php echo $lang->qualitygate->productId;?></th>
                      <td>
                          <?php echo html::select('productId',  $productList,  $info->productId, "class='form-control  chosen' onchange='changeProduct(this.value);'");?>
                      </td>
                      <th class='w-80px'><?php echo $lang->qualitygate->productVersion;?></th>
                      <td class="productVersion">
                          <?php echo html::select('productVersion',  $productVersionList,  $info->productVersion, "class='form-control  chosen' onchange='changeProductVersion();'");?>
                      </td>
                  </tr>

                  <tr>
                      <th><?php echo $lang->qualitygate->qualitygate;?></th>
                      <td colspan="3">
                          <span id="qualityGateResultInfo">
                              <?php echo $this->qualitygate->diffSeverityGateResult($severityGateResult);?>
                          </span>
                          <span style="margin-left: 40px;">
                                <?php echo html::a($this->createLink('report', 'qualityGateCheckResult', "projectId=$projectId&productId=$productId&productVersion=$info->productVersion", '', true).'#app=project', '点击查看详情', '_blank', "style='color: #0c60e1;' id='qualityGateResultDetail'");?>
                          </span>
                      </td>
                  </tr>

                  <tr>
                      <th><?php echo $lang->qualitygate->severityTest;?></th>
                      <td >
                          <?php echo html::select('status',  $lang->qualitygate->statusList,   $info->status, "class='form-control chosen' onchange='changeStatus(this.value);'");?>
                      </td>
                      <td colspan="2" style="padding-left: 20px;">
                          <span style="color: red" id="statusTipMsg" class="hidden">
                          <?php echo $lang->qualitygate->statusTipMsg; ?>
                          </span>
                      </td>
                  </tr>

                  <tr id="severityTestUserTr">
                      <th><?php echo $lang->qualitygate->severityTestUser;?></th>
                      <td id="severityTestUserTd" class="required">
                          <?php echo html::select('severityTestUser',  $severityTestUsers,   $info->severityTestUser, 'class="form-control  chosen"');?>
                      </td>
                      <td colspan="2">
                      </td>
                  </tr>


                  <tr>
                      <th class="w-120px"></th>
                      <td class='form-actions text-center' colspan='3'>
                          <?php echo html::submitButton($this->lang->submit) . html::backButton();?>
                      </td>
                  </tr>
              </tbody>
          </table>
      </form>
      <?php endif;?>

  </div>
</div>
<?php js::set('projectId', $projectId);?>
<?php js::set('severityGateResultList', $lang->qualitygate->severityGateResultList);?>
<?php js::set('status', $info->status);?>
<?php js::set('buildId', $info->buildId);?>

<?php include '../../common/view/footer.html.php';?>
