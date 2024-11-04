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
    .requirementTitleDiv{
        display:none;
    }
    .requirementDeadlineDiv{
        display:none;
    }
    .requirementEndDiv{
        display:none;
    }
    .requirementBackgroundDiv{
        display:none;
    }
    .requirementOverviewDiv{
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
    <?php if(!$followOpinion):?>
        <h2 style="color:black;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo $lang->requirement->changeIng;?></h2>
    <?php else:?>
    <?php if(!$allowChange):?>
        <h2 style="color:black;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo $lang->requirement->ifAllowChange;?></h2>
    <?php else:?>
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->requirement->change;?>
                <!--清总任务单-->
                <?php if($isGuestcn):?>
                  <span class="text-danger help-text" style="margin-left:20px; font-size: 12px; font-style: normal;"><?php echo $lang->requirement->changeTips;?></span>
                <?php endif;?>
            </h2>

        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->requirement->alteration;?></th>
                    <td colspan='5'>
                        <div class="alteration">
                            <?php echo html::checkbox('alteration', $this->lang->requirement->alterationList);?>
                        </div>
                    </td>
                </tr>
                <div>
                    <tr class="requirementTitleDiv">
                        <th><?php echo $lang->requirement->name;?></th>
                        <td colspan='5'><?php echo $requirement->name;?></td>
                    </tr>
                    <tr class="requirementTitleDiv">
                        <th><?php echo $lang->requirement->changeTitle;?></th>
                        <td colspan='5' class='required'><?php echo html::input('changeTitle', '', "class='form-control'");?></td>
                    </tr>
                    <td colspan='6' class="requirementTitleDiv">
                        <div style='width:96%;margin:5px 0 5px 5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>

                <div>
                    <tr class="requirementOverviewDiv">
                        <th><?php echo $lang->requirement->requirementOverview;?></th>
                        <?php if(strip_tags($requirement->desc) == $requirement->desc):?>
                            <td colspan='5' style="white-space: pre-line"><?php echo $requirement->desc;?></td>
                        <?php else:?>
                            <td colspan='5'><?php echo $requirement->desc;?></td>
                        <?php endif;?>
                    </tr>
                    <tr class="requirementOverviewDiv">
                        <th><?php echo $lang->requirement->changeOverview;?></th>
                        <td colspan='5' class='required' style="white-space: pre-line"><?php echo html::textarea('changeOverview', '', "rows='6' class='form-control'");?></td>
                    </tr>
                    <td colspan='6' class="requirementOverviewDiv">
                        <div style='width:96%;margin:5px 0 5px 5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>

                <div>
                    <tr class="requirementDeadlineDiv">
                        <th><?php echo $lang->requirement->requirementDeadline;?></th>
                        <td colspan='5'><?php echo $requirement->deadLine;?></td>
                    </tr>
                    <tr class="requirementDeadlineDiv">
                        <th><?php echo $lang->requirement->changeDeadline;?></th>
                        <td colspan='5' class='required'><?php echo html::input('changeDeadline', '', "class='form-control form-date'");?></td>
                    </tr>
                    <td colspan='6' class="requirementDeadlineDiv">
                        <div style='width:96%;margin:5px 0 5px 5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>

                <div>
                    <tr class="requirementEndDiv">
                        <th><?php echo $lang->requirement->planEnd;?></th>
                        <td colspan='5'><?php echo $requirement->planEnd;?></td>
                    </tr>
                    <tr class="requirementEndDiv">
                        <th><?php echo $lang->requirement->changePlanEnd;?></th>
                        <td colspan='5' class='required'><?php echo html::input('changePlanEnd', '', "class='form-control form-date'");?></td>
                    </tr>
                    <td colspan='6' class="requirementEndDiv">
                        <div style='width:96%;margin:5px 0 5px 5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>

                <div>
                    <tr class="fileDiv">
                        <th><?php echo $lang->requirement->fileTitle;?></th>
                        <td colspan='5'>
                            <div class='detail'>
                                <div class='detail-content article-content'>
                                    <?php
                                    if($requirement->files){
                                        echo $this->fetch('file', 'printFiles', array('files' => $requirement->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false, 'isAjaxDel' => false));
                                    }else{
                                        echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="fileDiv">
                        <th><?php echo $lang->requirement->changeFile;?></th>
                        <td colspan='5' class='required'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
                    </tr>
                    <td colspan='6' class="fileDiv">
                        <div style='width:96%;margin:5px 0 5px 5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>

                <tr class="guestcnNoNeed">
                    <th><?php echo $lang->requirement->affectDemandChoose;?></th>
                    <td colspan='5'>
                        <div class="alteration">
                            <?php echo html::radio('affectDemandCheck', $this->lang->opinion->affectCheckList,'yes',"onchange='changeCheck(this.value)'");?>
                        </div>
                    </td>
                </tr>

                <div>
                    <tr class="affect guestcnNoNeed">
                        <th><?php echo $lang->requirement->affectDemand;?></th>
                        <td colspan='5' class='required'><?php echo html::select('affectDemand[]',$affectDemands, $selectDemandIds,"class='form-control chosen' multiple");?></td>
                    </tr>
                </div>
                <tr>
                    <th><?php echo $lang->requirement->changeReason;?></th>
                    <td colspan='5' class='required' style="white-space: pre-line"><?php echo html::textarea('changeReason', '', "rows='6' class='form-control'");?></td>
                </tr>
                <div>
                    <tr class="usersDiv">
                        <th><?php echo $lang->requirement->manage;?></th>
                        <td colspan='5'class='required'><?php echo html::select('po', $poUsers,'', "class='form-control chosen'");?></td>
                    </tr>
                    <tr class="usersDiv guestcnNoNeed">
                        <th><?php echo $lang->requirement->deptLeader;?></th>
                        <td colspan='5' class='required'><?php echo html::select('deptLeader[]', $deptLeader, $define, "class='form-control chosen' multiple");?></td>
                    </tr>

                </div>
                <tr>
                    <td class='form-actions text-center' colspan='6'><?php echo html::submitButton($this->lang->requirement->submitBtn) . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <?php endif;?>
    <?php endif;?>
</div>
<?php js::set('chooseAlteration', $lang->requirement->chooseAlteration);?>
<?php js::set('defaultChoose', $defaultChoose);?>
<?php js::set('defineIndex', $defineIndex);?>
<!--判断是否是清总--->
<?php js::set('isGuestcn', $isGuestcn);?>

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
        //变更清总需求任务单(只允许修改计划完成时间)
        if(isGuestcn){
            var alterationArr = [];
            $('input[name="alteration[]"]').each(function(){
                var checkboxVal = $(this).val();
                if(checkboxVal == 'requirementEnd'){
                    $(this).prop("checked", true);
                    alterationArr.push(checkboxVal);
                }else {
                    $(this).prop("checked", false);
                    $(this).prop("disabled", true);
                }
            });
            setAlterationChoiceInfo(alterationArr);
            $('.guestcnNoNeed').addClass('hidden');
        }
    });

    /**
     * 设置变更选项信息
     *
     * @param alterationArr
     */
    function setAlterationChoiceInfo(alterationArr) {
        //需求意向主题
        var changeTitleIndex         = $.inArray("changeTitle",alterationArr)
        //需求意向概述
        var requirementOverviewIndex     = $.inArray("requirementOverview",alterationArr)
        //期望完成时间
        var requirementDeadlineIndex     = $.inArray("requirementDeadline",alterationArr)
        //计划完成时间
        var requirementEndIndex     = $.inArray("requirementEnd",alterationArr)
        //附件
        var requirementFileIndex   = $.inArray("requirementFile",alterationArr)

        if(changeTitleIndex >= 0)
        {
            $('.requirementTitleDiv').show();
        }else{
            $('.requirementTitleDiv').hide();
        }
        if(requirementDeadlineIndex >= 0)
        {
            $('.requirementDeadlineDiv').show();
        }else{
            $('.requirementDeadlineDiv').hide();
        }
        if(requirementEndIndex >= 0)
        {
            $('.requirementEndDiv').show();
        }else{
            $('.requirementEndDiv').hide();
        }
        if(requirementOverviewIndex >= 0)
        {
            $('.requirementOverviewDiv').show();
        }else{
            $('.requirementOverviewDiv').hide();
        }
        if(requirementFileIndex >= 0)
        {
            $('.fileDiv').show();
        }else{
            $('.fileDiv').hide();
        }
    }


    $('.alteration').change(function () {
        var alterationArr = [];
        $('input[name="alteration[]"]:checked').each(function(){
            alterationArr.push($(this).val());
        });
        setAlterationChoiceInfo(alterationArr);
    })


    function changeCheck($value)
    {
        if($value == 'yes')
        {
            $('.affect').show();
        }else{
            $('.affect').hide();
        }
    }
</script>