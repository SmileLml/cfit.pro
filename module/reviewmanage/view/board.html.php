<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datatable.html.php';?>
<?php error_reporting(E_ALL^E_NOTICE^E_WARNING)?>
<style>
    .calendar .cell-day.with-plus-sign .day>.heading:before{
        content: none;
    }
    .eventreview{
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
        width: auto!important;
        padding: 0 5px;
        margin: 0 0 1px;
        font-size: 12px;
        line-height: 18px;
        background-color: transparent;
        cursor: pointer;
    }
</style>

<div id="mainContent" class="main-row">

    <div class="main-col ">
        <!--待处理在线评审--->
        <div class='cell' style="max-height: <?php echo $maxHeight1;?>;" >
            <div class='detail' id="unsetIframe">
                <div class='detail-title'><?php echo $lang->reviewmanage->meetingMenu->waitFormalReview."【".$meetCount['waitFormalReview']."】";?>
                    <div class="btn-toolbar pull-right">
                        <?php common::printLink('reviewmanage', 'browse', "status=wait", "<i class='icon icon-checked'></i>" . $lang->reviewmanage->checkMore, '', "class='btn btn-primary '");?>
                    </div></div>

                <div id="mainContent" class="main-row fade">
                    <div class='main-col'>
                        <form class='main-table' method='post' id='reviewManageList1'>
                            <div class="table-header fixed-right">
                                <nav class="btn-toolbar pull-right"></nav>
                            </div>
                            <?php
                            $vars1 = "status=waitFormalReview&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";

                            //include '../../common/view/datatable.html.php';

                            $setting1 = $this->datatable->getSetting('waitreview');
                            $widths1  = $this->datatable->setFixedFieldWidth($setting1);
                            $widths1['rightWidth'] = '225';
                            ?>
                            <table class='table has-sort-head datatable id' id='reviewManageList'  data-checkByClickRow="true" data-fixed-left-width='<?php echo $widths1['leftWidth']?>' data-fixed-right-width='<?php echo $widths1['rightWidth']?>' >
                                <thead>
                                <tr style="height: 100px">
                                    <?php
                                    foreach($setting1 as $value)
                                    {
                                        //不参与排序初审部门，初审部门接口人，初审主审人员，初审参与人员
                                        if(in_array($value->id,['trialDept','trialDeptLiasisonOfficer','trialAdjudicatingOfficer','trialJoinOfficer'])){
                                            $value->sort = 'no';
                                        }
                                        if($value->show)
                                        {
                                            $this->datatable->printHead($value, $orderBy, $vars1, false);
                                        }
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($reviewList1 as $review):?>
                                    <tr data-id='<?php echo $review->id?>'>
                                        <?php foreach($setting1 as $value)  $this->reviewmanage->printReviewCell($value, $review, $users, $products);?>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>

                            <div class='table-footer'>
                                <!--<div class="pull-left">
                        <?php /*common::printLink('reviewmanage', 'setmeeting', "", "<i class='icon icon-calendar' style='font-size: 12px;'></i>" .$lang->reviewmanage->meeting->scheduling, '', "class='btn btn-primary iframe set_btn' style='display:none' data-width='700' data-height='500'",'true');*/?>
                    </div>-->
                                <?php $pager->show('right', 'pagerjs');?>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--待处理在线评审 end--->


        <!--待处理会议评审--->
        <div class='cell' style="max-height: <?php echo $maxHeight2; ?>;" >
            <div class='detail'>
                <div class='detail-title'><?php echo $lang->reviewmanage->meetingMenu->waitMeetingReview."【".$meetCount['waitMeetingReview']."】";?>
                    <div class="btn-toolbar pull-right">
                        <?php common::printLink('reviewmeeting', 'meetingreview', "status=wait", "<i class='icon icon-checked'></i>" . $lang->reviewmanage->checkMore, '', "class='btn btn-primary '");?>
                    </div>
                </div>

                <div id="mainContent" class="main-row fade">
                    <div class='main-col'>
                        <form class='main-table' method='post' id='reviewManageForm'>
                            <div class="table-header fixed-right">
                                <nav class="btn-toolbar pull-right"></nav>
                            </div>
                            <?php

                            $vars2 = "status=waitMeetingReview&orderBy=%s&recTotal=$recTotal2&recPerPage=$recPerPage2&pageID=$pageID2";
                            //include '../../common/view/datatable.html.php';

                            $setting2 = $this->datatable->getSetting('waitmeeting');
                            $widths2  = $this->datatable->setFixedFieldWidth($setting2);
                            $widths2['rightWidth'] = '225';
                            ?>
                            <table class='table has-sort-head datatable' id='reviewManageList2' data-fixed-left-width='<?php echo $widths2['leftWidth']?>' data-fixed-right-width='<?php echo $widths2['rightWidth']?>'>
                                <thead>
                                <tr>
                                    <?php if($status == 'wait'){ ?>
                                        <th>
                                            <?php echo html::checkbox('zmeet','','',"class='zmeet' onclick='setMeet()'");?>
                                        </th>
                                    <?php }?>
                                    <?php
                                    foreach($setting2 as $value)
                                    {
                                        if($value->id == 'actions'){
                                            $value->width = '150';
                                        }
                                        if($value->show)
                                        {
                                            $this->datatable->printHead($value, $orderBy, $vars2, false);

                                        }
                                    }
                                    ?>
                                </tr>

                                </thead>
                                <tbody>

                                <?php foreach($reviewList2 as $review):?>
                                    <tr data-id='<?php echo $review->id?>'>
                                        <?php foreach($setting2 as $value) $this->reviewmanage->printWaitMeetCell("waitmeeting",$value, $review, $users, $products);?>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                            <table class='table has-sort-head datatable' id='reviewManageList2Head' data-fixed-left-width='<?php echo $widths2['leftWidth']?>' data-fixed-right-width='<?php echo $widths2['rightWidth']?>'>
                                <thead>
                                <tr>
                                    <?php if($status == 'wait'){ ?>
                                        <th>
                                            <?php echo html::checkbox('zmeet','','',"class='zmeet' onclick='setMeet()'");?>
                                        </th>
                                    <?php }?>
                                    <?php
                                    foreach($setting2 as $value)
                                    {
                                        if($value->id == 'actions'){
                                            $value->width = '150';
                                        }
                                        if($value->show)
                                        {
                                            $this->datatable->printHead($value, $orderBy, $vars2, false);

                                        }
                                    }
                                    ?>
                                </tr>
                                </thead>
                            </table>

                           <!-- <div class='table-footer'><?php /*$pager2->show('right', 'pagerjs');*/?></div>-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--待处理会议评审 end--->


       <!--待参加会议评审--->
        <div class='cell' style="max-height: <?php echo $maxHeight3; ?>;" >
            <div class='detail'>
                <div class='detail-title'><?php echo$lang->reviewmanage->meetingMenu->waitjoin."【".$meetCount['waitjoin']."】";?>
                    <div class="btn-toolbar pull-right">
                        <?php common::printLink('reviewmeeting', 'suremeeting', "", "<i class='icon icon-checked'></i>" . $lang->reviewmanage->checkMore, '', "class='btn btn-primary '");?>
                    </div>
                </div>

                <div id="mainContent" class="main-row fade">
                    <div class='main-col'>
                        <form class='main-table' method='post' id='reviewManage3'>
                            <div class="table-header fixed-right">
                                <nav class="btn-toolbar pull-right"></nav>
                            </div>
                            <?php

                            $vars3 = "status=waitMeetingReview&orderBy=%s&recTotal=$recTotal3&recPerPage=$recPerPage3&pageID=$pageID3";
                            //include '../../common/view/datatable.html.php';

                            $setting3 = $this->datatable->getSetting('joinwait');
                            $widths3  = $this->datatable->setFixedFieldWidth($setting3);
                            $widths3['rightWidth'] = '100';
                            ?>
                            <table class='table has-sort-head datatable' id='reviewManageList3' data-fixed-left-width='<?php echo $widths3['leftWidth']?>' data-fixed-right-width='<?php echo $widths3['rightWidth']?>'>
                                <thead>
                                <tr>
                                    <?php if($status == 'wait'){ ?>
                                        <th>
                                            <?php echo html::checkbox('zmeet','','',"class='zmeet' onclick='setMeet()'");?>
                                        </th>
                                    <?php }?>
                                    <?php
                                    foreach($setting3 as $value)
                                    {
                                        if($value->id == 'actions'){
                                            $value->width = '50';
                                        }
                                        if($value->show)
                                        {
                                            $this->datatable->printHead($value, $orderBy, $vars3, false);
                                        }
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($reviewList3 as $review):?>
                                    <tr data-id='<?php echo $review->id?>'>
                                        <?php foreach($setting3 as $value) $this->reviewmanage->printWaitMeetCell("waitjoin",$value, $review, $users, $products);?>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                            <table class='table has-sort-head datatable' id='reviewManageList3Head' data-fixed-left-width='<?php echo $widths3['leftWidth']?>' data-fixed-right-width='<?php echo $widths3['rightWidth']?>'>
                                <thead>
                                <tr>
                                    <?php if($status == 'wait'){ ?>
                                        <th>
                                            <?php echo html::checkbox('zmeet','','',"class='zmeet' onclick='setMeet()'");?>
                                        </th>
                                    <?php }?>
                                    <?php
                                    foreach($setting3 as $value)
                                    {
                                        if($value->id == 'actions'){
                                            $value->width = '50';
                                        }
                                        if($value->show)
                                        {
                                            $this->datatable->printHead($value, $orderBy, $vars3, false);
                                        }
                                    }
                                    ?>
                                </tr>
                                </thead>
                            </table>

                           <!-- <div class='table-footer'><?php /*$pager3->show('right', 'pagerjs');*/?></div>-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
      <!--待参加会议评审 end--->


       <!--待处理评审问题--->
        <div class='cell' style="max-height: <?php echo $maxHeight4; ?>;" >
            <div class='detail'>
                <div class='detail-title'><?php echo$lang->reviewmanage->meetingMenu->waitReviewProblem."【".$meetCount['issue']."】";?>
                    <div class="btn-toolbar pull-right">
                        <?php common::printLink('reviewproblem', 'issue', "projectID=0&reviewID=0&browseType=wait", "<i class='icon icon-checked'></i>" . $lang->reviewmanage->checkMore, '', "class='btn btn-primary '");?>
                    </div>
                </div>
                <div id="mainContent" class="main-row fade">
                    <div class='main-col'>
                        <form class='main-table' method='post' id='reviewManage3'>
                            <div class="table-header fixed-right">
                                <nav class="btn-toolbar pull-right"></nav>
                            </div>
                            <?php

                            $vars4 = "status=problemissue&orderBy=%s&recTotal=$recTotal4&recPerPage=$recPerPage4&pageID=$pageID4";

                            $setting4 = $this->datatable->getSetting('reviewissue');
                            $widths4  = $this->datatable->setFixedFieldWidth($setting4);
                            $widths4['rightWidth'] = '100';
                            ?>
                            <table class='table has-sort-head datatable' id='reviewManageList4_1' data-fixed-left-width='<?php echo $widths4['leftWidth']?>' data-fixed-right-width='<?php echo $widths4['rightWidth']?>'>
                                <thead>
                                <tr>
                                    <?php
                                    foreach($setting4 as $value)
                                    {
                                        if($value->show)
                                        {
                                            $this->datatable->printHead($value, $orderBy, $vars4, false);
                                        }
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($reviewList4 as $review):?>
                                    <tr data-id='<?php echo $review->id?>'>
                                        <?php foreach($setting4 as $value) $this->reviewmanage->printIssueCell($value, $review, 0, $users, [],0,'noclose',$orderBy,$pager4);?>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </form>
    <!--                    <div class='table-footer'>-->
    <!--                        --><?php //$pager4->show('right', 'pagerjs');?>
    <!--                    </div>-->
                    </div>
                </div>
            </div>
        </div>
        <!--待处理评审问题 end--->

       <!--待处理清总评审--->
        <?php if(common::hasPriv('reviewqz', 'browse')):?>
            <div class='cell' style="max-height: <?php echo $maxHeight5;?>;">
            <div class='detail'>
                <div class='detail-title'><?php echo$lang->reviewmanage->meetingMenu->waitReviewQz."【".$meetCount['reviewqz']."】";?>
                    <div class="btn-toolbar pull-right">
                        <?php common::printLink('reviewqz', 'browse', "", "<i class='icon icon-checked'></i>" . $lang->reviewmanage->checkMore, '', "class='btn btn-primary '");?>
                    </div>
                </div>

                <div id="mainContent" class="main-row fade">
                    <div class='main-col'>
                        <form class='main-table' method='post' id='reviewManage3'>
                            <div class="table-header fixed-right">
                                <nav class="btn-toolbar pull-right"></nav>
                            </div>
                            <?php
                            // 该页面第5个待处理表(待处理清总评审,拼接参数以获取第五张表的表头)
                            $vars5 = "status=reviewqz&orderBy=%s";

                            $setting5 = $this->datatable->getSetting('reviewqz');
                            $widths5  = $this->datatable->setFixedFieldWidth($setting5);
                            $widths5['rightWidth'] = '100';
                            ?>
                            <table class='table has-sort-head datatable' id='reviewManageList5_1' data-fixed-left-width='<?php echo $widths5['leftWidth']?>' data-fixed-right-width='<?php echo $widths5['rightWidth']?>'>
                                <thead>
                                <tr>
                                    <?php
                                    foreach($setting5 as $value)
                                    {
                                        if($value->show)
                                        {
                                            $this->datatable->printHead($value, $orderBy, $vars5, false);
                                        }
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($reviewList5 as $review):?>
                                    <tr data-id='<?php echo $review->id?>'>
                                        <?php foreach($setting5 as $value) $this->reviewqz->printCell($value, $review, $users);?>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>

                            </table>
                        </form>
                    </div> <!--main-col end--->


                </div> <!--mainContent end--->
            </div>
        </div>
        <?php endif;?>
      <!--待处理清总评审 end--->

</div>

</div>

<?php include '../../common/view/footer.html.php';?>
<script type="application/javascript">
    $(window).load(function () {
        var disableButtonList = $($('#unsetIframe').find('button.disabled')).find('[data-toggle="modal"]');
        $(disableButtonList).attr('data-toggle','');
        $(disableButtonList).attr('data-type','');
    });
</script>
