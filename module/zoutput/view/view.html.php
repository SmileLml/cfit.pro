<?php
/**
 * The details view of zoutput module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology C
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     zoutput
 * @version     $Id: edit.html.php 4488 2013-02-27 02:54:49Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php
$browseLink = $this->createLink('zoutput', 'browse');
$createLink = $this->createLink('zoutput', 'create');

$dateFiled  = array('createdDate', 'editedDate');
foreach($output as $field => $value)
{
    if(in_array($field, $dateFiled) && strpos($value, '0000') === 0) $output->$field = '';
}
?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i>' . $lang->goback, '', 'class="btn btn-secondary"');?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $output->id;?></span>
      <span class="text" title="<?php echo $output->name;?>"><?php echo $output->name;?></span>
    </div>
  </div>
  <div class="btn-toolbar pull-right">
    <?php if(common::hasPriv('zoutput', 'create')) echo html::a($createLink, "<i class='icon icon-plus'></i> {$lang->zoutput->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div class="main-row" id="mainContent">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <p><strong><?php echo $lang->zoutput->name;?></strong> - <?php echo $output->name;?></p>
        <p><strong><?php echo $lang->zoutput->activity;?></strong> - <?php echo zget($activity, $output->activity, '');?></p>
        <p><strong><?php echo $lang->zoutput->optional;?></strong> - <?php echo zget($lang->zoutput->optionalList, $output->optional, '');?></p>
        <div class="detail-title"><?php echo $lang->zoutput->content;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($output->content) ? $output->content : '<div class="text-center text-muted">' . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php if($output->files):?>
      <div class="detail"><?php echo $this->fetch('file', 'printFiles', array('files' => $output->files, 'fieldset' => 'true'));?></div>
      <?php endif;?>
    </div>
    <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=zoutput&objectID=$output->id");?>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <?php if(!isonlybody()) echo "<div class='divider'></div>";?>
        <?php if(!$output->deleted):?>
        <?php
          $params = "outputID=$output->id";
          echo "<div class='divider'></div>";
          common::printIcon('zoutput', 'edit', $params, $output);
          common::printIcon('zoutput', 'delete', $params, $output, 'button', 'trash', 'hiddenwin');
        ?>
        <?php endif;?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="panel-heading"><strong><?php echo $lang->zoutput->basicInfo;?></strong></div>
      <div class="detail-content">
        <table class="table table-data">
          <tbody>
            <tr valign="middle">
              <th class="thWidth w-100px"><?php echo $lang->zoutput->id;?></th>
              <td><?php echo $output->id;?></td>
            </tr>
            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->zoutput->createdBy;?></th>
              <td><?php echo zget($users, $output->createdBy);?></td>
            </tr>
            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->zoutput->createdDate;?></th>
              <td><?php echo $output->createdDate;?></td>
            </tr>
            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->zoutput->editedBy;?></th>
              <td><?php echo zget($users, $output->editedBy);?></td>
            </tr>
            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->zoutput->editedDate;?></th>
              <td><?php echo $output->editedDate;?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
