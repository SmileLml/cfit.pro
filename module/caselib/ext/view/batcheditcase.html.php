<?php
/**
 * The batch create case view of caselib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     caselib
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php js::set('libID', $libID);?>
<style>
.checkbox-primary {width: 100px; margin: 0 5px 5px 0;}
</style>

<div id="mainContent" class="main-content fade">
  <div class="main-header">
    <h2><?php echo $lang->testcase->batchEdit;?></h2>
  </div>
  <form method='post' class='load-indicator main-form' enctype='multipart/form-data' target='hiddenwin' id="batchCreateForm">
    <table align='center' class='table table-form' id="tableBody">
      <thead>
        <tr class='text-center'>
          <th class='w-50px'> <?php echo $lang->idAB;?></th>
          <th class='w-180px'><?php echo $lang->testcase->module;?></th>
          <th class='required'><?php echo $lang->testcase->title;?></th>
          <th class='w-100px required'><?php echo $lang->testcase->type;?></th>
          <th class='w-140px'><?php echo $lang->testcase->categories;?></th>
          <th class='w-80px'> <?php echo $lang->testcase->pri;?></th>
          <th class='w-150px'><?php echo $lang->testcase->precondition;?></th>
          <th class='w-100px'><?php echo $lang->testcase->keywords;?></th>
          <th class='w-200px'><?php echo $lang->testcase->stage;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($caseIDList as $caseID):?>
        <?php if(!isset($cases[$caseID])) continue;?>
        <tr>
          <td><?php echo $caseID . html::hidden("caseIDList[$caseID]", $caseID);?></td>
          <td class='text-left' style='overflow:visible'><?php echo html::select("modules[$caseID]", $modules, $cases[$caseID]->module, "class='form-control chosen'");?></td>
          <td style='overflow:visible'>
            <div class="input-control has-icon-right">
              <?php echo html::input("title[$caseID]", $cases[$caseID]->title, "class='form-control title-import'");?>
              <div class="colorpicker">
                <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown"><span class="cp-title"></span><span class="color-bar"></span><i class="ic"></i></button>
                <ul class="dropdown-menu clearfix">
                  <li class="heading"><?php echo $lang->testcase->colorTag;?><i class="icon icon-close"></i></li>
                </ul>
                <?php echo html::hidden("color[$caseID]", $cases[$caseID]->color, "data-provide='colorpicker' data-icon='color' data-wrapper='input-control-icon-right' data-update-color='#title\\[$caseID\\]'");?>
              </div>
            </div>
          </td>
          <td><?php echo html::select("types[$caseID]", $this->lang->testcase->typeList, $cases[$caseID]->type, "class='form-control chosen'");?></td>
          <td style='overflow:visible'><?php echo html::select("categories[$caseID][]", $lang->testcase->categoryList, $cases[$caseID]->categories, "class='form-control chosen' multiple");?></td>
          <td><?php echo html::select("pris[$caseID]", $lang->testcase->priList, $cases[$caseID]->pri, "class='form-control chosen'");?></td>
          <td><?php echo html::textarea("precondition[$caseID]", $cases[$caseID]->precondition, "rows='1' class='form-control autosize'")?></td>
          <td><?php echo html::input("keywords[$caseID]", $cases[$caseID]->keywords, "class='form-control'");?></td>
          <td class='text-left' style='overflow:visible'><?php echo html::select("stage[$caseID][]", $lang->testcase->stageList, $cases[$caseID]->stage, "class='form-control chosen' multiple");?></td>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr><td colspan='8' class='text-center form-actions'><?php echo html::submitButton()?> <?php echo  html::backButton();?></td></tr>
      </tfoot>
    </table>
  </form>
</div>
<?php include '../../../common/view/footer.html.php';?>
