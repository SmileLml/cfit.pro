<?php
/**
 * The create view of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: create.html.php 4903 2013-06-26 05:32:59Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
include '../../common/view/header.html.php';
include '../../common/view/kindeditor.html.php';
include '../../common/view/datepicker.html.php';
js::set('holders', $lang->bug->placeholder);
js::set('page', 'create');
js::set('createRelease', $lang->release->create);
js::set('createBuild', $lang->build->create);
js::set('refresh', $lang->refresh);
js::set('flow', $config->global->flow);
js::set('stepsRequired', $stepsRequired);
js::set('stepsNotEmpty', $lang->bug->stepsNotEmpty);
js::set('isStepsTemplate', $isStepsTemplate);
js::set('oldProjectID', $projectID);
js::set('blockID', $blockID);
js::set('moduleID', $moduleID);
js::set('caseID', $caseID);
?>
<style>
    .chosen-disabled .chosen-choices{
        background-color: #eee;
    }
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->bug->create;?></h2>
      <div class="pull-right btn-toolbar">
        <?php $customLink = $this->createLink('custom', 'ajaxSaveCustomFields', 'module=bug&section=custom&key=createFields')?>
        <?php include '../../common/view/customfield.html.php';?>
      </div>
    </div>
    <?php
    foreach(explode(',', $config->bug->create->requiredFields) as $field)
    {
        if($field and strpos($showFields, $field) === false) $showFields .= ',' . $field;
    }
    ?>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <?php if($this->app->openApp == 'project'):?>
          <tr>
            <th class='w-110px'><?php echo $lang->bug->applicationID;?></th>
            <td>
              <div class='input-group'>
                <?php echo html::select('applicationName', $applicationList, $applicationID, 'class="form-control chosen" disabled="disabled"');?>
              </div>
            </td>
          </tr>
          <?php endif;?>
          <tr>
            <th class='w-110px'><?php echo $lang->bug->product;?></th>
            <td>
              <div class='input-group'>
                <?php $productID = (int)$productID;?>
                <?php echo html::select('product', $products, $productID, "onchange='loadAll(this.value);' class='form-control chosen control-product'");?>
                <?php echo html::hidden('applicationID', $applicationID);?>
              </div>
            </td>
            <td>
              <div class="input-group">
                  <div class='input-group'>
                    <span class='input-group-addon fix-border'><?php echo $lang->bug->productplan;?></span>
                    <?php echo html::select('linkPlan[]', $linkPlan, $planID, "class='form-control chosen productplan' multiple=multiple id='planIdBox'");?>
                </div>
                </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->bug->module?></th>
            <td colspan="2">
              <div class='input-group' id='moduleIdBox'>
                <?php
                echo html::select('module', $moduleOptionMenu, $moduleID, "onchange='loadModuleRelated()' class='form-control chosen'");
                ?>
              </div>
            </td>
          </tr>
          <tr>
            <th>
              <?php echo $lang->bug->project;?>
            </th>
            <td>
              <div class='table-row'>
                <div class='table-col' id='projectBox'>
                  <?php echo html::select('project', $projects, $projectID, "class='form-control chosen' onchange='loadProductExecutions({$productID}, this.value)'");?>
                </div>
                <div class='table-col'>
                  <div class='input-group' id='executionIdBox'>
                    <span class='input-group-addon fix-border'><?php echo $lang->bug->execution;?></span>
                    <?php echo html::select('execution', $executions, '', "class='form-control chosen' onchange='loadExecutionRelated(this.value)'");?>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <div class='input-group' id='buildBox'>
                <span class="input-group-addon"><?php echo $lang->bug->openedBuild?></span>
                <!-- todo  onchange='loadProductPlan(this.value)' 选择关联制版 联动选择所属产品版本 -->
                <?php echo html::select('openedBuild[]', $builds, empty($buildID) ? '' : $buildID, "multiple=multiple class='chosen form-control' onchange='loadProductLinkPlans()'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><nobr><?php echo $lang->bug->lblAssignedTo;?></nobr></th>
            <td>
              <div class='input-group'>
                <?php echo html::select('assignedTo', $productMembers, $assignedTo, "class='form-control chosen'");?>
                <span class='input-group-btn'><?php echo html::commonButton($lang->bug->allUsers, "class='btn btn-default' onclick='loadAllUsers()' data-toggle='tooltip'");?></span>
              </div>
            </td>
          <?php $showDeadline = strpos(",$showFields,", ',deadline,') !== false;?>
          <?php if($showDeadline):?>
            <td id='deadlineTd'>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->bug->deadline?></span>
                <span><?php echo html::input('deadline', $deadline, "class='form-control form-date'");?></span>
              </div>
            </td>
          </tr>
          <?php endif;?>
          <?php $showOS      = strpos(",$showFields,", ',os,')      !== false;?>
          <?php $showBrowser = strpos(",$showFields,", ',browser,') !== false;?>
          <tr>
            <th><?php echo $lang->bug->type;?></th>
            <td>
              <div class='table-row'>
                <div class='table-col' id='typeBox'>
                  <?php echo html::select('type', $lang->bug->typeList, $type, "class='form-control chosen'");?>
                </div>
                <div class='table-col' id='childTypeBox'>
                  <div class='input-group'>
                    <span class='input-group-addon fix-border'><?php echo $lang->bug->childType;?></span>
                    <?php echo html::select('childType', $childTypeList, $childType, "class='form-control chosen'");?>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <div class='table-row' style='width: 100%;'>
                <?php if($showOS):?>
                <div class='table-col'>
                  <div class='input-group'>
                    <span class='input-group-addon'><?php echo $lang->bug->os?></span>
                    <?php echo html::select('os', $lang->bug->osList, $os, "class='form-control chosen'");?>
                  </div>
                </div>
                <?php endif;?>
                <?php if($showBrowser):?>
                <div class='table-col'>
                  <div class='input-group'>
                    <span class='input-group-addon fix-border'><?php echo $lang->bug->browser?></span>
                    <?php echo html::select('browser', $lang->bug->browserList, $browser, "class='form-control chosen'");?>
                  </div>
                </div>
                <?php endif;?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->bug->case;?></th>
            <td>
              <div class="input-group title-group">
                <div class="input-control has-icon-right">
                  <?php echo html::select('case', array(0 => ''), 0, "class='form-control chosen'");?>
                </div>
              </div>
            </td>
            <td>
              <div class='table-row'>
                <?php if(strpos(",$showFields,", ',severity,') !== false): // begin print severity selector ?>
                <div class='table-col' style='width: 50%;'>
                  <div class='input-group'>
                    <span class="input-group-addon"><?php echo $lang->bug->severity;?></span>
                    <?php
                    $hasCustomSeverity = false;
                    foreach($lang->bug->severityList as $severityKey => $severityValue)
                    {
                        if(!empty($severityKey) and (string)$severityKey != (string)$severityValue)
                        {
                            $hasCustomSeverity = true;
                            break;
                        }
                    }
                    ?>
                    <?php if($hasCustomSeverity):?>
                    <?php echo html::select('severity', (array)$lang->bug->severityList, $severity, "class='form-control'");?>
                    <?php else: ?>
                    <div class="input-group-btn pri-selector" data-type="severity">
                      <button type="button" class="btn dropdown-toggle br-0" data-toggle="dropdown">
                        <span class="pri-text"><span class="label-severity" data-severity="<?php echo $severity;?>" title="<?php echo $severity;?>"></span></span> &nbsp;<span class="caret"></span>
                      </button>
                      <div class='dropdown-menu pull-right'>
                        <?php echo html::select('severity', (array)$lang->bug->severityList, $severity, "class='form-control' data-provide='labelSelector' data-label-class='label-severity'");?>
                      </div>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
                <?php endif;?>

                <?php if(strpos(",$showFields,", ',pri,') !== false): // begin print pri selector?>
                <div class='table-col'>
                  <div class='input-group'>
                    <span class="input-group-addon fix-border br-0"><?php echo $lang->bug->pri;?></span>
                    <?php
                    $hasCustomPri = false;
                    foreach($lang->bug->priList as $priKey => $priValue)
                    {
                        if(!empty($priKey) and (string)$priKey != (string)$priValue)
                        {
                            $hasCustomPri = true;
                            break;
                        }
                    }
                    $priList = $lang->bug->priList;
                    if(end($priList)) unset($priList[0]);
                    if(!isset($priList[$pri]))
                    {
                        reset($priList);
                        $pri = key($priList);
                    }
                    ?>
                    <?php if($hasCustomPri):?>
                    <?php echo html::select('pri', (array)$priList, $pri, "class='form-control'");?>
                    <?php else: ?>
                    <div class="input-group-btn pri-selector" data-type="pri">
                      <button type="button" class="btn dropdown-toggle br-0" data-toggle="dropdown">
                        <span class="pri-text"><span class="label-pri label-pri-<?php echo empty($pri) ? '0' : $pri?>" title="<?php echo $pri?>"><?php echo $pri?></span></span> &nbsp;<span class="caret"></span>
                      </button>
                      <div class='dropdown-menu pull-right'>
                        <?php echo html::select('pri', (array)$priList, $pri, "class='form-control' data-provide='labelSelector' data-label-class='label-pri'");?>
                      </div>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
                <?php endif;?>

                <?php
                $sessionString  = $config->requestType == 'PATH_INFO' ? '?' : '&';
                $sessionString .= session_name() . '=' . session_id();
                ?>
                <?php if(!empty($file) and common::hasPriv('file', 'download')):?>
                <div class="table-col" style="width: 120px;">
                  <div class='input-group text-right' style='line-height: 30px;'>
                    <span>
                    <?php
                    $uploadDate = $lang->file->uploadDate . substr($file->addedDate, 0, 10);
                    $fileTitle  = "<i class='icon icon-file-text'></i> &nbsp;" . $file->title;
                    if(strpos($file->title, ".{$file->extension}") === false && $file->extension != 'txt') $fileTitle .= ".{$file->extension}";
                    $imageWidth = 0;
                    if(stripos('jpg|jpeg|gif|png|bmp', $file->extension) !== false)
                    {
                        $imageSize  = $this->file->getImageSize($file);
                        $imageWidth = $imageSize ? $imageSize[0] : 0;
                    }

                    $fileSize = 0;
                    /* Show size info. */
                    if($file->size < 1024)
                    {
                        $fileSize = $file->size . 'B';
                    }
                    elseif($file->size < 1024 * 1024)
                    {
                        $file->size = round($file->size / 1024, 2);
                        $fileSize = $file->size . 'K';
                    }
                    elseif($file->size < 1024 * 1024 * 1024)
                    {
                        $file->size = round($file->size / (1024 * 1024), 2);
                        $fileSize = $file->size . 'M';
                    }
                    else
                    {
                        $file->size = round($file->size / (1024 * 1024 * 1024), 2);
                        $fileSize = $file->size . 'G';
                    }

                    echo html::a($this->createLink('file', 'download', "fileID=$file->id") . $sessionString, $lang->bug->viewGuide, '_blank', "class='text-primary' onclick=\"return downloadFile($file->id, '$file->extension', $imageWidth, '$file->title')\"");
                    ?>
                    </span>
                  </div>
                </div>
                <?php endif;?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->bug->title;?></th>
            <td colspan='2'>
                <div class="input-control has-icon-right">
                  <?php echo html::input('title', $bugTitle, "class='form-control' required");?>
                  <div class="colorpicker">
                    <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown"><span class="cp-title"></span><span class="color-bar"></span><i class="ic"></i></button>
                    <ul class="dropdown-menu clearfix">
                      <li class="heading"><?php echo $lang->story->colorTag;?><i class="icon icon-close"></i></li>
                    </ul>
                    <input type="hidden" class="colorpicker" id="color" name="color" value="" data-icon="color" data-wrapper="input-control-icon-right" data-update-color="#title"  data-provide="colorpicker">
                  </div>
                </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->bug->steps;?></th>
            <td colspan='2'>
              <?php echo $this->fetch('user', 'ajaxPrintTemplates', 'type=bug&link=steps');?>
              <?php echo html::textarea('steps', $steps, "rows='10' class='form-control'");?>
            </td>
          </tr>
          <?php
            $showStory = strpos(",$showFields,", ',story,') !== false;
            $showTask  = strpos(",$showFields,", ',task,')  !== false;
          ?>
          <?php if(($showStory or $showTask)):?>
          <tr>
            <th><?php echo ($showStory) ? $lang->bug->story : $lang->bug->task;?></th>
            <?php if($showStory):?>
            <td>
              <span id='storyIdBox'><?php echo html::select('story', empty($stories) ? '' : $stories, $storyID, "class='form-control chosen'");?></span>
            </td>
            <?php endif;?>
            <?php if($showTask):?>
            <td>
              <div class='input-group'>
                <?php if($showStory):?>
                <span class='input-group-addon'><?php echo $lang->bug->task?></span>
                <?php endif;?>
                <?php echo html::select('task', '', $taskID, "class='form-control chosen'") . html::hidden('oldTaskID', $taskID);?>
              </div>
            </td>
            <?php endif;?>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->bug->linkTesttask;?></th>
            <td colspan="2">
              <span id='testtasks'><?php echo html::select('linkTesttask[]', $testtasks, $linkTesttaskID, "class='form-control chosen' multiple");?></span>
            </td>
          </tr>
          <?php
          $showMailto   = strpos(",$showFields,", ',mailto,')   !== false;
          $showKeywords = strpos(",$showFields,", ',keywords,') !== false;
          ?>
          <?php if($showMailto or $showKeywords):?>
          <?php $colspan = ($showMailto and $showKeywords) ? '' : "colspan='2'";?>
          <tr>
            <th><?php echo ($showMailto) ? $lang->bug->lblMailto : $lang->bug->keywords;?></th>
            <?php if($showMailto):?>
            <td>
              <div class='input-group' id='contactListGroup'>
                <?php
                echo html::select('mailto[]', $users, str_replace(' ', '', $mailto), "class='form-control chosen' multiple");
                echo $this->fetch('my', 'buildContactLists');
                ?>
              </div>
            </td>
            <?php endif;?>
            <?php if($showKeywords):?>
            <td <?php echo $colspan?>>
              <div class='input-group'>
                <?php if($showMailto):?>
                <span class='input-group-addon' id='keywordsAddonLabel'><?php echo $lang->bug->keywords;?></span>
                <?php endif;?>
                <?php echo html::input('keywords', $keywords, "class='form-control'");?>
              </div>
            </td>
             <?php endif;?>
          </tr>
          <?php endif;?>
          <tr class='hide'>
            <th><?php echo $lang->bug->status;?></th>
            <td><?php echo html::hidden('status', 'active');?></td>
          </tr>
          <?php $this->printExtendFields('', 'table');?>
          <tr>
            <th><?php echo $lang->bug->files;?></th>
            <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-center form-actions">
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
              <?php echo html::hidden('caseVersion', (int)$version);?>
              <?php echo html::hidden('result', (int)$runID);?>
            </td>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>
