<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/ext/view/calendar.html.php';?>

<style>
    #tableCustomBtn+.dropdown-menu > li:last-child{display: none}
    #queryBox .table td{overflow: unset}

</style>

<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php foreach($lang->reviewmeeting->allMeetMenu as $key => $label):?>
        <?php $key = strtolower($key);?>
            <?php $active = $status == $key ? 'btn-active-text' : '';?>
            <?php $label = "<span class='text'>$label</span>";?>
            <?php if($status == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
            <?php echo html::a(inlink('meetingreview', "status=$key"), $label, '', "class='btn btn-link $active'");?>
        <?php endforeach;?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
    </div>
</div>

<div id="mainContent" class="main-row fade">
    <div class='main-col'>
        <div class="cell<?php if($status == 'bysearch') echo ' show';?>" id="queryBox" data-module='reviewmeeting'></div>
        <?php if(empty($reviewList)):?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData;?></span>
                </p>
            </div>
        <?php else:?>
            <form class='main-table' method='post' id='reviewMeetingForm'>
                <div class="table-header fixed-right">
                    <nav class="btn-toolbar pull-right"></nav>
                </div>
                <?php
                $vars = "status=$status&queryID=$queryID&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
                include '../../common/view/datatable.html.php';

                $setting = $this->datatable->getSetting('reviewmeet');
                $widths  = $this->datatable->setFixedFieldWidth($setting);
                $widths['rightWidth'] = '180';
                ?>
                <table class='table has-sort-head datatable' id='reviewManageList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>'>
                    <thead>
                    <tr>
                        <?php
                        foreach($setting as $value)
                        {
                            if($value->id == 'actions'){
                                $value->width = '180';
                            }
                            if($value->show)
                            {
                                $this->datatable->printHead($value, $orderBy, $vars, false);
                            }
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($reviewList as $review):?>
                        <tr data-id='<?php echo $review->id?>'>
                            <?php foreach($setting as $value) $this->reviewmeeting->printMeetCell($value, $review, $users, $products);?>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <div class='table-footer'><?php $pager->show('right', 'pagerjs');?></div>
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
    $(function(){$('#reviewManageForm').table();})
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
     $(document).ready(function(){
            if($('button').hasClass('disabled btn')){
                     $('button i').removeAttr('data-toggle')
            }

        });
</script>
<?php include '../../common/view/footer.html.php';?>
