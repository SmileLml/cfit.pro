<?php //include '../../common/view/header.html.php';?>
<?php //include '../../common/view/datepicker.html.php';?>
<?php //include '../../common/view/kindeditor.html.php';?>
<!--<div id="mainContent" class="main-content fade">-->
<!--    <div class="center-block">-->
<!--        <div class="main-header">-->
<!--            <h2>--><?php //echo $lang->opinion->change;?><!--</h2>-->
<!--        </div>-->
<!--        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>-->
<!--            <table class="table table-form">-->
<!--                <tbody>-->
<!--                <tr>-->
<!--                    <th>--><?php //echo $lang->comment;?><!--</th>-->
<!--                    <td colspan='2'>--><?php //echo html::textarea('comment', '', "rows='6' class='form-control'");?><!--</td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td class='form-actions text-center' colspan='3'>--><?php //echo html::submitButton($this->lang->opinion->submit) . html::backButton();?><!--</td>-->
<!--                </tr>-->
<!--                </tbody>-->
<!--            </table>-->
<!--        </form>-->
<!--    </div>-->
<!--</div>-->
<?php //include '../../common/view/footer.html.php';?>

<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .table-form > tbody > tr > th {
        width: 137px;
        font-weight: 700;
        text-align: right;
    }
    .alteration{
        display:flex;flex-wrap:wrap;
    }
    .alteration>div{
        margin-right: 20px;
    }
    .opinionTitleDiv{
        display:none;
    }
    .opinionDeadlineDiv{
        display:none;
    }
    .opinionBackgroundDiv{
        display:none;
    }
    .opinionOverviewDiv{
        display:none;
    }
    .fileDiv{
        display:none;
    }
    /*.usersDiv{*/
    /*    display:none;*/
    /*}*/
</style>

