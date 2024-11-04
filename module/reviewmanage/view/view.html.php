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
if($this->session->reviewmanageList == 'board'):?>
    <?php $browseLink = $this->session->reviewmanageList ? $this->session->reviewmanageList : inlink('board'); ?>
<?php else:?>
    <?php $browseLink = $this->session->reviewmanageList ? $this->session->reviewmanageList : inlink('review');?>
<?php endif;?>

<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
      <?php echo html::backButton('<i class="icon icon-back icon-sm"></i>' . $lang->goback , '','btn btn-secondary');?>
      <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $review->id?></span>
      <span class="text"><?php echo $review->title;?></span>
    </div>
  </div>
    <div class="btn-toolbar pull-right">
        <?php
        //$params = "projectID=$review->project&reviewID=$review->id&source=reviewmanage";
        $params = "projectID=$review->project&reviewID=$review->id";
        ?>
        <?php common::printLink('reviewproblem', 'batchCreate', $params, "<i class='icon icon-plus'></i>" . $lang->reviewissue->batchCreate, '', "class='btn btn-secondary'");?>
        <?php common::printLink('reviewproblem', 'create', $params, "<i class='icon icon-plus'></i>" . $lang->reviewissue->create, '', "class='btn btn-primary'");?>
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
                        echo $this->fetch('file', 'printFiles', array('files' => $review->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => $isAllowOperateFile));
                    }else{
                        echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        if(isset($review->meetingDetailInfo) && !empty($review->meetingInfo->meetingSummaryCode) && $review->status != 'waitFormalReview'):
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
          <?php include '../../common/view/action.html.php';?>
      </div>

        <div class='main-actions' style="/*margin-left:150px">
            <div class="btn-toolbar">
                <?php $params = "reviewID=$review->id"; ?>
                <?php common::printBack($browseLink);?>
                <div class='divider'></div>
                <?php
                $flag = $this->loadModel('reviewmanage')->isClickable($review, 'recall');
                $click = $flag ? 'onclick="return recall()"' : '';
                $closeflag = $this->loadModel('reviewmanage')->isClickable($review, 'close');
                $id = $review->id;
                $nodealissue = $this->loadModel('review')->getNoDealIssue($id);
                $count  = isset($nodealissue[$id]) ?  $nodealissue[$id] : '';
                $dealUser = explode(',', str_replace(' ', '', $review->dealUser));

               $dealReviewflag = $this->loadModel('review')->isClickable($review, 'singleReviewDeal');
              /*  $reviewDeals = [];
                $reviewDeal = $this->config->review->singleReviewDeal;
                if(isset($reviewDeal))  {
                    $reviewDeals = explode(',', $reviewDeal);
                }*/

                //取出最后一个评审人
                //判断当前用户是否是最后一个验证人
                $lastVerifyer ='';
                if(count($dealUser) == 1){
                    $lastVerifyer = 1;
                }
                //是否允许审批
                $verFlag = '';
                $checkRes =  $this->loadModel('review')->checkReviewIsAllowReview($review, $this->app->user->account);
                if($review->status == 'waitVerify' or $review->status == 'verifying' ){
                    $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCount2($review->id,'createAndAccept');
                    if($issueCount!=0 and $lastVerifyer ==1){
                        $verFlag = 1;
                    }elseif($issueCount!=0){
                        $verFlag = 2;
                    }
                }
                $reviewTipMsg = $this->loadModel('review')->getReviewTipMsg($review->status);
                $suspendFlag = $this->loadModel('reviewmanage')->isClickable($review, 'suspend');
                common::hasPriv('reviewmanage', 'edit') ?  common::printIcon('reviewmanage', 'edit',    $params."&flag =2", $review, 'list') : '';
                common::hasPriv('reviewmanage', 'submit') ? common::printIcon('reviewmanage', 'submit', $params, $review, 'list', 'play', '', 'iframe', true, 'data-position="50px"', $this->lang->review->submit) : '';
                common::hasPriv('reviewmanage', 'recall') ? common::printIcon('reviewmanage', 'recall', $params, $review, 'list', 'back', 'hiddenwin', '', '', "$click", $this->lang->review->recall) : '';
                common::hasPriv('reviewmanage', 'assign') ? common::printIcon('reviewmanage', 'assign', $params, $review, 'list','hand-right', '', 'iframe', true, 'data-position="50px"  data-width="1200px"', $this->lang->review->assign) : '';
                //非最最后一个人验证时
                if(($review->status == 'waitVerify' or $review->status == 'verifying' )&&$verFlag ==2){
                    $clickClose ='onclick="return reviewVerifyConfirm()"';
                    common::hasPriv('reviewmanage', 'review') ? common::printIcon('reviewmanage', 'review', $params, $review, 'list', 'glasses', 'hiddenwin', 'iframe', true,"$clickClose", $reviewTipMsg) : '';
                }else{
                    common::hasPriv('reviewmanage', 'review') ? common::printIcon('reviewmanage', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true,'data-width="1200px" ', $reviewTipMsg) : '';
                }

               // common::hasPriv('reviewmanage', 'review') ? common::printIcon('reviewmanage', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true,'data-position="50px" data-toggle="modal" data-type="iframe" data-width="1200px"', $reviewTipMsg) : '';
                common::hasPriv('reviewmanage', 'reviewreport') ? common::printIcon('reviewmanage', 'reviewreport',  $params, $review, 'list', 'bar-chart', '') : '';
                if($suspendFlag){
                    common::hasPriv('reviewmanage', 'suspend') ? common::printIcon('reviewmanage', 'suspend', $params, $review, 'list', 'pause', '', 'iframe', true, 'data-position="50px"', $this->lang->review->suspend) : '';
                }else{
                    common::hasPriv('reviewmanage', 'renew') ? common::printIcon('reviewmanage', 'renew', $params, $review, 'list', 'magic', '', 'iframe', true, 'data-position="50px"', $this->lang->review->renew) : '';
                }
                if($dealReviewflag){
                    common::hasPriv('review', 'singlereviewdeal') ? common::printIcon('review', 'singlereviewdeal', $params, $review, 'button','restart', '', 'iframe', true, 'data-position="50px" data-width="1200px"', $this->lang->review->singleReviewDeal) : '';
                }
                // common::hasPriv('review', 'close') ? common::printIcon('review', 'close', $params, $review, 'list', 'off','', 'iframe', true, 'data-position="50px"', $this->lang->review->close) : '';
                if(common::hasPriv('reviewmanage', 'close'))
                {
                    if($closeflag)
                    {
//                        common::printIcon('reviewmanage', 'close', $params, '', 'list', 'off', '', 'iframe', true, "data-position='50px' style='display:none;' id='reviewClose{$review->id}'", $this->lang->review->close);
//                common::printIcon('review', 'close', $params, '', 'list', 'off', '', 'iframe', true, "data-position='50px' style='display:none;' id='reviewClose{$review->id}'", $this->lang->review->close);
                        echo '<a href="javascript:;" onclick="reviewClose('.$review->id.','.$count.')" class="btn"><i class="icon-review-close icon-off"></i></a>';

                    }
                    else
                    {
                        common::printIcon('reviewmanage', 'close', $params, $review, 'list', 'off','', 'iframe', true, 'data-position="50px"', $this->lang->review->close);
                    }
                }
                common::hasPriv('reviewmanage', 'delete') ? common::printIcon('reviewmanage', 'delete', $params, $review, 'list', 'trash','', 'iframe', true, 'data-position="50px"', $this->lang->review->delete) : '';
                echo html::a($this->createLink('reviewmanage', 'setVerifyResult', $params, '', true), '<i class="icon-time" title=""></i> '.$this->lang->review->setVerifyResult, '', "data-toggle='modal' data-type='iframe' class='btn iframe' data-position='50px' data-width='1200px'");
                echo html::a($this->createLink('reviewmanage', 'sendUnDealIssueUsersMail', $params, '', true), '<i class="icon-feedback" title=""></i> '.$this->lang->review->sendUnDealIssueUsersMail, '', "data-toggle='modal' data-type='iframe' class='btn iframe' data-position='50px' data-width='1200px'");
                ?>
            </div>
        </div>
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