</div>
<?php js::set('bugModule', $lang->bug->module);?>
<?php js::set('bugExecution', $lang->bug->execution);?>
<?php js::set('systemMode', $config->systemMode);?>
<?php js::set('bugExecutionID', $executionID);?>
<script>
$(function()
{
    parent.$('body.hide-modal-close').removeClass('hide-modal-close');
    setTimeout(function()
    {
        $('#deadlineTd').find('.input-group-addon').removeAttr('style');
    }, 400);
});

$('#type').change(function()
{
    var type = $(this).val();
    $.get(createLink('bug', 'ajaxGetChildTypeList', 'type=' + type), function(data)
    {
        $('#childType_chosen').remove();
        $('#childType').replaceWith(data);
        $('#childType').chosen();
    });
});

function downloadFile(fileID, extension, imageWidth, fileTitle)
{
    if(!fileID) return;
    var fileTypes      = 'txt,jpg,jpeg,gif,png,bmp';
    var sessionString  = '<?php echo $sessionString;?>';
    var windowWidth    = $(window).width();
    var url            = createLink('file', 'download', 'fileID=' + fileID + '&mouse=left') + sessionString;
    var width          = (windowWidth > imageWidth) ? ((imageWidth < windowWidth * 0.5) ? windowWidth * 0.5 : imageWidth) : windowWidth;
    var checkExtension = fileTitle.lastIndexOf('.' + extension) == (fileTitle.length - extension.length - 1);
    if(fileTypes.indexOf(extension) >= 0 && checkExtension)
    {
        $('<a>').modalTrigger({url: url, type: 'iframe', width: width}).trigger('click');
    }
    else
    {
        window.open(url, '_blank');
    }
    return false;
}

if($('#openedBuild').val())
{
    $('#planIdBox').prop('disabled', true);
}
</script>
<?php include '../../common/view/footer.html.php';?>
