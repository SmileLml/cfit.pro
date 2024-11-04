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
      <h2><?php echo  $lang->putproduction->copy;?></h2>
    </div>

      <?php
            $firstStageHidden = 'hidden';
            $secondStageHidden = 'hidden';
            $firstStagePidHidden = 'hidden';
            if(in_array('1', $putproductionInfo->stageList) && (!in_array('2', $putproductionInfo->stageList))){ //仅仅包含第一阶段
                $firstStageHidden = '';
            }
            if(in_array('2', $putproductionInfo->stageList)){
                $secondStageHidden = '';
                if(!in_array('1', $putproductionInfo->stageList)){ //仅包含第二阶段，不包含第一阶段
                    $firstStagePidHidden =  '';
                }
            }
      ?>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
          <table class="table table-form">
            <tbody>
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
                  <td colspan="3"><?php echo html::input('desc', $putproductionInfo->desc, "class='form-control' maxlength='100'");?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->putproduction->outsidePlanId;?></th>
                  <td><?php echo html::select('outsidePlanId', $outsideProjectList, $putproductionInfo->outsidePlanId, "class='form-control chosen' onchange='getInProjectList()'");?></td>
                  <th><?php echo $lang->putproduction->inProjectIds;?></th>
                  <td class="inProjectIdsTd required"><?php echo html::select('inProjectIds[]', $inProjectList, $putproductionInfo->inProjectIds, "class='form-control chosen' multiple");?></td>
              </tr>

              <tr>
                <th><?php echo $lang->putproduction->app;?></th>
                <td colspan="3"><?php echo html::select('app[]', $appList, $putproductionInfo->app, "class='form-control chosen' onchange='getProductName()' multiple");?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->putproduction->productId;?></th>
                  <td colspan="3" class="productTd"><?php echo html::select('productId[]', $productList, $putproductionInfo->productId, "class='form-control chosen' multiple");?></td>
              </tr>
              <tr>
                <th><?php echo $lang->putproduction->demandId;?></th>
                <td colspan="3"><?php echo html::select('demandId[]', $demandList, $putproductionInfo->demandId, "class='form-control chosen' multiple");?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->putproduction->level;?> <i title="<?php echo $lang->putproduction->levelHelp;?>" class="icon icon-help"></i></th>
                  <td><?php echo html::select('level', $lang->putproduction->levelList, $putproductionInfo->level, "class='form-control chosen' onchange='changeLevel()'");?></td>
                  <th>
                      <?php echo $lang->putproduction->property; ?>
                  </th>
                  <td>
                      <?php echo html::select('property[]', $lang->putproduction->propertyList, $putproductionInfo->property, "class='form-control chosen' onchange='changeProperty()' multiple");?>
                  </td>
              </tr>

              <tr>
                 <th><?php echo $lang->putproduction->stage;?> <i title="<?php echo $lang->putproduction->stageHelp;?>" class="icon icon-help"></th>
                 <td class="required">
                     <div class="form-control" id="stage">
                         <?php foreach($lang->putproduction->stageList as $key => $val):?>
                         <div class='group-item checkbox-inline'>
                            <?php echo html::checkbox('stage', array($key => $val), $putproductionInfo->stage,"onclick='setStageInfo();'");?>
                         </div>
                         <?php endforeach;?>
                     </div>
                 </td>

                  <th class="firstStagePidInfo <?php echo $firstStagePidHidden; ?>">
                      <?php echo $lang->putproduction->firstStagePid; ?>
                      <i title="<?php echo $lang->putproduction->firstStagePidHelp;?>" class="icon icon-help">
                  </th>
                  <td class="firstStagePidInfo firstStagePidTd <?php echo $firstStagePidHidden; ?>">
                      <?php echo html::select('firstStagePid', $firstStagePutProductionList, $putproductionInfo->firstStagePid, "class='form-control chosen'");?>
                  </td>
              </tr>

              <tr class="secondStageInfo <?php echo $secondStageHidden;?>">
                  <th><?php echo $lang->putproduction->dataCenter;?></th>
                  <td class="required"><?php echo html::select('dataCenter[]', $lang->putproduction->dataCenterList, $putproductionInfo->dataCenter, "class='form-control chosen' multiple");?></td>

                  <th>
                      <?php echo $lang->putproduction->isPutCentralCloud; ?>
                  </th>
                  <td>
                      <?php echo html::select('isPutCentralCloud', $lang->putproduction->isPutCentralCloudList, $putproductionInfo->isPutCentralCloud, "class='form-control chosen'");?>
                  </td>
              </tr>

              <tr class="firstStageInfo <?php echo  $firstStageHidden;?>">
                  <th><?php echo $lang->putproduction->fileUrlRevision;?>
                      <i title="<?php echo $lang->putproduction->fileUrlRevisionHelp;?>" class="icon icon-help"></i>
                  </th>
                  <td colspan="3"  class="required"><?php echo html::input('fileUrlRevision', $putproductionInfo->fileUrlRevision, "class='form-control' maxlength='300'");?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->putproduction->isReview;?></th>
                  <td colspan="3">
                      <?php echo html::select('isReview', $lang->putproduction->isReviewList, $putproductionInfo->isReview, "class='form-control chosen' onchange='changeIsReview();'");?>
                  </td>
              </tr>
              <tr class="reviewCommentInfo <?php if($putproductionInfo->isReview == 1):?> hidden <?php endif;?>">
                  <th><?php echo $lang->putproduction->reviewComment;?></th>
                  <td colspan="3"><?php echo html::textarea('reviewComment', $putproductionInfo->reviewComment, "placeholder='{$lang->putproduction->reviewCommentPlaceholder}' class='form-control' ");?></td>
              </tr>
              <tr class="secondStageInfo <?php echo $secondStageHidden;?>">
                  <th><?php echo $lang->putproduction->isBusinessCoopera;?></th>
                  <td colspan="3" class="required">
                      <?php echo html::select('isBusinessCoopera', $lang->putproduction->isBusinessCooperaList, $putproductionInfo->isBusinessCoopera, "class='form-control chosen' onchange='changeIsBusinessCoopera();'");?>
                  </td>
              </tr>

              <tr class="secondStageInfo businessCooperaContentInfo  <?php if($putproductionInfo->isBusinessCoopera == '1'):?> hidden <?php endif;?>">
                  <th><?php echo $lang->putproduction->businessCooperaContent;?></th>
                  <td colspan="3" class="required"><?php echo html::textarea('businessCooperaContent', $putproductionInfo->businessCooperaContent, "class='form-control'");?></td>
              </tr>

              <tr class="secondStageInfo secondStageInfo <?php echo $secondStageHidden;?>">
                  <th><?php echo $lang->putproduction->isBusinessAffect;?></th>
                  <td colspan="3" class="required">
                      <?php echo html::select('isBusinessAffect', $lang->putproduction->isBusinessAffectList, $putproductionInfo->isBusinessAffect, "class='form-control chosen' onchange='changeIsBusinessAffect();'");?>
                  </td>
              </tr>

              <tr class="businessAffectInfo <?php if($putproductionInfo->isBusinessAffect == '1'):?> hidden <?php endif;?>">
                  <th><?php echo $lang->putproduction->businessAffect;?></th>
                  <td colspan="3" class="required"><?php echo html::textarea('businessAffect', $putproductionInfo->businessAffect, "class='form-control'");?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->putproduction->remark;?></th>
                  <td colspan="3"><?php echo html::textarea('remark', $putproductionInfo->remark, "class='form-control'");?></td>
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

                      <div class='input-group node-item hidden node<?php echo $nodeCode;?>' style='width:80%' id="<?php echo $nodeCode;?>">
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
                      <?php echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn buttonInfo') . html::commonButton($lang->putproduction->submit, '', 'btn btn-wide btn-primary submitBtn buttonInfo') . html::backButton();?>
                  </td>
              </tr>
            </tbody>
          </table>
        </form>
  </div>
</div>
<input type="hidden" id="responseid">
<?php include '../../common/view/footer.html.php';?>
