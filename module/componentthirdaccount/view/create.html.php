<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .input-group-addon{min-width: 100px;} .input-group{margin-bottom: 6px;}
    .top-table{border-top: 0px solid; border-left: 0px solid; border-right: 0px solid;}
    .middle-table{border-left: 0px solid; border-right: 0px solid;}
    .tail-table{border-bottom: 0px solid; border-left: 0px solid; border-right: 0px solid;}
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
    .panel{border-color: #ddd;}
    .input-group-btn{padding: 4px;}
    .chosen-auto-max-width{width: 100% !important;}
    .errertip{color: #FFA500;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentthirdaccount->create; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->componentthirdaccount->appname; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('appname', $apps, '', "class='form-control chosen' onchange='selectApp(this.value)'"); ?></td>

                        <th><?php echo $lang->componentthirdaccount->productname; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('productname', '', '', "class='form-control chosen' onchange='selectProduct(this.value)'"); ?></td>

                        <th><?php echo $lang->componentthirdaccount->productversion; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('productversion', '', '', "class='form-control chosen' onchange='selectProductVersion(this.value)'"); ?></td>

                        <th><?php echo $lang->componentthirdaccount->tips;?><i title="<?php echo $lang->componentthirdaccount->tipContent;?>" class="icon icon-help"></i></th>
                    </tr>
                    <tr>
                        <th></th>
                        <td id = 'msg' class = 'hidden errertip' colspan="10" ><?php echo $lang->componentthirdaccount->notproductconnect;?></td>
                        <td id = 'msg1' class = 'hidden errertip' colspan="10" ></td>
                    </tr>
                    <tr class="component-partition">
                        <th><?php echo $lang->componentthirdaccount->usedbrowse;?></th>
                        <td colspan="10" class=" component-partitions-content">
                            <div class="table-row component-partitions">
                                <div class="table-col w-250px">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $lang->componentthirdaccount->createcomponentname;?></span>
                                        <?php echo html::select('componentname[0]', $components, ' ', "id='componentname0' data-index='0' class='form-control chosen' onchange='selectComponent(this.value,this.id)'");?>
                                    </div>
                                </div>
                                <div class="table-col w-250px">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $lang->componentthirdaccount->createcomponentversion;?></span>
                                        <?php echo html::select('componentversion[0]', '', ' ', "id='componentversion0' class='form-control chosen'");?>
                                    </div>
                                </div>
                                <div class="table-col w-400px">
                                    <div class="input-group">
                                        <span class="input-group-addon fix-border fix-padding"><?php echo $lang->componentthirdaccount->comment;?></span>
                                        <?php echo html::input('comment[0]', '', "maxlength='40' id='comment0' data-index='0' class='form-control'");?>
                                        <a class="input-group-btn" href="javascript:void(0)" onclick="addPartition(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                        <a class="input-group-btn" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="custom-component-partition">
                        <th><?php echo $lang->componentthirdaccount->customusedbrowse;?></th>
                        <td colspan="10" class=" custom-component-partitions-content">
                            <div class="table-row custom-component-partitions">
                                <div class="table-col w-250px">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $lang->componentthirdaccount->customComponent;?></span>
                                        <?php echo html::input('customComponent[0]', '', "id='customComponent0' data-index='0' class='form-control'");?>
                                    </div>
                                </div>
                                <div class="table-col w-250px">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo $lang->componentthirdaccount->customComponentVersion;?></span>
                                        <?php echo html::input('customComponentVersion[0]', '', "id='customComponentVersion0' data-index='0' class='form-control'");?>
                                    </div>
                                </div>
                                <div class="table-col w-400px">
                                    <div class="input-group">
                                        <span class="input-group-addon fix-border fix-padding"><?php echo $lang->componentthirdaccount->comment;?></span>
                                        <?php echo html::input('customcomment[0]', '', "maxlength='40' id='customcomment0' data-index='0' class='form-control'");?>
                                        <a class="input-group-btn" href="javascript:void(0)" onclick="addCustomPartition(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                        <a class="input-group-btn" href="javascript:void(0)" onclick="delCustomPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
    </div>
    <table class="table table-form">
        <tbody>
        <tr>
            <td class='form-actions text-center' colspan='5'>
                <?php echo html::submitButton(); ?>
                <?php echo html::backButton(); ?>
        </tr>
        </tbody>
    </table>
    </form>
</div>
</div>
<script>
    var partitionIndex = 0;
    var isConnect = false;
    function addPartition(obj)
    {
        var originIndex = $(obj).attr('data-id');
        partitionIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();

        $currentRow.find('#addItem' + originIndex).attr({'data-id': partitionIndex, 'id':'addItem' + partitionIndex});

        $currentRow.find('#componentname' + originIndex + '_chosen').remove();
        $currentRow.find('#componentname' + originIndex).attr({'id':'componentname' + partitionIndex,'name':'componentname['+partitionIndex+']'});

        $currentRow.find('#componentversion' + originIndex + '_chosen').remove();
        $currentRow.find('#componentversion' + originIndex).attr({'id':'componentversion' + partitionIndex,'name':'componentversion['+partitionIndex+']'});

        $currentRow.find('#comment' + originIndex).attr({'id':'comment' + partitionIndex,'name':'comment['+partitionIndex+']'});

        $(obj).parent().parent().parent().after($currentRow);

        $('#componentname' + partitionIndex).attr('class','form-control chosen');
        $('#componentname' + partitionIndex).chosen();
        $('#componentname' + partitionIndex).val('').trigger("chosen:updated");

        $('#componentversion' + partitionIndex).attr('class','form-control chosen');
        $('#componentversion' + partitionIndex).chosen();
        $('#componentversion' + partitionIndex).val('').trigger("chosen:updated");

        $('#comment' + partitionIndex).attr('class','form-control');
        $('#comment' + partitionIndex).val('');

        $('#componentname'+partitionIndex).change();
    }

    function delPartition(obj)
    {
        var $currentRow = $(obj).parent().parent().parent();

        if($(".component-partitions").length > 1)
        {
            $currentRow.remove();
        }

    }

    function selectApp(value){
        $.get(createLink('componentthirdaccount', 'ajaxGetproductByAppcreate', 'id=' + value), function(data)
        {
            $('#productname').nextAll().remove();
            $('#productname').replaceWith(data);
            $('#productname').chosen();
            $('#productname').change();
            selectProductVersion(0);
        });
    }

    function selectProduct(value){
        $.get(createLink('componentthirdaccount', 'ajaxGetVersionByProductcreate', 'id=' + value), function(data)
        {
            $('#productversion').nextAll().remove();
            $('#productversion').replaceWith(data);
            $('#productversion').chosen();
            selectProductVersion(0);
        });

        $.get(createLink('componentthirdaccount', 'ajaxGetProductConnect', 'id=' + value), function(data)
        {
            if(!data){
                $('#msg').removeClass('hidden');
                isConnect = false;
            }else{
                $('#msg').addClass('hidden');
                isConnect = true;
            }
        });
    }

    function selectComponent(value,id)
    {
        var index = id.split('componentname')[1]
        $.get(createLink('componentthirdaccount', 'ajaxGetVersionByComponentcreate', 'id=' + value+'&index='+index), function(data)
        {
            $('#componentversion' + index + '_chosen').remove();
            $('#componentversion' + index).replaceWith(data);
            $('#componentversion' + index).chosen();
        });
    }

    function selectProductVersion(value)
    {
        $.get(createLink('componentthirdaccount', 'ajaxGetAccount', 'id=' + value + '&isConnect='+isConnect), function(data)
        {
            if(data != null && data != ''){
                $('.component-partitions').remove();
                $('.component-partitions-content').append(data);
                $('.initClass').chosen();
                partitionIndex = $(".component-partitions").length-1;
                if(!isConnect){
                    $('.initClass').prop('disabled', true).trigger("chosen:updated");
                    $('.initCommentClass').attr('readonly','readonly');
                    $('.input-group-btn').addClass('hidden');
                    $('#submit').addClass('hidden');
                }else{
                    $('#submit').removeClass('hidden');
                    $.get(createLink('componentthirdaccount', 'ajaxGetTips', 'id=' + value), function(data)
                    {
                        if(!data == ''){
                            $('#msg1').removeClass('hidden');
                            $('#msg1').text(data);
                        }else{
                            $('#msg1').addClass('hidden');
                            $('#msg1').text('');
                        }
                    });
                }
            }
        });

        $.get(createLink('componentthirdaccount', 'ajaxGetCustomAccount', 'id=' + value + '&isConnect='+isConnect), function(data)
        {
            if(data != null && data != ''){
                $('.custom-component-partitions').remove();
                $('.custom-component-partitions-content').append(data);
                partitionCustomIndex = $(".custom-component-partitions").length-1;
                if(!isConnect){
                    $('.initCommentClass').attr('readonly','readonly');
                    $('.input-group-btn').addClass('hidden');
                    $('#submit').addClass('hidden');
                }else{
                    $('#submit').removeClass('hidden');
                }
            }
        });
    }

    var partitionCustomIndex = 0;
    function addCustomPartition(obj)
    {
        var originIndex = $(obj).attr('data-id');
        partitionCustomIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();

        $currentRow.find('#addItem' + originIndex).attr({'data-id': partitionCustomIndex, 'id':'addItem' + partitionCustomIndex});

        $currentRow.find('#customComponent' + originIndex).attr({'id':'customComponent' + partitionCustomIndex,'name':'customComponent['+partitionCustomIndex+']'});

        $currentRow.find('#customComponentVersion' + originIndex).attr({'id':'customComponentVersion' + partitionCustomIndex,'name':'customComponentVersion['+partitionCustomIndex+']'});

        $currentRow.find('#customcomment' + originIndex).attr({'id':'customcomment' + partitionCustomIndex,'name':'customcomment['+partitionCustomIndex+']'});

        $(obj).parent().parent().parent().after($currentRow);

        $('#customComponent' + partitionCustomIndex).attr('class','form-control');
        $('#customComponent' + partitionCustomIndex).val('');

        $('#customComponentVersion' + partitionCustomIndex).attr('class','form-control');
        $('#customComponentVersion' + partitionCustomIndex).val('');

        $('#customcomment' + partitionCustomIndex).attr('class','form-control');
        $('#customcomment' + partitionCustomIndex).val('');

    }

    function delCustomPartition(obj)
    {
        var $currentRow = $(obj).parent().parent().parent();

        if($(".custom-component-partitions").length > 1)
        {
            $currentRow.remove();
        }

    }

</script>


<?php include '../../common/view/footer.html.php'; ?>
