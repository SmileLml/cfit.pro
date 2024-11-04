<?php
/**
 * The batch create view of story module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     story
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class='main-content fade'>
  <div class='main-header'>
    <h2>
      <?php echo $lang->bug->batchCreate;?>
    </h2>
    <div class="pull-right btn-toolbar">
      <?php if(common::hasPriv('file', 'uploadImages')) echo html::a($this->createLink('file', 'uploadImages', 'module=bug&params=' . helper::safe64Encode("productID=$productID&executionID=$executionID&moduleID=$moduleID")), $lang->uploadImages, '', "data-toggle='modal' data-type='iframe' class='btn btn-primary' data-width='70%'")?>
      <button type='button' data-toggle='modal' data-target="#importLinesModal" class="btn btn-primary"><?php echo $lang->pasteText;?></button>
      <?php $customLink = $this->createLink('custom', 'ajaxSaveCustomFields', 'module=bug&section=custom&key=batchCreateFields')?>
      <?php include '../../common/view/customfield.html.php';?>
    </div>
  </div>

  <?php
  $visibleFields  = array();
  $requiredFields = array();
  foreach(explode(',', $showFields) as $field)
  {
      if($field) $visibleFields[$field] = '';
  }
  foreach(explode(',', $config->bug->create->requiredFields) as $field)
  {
      if($field)
      {
          $requiredFields[$field] = '';
          if(strpos(",{$config->bug->list->customBatchCreateFields},", ",{$field},") !== false) $visibleFields[$field] = '';
      }
  }
  ?>
  <form class='main-form' method='post' target='hiddenwin' id='batchCreateForm'>
    <div class="table-responsive">
      <table class='table table-form'>
        <thead>
          <tr>
            <th class='w-50px'><?php echo $lang->idAB;?></th>
            <th class='w-130px required project-input-item'><?php echo $lang->bug->project;?></th>
            <th class='w-130px'><?php echo $lang->bug->execution;?></th>
            <th class='w-120px<?php echo zget($requiredFields, 'module', '', ' required');?>'> <?php echo $lang->bug->module;?></th>
            <th class='w-100px<?php echo zget($visibleFields, 'openedBuild', ' hidden');?>'><?php echo $lang->bug->openedBuild;?></th>
            <th class='c-title required'><?php echo $lang->bug->title;?></th>
            <th class='w-150px<?php echo zget($visibleFields, 'steps', ' hidden') . zget($requiredFields, 'steps', '', ' required');?>'><?php echo $lang->bug->steps;?></th>
            <th class='w-100px'><?php echo $lang->bug->type;?></th>
            <th class='w-100px'><?php echo $lang->bug->childType;?></th>
            <th class='w-100px<?php echo zget($visibleFields, 'deadline', ' hidden') . zget($requiredFields, 'deadline', '', ' required');?>'><?php echo $lang->bug->deadline;?></th>
            <th class='w-80px<?php echo zget($visibleFields, 'pri', ' hidden');?>'><?php echo $lang->bug->pri;?></th>
            <th class='w-80px<?php echo zget($visibleFields, 'severity', ' hidden');?>'><?php echo $lang->bug->severity;?></th>
            <th class='w-120px<?php echo zget($visibleFields, 'os', ' hidden') . zget($requiredFields, 'os', '', ' required');?>'><?php echo $lang->bug->os;?></th>
            <th class='w-100px<?php echo zget($visibleFields, 'browser', ' hidden') . zget($requiredFields, 'browser', '', ' required');?>'><?php echo $lang->bug->browser;?></th>
            <th class='w-100px<?php echo zget($visibleFields, 'keywords', ' hidden') . zget($requiredFields, 'keywords', '', ' required');?>'><?php echo $lang->bug->keywords;?></th>
            <?php
            $extendFields = $this->bug->getFlowExtendFields();
            foreach($extendFields as $extendField) echo "<th class='w-100px'>{$extendField->name}</th>";
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
          $moduleOptionMenu        += array('ditto' => $lang->bug->ditto);
          $projects                += array('ditto' => $lang->bug->ditto);
          $executions              += array('ditto' => $lang->bug->ditto);
          $lang->bug->priList      += array('ditto' => $lang->bug->ditto);
          $lang->bug->severityList += array('ditto' => $lang->bug->ditto);
          $lang->bug->osList       += array('ditto' => $lang->bug->ditto);
          $lang->bug->browserList  += array('ditto' => $lang->bug->ditto);
          ?>
          <?php $i = 0; ?>
          <?php if(!empty($titles)):?>
          <?php foreach($titles as $bugTitle => $fileName):?>
          <?php
          $moduleID    = $i == 0 ? $moduleID : 'ditto';
          $executionID = $i == 0 ? $executionID : 'ditto';
          $projectID   = $i == 0 ? $fromProjectID : 'ditto';
          $pri         = $i == 0 ? 2  : 'ditto';
          $severity    = $i == 0 ? 3  : 'ditto';
          $os          = $i == 0 ? '' : 'ditto';
          $browser     = $i == 0 ? '' : 'ditto';
          ?>
          <tr>
            <td class='text-center'><?php echo $i+1;?></td>
            <td class="project-input-item" style='overflow:visible'><?php echo html::select("projects[$i]", $projects, $projectID, "class='form-control chosen' onchange='loadExecutionPairs(this.value, $i)'");?></td>
            <td style='overflow:visible'><?php echo html::select("executions[$i]", $executions, $executionID, "class='form-control chosen' onchange='loadExecutionBuilds($productID, this.value, $i)'");?></td>
            <td><?php echo html::select("modules[$i]", $moduleOptionMenu, $moduleID, "class='form-control chosen'");?></td>
            <td class='<?php echo zget($visibleFields, 'openedBuild', ' hidden');?>' id='buildBox<?php echo $i;?>'><?php echo html::select("openedBuilds[$i][]", $builds, 'trunk', "class='form-control chosen' multiple");?></td>
            <td>
              <div class='input-group'>
                <div class="input-control has-icon-right">
                  <?php echo html::input("title[$i]", $bugTitle, "class='form-control title-import'") . html::hidden("uploadImage[$i]", $fileName);?>
                  <div class="colorpicker">
                    <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown"><span class="cp-title"></span><span class="color-bar"></span><i class="ic"></i></button>
                    <ul class="dropdown-menu clearfix">
                      <li class="heading"><?php echo $lang->story->colorTag;?><i class="icon icon-close"></i></li>
                    </ul>
                    <?php echo html::hidden("color[$i]", '', "data-provide='colorpicker' data-icon='color' data-wrapper='input-control-icon-right'  data-update-color='#title\\[{$i}\\]'");?>
                  </div>
                </div>
              </div>
            </td>
            <td class='<?php echo zget($visibleFields, 'steps', 'hidden')?>'><?php echo html::textarea("stepses[$i]", '', "rows='1' class='form-control autosize'");?></td>
            <td style='overflow:visible'>    <?php echo html::select("types[$i]", $lang->bug->typeList, $type, "class='form-control' onchange='loadChildTypeList(this.value, $i)'");?></td>
            <td style='overflow:visible'>    <?php echo html::select("childTypes[$i]", $childTypeList, $childType, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'deadline', 'hidden')?>'><?php echo html::input("deadlines[$i]", '', "class='form-control form-date'");?></td>
            <td class='<?php echo zget($visibleFields, 'pri', 'hidden')?>' style='overflow:visible'>     <?php echo html::select("pris[$i]", $lang->bug->priList, $pri, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'severity', 'hidden')?>' style='overflow:visible'><?php echo html::select("severities[$i]", $lang->bug->severityList, $severity, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'os', 'hidden')?>' style='overflow:visible'>      <?php echo html::select("oses[$i]", $lang->bug->osList, $os, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'browser', 'hidden')?>' style='overflow:visible'> <?php echo html::select("browsers[$i]", $lang->bug->browserList, $browser, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'keywords', 'hidden')?>'><?php echo html::input("keywords[$i]", '', "class='form-control'");?></td>
            <?php foreach($extendFields as $extendField) echo "<td" . (($extendField->control == 'select' or $extendField->control == 'multi-select') ? " style='overflow:visible'" : '') . ">" . $this->loadModel('flow')->getFieldControl($extendField, '', $extendField->field . "[$i]") . "</td>";?>
          </tr>
          <?php $i++;?>
          <?php endforeach;?>
          <?php endif;?>
          <?php $nextStart = $i;?>
          <?php for($i = $nextStart; $i < $config->bug->batchCreate; $i++):?>
          <?php
          $moduleID    = $i - $nextStart == 0 ? $moduleID : 'ditto';
          $projectID   = $i - $nextStart == 0 ? $fromProjectID : 'ditto';
          $executionID = $i - $nextStart == 0 ? $executionID : 'ditto';
          $pri         = $i - $nextStart == 0 ? 2  : 'ditto';
          $severity    = $i - $nextStart == 0 ? 3  : 'ditto';
          $os          = $i - $nextStart == 0 ? '' : 'ditto';
          $browser     = $i - $nextStart == 0 ? '' : 'ditto';
          ?>
          <tr>
            <td><?php echo $i+1;?></td>
            <td class="project-input-item"><?php echo html::select("projects[$i]", $projects, $projectID, "class='form-control chosen' onchange='loadExecutionPairs(this.value, $i)'");?></td>
            <td style='overflow:visible'><?php echo html::select("executions[$i]", $executions, $executionID, "class='form-control chosen' onchange='loadExecutionBuilds($productID, this.value, $i)'");?></td>
            <td><?php echo html::select("modules[$i]", $moduleOptionMenu, $moduleID, "class='form-control chosen'");?></td>
            <td class='<?php echo zget($visibleFields, 'openedBuild', ' hidden');?>' id='buildBox<?php echo $i;?>'><?php echo html::select("openedBuilds[$i][]", $builds, '', "class='form-control chosen' multiple");?></td>
            <td>
              <div class='input-group'>
                <div class="input-control has-icon-right">
                  <?php echo html::input("title[$i]", '', "class='form-control title-import'");?>
                  <div class="colorpicker">
                    <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown"><span class="cp-title"></span><span class="color-bar"></span><i class="ic"></i></button>
                    <ul class="dropdown-menu clearfix">
                      <li class="heading"><?php echo $lang->bug->colorTag;?><i class="icon icon-close"></i></li>
                    </ul>
                    <?php echo html::hidden("color[$i]", '', "data-provide='colorpicker' data-icon='color' data-wrapper='input-control-icon-right'  data-update-color='#title\\[$i\\]'");?>
                  </div>
                </div>
              </div>
            </td>
            <td class='<?php echo zget($visibleFields, 'steps', 'hidden')?>'><?php echo html::textarea("stepses[$i]", '', "rows='1' class='form-control autosize'");?></td>
            <td style='overflow:visible'>    <?php echo html::select("types[$i]", $lang->bug->typeList, $type, "class='form-control' onchange='loadChildTypeList(this.value, $i)'");?></td>
            <td style='overflow:visible'>    <?php echo html::select("childTypes[$i]", $childTypeList, $childType, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'deadline', 'hidden')?>'><?php echo html::input("deadlines[$i]", '', "class='form-control form-date'");?></td>
            <td class='<?php echo zget($visibleFields, 'pri', 'hidden')?>' style='overflow:visible'>     <?php echo html::select("pris[$i]", $lang->bug->priList, $pri, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'severity', 'hidden')?>' style='overflow:visible'><?php echo html::select("severities[$i]", $lang->bug->severityList, $severity, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'os', 'hidden')?>' style='overflow:visible'>      <?php echo html::select("oses[$i]", $lang->bug->osList, $os, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'browser', 'hidden')?>' style='overflow:visible'> <?php echo html::select("browsers[$i]", $lang->bug->browserList, $browser, "class='form-control'");?></td>
            <td class='<?php echo zget($visibleFields, 'keywords', 'hidden')?>'><?php echo html::input("keywords[$i]", '', "class='form-control'");?></td>
            <?php foreach($extendFields as $extendField) echo "<td" . (($extendField->control == 'select' or $extendField->control == 'multi-select') ? " style='overflow:visible'" : '') . ">" . $this->loadModel('flow')->getFieldControl($extendField, '', $extendField->field . "[$i]") . "</td>";?>
          </tr>
          <?php endfor;?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan='<?php echo count($visibleFields) + 3?>' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </form>
</div>
<table class='template' id='trTemp'>
  <tbody>
    <tr>
      <td>%s</td>
      <td class="project-input-item" style='overflow:visible'><?php echo html::select("projects[%s]", $projects, $projectID, "class='form-control chosen' onchange='loadExecutionPairs(this.value, \"%s\")'");?></td>
      <td style='overflow:visible'><?php echo html::select("executions[%s]", $executions, $executionID, "class='form-control chosen' onchange='loadExecutionBuilds($productID, this.value, \"%s\")'");?></td>
      <td><?php echo html::select("modules[%s]", $moduleOptionMenu, $moduleID, "class='form-control'");?></td>
      <td class='<?php echo zget($visibleFields, 'openedBuild', ' hidden');?>' id='buildBox%s'><?php echo html::select("openedBuilds[%s][]", $builds, '', "class='form-control chosen' multiple");?></td>
      <td>
        <div class='input-group'>
          <div class="input-control has-icon-right">
            <?php echo html::input("title[%s]", '', "class='form-control title-import'");?>
            <div class="colorpicker">
              <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown"><span class="cp-title"></span><span class="color-bar"></span><i class="ic"></i></button>
              <ul class="dropdown-menu clearfix">
                <li class="heading"><?php echo $lang->story->colorTag;?><i class="icon icon-close"></i></li>
              </ul>
              <?php echo html::hidden("color[%s]", '', "data-provide='colorpicker-later' data-icon='color' data-wrapper='input-control-icon-right'  data-update-color='#title\\[%s\\]'");?>
            </div>
          </div>
        </div>
      </td>
      <td class='<?php echo zget($visibleFields, 'steps', 'hidden')?>'><?php echo html::textarea("stepses[%s]", '', "rows='1' class='form-control autosize'");?></td>
      <td style='overflow:visible'>    <?php echo html::select("types[%s]", $lang->bug->typeList, $type, "class='form-control' onchange='loadChildTypeList(this.value, \"%s\")'");?></td>
      <td style='overflow:visible'>    <?php echo html::select("childTypes[%s]", $childTypeList, $childType, "class='form-control'");?></td>
      <td class='<?php echo zget($visibleFields, 'deadline', 'hidden')?>'><?php echo html::input("deadlines[%s]", '', "class='form-control form-date'");?></td>
      <td class='<?php echo zget($visibleFields, 'pri', 'hidden')?>' style='overflow:visible'>     <?php echo html::select("pris[%s]", $lang->bug->priList, $pri, "class='form-control'");?></td>
      <td class='<?php echo zget($visibleFields, 'severity', 'hidden')?>' style='overflow:visible'><?php echo html::select("severities[%s]", $lang->bug->severityList, $severity, "class='form-control'");?></td>
      <td class='<?php echo zget($visibleFields, 'os', 'hidden')?>' style='overflow:visible'>      <?php echo html::select("oses[%s]", $lang->bug->osList, $os, "class='form-control'");?></td>
      <td class='<?php echo zget($visibleFields, 'browser', 'hidden')?>' style='overflow:visible'> <?php echo html::select("browsers[%s]", $lang->bug->browserList, $browser, "class='form-control'");?></td>
      <td class='<?php echo zget($visibleFields, 'keywords', 'hidden')?>'><?php echo html::input("keywords[%s]", '', "class='form-control'");?></td>
      <?php foreach($extendFields as $extendField) echo "<td" . (($extendField->control == 'select' or $extendField->control == 'multi-select') ? " style='overflow:visible'" : '') . ">" . $this->loadModel('flow')->getFieldControl($extendField, '', $extendField->field . "[%s]") . "</td>";?>
    </tr>
  </tbody>
</table>
<?php js::set('branch', $branch);?>
<?php js::set('productID', $productID);?>
<?php js::set('fromProjectID', $fromProjectID);?>

<script>
function loadProductLinkProject(applicationID, productID, currentIndex)
{
    var projectID = 0;
    var link      = createLink('rebirth', 'ajaxGetProductLinkProject', 'applicationID=' + applicationID + '&productID=' + productID + '&projectID=' + projectID + '&currentIndex=' + currentIndex);
    $.get(link, function(data)
    {
        if(data)
        {
            $('#projects' + currentIndex).replaceWith(data);
            $('#projects' + currentIndex + '_chosen').remove();
            $('#projects' + currentIndex).chosen();
        }
    });
}

function loadExecutionPairs(projectID, currentIndex)
{
    if(projectID == 'ditto') return false;
    var link = createLink('product', 'ajaxGetExecutions', 'productID=' + productID + '&projectID=' + projectID + '&branch=0&currentIndex=' + currentIndex);
    $.get(link, function(data)
    {
        if(data)
        {
            $('#executions' + currentIndex).replaceWith(data);
            $('#executions' + currentIndex + '_chosen').remove();
            $('#executions' + currentIndex).chosen();
        }
    });
}
function loadChildTypeList(type, currentIndex)
{
    var link = createLink('bug', 'ajaxGetChildTypeList', 'type=' + type + '&currentIndex=' + currentIndex);
    $.get(link, function(data)
    {
        if(data)
        {
            $('#childTypes' + currentIndex).replaceWith(data);
            //$('#childTypes' + currentIndex + '_chosen').remove();
            //$('#childTypes' + currentIndex).chosen();
        }
    });
}
//批量增加测试实验室缺陷
if(fromProjectID > 0)
{
  loadExecutionPairs(fromProjectID,0);
  $('.project-input-item').addClass('hidden')
}
</script>
<?php include '../../common/view/pastetext.html.php';?>
<?php include '../../common/view/footer.html.php';?>
