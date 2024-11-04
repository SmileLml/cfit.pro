<?php
/**
 * The create view of testtask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: create.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datepicker.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<?php js::import($jsRoot . 'misc/date.js');?>
<?php js::set('projectID', $projectID);?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->testtask->create;?></h2>
    </div>
    <form method='post' class="main-form form-ajax" enctype="multipart/form-data" id='dataform'>
      <table class='table table-form'>
        <?php if($this->app->openApp == 'project'):?>
        <tr>
          <th class='w-120px'><?php echo $lang->testtask->applicationID;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('applicationName', $applicationList, $applicationID, 'class="form-control chosen" disabled="disabled"');?>
            </div>
          </td>
        </tr>
        <?php endif;?>
        <tr>
          <th class='w-120px'><?php echo $lang->testtask->product;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('product', $products, $productID, "class='form-control chosen' onchange='loadProductBuilds(this.value)'");?>
              <?php echo html::hidden('applicationID', $applicationID);?>
            </div>
          </td>
          <td>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->testtask->project;?></span>
              <?php echo html::select('project', $projects, $projectID, "class='form-control chosen'");?>
            </div>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->testtask->build;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('build[]', empty($builds) ? '' : $builds, $build, "class='form-control chosen' multiple");?>
            </div>
          </td>
          <td>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->testtask->problem;?></span>
              <?php echo html::select('problem[]', $problems, '', "class='form-control chosen' multiple");?>
            </div>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->testtask->requirement;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('requirement[]', $requirements, '', "class='form-control chosen' multiple");?>
            </div>
          </td>
          <td>
            <div class='table-row'>
              <div class='table-col'>
                <div class='input-group'>
                  <span class='input-group-addon'><?php echo $lang->testtask->owner;?></span>
                  <?php echo html::select('owner', $users, '', "class='form-control chosen'");?>
                </div>
              </div>
              <div class='table-col'>
                <div class='input-group'>
                  <span class='input-group-addon fix-border'><?php echo $lang->testtask->pri;?></span>
                  <?php echo html::select('pri', $lang->testtask->priList, 0, "class='form-control chosen'");?>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->testtask->status;?></th>
          <td>
            <div class='input-group'>
            <?php echo html::select('status', $lang->testtask->statusList, '',  "class='form-control chosen'");?>
            </div>
          </td>
          <td>
            <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->testtask->begin;?></span>
              <?php echo html::input('begin', '', "class='form-control form-date' onchange='suitEndDate()'");?>
              <span class='input-group-addon fix-border'><?php echo $lang->testtask->end;?></span>
              <?php echo html::input('end', '', "class='form-control form-date'");?>
            </div>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->testtask->name;?></th>
          <td colspan='2'><?php echo html::input('name', '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->testtask->desc;?></th>
          <td colspan='2'><?php echo html::textarea('desc', '', "rows=10 class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->testtask->mailto;?></th>
          <td colspan='2'>
            <div id='mailtoGroup' class='input-group'>
              <?php
              echo html::select('mailto[]', $users, '', "multiple class='form-control chosen'");
              echo $this->fetch('my', 'buildContactLists');
              ?>
            </div>
          </td>
        </tr>
        <?php $this->printExtendFields('', 'table');?>
        <tr>
          <td class='text-center form-actions' colspan='3'>
            <?php echo html::submitButton();?>
            <?php echo html::backButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
