<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .input-group-addon{min-width: 100px;} .input-group{margin-bottom: 6px;}
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="main-content">
        <div class="main-header">
            <h2><?php echo $lang->componentpublicaccount->create; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th></th>
                        <td colspan='6'><span style="color:#ed3131;"><?php echo $lang->componentpublicaccount->componentpublicaccountWaring?></span></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublicaccount->createcomponentname; ?></th>
                        <td class='required'  colspan='2'><?php echo html::select('componentName', $componentNames, empty($componentName)? '' : $componentName, "class='form-control chosen' onchange='changeComponent(this.value)'"); ?></td>
                        <th><?php echo $lang->componentpublicaccount->createcomponentversion; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('componentVersion', $versions, empty($componentVersion)? '' : $componentVersion, "class='form-control chosen' onchange='changeVersion()'"); ?></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td colspan='6'><span style="color:#fb9902;"><?php echo $lang->componentpublicaccount->componentpublicaccountTip?></span></td>
                    </tr>
                    <tr class="component-partition">
                        <th><?php echo $lang->componentpublicaccount->componentProjectList;?></th>
                        <?php if(empty($componentList)):?>
                        <td colspan="10" class="required component-partitions-content">
                            <div class="table-row component-partitions">
                                <div class="table-col w-250px">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $lang->componentpublicaccount->projectDept;?></span>
                                        <?php echo html::select('projectDept[0]', $depts, ' ', "id='projectDept0' data-index='0' class='form-control chosen' onchange='changeDept(this.value,this.id)'");?>
                                    </div>
                                </div>
                                <div class="table-col w-250px">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $lang->componentpublicaccount->projectName;?></span>
                                        <?php echo html::select('projectName[0]', '', ' ', "id='projectName0' class='form-control chosen'");?>
                                    </div>
                                </div>
                                <div class="table-col w-250px">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $lang->componentpublicaccount->startYear;?></span>
                                        <?php echo html::select('startYear[0]', $lang->componentpublicaccount->years,'', "id='startYear0' class='form-control chosen'");?>
                                    </div>
                                </div>
                                <div class="table-col w-250px">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $lang->componentpublicaccount->startQuarter;?></span>
                                        <?php echo html::select('startQuarter[0]', $quarters, ' ', "id='startQuarter0' class='form-control chosen'");?>
                                    </div>
                                </div>
                                <div class="table-col w-400px">
                                    <div class="input-group">
                                        <span class="input-group-addon fix-border fix-padding"><?php echo $lang->componentpublicaccount->comment;?></span>
                                        <?php echo html::input('comment[0]', '', "maxlength='300' id='comment0' data-index='0' class='form-control'");?>

                                    </div>
                                </div>
                                <div class="table-col actionCol" style="width:80px">
                                    <div class="btn-group">

                                        <a class="btn" href="javascript:void(0)" onclick="addPartition(this)" data-id='0' id='addItem0' ><i class="icon-plus"></i></a>
                                        <a class="btn" href="javascript:void(0)" onclick="delPartition(this)" ><i class="icon-close"></i></a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <?php else:?>
                            <td colspan="10" class="required component-partitions-content">
                            <?php foreach($componentList as $key => $component):?>
                                    <div class="table-row component-partitions">
                                        <input type="hidden" id="dataid<?php echo $key;?>" name = "id[<?php echo $key;?>]" value="<?php echo $component->id; ?>">
                                        <div class="table-col w-250px">
                                            <div class="input-group">
                                                <span class="input-group-addon"><?php echo $lang->componentpublicaccount->projectDept;?></span>
                                                <?php echo html::select("projectDept[$key]", $depts, $component->projectDept, "id='projectDept{$key}' data-index=$key class='form-control chosen' onchange='changeDept(this.value,this.id)'");?>
                                            </div>
                                        </div>
                                        <div class="table-col w-250px">
                                            <div class="input-group">
                                                <span class="input-group-addon"><?php echo $lang->componentpublicaccount->projectName;?></span>
                                                <?php echo html::select("projectName[$key]", $component->projects, $component->projectName, "id='projectName{$key}' class='form-control chosen'");?>
                                            </div>
                                        </div>
                                        <div class="table-col w-250px">
                                            <div class="input-group">
                                                <span class="input-group-addon"><?php echo $lang->componentpublicaccount->startYear;?></span>
                                                <?php echo html::select("startYear[$key]",$lang->componentpublicaccount->years, $component->startYear, "id='startYear{$key}' class='form-control chosen'");?>
                                            </div>
                                        </div>
                                        <div class="table-col w-250px">
                                            <div class="input-group">
                                                <span class="input-group-addon"><?php echo $lang->componentpublicaccount->startQuarter;?></span>
                                                <?php echo html::select("startQuarter[$key]", $quarters, $component->startQuarter, "id='startQuarter{$key}' class='form-control chosen'");?>
                                            </div>
                                        </div>
                                        <div class="table-col w-400px">
                                            <div class="input-group">
                                                <span class="input-group-addon fix-border fix-padding"><?php echo $lang->componentpublicaccount->comment;?></span>
                                                <?php echo html::input("comment[$key]", $component->comment, "maxlength='300' id='comment{$key}' data-index=$key class='form-control commentaction'");?>

                                            </div>
                                        </div>
                                        <div class="table-col actionCol" style="width:80px">
                                            <div class="btn-group">

                                                <a  href="javascript:void(0)" onclick="addPartition(this)" data-id=<?php echo $key;?> id='addItem<?php echo $key;?>' class="btn"><i class="icon-plus"></i></a>
                                                <a  href="javascript:void(0)" onclick="delPartition(this)" class="btn "><i class="icon-close"></i></a>
                                            </div>
                                        </div>
                                    </div>

                            <?php endforeach; ?>
                            </td>
                        <?php endif;?>
                    </tr>
                    </tbody>
                </table>
            </div>
    </div>
    <table class="table table-form">
        <tbody>
        <tr>
            <td class='form-actions text-center' colspan='5'>
                <?php if(!empty($maintainer->maintainer) && $maintainer->maintainer == $this->app->user->account || empty($maintainer->maintainer)) echo html::submitButton(); ?>
                <?php echo html::backButton(); ?>
        </tr>
        </tbody>
    </table>
    </form>
