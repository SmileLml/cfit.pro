<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.files-list{margin-bottom: 0 !important;}
.input-group-addon{min-width: 150px;}
.input-group{margin-bottom: 2px;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->copyright->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                    <tr>
                        <th class="w-140px">
                            <?php echo $lang->copyright->docDownload;?>
                        </th>
                        <td >
                            <?php echo $this->fetch('file', 'printFiles', array('files' => $docDownload, 'fieldset' => 'false', 'object' => null, 'canOperate' => false, 'isAjaxDel' => false));?>        
                        </td>
                        <th class="w-140px"></th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->modifyCode;?>
                        </th>
                        <td>
                            <?php echo html::select('modifyCode', $modifyCodeWithoutUse, '', "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->createdBy;?>
                        </th>
                        <td>
                            <?php echo html::select('createdBy', $users, $creator, "class='form-control chosen' disabled");?>
                        </td>
                    </tr>
                    <tr class="product-partition">
                        <th class='w-100px'><?php echo $lang->copyright->product;?>
                            <i title="<?php echo $lang->copyright->productTip;?>" class="icon icon-help"></i></th>
                        <td colspan="3" class="required product-partitions-content">
                            <div class="table-row product-partitions">
                                <div class="table-col w-300px">
                                    <div class="input-group">
                                        <!--软件全称 -->
                                        <span class="input-group-addon"><?php echo $lang->copyright->fullname;?></span>
                                        <?php echo html::input('fullname[]', '', "id='fullname0' data-index='0' class='form-control'");?>
                                    </div>
                                </div>
                                <div class="table-col w-300px">
                                    <div class="input-group">
                                        <!--软件简称 -->
                                        <span class="input-group-addon fix-border fix-padding"><?php echo $lang->copyright->shortName;?></span>
                                        <?php echo html::input('shortName[]', '', "id='shortName0' class='form-control'");?>
                                    </div>
                                </div>
                                <div class="table-col w-300px">
                                    <div class="input-group">
                                        <!--软件版本号 -->
                                        <span class="input-group-addon fix-border fix-padding"><?php echo $lang->copyright->version;?></span>
                                        <?php echo html::input('version[]', '', "id='version0' class='form-control'");?>
                                        <a class="input-group-btn" href="javascript:void(0)" onclick="addPartition(this)" data-id='0' id='addItem0' class="btn btn-link"><i class="icon-plus"></i></a>
                                        <a class="input-group-btn" href="javascript:void(0)" onclick="delPartition(this)" class="btn btn-link"><i class="icon-close"></i></a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->buildDept;?>
                        </th>
                        <td>
                            <?php echo html::select('buildDept', $depts, '', "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->system;?>
                        </th>
                        <td>
                            <?php echo html::select('system', $systemList, '', "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <!-- 软件作品说明 -->
                    <tr>
                        <th>
                            <?php echo $lang->copyright->descType;?>
                        </th>
                        <td>
                            <?php echo html::select('descType', $lang->copyright->descTypeList, '', "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->devFinishedTime;?>
                        </th>
                        <td>
                            <?php echo html::input('devFinishedTime', '', "class='form-control form-date'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->description;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('description', '', "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                    <!-- 开发方式 -->
                    <tr>
                        <th>
                            <?php echo $lang->copyright->devMode;?>
                        </th>
                        <td>
                            <?php echo html::select('devMode', $lang->copyright->devModeList, '', "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->publishStatus;?>
                        </th>
                        <td>
                            <?php echo html::select('publishStatus', $lang->copyright->publishStatusList, '', "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <!-- 权利取得方式 -->
                    <tr>
                        <th>
                            <?php echo $lang->copyright->rightObtainMethod;?>
                        </th>
                        <td>
                            <?php echo html::select('rightObtainMethod', $lang->copyright->rightObtainMethodList, '', "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->firstPublicTime;?>
                        </th>
                        <td>
                            <?php echo html::input('firstPublicTime', '', "class='form-control form-date'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->rightRange;?>
                        </th>
                        <td>
                            <?php echo html::select('rightRange', $lang->copyright->rightRangeList, '', "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->firstPublicCountry;?>
                        </th>
                        <td>
                            <?php echo html::select('firstPublicCountry', $lang->copyright->firstPublicCountryList, '', "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->sourceProgramAmount;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('sourceProgramAmount', '', "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->firstPublicPlace;?>
                        </th>
                        <td>
                            <?php echo html::input('firstPublicPlace', '', "class='form-control' placeholder='【例：北京海淀】'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->softwareType;?>
                        </th>
                        <td>
                            <?php echo html::select('softwareType', $lang->copyright->softwareTypeList, '', "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->isRegister;?>
                        </th>
                        <td>
                            <?php echo html::select('isRegister', $lang->copyright->isRegisterList, '', "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->isOriRegisNumChanged;?>
                        </th>
                        <td>
                            <?php echo html::select('isOriRegisNumChanged', $lang->copyright->isOriRegisNumChangedList, '', "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->oriRegisNum;?>
                        </th>
                        <td>
                            <?php echo html::select('oriRegisNum', $codeList, '', "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->proveNum;?>
                        </th>
                        <td>
                            <?php echo html::input('proveNum', '', "class='form-control'");?>
                        </td>
                    </tr>
                    <!-- 软件鉴别材料 -->
                    <tr>
                        <th>
                            <?php echo $lang->copyright->identityMaterial;?>
                        </th>
                        <td>
                            <?php echo html::select('identityMaterial', $lang->copyright->identityMaterialList, '', "class='form-control chosen'");?>
                        </td>
                        <th class="identityMaterial-0 hidden">
                            <?php echo $lang->copyright->generalDeposit;?>
                        </th>
                        <td class="identityMaterial-01 hidden">
                            <?php echo html::select('generalDeposit', $lang->copyright->generalDepositList, '', "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <tr>
                        <th class="identityMaterial-0 hidden">
                            <?php echo $lang->copyright->generalDepositType;?>
                        </th>
                        <td class="identityMaterial-01 hidden" colspan="3">
                            <?php echo html::textarea('generalDepositType', '', "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                    <tr>
                        <th class="identityMaterial-1 hidden">
                            <?php echo $lang->copyright->exceptionalDeposit;?>
                        </th>
                        <td class="identityMaterial-11 hidden">
                            <?php echo html::select('exceptionalDeposit', $lang->copyright->exceptionalDepositList, '', "class='form-control chosen'");?>
                        </td>
                        <th class="exceptionalDeposit-99 hidden">
                            <?php echo $lang->copyright->pageNum;?>
                        </th>
                        <td  class="exceptionalDeposit-991 hidden">
                            <?php echo html::number('pageNum', '', "class='form-control'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->file;?>
                            <i title="<?php echo $lang->copyright->fileTip;?>" class="icon icon-help"></i>
                        </th>
                        <td colspan="3" class='required'>
                            <?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->devLanguage;?>
                        </th>
                        <td>
                            <?php echo html::select('devLanguage[]', $lang->copyright->devLanguageList, '', "class='form-control chosen' multiple");?>
                        </td>
                        <th>
                            <?php echo $lang->copyright->techFeatureType;?>
                        </th>
                        <td>
                            <?php echo html::select('techFeatureType[]', $lang->copyright->techFeatureTypeList, '', "class='form-control chosen' multiple");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->devHardwareEnv;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('devHardwareEnv', '', "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->opsHardwareEnv;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('opsHardwareEnv', '', "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->devOS;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('devOS', '', "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->devEnv;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('devEnv', '', "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->operatingPlatform;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('operatingPlatform', '', "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->operationSupportEnv;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('operationSupportEnv', '', "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->devPurpose;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('devPurpose', '', "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->industryOriented;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('industryOriented', '', "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->mainFunction;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('mainFunction', '', "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->techFeature;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('techFeature', '', "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyright->others;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('others', '', "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                   <!-- <tr>
                        <th><?php /*echo $lang->copyright->consumed;*/?></th>
                        <td colspan="3" class="required">
                            <?php /*echo html::input('consumed', '', "class='form-control'");*/?>
                        </td>
                    </tr>-->
                    <tr class="nodes">
                        <th>
                            <?php echo $lang->copyright->reviewNodes;?>
                            <i title="<?php echo $lang->copyright->reviewNodesTip;?>" class="icon icon-help"></i>
                        </th>
                        <td colspan='3'>
                        <?php
                        foreach($lang->copyright->reviewerList as $key => $nodeName):
                            $currentAccounts = '';
                            if(isset($reviewerAccounts[$key])):
                                $currentAccounts = implode(',', $reviewerAccounts[$key]);
                            endif;
                        ?>
                        <div class='input-group node-item node<?php echo $key;?>'>
                            <span class='input-group-addon'><?php echo $nodeName;?></span>
                            <?php
                                echo html::select("nodes[$key][]", $reviewers[$key], $currentAccounts, "class='form-control chosen' required multiple");
                           ?>
                        </div>
                        <?php endforeach;?>
                        </td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'><?php echo html::commonButton('提交','id="submit"','btn btn-wide btn-primary') . html::commonButton('保存','id="save"','btn btn-wide') . html::commonButton('重置','id="reset"','btn btn-wide') . html::backButton();?></td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>
    $(function() {
        var locUrl = location.href;
        $('#save').click(function() {
            var saveUrl = locUrl.replace('copyright-create','copyright-create-1')
            $('#dataform').attr('action',saveUrl)
            $('#dataform').submit();
        });
        $('#submit').click(function() {
            var saveUrl = locUrl.replace('copyright-create','copyright-create-0')
            $('#dataform').attr('action',saveUrl)
            $('#dataform').submit();
        });
        $('#reset').click(function() {
            $('input').val('')
            $('textarea').text('')
            $('textarea').val('')
            $('select').not('#createdBy').val('').trigger("chosen:updated");
        })
    })

    $('#modifyCode').change(function(){
        var val = $(this).val();
        //替换“-”为“_”
        var reg = new RegExp("-","g");
        var val = val.replace(reg,"_");
        var addButton = $('a[id^=addItem]')[0];
        //获取产品表单行数
        var rows = $(".table-row.product-partitions");
        var rowsLength = rows.length;
        //将产品表单行数删除到只剩1行
        if(rowsLength > 1){
            for(var i = 1; i < rowsLength; i++){
                delPartition01(rows[i]);
            }
        }
        $('input[id^=fullname]').val('');
        $('input[id^=version]').val('');
        $('input[id^=shortName]').val('');

        $.get(createLink('copyright', 'ajaxGetProduct', "id=" + val), function(data)
        {
            data = JSON.parse(data);
            productName = data.productName;
            if(productName.length > 0){
                var firstFullname = $('input[id^=fullname]').attr('id');
                var firstFullnameId = '#'+firstFullname;
                $(firstFullnameId).val(productName[0]);
                if(productName.length > 1){
                    addPartition(addButton);
                }
                for (var i = 1; i < productName.length; i++) {
                    var j = partitionIndex;
                    idNum = '#fullname'+j;
                    $(idNum).val(productName[i]);
                    if (i<productName.length-1){
                        addPartition(addButton);
                    }
                }
            }
        });
    })

    $('#identityMaterial').change(function(){
        var val = $(this).val();
        if(val&&val.includes('99')){
            $('.identityMaterial-01').addClass('required')
            $('.identityMaterial-0').removeClass('hidden')
            $('.identityMaterial-01').removeClass('hidden')
        }else{
            $('.identityMaterial-01').removeClass('required')
            $('.identityMaterial-0').addClass('hidden')
            $('.identityMaterial-01').addClass('hidden')
        }
        if(val&&val.includes('1')){
            $('.identityMaterial-11').addClass('required')
            $('.identityMaterial-1').removeClass('hidden')
            $('.identityMaterial-11').removeClass('hidden')
        }else{
            $('.identityMaterial-11').removeClass('required')
            $('.identityMaterial-1').addClass('hidden')
            $('.identityMaterial-11').addClass('hidden')
        }
    })
    $('#exceptionalDeposit').change(function(){
        var val = $(this).val();
        if(val&&val.includes('99')){
            $('.exceptionalDeposit-991').addClass('required')
            $('.exceptionalDeposit-99').removeClass('hidden')
            $('.exceptionalDeposit-991').removeClass('hidden')
        }else{
            $('.exceptionalDeposit-991').removeClass('required')
            $('.exceptionalDeposit-99').addClass('hidden')
            $('.exceptionalDeposit-991').addClass('hidden')
        }
    })

    var partitionIndex = 0;
    var isConnect = false;
    function addPartition(obj)
    {
        var originIndex = $(obj).attr('data-id');
        partitionIndex++;

        var $currentRow = $(obj).parent().parent().parent().clone();

        $currentRow.find('#addItem' + originIndex).attr({'data-id': partitionIndex, 'id':'addItem' + partitionIndex});

        $currentRow.find('#fullname' + originIndex).attr({'id':'fullname' + partitionIndex,'name':'fullname['+partitionIndex+']'});

        $currentRow.find('#shortName' + originIndex).attr({'id':'shortName' + partitionIndex,'name':'shortName['+partitionIndex+']'});

        $currentRow.find('#version' + originIndex).attr({'id':'version' + partitionIndex,'name':'version['+partitionIndex+']'});

        $(obj).parent().parent().parent().after($currentRow);

        $('#fullname' + partitionIndex).attr('class','form-control');
        $('#fullname' + partitionIndex).val('');

        $('#shortName' + partitionIndex).attr('class','form-control');
        $('#shortName' + partitionIndex).val('');

        $('#version' + partitionIndex).attr('class','form-control');
        $('#version' + partitionIndex).val('');

        $('#fullname'+partitionIndex).change();
    }

    function delPartition(obj)
    {
        var $currentRow = $(obj).parent().parent().parent();

        if($(".product-partitions").length > 1)
        {
            $currentRow.remove();
        }
    }

    function delPartition01(obj)
    {
        var $currentRow = $(obj)

        $currentRow.remove();
    }

</script>