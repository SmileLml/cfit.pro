<?php
/**
 * The view file of feedback module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     feedback
 * @version     $Id: view.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('browseType', $browseType);?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php $jumpMethod = empty($this->app->user->feedback) ? 'admin' : 'browse';?>
    <?php $browseLink = $app->session->feedbackList != false ? $app->session->feedbackList : inlink($jumpMethod, 'browseType=unclosed');?>
    <?php if(!isonlybody()):?>
    <?php echo html::a($browseLink, "<i class='icon-back'></i> " . $lang->goback, '', "class='btn btn-primary'");?>
    <div class='divider'></div>
    <?php endif;?>
    <div class='page-title'>
      <span class='label label-id'><?php echo $feedback->id;?></span>
      <strong>
      <?php
      if($feedback->public) echo "<span class='label label-info'>{$lang->feedback->public}</span>";
      echo $feedback->title;
      ?>
      </strong>
      <?php $status = $this->processStatus('feedback', $feedback);?>
      <?php echo " <span class='label label-status status-{$feedback->status}'>{$status}</span>";?>
      <?php echo " <span class='label label-info'>{$product}</span>";?>
      <?php
      if($feedback->deleted) echo "<span class='label label-danger'>{$lang->feedback->deleted}</span>";
      ?>
    </div>
  </div>
</div>
<div id='mainContent' class='main-row'>
  <div class='main-col col-8'>
    <div class='cell'>
      <div class='detail'>
        <div class='detail-title'><?php echo $lang->feedback->desc;?></div>
        <div class='detail-content'><?php echo $feedback->desc;?></div>
      </div>
      <?php echo $this->fetch('file', 'printFiles', array('files' => $feedback->files, 'fieldset' => 'true'));?>
      <?php if($feedback->result and $type):?>
      <div id='resultInfoBox' class='detail'>
        <div class='detail-title'><?php echo $lang->feedback->$type;?></div>
        <div class='detail-content'>
          <span class="prefix"> <strong>#<?php echo $feedback->resultInfo->id;?></strong> </span>
          <span> <?php echo common::hasPriv($type, 'view') ? html::a($this->createLink($type, 'view', "id={$feedback->resultInfo->id}", 'html', true), $feedback->resultInfo->title, '', "class='iframe'") : $feedback->resultInfo->title;?> </span>
          <span class='<?php echo 'pri' . zget($lang->$type->priList, $feedback->resultInfo->pri);?>'> <?php echo zget($lang->$type->priList, $feedback->resultInfo->pri)?> </span>
          <span class="label label-info"><?php echo $this->processStatus($type, $feedback->resultInfo);?></span>
        </div>
      </div>
      <?php endif;?>
    </div>
    <?php echo $this->loadModel('flow')->printFields('feedback', $this->app->getMethodName(), $feedback, 'div', "position=left&inCell=1&inForm=0");?>
    <div class='cell'><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <?php if(!isonlybody()) echo "<div class='divider'></div>";?>
        <?php if(!$feedback->deleted):?>
        <?php
        $params      = "feedbackID=$feedback->id";
        $likeByTitle = '';
        if($feedback->public and $feedback->likes)
        {
            foreach(explode(',', $feedback->likes) as $likeBy) $likeByTitle .= zget($users, $likeBy, $likeBy) . ',';
            $likeByTitle .= $lang->feedback->feelLike;
        }

        if($feedback->status != 'closed') echo $this->loadModel('effort')->createAppendLink('feedback', $feedback->id);
        if($app->user->account == $feedback->openedBy and common::hasPriv('feedback', 'comment')) echo html::a($this->createLink('feedback', 'comment', "feedbackID=$feedback->id&type=asked", 'html', true), "<i class='icon icon-chat-line'></i> " . $lang->feedback->ask, '', "class='btn btn-link iframe'");
        common::printIcon('feedback', 'assignTo', "feedbackID=$feedback->id", $feedback, 'button', '', '', 'iframe', true);
        common::printIcon('feedback', 'review', "feedbackID=$feedback->id", $feedback, 'button', 'glasses', '', 'iframe', true, '', $lang->feedback->review);
        if(empty($app->user->feedback) and strpos('closed|clarify|noreview', $feedback->status) === false)
        {
            common::printIcon('feedback', 'comment', "feedbackID=$feedback->id&type=replied", $feedback, 'button', 'restart', '', 'iframe', true, '', $lang->feedback->reply);
            $this->app->loadLang('story');
            $this->app->loadLang('bug');
            $this->app->loadLang('task');
            $this->app->loadLang('todo');
            echo "<div class='btn-group dropup'>";
            echo "<button type='button' class='btn dropdown-toggle' data-toggle='dropdown'><i class='icon icon-arrow-right'></i> " . $lang->feedback->convert . " <span class='caret'></span></button>";
            echo "<ul class='dropdown-menu' id='createCaseActionMenu'>";
            $link = $this->createLink('story', 'create', "product=$feedback->product&branch=0&moduleID=0&storyID=0&projectID=0&bugID=0&planID=0&todoID=0&extra=$params");
            if(common::hasPriv('story', 'create') and $config->global->flow == 'full' or $config->global->flow == 'onlyStory') echo "<li>" . html::a($link, $lang->story->common, '', "data-app='feedback'") . "</li>";

            $link = '#toTask';
            if(common::hasPriv('task', 'create') and $config->global->flow == 'full' or $config->global->flow == 'onlyTask') echo "<li>" . html::a('#toTask', $lang->task->common, '', "data-toggle='modal' data-id='$feedback->id' onclick='getFeedbackID(this)'") . "</li>";

            $link = '#toBug';
            if(common::hasPriv('bug', 'create') and $config->global->flow == 'full' or $config->global->flow == 'onlyTest') echo "<li>" . html::a('#toBug', $lang->bug->common, '', "data-toggle='modal' data-id='$feedback->id' data-product={$feedback->product} onclick='initToBug(this)'") . "</li>";

            $link = $this->createLink('todo', 'create', "date=today&userID=&from=feedback&feedbackID=$feedbackID");
            if(common::hasPriv('todo', 'create') and $config->global->flow == 'full') echo "<li>" . html::a($link, $lang->todo->common, '', "data-app='feedback'") . "</li>";
            echo "</ul>";
            echo "</div>";
        }
        common::printIcon('feedback', 'close', "feedbackID=$feedback->id", $feedback, 'button', '', '', 'iframe', true, '', $lang->feedback->close);

        if($feedback->public)
        {
            if(common::hasPriv('feedback', 'comment')) echo html::a($this->createLink('feedback', 'comment', "feedbackID=$feedback->id&type=commented", 'html', true), "<i class='icon-confirm'></i> {$lang->feedback->comment}", '', "class='btn iframe'");
            echo "<span class='likesBox'>";
            echo html::a("javascript:like($feedback->id)", "<i class='icon icon-chevron-double-up'></i> ({$feedback->likesCount})", '', "class='btn' title='$likeByTitle'");
            echo "</span>";
        }

        echo $this->buildOperateMenu($feedback, 'view');

        common::printIcon('feedback', 'edit', "feedbackID=$feedback->id&browseType=$browseType", $feedback, 'button');
        common::printIcon('feedback', 'delete', $params, $feedback, 'button', 'trash', 'hiddenwin');
        ?>
        <?php endif;?>
      </div>
    </div>
  </div>
  <div class='side-col col-4'>
    <div class='cell'>
      <div class='detail'>
        <div class='detail-title'><?php echo $lang->feedback->lblBasic;?></div>
        <table class='table table-data table-condensed table-borderless'>
          <tr>
            <th class='w-80px'><?php echo $lang->feedback->product?></th>
            <td><?php echo common::hasPriv('product', 'view') ? html::a($this->createLink('product', 'view', "id={$feedback->product}"), zget($products, $feedback->product)) : zget($products, $feedback->product);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->feedback->module;?></th>
            <?php
            $moduleTitle = '';
            ob_start();
            if(empty($modulePath))
            {
                $moduleTitle .= '/';
                echo "/";
            }
            else
            {
               foreach($modulePath as $key => $module)
               {
                   $moduleTitle .= $module->name;
                   echo $module->name;
                   if(isset($modulePath[$key + 1]))
                   {
                       $moduleTitle .= '/';
                       echo $lang->arrow;
                   }
               }
            }
            $printModule = ob_get_contents();
            ob_end_clean();
            ?>
            <td title='<?php echo $moduleTitle?>'><?php echo $printModule?></td>
          </tr>
          <tr>
            <th><?php echo $lang->feedback->status?></th>
            <td><?php echo $status;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->feedback->openedBy?></th>
            <td>
            <?php
            echo zget($users, $feedback->openedBy);
            echo $lang->at . $feedback->openedDate;
            ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->feedback->assignedTo?></th>
            <td><?php echo zget($users, $feedback->assignedTo);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->feedback->mailto;?></th>
            <td><?php $mailto = explode(',', str_replace(' ', '', $feedback->mailto)); foreach($mailto as $account) echo ' ' . $users[$account]; ?></td>
          </tr>
          <?php if($feedback->reviewedBy):?>
          <tr>
            <th><?php echo $lang->feedback->reviewedBy;?></th>
            <td>
              <?php
              foreach(explode(',', $feedback->reviewedBy) as $reviewedBy) echo zget($users, $reviewedBy) . ' ';
              echo $lang->at . substr($feedback->reviewedDate, 0, 10);
              ?>
            </td>
          </tr>
          <?php endif;?>
          <?php if($feedback->processedBy):?>
          <tr>
            <th><?php echo $lang->feedback->processedBy?></th>
            <td><?php echo zget($users, $feedback->processedBy) . $lang->at . $feedback->processedDate;?></td>
          </tr>
          <?php endif;?>
          <?php if($feedback->closedBy):?>
          <tr>
            <th><?php echo $lang->feedback->closedBy?></th>
            <td><?php echo zget($users, $feedback->closedBy) . $lang->at . $feedback->closedDate;?></td>
          </tr>
          <?php endif;?>
          <?php if($feedback->closedReason and $feedback->status == 'closed'):?>
          <tr>
            <th><?php echo $lang->feedback->closedReason?></th>
            <td><?php echo zget($lang->feedback->closedReasonList, $feedback->closedReason);?></td>
          </tr>
          <?php endif;?>
        </table>
      </div>
    </div>
    <div class='cell'>
      <div class='detail'>
        <div class='detail-title'><?php echo $lang->feedback->contactInfo;?></div>
        <table class='table table-data table-condensed'>
          <?php foreach($contacts as $contact):?>
          <tr>
            <th class='w-80px text-top'><?php echo $contact->realname ? $contact->realname : $contact->account;?></th>
            <td>
              <?php
              if($contact->mobile)  echo "<p>" . $lang->user->mobile   . ': '. $contact->mobile   . "</p>";
              if($contact->email)   echo "<p>" . $lang->user->email    . ': '. $contact->email    . "</p>";
              if($contact->phone)   echo "<p>" . $lang->user->phone    . ': '. $contact->phone    . "</p>";
              if($contact->qq)      echo "<p>" . $lang->user->qq       . ': '. $contact->qq       . "</p>";
              if($contact->skype)   echo "<p>" . $lang->user->skype    . ': '. $contact->skype    . "</p>";
              ?>
            </td>
          </tr>
          <?php endforeach;?>
        </table>
      </div>
    </div>
    <?php echo $this->loadModel('flow')->printFields('feedback', $this->app->getMethodName(), $feedback, 'div', "position=right&inCell=1&inForm=0");?>
  </div>
</div>
<div id='mainActions' class='main-actions'>
  <?php common::printPreAndNext($preAndNext);?>
</div>
<?php include './selectproject.html.php';?>
<script>
$(function()
{
    $(".effort").modalTrigger({width:1024, iframe:true, transition:'elastic'});

    $('#feedbackTree').tree(
    {
        name: 'feedbackTree',
        initialState: 'preserve'
    });
})
</script>
<?php include '../../common/view/footer.html.php';?>