<div id="mainContent" class="main-content fade">
    <?php if($allowChange === false):?>
        <h2 style="color:black;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo $lang->opinion->ifAllowChange;?></h2>
    <?php elseif($rChange === false):?>
        <h2 style="color:black;text-align: center;margin-top:-3%;letter-spacing:3px;"><?php echo $lang->opinion->noChangeTip;?></h2>
    <?php else:?>
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->opinion->change;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->opinion->alteration;?></th>
                    <td colspan='5'>
                        <div class="alteration">
                            <?php echo html::checkbox('alteration', $this->lang->opinion->alterationList);?>
                        </div>
                    </td>
                </tr>
                <div>
                    <tr class="opinionTitleDiv">
                        <th><?php echo $lang->opinion->name;?></th>
                        <td colspan='5'><?php echo $opinion->name;?></td>
                    </tr>
                    <tr class="opinionTitleDiv">
                        <th><?php echo $lang->opinion->changeTitle;?></th>
                        <td colspan='5' class='required'><?php echo html::input('changeTitle', '', "class='form-control'");?></td>
                    </tr>
                    <td colspan='6' class="opinionTitleDiv">
                        <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>
                <div>
                    <tr class="opinionBackgroundDiv">
                        <th><?php echo $lang->opinion->opinionBackground;?></th>
                        <?php if(strip_tags($opinion->background) == $opinion->background):?>
                            <td colspan='5' style="white-space: pre-line"><?php echo $opinion->background;?></td>
                        <?php else:?>
                            <td colspan='5'><?php echo $opinion->background;?></td>
                        <?php endif;?>
                    </tr>
                    <tr class="opinionBackgroundDiv">
                        <th><?php echo $lang->opinion->changeBackground;?></th>
                        <td colspan='5' class='required'>
                            <?php echo html::textarea('changeBackground', '', "rows='6' class='form-control'");?>
                        </td>
                    </tr>
                    <td colspan='6' class="opinionBackgroundDiv">
                        <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>
                <div>
                    <tr class="opinionOverviewDiv">
                        <th><?php echo $lang->opinion->opinionOverview;?></th>
                        <?php if(strip_tags($opinion->overview) == $opinion->overview):?>
                            <td colspan='5' style="white-space: pre-line"><?php echo $opinion->overview;?></td>
                        <?php else:?>
                        <td colspan='5'><?php echo $opinion->overview;?></td>
                        <?php endif;?>
                    </tr>
                    <tr class="opinionOverviewDiv">
                        <th><?php echo $lang->opinion->changeOverview;?></th>
                        <td colspan='5' class='required'><?php echo html::textarea('changeOverview', '', "rows='6' class='form-control'");?></td>
                    </tr>
                    <td colspan='6' class="opinionOverviewDiv">
                        <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>
                <div>
                    <tr class="opinionDeadlineDiv">
                        <th><?php echo $lang->opinion->opinionDeadline;?></th>
                        <td colspan='5'><?php echo $opinion->deadline;?></td>
                    </tr>
                    <tr class="opinionDeadlineDiv">
                        <th><?php echo $lang->opinion->changeDeadline;?></th>
                        <td colspan='5' class='required'><?php echo html::input('changeDeadline', '', "class='form-control form-date'");?></td>
                    </tr>
                    <td colspan='6' class="opinionDeadlineDiv">
                        <div style='width:96%;margin:5px 0 5px 5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>
                <div>
                    <tr class="fileDiv">
                        <th><?php echo $lang->opinion->opinionFile;?></th>
                        <td colspan='5'>
                            <div class='detail'>
                                <div class='detail-content article-content'>
                                    <?php
                                    if($opinion->files){
                                        echo $this->fetch('file', 'printFiles', array('files' => $opinion->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false, 'isAjaxDel' => false));
                                    }else{
                                        echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="fileDiv">
                        <th><?php echo $lang->opinion->changeFile;?></th>
                        <td colspan='5' class='required'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
                    </tr>
                    <td colspan='6' class="fileDiv">
                        <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>
                <tr>
                    <th><?php echo $lang->opinion->affectRequirementChoose;?></th>
                    <td colspan='5'>
                        <div class="alteration">
                            <?php echo html::radio('affectRequirementCheck', $this->lang->opinion->affectCheckList,'yes',"onchange='changeCheck(this.value)'");?>
                        </div>
                    </td>
                </tr>
                <tr class="affect">
                    <th><?php echo $lang->opinion->affectRequirement;?></th>
                    <td colspan='5' class='required'><?php echo html::select('affectRequirement[]', $requirement, $selectRequirementIds, "class='form-control chosen' multiple onchange='changeRequirement()'");?></td>
                </tr>
                <tr class="affectRequirementCheck">
                    <th><?php echo $lang->opinion->affectDemandChoose;?></th>
                    <td colspan='5'>
                        <div>
                            <?php echo html::radio('affectDemandCheck', $this->lang->opinion->affectCheckList,'yes',"onchange='changeDemandCheck(this.value)'");?>
                        </div>
                    </td>
                </tr>
                <tr class="affectDemand">
                    <th><?php echo $lang->opinion->affectDemand;?></th>
                    <td colspan='5' class='required'><?php echo html::select('affectDemand[]', $affectDemand, $selectDemandIds, "class='form-control chosen' multiple");?></td>
                </tr>
                <tr  class="affectDemand">
                    <th></th>
                    <td colspan='4' style="color:#F00010;"><?php echo $this->lang->opinion->affectDemandTip;?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->opinion->changeReason;?></th>
                    <td colspan='5' class='required'><?php echo html::textarea('changeReason', '', "rows='6' class='form-control'");?></td>
                </tr>
                <div>
                    <tr class="usersDiv">
                        <th><?php echo $lang->opinion->manage;?></th>
                        <td colspan='5'class='required'><?php echo html::select('po', $poUsers,'', "class='form-control chosen'");?></td>
                    </tr>
                    <tr class="usersDiv">
                        <th><?php echo $lang->opinion->deptLeader;?></th>
                        <td colspan='5' class='required'><?php echo html::select('deptLeader[]', $deptLeader, $define, "class='form-control chosen' multiple");?></td>
                    </tr>
                </div>
                <tr>
                    <td class='form-actions text-center' colspan='6'><?php echo html::submitButton($this->lang->opinion->submit) . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <?php endif;?>
</div>
<?php js::set('chooseAlteration', $lang->opinion->chooseAlteration);?>
<?php js::set('defaultChoose', $defaultChoose);?>
<?php js::set('defineIndex', $defineIndex);?>
<?php include '../../common/view/footer.html.php';?>
<script>
    $(document).ready(function() {
        //默认选中后台配置的人不可编辑
        if (defaultChoose) {
            setTimeout(function (args) {
                $("#deptLeader_chosen li").each(function () {
                    if ($(this).find(".search-choice-close").attr('data-option-array-index') == defineIndex) {
                        $(this).find(".search-choice-close").remove()
                    }
                })
            }, 10);
        }
    })

    $('.alteration').change(function () {
        var alterationArr = [];
        $('input[name="alteration[]"]:checked').each(function(){
            alterationArr.push($(this).val());
        });
        //需求意向主题
        var changeTitleIndex         = $.inArray("changeTitle",alterationArr)
        //需求意向背景
        var opinionBackgroundIndex   = $.inArray("opinionBackground",alterationArr)
        //需求意向概述
        var opinionOverviewIndex     = $.inArray("opinionOverview",alterationArr)
        //期望完成时间
        var opinionDeadlineIndex     = $.inArray("opinionDeadline",alterationArr)
        //附件
        var opinionFileIndex   = $.inArray("opinionFile",alterationArr)

        if(changeTitleIndex >= 0)
        {
            $('.opinionTitleDiv').show();
        }else{
            $('.opinionTitleDiv').hide();
        }
        if(opinionBackgroundIndex >= 0)
        {
            $('.opinionBackgroundDiv').show();
        }else{
            $('.opinionBackgroundDiv').hide();
        }
        if(opinionDeadlineIndex >= 0)
        {
            $('.opinionDeadlineDiv').show();
        }else{
            $('.opinionDeadlineDiv').hide();
        }
        if(opinionOverviewIndex >= 0)
        {
            $('.opinionOverviewDiv').show();
        }else{
            $('.opinionOverviewDiv').hide();
        }
        if(opinionFileIndex >= 0)
        {
            $('.fileDiv').show();
        }else{
            $('.fileDiv').hide();
        }
    })

    //是否涉及需求任务
    function changeCheck($value)
    {
        if($value == 'yes')
        {
            $('.affect').show();
            $('.affectRequirementCheck').show();
            $('#affectDemandCheckyes').attr('checked','checked');
            $('.affectDemand').show();
        }else{
            $('.affect').hide();
            $('.affectRequirementCheck').hide();
            $('.affectDemand').hide();
        }
    }
    //是否涉及需求条目
    function changeDemandCheck($value)
    {
        if($value == 'yes')
        {
            $('.affectDemand').show();
        }else{
            $('.affectDemand').hide();
        }
    }

    //更改需求任务，需求条目跟随联动
    function changeRequirement()
    {
        var checkValue = $("#affectRequirement").val();
        $.get(createLink('demand', 'ajaxGetDemandSelected', "ids=" + checkValue), function(data)
        {
            $('#affectDemand_chosen').remove();
            $('#affectDemand').replaceWith(data);
            $('#affectDemand').chosen();
        });
    }


</script>