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
<?php
if($this->session->reviewmanageList=='board'):?>
    <?php $browseLink = $this->session->reviewmanageList ? $this->session->reviewmanageList : inlink('board'); ?>
<?php else:?>
    <?php $browseLink = $this->session->reviewmanageList ? $this->session->reviewmanageList : inlink('review');?>
<?php endif;?>

<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
      <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
      <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $review->id?></span>
      <span class="text"><?php echo $review->title;?></span>
    </div>
  </div>
    <div class="btn-toolbar pull-right">
        <?php if(common::hasPriv('reviewproblem', 'batchCreate')) echo html::a($this->createLink('reviewproblem', 'batchCreate', "projectID=$review->project&reviewID=$review->id&source=review"), "<i class='icon-plus'></i> {$lang->review->addproblem }", '', "class='btn btn-primary'");?>
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
                        <?php  common::printIcon('reviewmanage', 'editfiles', "reviewID=$review->id", $review, 'list','edit', '', 'iframe', true,'data-position="50px"'); ?>
                 </span>
                </div>
                <div class='detail-content article-content'>
                    <?php
                    if($review->files){
                        echo $this->fetch('file', 'printFiles', array('files' => $review->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true));
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
            <div class="detail-title btn-toolbar pull-left"><?php echo sprintf($lang->review->reviewIssueTotal, count($issueList)) ;?></div>
            <div class="btn-toolbar pull-right">
                <?php if(common::hasPriv('reviewproblem','issue'))common::printLink('reviewproblem', 'issue', "projectID=$review->project&reviewID=$review->id&browseType=all", "<i class='icon icon-checked'></i>" . $lang->review->dealIssue, '', "class='btn btn-primary '");?>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class='panel-body  scrollbar-hover detail-content article-content' style="height: 300px;">
                    <table class='table table-detail table-bordered table-condensed table-striped table-fixed  '  >
                        <thead>
                        <tr>
                            <th class='w-500px'><?php echo $lang->reviewissue->title;?></th>
                            <th class='w-900px'><?php  echo $lang->reviewissue->desc ;?></th>
                            <th class='w-120px'><?php echo $lang->reviewissue->type;?></th>
                            <th class='w-80px'><?php echo  $lang->reviewissue->raiseBy ;?></th>
                            <th class='w-100px'><?php echo  $lang->reviewissue->raiseDate?></th>
                            <th class='w-80px'><?php echo  $lang->reviewissue->status;?></th>
                            <th class='w-80px'><?php echo  $lang->reviewissue->resolutionBy;?></th>
                            <th class='w-100px'><?php echo  $lang->reviewissue->resolutionDate ;?></th>
                            <th class='w-80px'><?php echo  $lang->reviewissue->validation ;?></th>
                            <th class='w-100px'><?php echo  $lang->reviewissue->verifyDate ;?></th>
                            <th class='w-300px'><?php echo  $lang->reviewissue->dealDesc  ;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(!empty($issueList)) :?>
                            <?php foreach($issueList as $issue):?>
                                <tr>
                                    <td class="text-left"><?php echo  html_entity_decode($issue->title);?></td>
                                    <td class="text-left text-ellipsis"><?php echo $issue->desc ;?></td>
                                    <td><?php echo zget($lang->reviewissue->typeList,$issue->type,'');?></td>
                                    <td><?php echo zget($users,$issue->raiseBy,'');?></td>
                                    <td><?php echo $issue->raiseDate;?></td>
                                    <td><?php echo zget($lang->reviewissue->statusList,$issue->status,'');?></td>
                                    <td><?php echo zget($users,$issue->resolutionBy,'');?></td>
                                    <td><?php echo $issue->resolutionDate;?></td>
                                    <td><?php echo zget($users,$issue->validation,'');?></td>
                                    <td><?php echo $issue->verifyDate;?></td>
                                    <td class="text-left text-ellipsis"><?php echo html_entity_decode($issue->dealDesc);?></td>
                                </tr>
                            <?php endforeach;?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11"> <?php echo   "<div class='text-center text-muted'>" . $lang->noData . '</div>';?></td>
                            </tr>
                        <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <div class='cell'>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=review&objectID=$review->id");?>
        <?php include '../../common/view/action.html.php';?></div>

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
    if (scroll_height>window_height){
        var _top = 120;
        if (window_height<=700){
            _top = 60
        }
        $(".dialog").append(".modal-dialog{top:"+_top+'px'+"!important}");
    }
</script>
<?php include '../../common/view/footer.html.php';?>
