<?php
/**
 * The details view of issue module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology C
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     issue
 * @version     $Id: view.html.php 4488 2013-02-27 02:54:49Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php
$browseLink = $app->session->issueList ? $app->session->issueList : $this->createLink('issue', 'browse', "projectID={$issue->project}");
$createLink = $this->createLink('issue', 'create');
$dateFiled  = array('deadline', 'resolvedDate', 'createdDate', 'editedDate', 'activateDate', 'closedDate', 'assignedDate');
foreach($issue as $field => $value)
{
    if(in_array($field, $dateFiled) && strpos($value, '0000') === 0) $issue->$field = '';
}
?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i>' . $lang->goback, '', 'class="btn btn-secondary"');?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $issue->id?></span>
      <span class="text" title="<?php echo $issue->title?>"><?php echo $issue->title?></span>
      <?php if($issue->deleted):?>
      <span class='label label-danger'><?php echo $lang->issue->deleted;?></span>
      <?php endif; ?>
    </div>
  </div>
  <?php if(!isonlybody()):?>
  <div class="btn-toolbar pull-right">
    <?php if(common::hasPriv('issue', 'create')) echo html::a($createLink, "<i class='icon icon-plus'></i> {$lang->issue->create}", '', "class='btn btn-primary'");?>
  </div>
  <?php endif;?>
</div>
<div class="main-row" id="mainContent">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->issue->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($issue->desc) ? $issue->desc : '<div class="text-center text-muted">' . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php if($issue->files):?>
      <div class="detail"><?php echo $this->fetch('file', 'printFiles', array('files' => $issue->files, 'fieldset' => 'true'));?></div>
      <?php endif;?>
    </div>

      <div class="cell">
          <div class="detail">
              <div class="detail-title"><?php echo $lang->issue->progressDesc;?></div>
              <div class="detail-content article-content">
                  <?php if(empty($progressInfo)):?>
                      <div class="text-center text-muted"> <?php echo $lang->noData;?> </div>
                  <?php else:?>
                  <ol class='histories-list'>
                      <?php foreach ($progressInfo as  $val):?>
                      <li>
                          <span><?php echo zget($users, $val->actor);?> （<?php echo substr($val->date, 0, 10);?> 指派给 <?php echo zget($users, $val->extra);?>）说明如下：</span><br/>
                          <span><?php echo ($val->comment) ? $val->comment: '无';?></span>
                      </li>
                      <?php endforeach;?>
                  </ol>
                  <?php endif;?>
              </div>
          </div>
      </div>

      <div class="cell">
          <div class="detail">
              <div class="detail-title"><?php echo $lang->issue->resolutionComment;?></div>
              <div class="detail-content article-content">
                  <?php echo !empty($issue->resolutionComment) ? $issue->resolutionComment : '<div class="text-center text-muted">' . $lang->noData . '</div>';?>
              </div>
          </div>
      </div>


    <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=issue&objectID=$issue->id");?>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <?php if(!isonlybody()) echo "<div class='divider'></div>";?>
        <?php if(!$issue->deleted):?>
        <?php
          $params = "issueID=$issue->id";
          common::printIcon('issue', 'confirm', $params, $issue, 'button', 'start', '', 'iframe showinonlybody', true);
          common::printIcon('issue', 'resolve', $params, $issue, 'button', 'checked', '', 'iframe showinonlybody', true);
          common::printIcon('issue', 'assignTo', $params, $issue, 'button', '', '', 'iframe showinonlybody', true);
          common::printIcon('issue', 'assignedToFrameWork', $params, $issue, 'button', 'hand-right', '', 'iframe showinonlybody', true);
          common::printIcon('issue', 'cancel', $params, $issue, 'button', '', '', 'iframe showinonlybody', true);
          common::printIcon('issue', 'close', $params, $issue, 'button', '', '', 'iframe showinonlybody', true);
          common::printIcon('issue', 'activate', $params, $issue, 'button', '', '', 'iframe showinonlybody', true);
          echo "<div class='divider'></div>";
          common::printIcon('issue', 'edit', $params, $issue);
          common::printIcon('issue', 'delete', $params, $issue, 'button', 'trash', 'hiddenwin');
        ?>
        <?php endif;?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <details class="detail" open="">
      <summary class="detail-title"><?php echo $lang->issue->basicInfo;?></summary>
      <div class="detail-content">
        <table class="table table-data">
          <tbody>
            <tr valign="middle">
              <th class="thWidth w-100px"><?php echo $lang->issue->id;?></th>
              <td><?php echo $issue->id;?></td>
            </tr>

            <tr valign="middle">
                <th class="thWidth w-80px"><?php echo $lang->issue->owner;?></th>
                <td>
                    <?php
                    $owner = $issue->owner;
                    $ownerArray = explode(',', $owner);
                    $ownerUsers = getArrayValuesByKeys($users, $ownerArray);
                    $ownerUsersStr = implode(',', $ownerUsers);
                    echo $ownerUsersStr;
                    ?>
                </td>
            </tr>

            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->issue->type;?></th>
              <td><?php echo zget($lang->issue->typeList, $issue->type);?></td>
            </tr>
            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->issue->severity;?></th>
              <td><?php echo zget($lang->issue->severityList, $issue->severity);?></td>
            </tr>
            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->issue->pri;?></th>
              <td><?php echo $issue->pri;?></td>
            </tr>
            <tr valign="middle">
                <th class="thWidth w-80px"><?php echo $lang->issue->assignedTo;?></th>
                <td><?php echo zget($users, $issue->assignedTo);?></td>
            </tr>
            <tr valign="middle">
                <th class="thWidth w-80px"><?php echo $lang->issue->frameworkUser;?></th>
                <td><?php echo zget($users, $issue->frameworkUser);?></td>
            </tr>
            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->issue->deadline;?></th>
              <td><?php echo $issue->deadline;?></td>
            </tr>
            <tr valign="middle">
                <th class="thWidth w-80px"><?php echo $lang->issue->status;?></th>
                <td><?php echo zget($lang->issue->statusList, $issue->status);?></td>
            </tr>

            <tr>
                <th colspan="2"><hr style=" border: 1px dotted #838a9d"></th>
            </tr>

            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->issue->createdBy;?></th>
              <td><?php echo zget($users, $issue->createdBy);?></td>
            </tr>

            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->issue->createdDate;?></th>
              <td><?php echo $issue->createdDate;?></td>
            </tr>

            <tr valign="middle">
                <th class="thWidth w-80px"><?php echo $lang->issue->assignedBy;?></th>
                <td><?php echo zget($users, $issue->assignedBy);?></td>
            </tr>
            <tr valign="middle">
                <th class="thWidth w-80px"><?php echo $lang->issue->assignedDate;?></th>
                <td><?php echo $issue->assignedDate;?></td>
            </tr>

                <tr valign="middle">
                    <th class="thWidth w-80px"><?php echo $lang->issue->resolvedBy;?></th>
                    <td><?php echo zget($users, $issue->resolvedBy);?></td>
                </tr>

                <tr valign="middle">
                    <th class="thWidth w-80px"><?php echo $lang->issue->resolvedDate;?></th>
                    <td><?php echo $issue->resolvedDate;?></td>
                </tr>

                <tr valign="middle">
                    <th class="thWidth w-80px"><?php echo $lang->issue->resolution;?></th>
                    <td><?php echo zget($lang->issue->resolveMethods, $issue->resolution) ;?></td>
                </tr>

                <tr valign="middle">
                    <th class="thWidth w-80px"><?php echo $lang->issue->closedBy;?></th>
                    <td><?php echo zget($users, $issue->closedBy);?></td>
                </tr>

                <tr valign="middle">
                    <th class="thWidth w-80px"><?php echo $lang->issue->closedDate;?></th>
                    <td><?php echo $issue->closedDate;?></td>
                </tr>

            <tr>
                <th colspan="2"><hr style=" border: 1px dotted #838a9d"></th>
            </tr>


            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->issue->editedBy;?></th>
              <td><?php echo zget($users, $issue->editedBy);?></td>
            </tr>
            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->issue->editedDate;?></th>
              <td><?php echo $issue->editedDate;?></td>
            </tr>

            <tr valign="middle">
                <th class="thWidth w-80px"><?php echo $lang->issue->activateBy;?></th>
                <td><?php echo zget($users, $issue->activateBy);?></td>
            </tr>
            <tr valign="middle">
              <th class="thWidth w-80px"><?php echo $lang->issue->activateDate;?></th>
              <td><?php echo $issue->activateDate;?></td>
            </tr>


          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
