<?php
/**
 * The view file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: view.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<style>
     td p {margin-bottom: 0;}
    .table-fixed td{
        white-space: unset!important;
    }
     .side-col .cell, .main-col .cell {
         overflow-y: auto;
     }
</style>
<style class="dialog"></style>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php $browseLink = $this->session->nomeetList ? $this->session->nomeetList : inlink('nomeet','type=all');?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
      <?php if($flag):?>
      <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
      <div class="divider"></div>
      <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $review->id?></span>
      <span class="text"><?php echo $review->title;?></span>
    </div>
  </div>
    <div class="btn-toolbar pull-right">
        <?php if($flag):?>
            <?php if(common::hasPriv('reviewproblem', 'batchCreate')) echo html::a($this->createLink('reviewproblem', 'batchCreate', "projectID=$review->project&reviewID=$review->id&source=review"), "<i class='icon-plus'></i> {$lang->review->addproblem }", '', "class='btn btn-primary'");?>
        <?php endif;?>
    </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class='cell'>
      <div class='detail'>
        <div class='detail-title'><?php echo $lang->review->object;?></div>
        <div class='detail-content article-content no-margin no-padding'>
        <?php
        foreach($review->objects as $object)
        {
            echo zget($this->lang->review->objectList,$object,'').'&nbsp;&nbsp;';
        }
        ?> 
        </div>
      </div>

    </div>
      <div class="cell">
          <?php include '../../review/ext/view/viewReviewFlow.html.php';?>
      </div>
    <div class="cell">
          <div class='detail'>
              <div class='detail-title'><?php echo $lang->files;?>
                  <span class="action-span pull-right detail-content article-content">
                        <?php  common::printIcon('reviewmeeting', 'editfiles', "reviewID=$review->id", $review, 'list','edit', '', 'iframe', true,'data-position="50px"'); ?>
                 </span>
              </div>
              <div class='detail-content article-content'>
                  <?php
                  if($review->files){
                      echo $this->fetch('file', 'printFiles', array('files' => $review->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => $isAllowOperateFile));
                  }else{
                      echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                  }
                  ?>
              </div>
          </div>
    </div>
      <?php
        if(isset($review->meetingDetailInfo) && !empty($review->meetingInfo->meetingSummaryCode)):
            $meetingDetailInfo = $review->meetingDetailInfo;
          ?>
            <?php include '../../review/ext/view/viewReviewMeeting.html.php';?>
      <?php endif;?>

      <div class="cell">
          <?php include '../../review/ext/view/viewReviewIssue.html.php';?>
      </div>

      <?php include '../../review/ext/view/viewArchive.html.php';?>
      <?php include '../../review/ext/view/viewBaseLine.html.php';?>
      
    <div class='cell'>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=review&objectID=$review->id");?>
        <?php include '../../common/view/action.html.php';?></div>
      <?php if($flag):?>
      <div class='main-actions' style="/*margin-left:150px">
          <div class="btn-toolbar">
              <?php $params = "reviewID=$review->id"; ?>
              <?php common::printBack($browseLink);?>
              <div class='divider'></div>
              <?php
             common::hasPriv('reviewmeeting', 'arrangemeet') ? common::printIcon('reviewmeeting', 'arrangemeet', $params, $review, 'list','calendar') : '';
             common::hasPriv('reviewmeeting', 'nomeetEdit') ? common::printIcon('reviewmeeting', 'nomeetEdit', $params, $review, 'list','edit') : '';
             ?>
          </div>
      </div>
      <?php endif;?>
  </div>
  <div class="side-col col-4">
    <div class="cell">
        <?php include '../../review/ext/view/viewBasicInfo.html.php';?>
    </div>
      <div class="cell">
          <?php include '../../review/ext/view/viewConsumedInfo.html.php';?>
      </div>
  </div>
</div>
<script>
    var scroll_height = document.body.scrollHeight;
    var window_height = window.innerHeight;
    if (scroll_height>window_height) {
        var _top = 120;
        if (window_height <= 700) {
            _top = 60
        }
        $(".dialog").append(".modal-dialog{top:" + _top + 'px' + "!important}");
    }
</script>
<?php include '../../common/view/footer.html.php';?>
