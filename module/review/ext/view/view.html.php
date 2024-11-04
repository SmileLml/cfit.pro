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
</style>
<style class="dialog"></style>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
<?php if($this->app->rawModule == 'review'):?>
    <?php $browseLink = $this->session->reviewList ? $this->session->reviewList : inlink('browse', "project=$review->project");?>
<?php else:?>
    <?php $browseLink = $this->session->reviewmanageList ? $this->session->reviewmanageList : inlink('review');?>
<?php endif;?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
      <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
      <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $review->id?></span>
      <span class="text"><?php echo $review->title;?></span>
    </div>
    <?php else:?>
    <div class="page-title">
      <span class="text"><?php echo $review->title;?></span>
    </div>
    <?php endif;?>

  </div>
    <?php
        $params = "projectID=$review->project&reviewID=$review->id";
        if(!isonlybody()):
    ?>
    <div class="btn-toolbar pull-right">
        <?php if($this->app->rawModule == 'review'):?>
            <?php common::printLink('reviewissue', 'batchCreate', $params, "<i class='icon icon-plus'></i>" . $lang->reviewissue->batchCreate, '', "class='btn btn-secondary'");?>
            <?php common::printLink('reviewissue', 'create', $params, "<i class='icon icon-plus'></i>" . $lang->reviewissue->create, '', "class='btn btn-primary'");?>
        <?php else:?>
            <?php common::printLink('reviewproblem', 'batchCreate', $params, "<i class='icon icon-plus'></i>" . $lang->reviewissue->batchCreate, '', "class='btn btn-secondary'");?>
            <?php common::printLink('reviewproblem', 'create', $params, "<i class='icon icon-plus'></i>" . $lang->reviewissue->create, '', "class='btn btn-primary'");?>
        <?php endif;?>
    </div>
    <?php endif;?>
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
          <?php include 'viewReviewFlow.html.php';?>
      </div>
    <div class="cell">
          <div class='detail'>
             <div class='detail-title'><?php echo $lang->files;?>
                 <span class="action-span pull-right detail-content article-content">
                        <?php  common::printIcon('review', 'editfiles', "reviewID=$review->id", $review, 'list','edit', '', 'iframe', true,'data-position="50px"'); ?>
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
        if(isset($review->meetingInfo) && !empty($review->meetingInfo->meetingSummaryCode)):
            $meetingDetailInfo = $review->meetingDetailInfo;
          ?>
            <?php include 'viewReviewMeeting.html.php';?>
      <?php endif;?>

      <div class="cell">
          <?php include 'viewReviewIssue.html.php';?>
      </div>

      <?php include 'viewArchive.html.php';?>
      <?php include 'viewBaseLine.html.php';?>
      <div class='cell'>
          <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=review&objectID=$review->id");?>
          <?php include '../../../common/view/action.html.php';?>
      </div>
    <div class='main-actions' style="/*margin-left:150px">
      <div class="btn-toolbar">
        <?php $params = "reviewID=$review->id"; ?>
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
        $flag = $this->loadModel('review')->isClickable($review, 'recall');
        $click = $flag ? 'onclick="return recall()"' : '';
        $closeflag = $this->loadModel('review')->isClickable($review, 'close');
        $id = $review->id;
        $nodealissue = $this->loadModel('review')->getNoDealIssue($id);
        $count  = isset($nodealissue[$id]) ?  $nodealissue[$id] : '';
        $reviewTipMsg = $this->loadModel('review')->getReviewTipMsg($review->status);
        $suspendFlag = $this->loadModel('review')->isClickable($review, 'suspend');
        $dealUser = explode(',', str_replace(' ', '', $review->dealUser));

       $dealReviewflag = $this->loadModel('review')->isClickable($review, 'singleReviewDeal');
       /* $reviewDeals = [];
        $reviewDeal = $this->config->review->singleReviewDeal;
        if(isset($reviewDeal))  {
            $reviewDeals = explode(',', $reviewDeal);
        }*/
        $reviewer = [];
        if(isset($review->reviewer)){
            $reviewer = explode(',', $review->reviewer);
        }
        //取出最后一个评审人
        //判断当前用户是否是最后一个验证人
        $lastVerifyer ='';
        if(count($dealUser) == 1){
            $lastVerifyer = 1;
        }
        //是否允许审批
        $verFlag = '';
        $checkRes = $this->review->checkReviewIsAllowReview($review, $this->app->user->account);
        if($review->status == 'waitVerify' or $review->status == 'verifying' ){
            $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCount2($review->id,'createAndAccept');
            if($issueCount!=0 and $lastVerifyer ==1){
                $verFlag = 1;
            }elseif($issueCount!=0){
                $verFlag = 2;
            }
        }
        common::hasPriv('review', 'edit') ?  common::printIcon('review', 'edit',    $params."&flag =2", $review, 'button') : '';
        common::hasPriv('review', 'submit') ? common::printIcon('review', 'submit', $params, $review, 'button', 'play', '', 'iframe', true, 'data-position="50px"', $this->lang->review->submit) : '';
        common::hasPriv('review', 'recall') ? common::printIcon('review', 'recall', $params, $review, 'button', 'back', 'hiddenwin', '', '', "$click", $this->lang->review->recall) : '';
        common::hasPriv('review', 'assign') ? common::printIcon('review', 'assign', $params, $review, 'button','hand-right', '', 'iframe', true, 'data-position="50px" data-width="1200px"', $this->lang->review->assign) : '';
        //非最最后一个人验证时
        if(($review->status == 'waitVerify' or $review->status == 'verifying' )&&$verFlag ==2){
            $clickClose ='onclick="return reviewVerifyConfirm()"';
            common::hasPriv('review', 'review') ? common::printIcon('review', 'review', $params, $review, 'list', 'glasses', 'hiddenwin', 'iframe', true,"$clickClose", $reviewTipMsg) : '';
        }else{
            common::hasPriv('review', 'review') ? common::printIcon('review', 'review', $params, $review, 'button', 'glasses', '', 'iframe', true,' data-position = "50px" data-toggle="modal" data-type="iframe" data-width="1200px" ', $reviewTipMsg) : '';
        }
        common::hasPriv('review', 'reviewreport') ? common::printIcon('review', 'reviewreport',  $params, $review, 'button', 'bar-chart', '') : '';
        if($suspendFlag){
            common::hasPriv('review', 'suspend') ? common::printIcon('review', 'suspend', $params, $review, 'list', 'pause', '', 'iframe', true, 'data-position="50px"', $this->lang->review->suspend) : '';
        }else{
            common::hasPriv('review', 'renew') ? common::printIcon('review', 'renew', $params, $review, 'button', 'magic', '', 'iframe', true, 'data-position="50px"', $this->lang->review->renew) : '';
        }
        common::hasPriv('review', 'projectswap') ? common::printIcon('review', 'projectswap', $params, $review, 'button','swap', '', 'iframe', true, 'data-position="50px" data-width="1200px"', $this->lang->review->projectswap) : '';

        if($dealReviewflag){
            common::hasPriv('review', 'singlereviewdeal') ? common::printIcon('review', 'singlereviewdeal', $params, $review, 'button','restart', '', 'iframe', true, 'data-position="50px" data-width="1200px"', $this->lang->review->singleReviewDeal) : '';
        }
        // common::hasPriv('review', 'close') ? common::printIcon('review', 'close', $params, $review, 'list', 'off','', 'iframe', true, 'data-position="50px"', $this->lang->review->close) : '';
        if(common::hasPriv('review', 'close'))
        {
            if($closeflag)
            {
//                common::printIcon('review', 'close', $params, '', 'list', 'off', '', 'iframe', true, "data-position='50px' style='display:none;' id='reviewClose{$review->id}'", $this->lang->review->close);
//                common::printIcon('review', 'close', $params, '', 'list', 'off', '', 'iframe', true, "data-position='50px' style='display:none;' id='reviewClose{$review->id}'", $this->lang->review->close);
                echo '<a href="javascript:;" onclick="reviewClose('.$review->id.','.$count.')" class="btn"><i class="icon-review-close icon-off"></i></a>';

            }
            else
            {
                common::printIcon('review', 'close', $params, $review, 'button', 'off','', 'iframe', true, 'data-position="50px"', $this->lang->review->close);
//                common::printIcon('review', 'close', $params, $review, 'list', 'off','', 'iframe', true, $this->lang->review->close);
            }
        }
        common::hasPriv('review', 'delete') ? common::printIcon('review', 'delete', $params, $review, 'button', 'trash','', 'iframe', true, 'data-position="50px"', $this->lang->review->delete) : '';


        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
        <?php include 'viewBasicInfo.html.php';?>
    </div>
      <div class="cell">
          <?php include 'viewConsumedInfo.html.php';?>
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
<?php include '../../../common/view/footer.html.php';?>