</div>
<hr />
<div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
</div>
<script>
    <?php
    if(isset($componentList) && $componentList){
        $count = count($componentList)-1;
    }else{
        $count = 0;
    }
    ?>
    var partitionIndex = <?php echo $count;?>;
    function addPartition(obj)
    {
        var originIndex = $(obj).attr('data-id');
        partitionIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();


        $currentRow.find('#addItem' + originIndex).attr({'data-id': partitionIndex, 'id':'addItem' + partitionIndex});
        $currentRow.find('#dataid' + originIndex).val(0);
        $currentRow.find('#dataid' + originIndex).attr({ 'id':'dataid'+partitionIndex,'name':'id['+partitionIndex+']'});


        $currentRow.find('#projectDept' + originIndex + '_chosen').remove();
        $currentRow.find('#projectDept' + originIndex).attr({'id':'projectDept' + partitionIndex,'name':'projectDept['+partitionIndex+']'});

        $currentRow.find('#projectName' + originIndex + '_chosen').remove();
        $currentRow.find('#projectName' + originIndex).attr({'id':'projectName' + partitionIndex,'name':'projectName['+partitionIndex+']'});

        $currentRow.find('#startYear' + originIndex + '_chosen').remove();
        $currentRow.find('#startYear' + originIndex).attr({'id':'startYear' + partitionIndex,'name':'startYear['+partitionIndex+']'});

        $currentRow.find('#startQuarter' + originIndex + '_chosen').remove();
        $currentRow.find('#startQuarter' + originIndex).attr({'id':'startQuarter' + partitionIndex,'name':'startQuarter['+partitionIndex+']'});

        $currentRow.find('#comment' + originIndex).attr({'id':'comment' + partitionIndex,'name':'comment['+partitionIndex+']'});

        $(obj).parent().parent().parent().after($currentRow);

        $('#projectDept' + partitionIndex).attr('class','form-control chosen');
        $('#projectDept' + partitionIndex).chosen();
        $('#projectDept' + partitionIndex).val('').trigger("chosen:updated");

        $('#projectName' + partitionIndex).attr('class','form-control chosen');
        $('#projectName' + partitionIndex).chosen();
        $('#projectName' + partitionIndex).val('').trigger("chosen:updated");

        // $('#startYear' + partitionIndex).attr('class','form-control form-date');
        $('#startYear' + partitionIndex).attr('class','form-control chosen');
        $('#startYear' + partitionIndex).chosen();
        $('#startYear' + partitionIndex).val('').trigger("chosen:updated");

        $('#startQuarter' + partitionIndex).attr('class','form-control chosen');
        $('#startQuarter' + partitionIndex).chosen();
        $('#startQuarter' + partitionIndex).val('').trigger("chosen:updated");

        $('#comment' + partitionIndex).attr('class','form-control');
        $('#comment' + partitionIndex).val('');

        $('#projectDept'+partitionIndex).change();
    }

    function delPartition(obj)
    {
        var $currentRow = $(obj).parent().parent().parent();

        if($(".component-partitions").length > 1)
        {
            $currentRow.remove();
        }else if($(".component-partitions").length == 1){
            $currentRow.find(".chosen-controled").val('').trigger("chosen:updated");
            $currentRow.find(".commentaction").val('');
        }

    }

    function changeComponent(value){
        $.get(createLink('componentpublicaccount', 'ajaxGetVersionByComponentName', 'id=' + value), function(data)
        {
            $('#componentVersion').nextAll().remove();
            $('#componentVersion').replaceWith(data);
            $('#componentVersion').chosen();
        });
    }

    function changeDept(value,id)
    {
        var index = id.split('projectDept')[1]
        $.get(createLink('componentpublicaccount', 'ajaxGetProjects', 'id=' + value+'&index='+index), function(data)
        {
            $('#projectName' + index + '_chosen').remove();
            $('#projectName' + index).replaceWith(data);
            $('#projectName' + index).chosen();
        });
    }

    function changeVersion(value)
    {
        var component = $('#componentName').val();
        var componentVersion = $('#componentVersion').val();
        window.location.href = createLink('componentpublicaccount', 'create', 'component=' + component+'&version='+componentVersion);
    }

    $(".form-date").datetimepicker({
        startView:4,
        minView:4,

        format:'yyyy'
    });

</script>


<?php include '../../common/view/footer.html.php'; ?>
