<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->review->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='reviewcreate'>
      <table class="table table-form">
        <tbody>
        <tr>
            <th class='w-130px'><?php echo $lang->review->object;?></th>
            <td >
                <?php echo html::select('object[]', empty($objectList) ? $lang->review->objectList : $objectList, '', "class='form-control chosen' multiple");?>
            </td>
        </tr>
          <tr>
              <th><?php echo $lang->review->title;?></th>
              <td ><?php echo html::input('title', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->type;?></th>
            <td ><?php echo html::select('type', $lang->review->typeList, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->review->qualityCm;?></th>
              <td ><?php echo html::select('qualityCm', $users,array_keys($cmList)[0],"class='form-control chosen'");?></td>
          </tr>
        <!--  <tr>
            <th><?php /*echo $lang->review->grade;*/?></th>
            <td ><?php /*echo html::select('grade', $lang->review->gradeList, '', "class='form-control chosen'");*/?></td>
          </tr>-->
          <tr>
              <th><?php echo $lang->review->qapre;?></th>
              <td ><?php echo html::select('qa', '','',"class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->review->reviewer;?></th>
              <td ><?php echo html::select('reviewer', '', '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->owner;?></th>
            <td ><?php echo html::select('owner[]', '', '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->expert;?></th>
            <td ><?php echo html::select('expert[]', '', '', "class='form-control chosen'  multiple");?></td>
              <td class='muted'>
                  <div class="expertTip"><span > <?php echo $lang->review->expertTip?></span></div>
              </td>
          </tr>
          <tr id="reviewedBy">
              <th><?php echo $lang->review->reviewedBy;?></th>
              <td ><?php echo html::select('reviewedBy[]', $outsideList1, '', "class='form-control chosen' multiple");?></td>
              <td class='muted'>
                  <div class="reviewedByTip"><span > <?php echo $lang->review->reviewedByTip?></span></div>
              </td>
          </tr>
          <tr id="outside">
            <th><?php echo $lang->review->outside;?></th>
            <td ><?php echo html::select('outside[]', $outsideList2, '',"class='form-control chosen' multiple ");?></td>
              <td class='muted'>
                  <div class="outsideTip"><span > <?php echo $lang->review->outsideTip?></span></div>
              </td>
          </tr>
        <tr>
            <th><?php echo $lang->review->relatedUsers;?></th>
            <td ><?php echo html::select('relatedUsers[]', $users, '', "class='form-control chosen'  multiple");?></td>
            <td class='muted'>
                <div class="relatedUsersTip"><span > <?php echo $lang->review->relatedUsersTip?></span></div>
            </td>
        </tr>
          <tr>
            <th><?php echo $lang->review->deadline;?></th>
            <td ><?php echo html::input('deadline', '', "class='form-date form-control' ");?></td>
          </tr>
         <!-- <tr>
              <th><?php /*echo $lang->review->consumed;*/?></th>
              <td ><?php /*echo html::input('consumed', '', "class='form-control'");*/?></td>
          </tr>-->
        <tr>
            <th><?php echo $lang->review->mainRelationInfo;?></th>
            <td ><?php
                $tempslaveProjectPlanStr = '';
                if($mainRelationInfo){ //从项目，查找属于哪些主项目
                    foreach($mainRelationInfo as $mainRelation){
                        $tempslaveProjectPlanStr .= zget($relationProjectplanList,$mainRelation->mainPlanID,'').',';
                    }
                }else{
                    $tempslaveProjectPlanStr =  $lang->review->noRelationRecord;
                }
                echo html::input('mainRelationInfo', trim($tempslaveProjectPlanStr,','), "class='form-control' readonly");?>
            </td>
         </tr>
        <tr>
           <th><?php echo $lang->review->slaveRelationInfo;?></th>
           <td>
               <?php
                $tempslaveProjectPlanStr = '';
                if($slaveRelationInfo){ //如果是主项目，查找从项目信息
                    $slaveRelationArr = explode(",", $slaveRelationInfo->slavePlanID); //从项目
                    foreach($slaveRelationArr as $slave){
                        $tempslaveProjectPlanStr .= zget($relationProjectplanList,$slave,'').',';
                    }
                }else{
                    $tempslaveProjectPlanStr =  $lang->review->noRelationRecord;
                }
                echo html::input('slaveRelationInfo', trim($tempslaveProjectPlanStr,','), "class='form-control' readonly");?>
           </td>
        </tr>
          <tr>
            <th><?php echo $lang->review->comment;?></th>
            <td ><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->files;?></th>
              <td class = 'required'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
              <td class='muted'>
                  <div class="fileOverSize"><span > <?php echo sprintf($lang->review->fileOverSize, $this->config->review->fileSize->fileSize);?></span></div>
              </td>
          </tr>
          <tr>
            <td colspan='3' class='form-actions text-center'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>

<?php js::set('projectID', $projectID)?>
<?php js::set('reviewText', $lang->review->common)?>
<?php js::set('mark', $mark);?>
<?php js::set('bearDept', $bearDept);?>
<?php include '../../../common/view/footer.html.php';?>
