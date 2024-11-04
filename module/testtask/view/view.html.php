<?php
/**
 * The view file of case module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     case
 * @version     $Id: view.html.php 4141 2013-01-18 06:15:13Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php $browseLink = $this->session->testtaskList ? $this->session->testtaskList : $this->createLink('testtask', 'browse', "productID=$task->product");?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php common::printBack($browseLink, 'btn btn-link');?>
    <div class='divider'></div>
    <div class='page-title'>
      <span class='label label-id'><?php echo $task->id;?></span>
      <span class='text' title='<?php echo $task->name;?>'><?php echo $task->name;?></span>
      <?php if($task->deleted):?>
      <span class='label label-danger'><?php echo $lang->testtask->deleted;?></span>
      <?php endif; ?>
    </div>
  </div>
</div>
<div id='mainContent' class='main-row'>
  <div class="col-8 main-col">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->testtask->desc;?></div>
        <div class="detail-content article-content"><?php echo !empty($task->desc) ? $task->desc : $lang->noData;?></div>
      </div>
      <?php if($task->report):?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->testtask->report;?></div>
        <div class="detail-content article-content"><?php echo $task->report;?></div>
      </div>
      <?php endif;?>
      <?php
      $canBeChanged = common::canBeChanged('testtask', $task);
      if($canBeChanged) $actionFormLink = $this->createLink('action', 'comment', "objectType=testtask&objectID=$task->id");
      ?>
    </div>
    <?php $this->printExtendFields($task, 'div', "position=left&inForm=0&inCell=1");?>
    <div class='cell'><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <?php if(!$task->deleted):?>
        <div class='divider'></div>
        <?php
        common::printIcon('testtask', 'start',    "taskID=$task->id", $task, 'button', '', '', 'iframe showinonlybody', true);
        common::printIcon('testtask', 'close',    "taskID=$task->id", $task, 'button', '', '', 'iframe showinonlybody', true);
        common::printIcon('testtask', 'block',    "taskID=$task->id", $task, 'button', 'pause', '', 'iframe showinonlybody', true);
        common::printIcon('bug', 'browse',    "applicationID=testtask$task->id", $task, 'button');
        common::printIcon('testtask', 'activate', "taskID=$task->id", $task, 'button', 'magic', '', 'iframe showinonlybody', true);
        common::printIcon('testtask', 'cases',    "taskID=$task->id", $task, 'button', 'sitemap');
        common::printIcon('testtask', 'linkCase', "taskID=$task->id", $task, 'button', 'link');
        ?>

        <?php echo $this->buildOperateMenu($task, 'view');?>

        <div class='divider'></div>
        <?php
        common::printIcon('testtask', 'edit',     "taskID=$task->id", $task);
        common::printIcon('testtask', 'delete',   "taskID=$task->id", $task, 'button', 'trash', 'hiddenwin');
        ?>
        <?php endif;?>
      </div>
    </div>
  </div>
  <div class="col-4 side-col">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->testtask->legendBasicInfo;?></div>
        <div class="detail-content">
          <table class="table table-data table-fixed">
            <tr>
              <th class='w-100px'><?php echo $lang->testtask->project;?></th>
              <td><?php if(!empty($task->projectPlan)) common::printLinks('projectplan', 'view', "id={$task->projectPlan->id}", $task->projectPlan->name, '', 'data-app="platform"');?></td>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->applicationID;?></th>
              <td>
              <?php $applicationName = zget($applicationList, $task->applicationID, '');
              common::printLinks('application', 'view', "applicationID={$task->applicationID}", $applicationName, '', 'data-app="application"');
              ?>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->product;?></th>
              <td>
              <?php
              $productName = zget($products, $task->product, '');
              common::printLinks('product', 'browse', "id={$task->product}", $productName, '', 'data-app="product"');
              ?>
              </td>
            </tr>
            <tr class='w-100px'>
              <th><?php echo $lang->testtask->build;?></th>
              <td>
                <?php
                foreach($task->buildList as $buildID => $buildName)
                {
                    common::printLinks('build', 'view', "buildID=$buildID", $buildName, '', "title='{$buildName}' data-app='project'");
                    echo '<br>';
                }
                ?>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->problem;?></th>
              <?php if($task->problem):?>
              <td>
                <?php foreach($problems as $problemID => $problemCode):?>
                <?php echo html::a($this->createLink('problem', 'view', array('id' => $problemID)), $problemCode, '', 'data-app="problempool"') . '<br>';?>
                <?php endforeach;?>
              </td>
              <?php endif;?>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->requirement;?></th>
              <?php if($task->requirement):?>
              <td>
                <?php foreach($demands as $demand):?>
                <?php
                    if($demand->sourceDemand == 1){
                        echo html::a($this->createLink('demand', 'view', 'id=' . $demand->id, '', true), $demand->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                    }else{
                        echo html::a($this->createLink('demandinside', 'view', 'id=' . $demand->id, '', true), $demand->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                    }
                ?>
<!--                --><?php //echo html::a($this->createLink('demand', 'view', array('id' => $demandID)), $demandCode, '', 'data-app="backlog"') . '<br>';?>
                <?php endforeach;?>
              </td>
              <?php endif;?>
            </tr>
            <tr>
               <th><?php echo $lang->testtask->secondorder;?></th>
               <?php if($task->secondorder):?>
               <td>
                 <?php foreach($secondorders as $secondorderID => $secondorderCode):?>
                 <?php echo html::a($this->createLink('secondorder', 'view', array('id' => $secondorderID)), $secondorderCode, '', 'data-app="secondorder"') . '<br>';?>
                 <?php endforeach;?>
               </td>
               <?php endif;?>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->owner;?></th>
              <td><?php echo zget($users, $task->owner);?></td>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->mailto;?></th>
              <td><?php $mailto = explode(',', str_replace(' ', '', $task->mailto)); foreach($mailto as $account) echo ' ' . zget($users, $account, $account);?></td>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->pri;?></th>
              <td><?php echo $task->pri;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->begin;?></th>
              <td><?php echo $task->begin;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->end;?></th>
              <td><?php echo $task->end;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->progress;?></th>
              <td><?php echo $task->progress;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->testtask->status;?></th>
              <td class='task-<?php echo $task->status?>'><?php echo $this->processStatus('testtask', $task);?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <?php $this->printExtendFields($task, 'div', "position=right&inForm=0&inCell=1");?>
  </div>
</div>

<div id='mainActions' class='main-actions'>
  <?php common::printPreAndNext($browseLink);?>
</div>
<?php include '../../common/view/footer.html.php';?>
