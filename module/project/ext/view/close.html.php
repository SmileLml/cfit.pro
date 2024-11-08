<?php
/**
 * The suspend file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang<wwccss@gmail.com>
 * @package     project 
 * @version     $Id: suspend.html.php 935 2013-01-16 07:49:24Z wwccss@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='main-header'>
    <h2>
      <span class='prefix label-id'><strong><?php echo $project->id;?></strong></span>
      <?php echo isonlybody() ? ("<span title='$project->name'>" . $project->name . '</span>') : html::a($this->createLink('project', 'view', 'project=' . $project->id), $project->name, '_blank');?>
      <?php if(!isonlybody()):?>
      <small> <?php echo $lang->arrow . $lang->{$project->type}->close;?></small>
      <?php endif;?>
    </h2>
  </div>
  <form class='load-indicator main-form' method='post' target='hiddenwin'>
    <table class='table table-form'>
      <tbody>
        <?php $this->printExtendFields($project, 'table');?>
        <tr>
          <th class='w-100px'><?php echo $lang->project->realEnd;?></th>
          <td><?php echo html::input('realEnd', strpos('0000-00-00',$project->realEnd) == false ? $project->realEnd : helper::today(), "class='form-control form-date'");?></td>
        </tr>
        <tr class='hide'>
          <th class='w-50px'><?php echo $lang->project->status;?></th>
          <td><?php echo html::hidden('status', 'closed');?></td>
        </tr>
        <tr>
          <th class='w-50px'><?php echo $lang->comment;?></th>
          <td colspan='3'><?php echo html::textarea('comment', '', "rows='6' class='form-control kindeditor' hidefocus='true'");?></td>
        </tr>
        <tr>
          <td class='text-center form-actions' colspan='4'><?php echo html::submitButton($lang->{$project->type}->close) . html::linkButton($lang->goback, $this->session->projectList, 'self', '', 'btn btn-wide'); ?></td>
        </tr>
      </tbody>
    </table>
  </form>
   <hr class='small' />
  <div class='main'><?php include '../../../common/view/action.html.php';?></div>
</div>
<?php include '../../../common/view/footer.html.php';?>
