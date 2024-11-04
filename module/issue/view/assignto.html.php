<?php
/**
 * The assign of issue module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     issue
 * @version     $Id: complete.html.php 935 2010-07-06 07:49:24Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $issue->id;?></span>
        <?php echo "<span title='$issue->title'>" . $issue->title .'</span>';?>
        <small><?php echo $lang->arrow.$lang->issue->assignToTitle ;?></small>
      </h2>
      </div>
    </div>
    <div class="modal-body" style="min-height: 320px; overflow: auto;">
      <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' >
        <table class='table table-form'>
          <tr>
            <th><?php echo $lang->issue->assignedTo;?></th>
            <td colspan='2'><?php echo html::select('assignedTo', $assignUsers, $issue->assignedTo, 'class="form-control chosen"');?></td>
          </tr>
            <!--后台配置的部门管理层人员 可指派架构部接口人-->
            <?php if(in_array($app->user->account,explode(',',trim($this->lang->issue->leaderList['deptLeader'],','))) || $app->user->account == 'admin'):?>
            <tr>
                <th><?php echo $lang->issue->frameworkUser;?></th>
                <td colspan='2'><?php echo html::select('frameworkUser', $frameworkUsers, zmget($users,$issue->frameworkUser,''), 'class="form-control chosen"');?></td>
            </tr>
           <?php endif;?>
          <tr>
                <th><?php echo $lang->issue->mailTo;?></th>
                <td colspan='2'><?php echo html::select('mailTo[]', $users, '', 'class="form-control chosen" multiple');?></td>
          </tr>
            <tr>
                <th><?php echo $lang->issue->comment;?></th>
                <td colspan='2'><?php echo html::textarea('comment', '', 'row="6"');?></td>
            </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
              <?php echo html::submitButton();?>
            </td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
