<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .input-group-addon{min-width: 150px;} .input-group{margin-bottom: 6px;}
    .checkbox-skipReview {width: 100px; margin-left: 5px;}
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
    .panel{border-color: #ddd;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->change->create;?></h2>
    </div>

    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
        <div class="panel">
        <div class="panel-heading">
            <?php echo $lang->change->projectchangesubtitle;?>
        </div>
        <div class="panel-body">


      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-150px'><?php echo $lang->change->level;?><i class="icon icon-help hover" title="<?php echo $lang->change->changeLevelMark;?>"></i></th>
            <td><?php echo html::select('level', $lang->change->levelList, '', "class='form-control chosen' onchange='changeLevel()'");?></td>
            <td colspan='2'>
              <div class='input-group'>
                  <span class='input-group-addon'><?php echo $lang->change->type; ?></span>
                  <?php echo html::select('type', $lang->change->typeList, '', "class='form-control chosen'");?>
              </div>
            </td>
          </tr>
          <tr>
              <th></th>
              <td colspan="3"><span class="fieldExplainDesc"><?php echo $this->lang->change->levelDesc;?></span></td>
          </tr>

          <tr>
              <th><?php echo $lang->change->category;?></th>
              <td>
                  <?php echo html::select('category', $lang->change->categoryList, '', "class='form-control chosen' onchange='changeCategory();'");?>
              </td>
              <td colspan='2'>
                  <div class='input-group hidden' id="subCategoryDiv">
                      <span class='input-group-addon'><?php echo $lang->change->subCategory; ?></span>
                      <?php echo html::select('subCategory[]', $lang->change->subCategoryList, '', "class='form-control chosen' multiple");?>
                  </div>
              </td>
          </tr>

          <tr>
              <th><?php echo $lang->change->isInteriorPro;?></th>
              <td>
                  <?php echo html::select('isInteriorPro', $lang->change->isInteriorProList, '', "class='form-control chosen' onchange='setReviewNodes()'");?>
              </td>
              <td colspan='2'>
              </td>
          </tr>
          <tr>
              <th></th>
              <td colspan="3"><span class="fieldExplainDesc"><?php echo $this->lang->change->isInteriorProDesc;?></span></td>
          </tr>

          <tr>
              <th><?php echo $lang->change->isMasterPro;?></th>
              <td>
                  <?php echo html::select('isMasterPro', $lang->change->isMasterProList, '', "class='form-control chosen' onchange='changeIsMasterPro()'");?>
              </td>
              <td colspan='2'>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->change->isSlavePro; ?></span>
                      <?php echo html::select('isSlavePro', $lang->change->isSlaveProList, '', "class='form-control chosen' onchange='setReviewNodes()'");?>
                  </div>
              </td>
          </tr>

          <tr>
              <th></th>
              <td colspan="3"><span class="fieldExplainDesc"><?php echo $this->lang->change->isMasterProDesc;?></span></td>
          </tr>

          <tr>
              <th><?php echo $lang->change->mailUsers;?></th>
              <td colspan='3' id="mailUsersTd">
                  <?php echo html::select('mailUsers[]', $users, '', "class='form-control chosen' multiple");?>
              </td>
          </tr>

          <tr class="nodes hidden">
            <th><?php echo $lang->change->reviewNodes;?></th>
              <td colspan='3'>
                  <?php
                  foreach($lang->change->reviewNodeCodeLabelList as $key => $nodeName):
                      $nodeCode = $key;
                      $currentAccounts = '';
                      if(in_array($nodeCode, $lang->change->defaultAllUserNodeCodeList)){
                          $allowSelectUsers = $users;
                      }else{
                          $allowSelectUsers = $reviewers[$key];
                      }
                      if(isset($reviewerAccounts[$key])):
                          $currentAccounts = implode(',', $reviewerAccounts[$key]);
                      endif;
                      //是否展示跳过审核按钮
                      $isShowSkipReview = false;
                      if(in_array($key, $lang->change->allowSkipReviewNodeCodeList)):
                          $isShowSkipReview = true;
                      endif;
                      js::set('node_'.$key.'_reviewers', $currentAccounts);
                  ?>
                      <div class="table-row node-item node<?php echo $key;?>" id="<?php echo $key;?>">
                          <div class='table-col reviewer-node-info-col'>
                              <div class='input-group'>
                                  <span class='input-group-addon'><?php echo $nodeName;?></span>
                                  <?php echo html::select("nodes[$key][]", $allowSelectUsers, $currentAccounts, "class='form-control chosen' required multiple");?>
                                  <!---隐藏域查询哪些层级是需要设置的节点-->
                                  <input type="hidden" name = "requiredNodes[<?php echo $key;?>]" id="requiredNodes-<?php echo $key;?>" class="requiredNodes" value="0">
                              </div>
                          </div>

                          <!--<div class="table-col c-actions">
                              <?php /*if($isShowSkipReview):*/?>
                                  <div class='checkbox-primary checkbox-skipReview'>
                                      <input type="checkbox" name="skipReviewNode[]" id="skipReviewNode_<?php /*echo  $key;*/?>" value="<?php /*echo  $key;*/?>" onclick="setSkipReviewNode(this.value);">
                                      <label><?php /*echo  $lang->change->skipReviewDesc */?></label>
                                  </div>
                              <?php /*else:*/?>
                                  &nbsp;
                              <?php /*endif;*/?>
                          </div>-->
                      </div>
                  <?php endforeach;?>

              </td>
          </tr>

          <!--<tr>
            <th><?php /*echo $lang->change->consumed;*/?></th>
            <td colspan='2'><?php /*echo html::input('consumed', '', "class='form-control'");*/?></td>
          </tr>-->
          <tr>
            <th><?php echo $lang->change->reason;?></th>
            <td colspan='3'><?php echo html::textarea('reason', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->change->content;?>
             <i class="icon icon-help hover" title="<?php echo $lang->change->contentTip?>"></i>
            </th>
            <td colspan='3'><?php echo html::textarea('content', '', "class='form-control' placeholder=' ".htmlspecialchars($lang->change->contentTip)."'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->change->effect;?></th>
            <td colspan='3'><?php echo html::textarea('effect', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->files;?></th>
            <td colspan='3'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
          </tr>


        </tbody>
      </table>
        </div>
        </div>
        <div class="panel hidden" id="projectplanshowpanel">
            <div class="panel-heading">
                <?php echo $lang->change->projectplantitle;?>
            </div>
            <div class="panel-body">
        <table class="table table-form">
            <tbody>
            <tr  class="">
                <th><?php echo $lang->change->innerprojectname;?></th>
                <td colspan='3' class="required"><?php echo html::input('innerprojectname', $projectplantext->innerprojectname, "class='form-control ' ");?></td>
            </tr>
            <tr  class="projectplanedit ">
                <th><?php echo $lang->change->projectowner;?></th>
                <td colspan='3' class="required"><?php echo html::select('projectowner', $users, $projectplantext->projectowner, "class='form-control chosen'");?></td>
            </tr>
            <tr  class="projectplanedit">
                <th><?php echo $lang->change->ownerphone;?></th>
                <td colspan='3' class="required"><?php echo html::input('ownerphone', $projectplantext->ownerphone, "class='form-control ' ");?></td>

            </tr>
            </tbody>
        </table>
            </div>
        </div>
        <table class="table table-form">
            <tbody>
            <tr>
                <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
            </tr>
            </tbody>
        </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>