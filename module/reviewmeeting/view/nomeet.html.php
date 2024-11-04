<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/ext/view/calendar.html.php';?>
<style>
    #queryBox .table td{overflow: unset}
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php foreach($lang->reviewmeeting->noMeetMenu as $key => $label):?>
            <?php $key = strtolower($key);?>
            <?php $active = $status == $key ? 'btn-active-text' : '';?>
            <?php $label = "<span class='text'>$label</span>";?>
            <?php if($status == $key) {$label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";}?>
            <?php echo html::a(inlink('nomeet', "type=$key"), $label, '', "class='btn btn-link $active'");?>
        <?php endforeach;?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
</div>
<div id="mainContent" class="main-row fade">
    <div class='main-col'>
        <div class="cell<?php if($status == 'bysearch') echo ' show';?>" id="queryBox" data-module='reviewnomeet'></div>
        <?php if(empty($reviewList)):?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData;?></span>
                </p>
            </div>
        <?php else:?>
            <?php include '../../common/view/datatable.fix.html.php';?>
            <form class='main-table' method='post' id='reviewNoMeetForm' >
                <div class="table-header fixed-right">
                    <nav class="btn-toolbar pull-right"></nav>
                </div>
                <?php
                $vars = "status=$status&queryID=$queryID&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
                include '../../common/view/datatable.html.php';

                $setting = $this->datatable->getSetting('reviewmeeting');
                $widths  = $this->datatable->setFixedFieldWidth($setting);
                $widths['rightWidth'] = '65';
                ?>
                <table class='table has-sort-head datatable id' id='reviewManageList'  data-checkByClickRow="true" data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' >
                    <thead>
                    <tr>
                        <?php
                        foreach($setting as $value)
                        {
                            //不参与排序初审部门，初审部门接口人，初审主审人员，初审参与人员
                            if(in_array($value->id,['trialDept','trialDeptLiasisonOfficer','trialAdjudicatingOfficer','trialJoinOfficer'])){
                                $value->sort = 'no';
                            }
                            if($value->id == 'actions'){
                                $value->width = '65';
                            }
                            if($value->id == 'createdDept'){
                                $value->title ='创建部门';
                            }
                            if($value->show)
                            {
                                $this->datatable->printHead($value, $orderBy, $vars, true);
                            }
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($reviewList as $review):?>
                        <tr data-id='<?php echo $review->id?>'>
                            <?php foreach($setting as $value) $this->reviewmeeting->printCell($value, $review, $users, $products,1);?>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <div class='table-footer'>
                    <div class="pull-left">
                        <?php common::printLink('reviewmeeting', 'setmeeting', "", "<i class='icon icon-calendar' style='font-size: 12px;'></i>" .$lang->reviewmeeting->meeting->scheduling, '', "class='btn btn-primary iframe' id='set_btn' style='display:none'",'true');?>
                        <button class="btn btn-primary set_btn" style="display: none;" type="button" title="<?php echo $lang->reviewmeeting->setmeeting;?>" onclick="checkType()"><i class='icon icon-calendar' style='font-size: 12px;'></i><?php echo $lang->reviewmeeting->meeting->scheduling?></button>
                    </div>
                    <?php $pager->show('right', 'pagerjs');?>
                </div>
            </form>
        <?php endif;?>
    </div>
</div>
<script>
    function checkType() {
        var ids = ""
        $("[name='idList[]']").each(function (i) {
            if ($(this).is(":checked")) {
                ids += $(this).val() + ',';
            }
        });
        var link = createLink("reviewmeeting", "setmeeting", "ids=" + ids + "&types=1");
        $.get(link,{},function (res) {
            if (res == 'fail'){
                alert("评审类型不一致");
            }else{
                $("body #set_btn").click()
            }
        })
    }
    $(function(){$('#reviewNoMeetForm').table();})
    $("[name='idList[]']").attr("onclick","selectMeet()")
    $("body").on("click",".check-all",function () {
        selectMeet();
    })
    function selectMeet() {
        var ids = ""
        $("[name='idList[]']").each(function (i) {
            if ($(this).is(":checked")) {
                ids += $(this).val() + ',';
            }
        });
        if (ids != ''){
            var checklink = createLink("reviewmeeting",'ajaxcheckSetmeeting','ids='+ids)
            $.get(checklink,{},function (res) {
                if (res == 0){
                    $(".set_btn").attr("disabled",true);
                }else{
                    $(".set_btn").attr("disabled",false);
                }
            })
            var link = createLink("reviewmeeting","setmeeting","ids="+ids)
            $("#set_btn").attr("href",link+"?onlybody=yes");
            $(".set_btn").slideDown();
        }else{
            $(".set_btn").slideUp()
        }
    }
</script>

<script>
    //调整位置
    $(document).ready(function(){
       // $("th[data-index='21']").addClass("datatable-head-cell c-actions text-left");
         $("th[title='操作']").addClass("datatable-head-cell c-actions text-left");
        if($('button').hasClass('disabled btn')){
                 $('button i').removeAttr('data-toggle')
        }

    });
</script>
<?php include '../../common/view/footer.html.php';?>
