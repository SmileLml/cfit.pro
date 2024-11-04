<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.input-group-addon{min-width: 150px;}
.input-group{margin-bottom: 2px;}
.checkbox-inline{float: left;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo  $lang->putproduction->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
        <!--勿删调整格式-->
        <tr>
            <th class='w-180px' style="height: 0;padding:0"></th>
            <td style="height: 0;padding:0"></td>
            <th class='w-150px' style="height: 0;padding:0"></th>
            <td style="height: 0;padding:0"></td>
        </tr>
          <tr>
              <th>
                  <?php echo $lang->putproduction->desc;?>
                  <i title="<?php echo $lang->putproduction->descHelp;?>" class="icon icon-help"></i>
              </th>
              <td colspan="3"><?php echo html::input('desc', '', "class='form-control' maxlength='100'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->putproduction->outsidePlanId;?></th>
              <td><?php echo html::select('outsidePlanId', $outsideProjectList, '', "class='form-control chosen' onchange='getInProjectList()'");?></td>
              <th><?php echo $lang->putproduction->inProjectIds;?></th>
              <td class="inProjectIdsTd required"><?php echo html::select('inProjectIds[]', [], '', "class='form-control chosen' multiple");?></td>
          </tr>

          <tr>
            <th><?php echo $lang->putproduction->app;?></th>
            <td colspan="3" ><?php echo html::select('app[]', $appList, '', "class='form-control chosen' onchange='getProductName()' multiple");?></td>
          </tr>


          <tr>
              <th><?php echo $lang->putproduction->productId;?></th>
              <td colspan="3" class="productTd"><?php echo html::select('productId[]', $productList, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->putproduction->demandId;?></th>
            <td colspan="3"><?php echo html::select('demandId[]', $demandList, '', "class='form-control chosen' multiple");?></td>
          </tr>

          <tr>
            <th><?php echo $lang->putproduction->level;?> <i title="<?php echo $lang->putproduction->levelHelp;?>" class="icon icon-help"></i></th>
            <td><?php echo html::select('level', $lang->putproduction->levelList, '', "class='form-control chosen' onchange='changeLevel()'");?></td>
            <th>
                <?php echo $lang->putproduction->property; ?>
            </th>
              <td>
                  <?php echo html::select('property[]', $lang->putproduction->propertyList, '', "class='form-control chosen' onchange='changeProperty()' multiple");?>
              </td>
          </tr>

          <tr>
             <th><?php echo $lang->putproduction->stage;?> <i title="<?php echo $lang->putproduction->stageHelp;?>" class="icon icon-help"></th>
             <td  class="required">
                 <div class="form-control" id="stage">
                     <?php foreach($lang->putproduction->stageList as $key => $val):?>
                     <div class='group-item checkbox-inline'>
                        <?php echo html::checkbox('stage', array($key => $val), '',"onclick='setStageInfo();'");?>
                     </div>
                     <?php endforeach;?>
                 </div>
             </td>

              <th class="firstStagePidInfo hidden">
                  <?php echo $lang->putproduction->firstStagePid; ?>
                  <i title="<?php echo $lang->putproduction->firstStagePidHelp;?>" class="icon icon-help">
              </th>
              <td class="firstStagePidInfo firstStagePidTd hidden">
                  <?php echo html::select('firstStagePid', $firstStagePutProductionList, '', "class='form-control chosen'");?>
              </td>
          </tr>

          <tr class="secondStageInfo  hidden">
              <th><?php echo $lang->putproduction->dataCenter;?></th>
              <td class="required"><?php echo html::select('dataCenter[]', $lang->putproduction->dataCenterList, '', "class='form-control chosen' multiple");?></td>
              <th>
                  <?php echo $lang->putproduction->isPutCentralCloud; ?>
              </th>
              <td>
                  <?php echo html::select('isPutCentralCloud', $lang->putproduction->isPutCentralCloudList, '', "class='form-control chosen'");?>
              </td>
          </tr>

          <tr class="firstStageInfo hidden">
              <th><?php echo $lang->putproduction->fileUrlRevision;?>
                  <i title="<?php echo $lang->putproduction->fileUrlRevisionHelp;?>" class="icon icon-help"></i>
              </th>
              <td colspan="3"  class="required"><?php echo html::input('fileUrlRevision', '', "class='form-control' maxlength='300'");?></td>
          </tr>

          <tr>
              <th><?php echo $lang->putproduction->isReview;?></th>
              <td colspan="3">
                  <?php echo html::select('isReview', $lang->putproduction->isReviewList, '', "class='form-control chosen' onchange='changeIsReview();'");?>
              </td>
          </tr>
          <tr class="reviewCommentInfo hidden">
              <th><?php echo $lang->putproduction->reviewComment;?></th>
              <td colspan="3"><?php echo html::textarea('reviewComment', '', "placeholder='{$lang->putproduction->reviewCommentPlaceholder}' class='form-control'");?></td>
          </tr>

          <tr class="secondStageInfo hidden">
              <th><?php echo $lang->putproduction->isBusinessCoopera;?></th>
              <td colspan="3" class="required">
                  <?php echo html::select('isBusinessCoopera', $lang->putproduction->isBusinessCooperaList, '', "class='form-control chosen' onchange='changeIsBusinessCoopera();'");?>
              </td>
          </tr>

          <tr class="secondStageInfo businessCooperaContentInfo hidden">
              <th><?php echo $lang->putproduction->businessCooperaContent;?></th>
              <td colspan="3" class="required"><?php echo html::textarea('businessCooperaContent', '', "class='form-control'");?></td>
          </tr>

          <tr class="secondStageInfo hidden">
              <th><?php echo $lang->putproduction->isBusinessAffect;?></th>
              <td colspan="3" class="required">
                  <?php echo html::select('isBusinessAffect', $lang->putproduction->isBusinessAffectList, '', "class='form-control chosen' onchange='changeIsBusinessAffect();'");?>
              </td>
          </tr>

          <tr class="secondStageInfo businessAffectInfo hidden">
              <th><?php echo $lang->putproduction->businessAffect;?></th>
              <td colspan="3" class="required"><?php echo html::textarea('businessAffect', '', "class='form-control'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->putproduction->remark;?></th>
              <td colspan="3"><?php echo html::textarea('remark', '', "class='form-control'");?></td>
          </tr>

          <tr>
              <th>
                  <!--评审人员 -->
                  <?php echo $lang->putproduction->reviewNodes;?>
                  <!--
                  <i title="<?php echo $lang->putproduction->reviewNodesTip;?>" class="icon icon-help"></i>
                  -->
              </th>
              <td colspan="3">

              <?php
                  foreach($lang->putproduction->internalReviewNodeCodeList as $nodeCode):
                      $nodeName = zget($lang->putproduction->reviewNodeCodeNameList, $nodeCode);
                      $reviewerList = zget($reviewers, $nodeCode, []);
                      $currentAccounts = zget($reviewerAccounts, $nodeCode, '');
                  ?>

                  <div class='input-group node-item node<?php echo $nodeCode;?>' style='width:80%' id="<?php echo $nodeCode;?>">
                      <span class='input-group-addon'><?php echo $nodeName;?></span>
                      <?php echo html::select("nodes[$nodeCode][]", $reviewerList, $currentAccounts, "class='form-control chosen' required multiple"); ?>
                      <input type="hidden" name = "requiredNodes[<?php echo $nodeCode;?>]" id="requiredNodes-<?php echo $nodeCode;?>" class="requiredNodes" value="0">
                  </div>

                  <?php endforeach;?>
              </td>
          </tr>
          <tr>
              <th>
                <input type="hidden" name="issubmit" value="save">
              </th>
              <td class='form-actions text-center' colspan='3'>
                  <?php echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn buttonInfo') . html::commonButton($lang->putproduction->submit, '', 'btn btn-wide btn-primary submitBtn buttonInfo') . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<input type="hidden" id="responseid">
<?php include '../../common/view/footer.html.php';?>
