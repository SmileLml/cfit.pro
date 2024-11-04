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
            <h2><?php echo $lang->copyrightqz->edit;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                    <tr>
                        <th class="w-140px">
                            <?php echo $lang->copyrightqz->docDownload;?>
                        </th>
                        <td>
                            <?php echo $this->fetch('file', 'printFiles', array('files' => $docDownload, 'fieldset' => 'false', 'object' => null, 'canOperate' => false, 'isAjaxDel' => false));?>        
                        </td>
                        <th class="w-140px"></th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->emisCode;?>
                        </th>
                        <td>
                            <?php echo html::select('emisCode', $emisCodeListWithoutUse, $copyrightqz->emisCode, "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->applicant;?>
                        </th>
                        <td>
                            <?php echo html::select('applicant', $users, $copyrightqz->applicant, "class='form-control chosen' disabled");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->fullname;?>
                        </th>
                        <td>
                            <?php echo html::input('fullname', $copyrightqz->fullname, "class='form-control'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->shortName;?>
                        </th>
                        <td>
                            <?php echo html::input('shortName', $copyrightqz->shortName, "class='form-control'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->version;?>
                        </th>
                        <td>
                            <?php echo html::input('version', $copyrightqz->version, "class='form-control'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->system;?>
                        </th>
                        <td>
                            <?php echo html::select('system', $lang->copyrightqz->systemList, $copyrightqz->system, "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <!-- 软件作品说明 -->
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->descType;?>
                        </th>
                        <td>
                            <?php echo html::select('descType', $lang->copyrightqz->descTypeList, $copyrightqz->descType, "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->devFinishedTime;?>
                        </th>
                        <td>
                            <?php echo html::input('devFinishedTime', $copyrightqz->devFinishedTime, "class='form-control form-date'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->description;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('description', $copyrightqz->description, "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                    <!-- 开发方式 -->
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->devMode;?>
                        </th>
                        <td>
                            <?php echo html::select('devMode', $lang->copyrightqz->devModeList, $copyrightqz->devMode, "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->publishStatus;?>
                        </th>
                        <td>
                            <?php echo html::select('publishStatus', $lang->copyrightqz->publishStatusList, $copyrightqz->publishStatus, "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <!-- 权利取得方式 -->
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->rightObtainMethod;?>
                        </th>
                        <td>
                            <?php echo html::select('rightObtainMethod', $lang->copyrightqz->rightObtainMethodList, $copyrightqz->rightObtainMethod, "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->firstPublicTime;?>
                        </th>
                        <td>
                            <?php echo html::input('firstPublicTime', $copyrightqz->firstPublicTime, "class='form-control form-date'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->rightRange;?>
                        </th>
                        <td>
                            <?php echo html::select('rightRange', $lang->copyrightqz->rightRangeList, $copyrightqz->rightRange, "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->firstPublicCountry;?>
                        </th>
                        <td>
                            <?php echo html::select('firstPublicCountry', $lang->copyrightqz->firstPublicCountryList, $copyrightqz->firstPublicCountry, "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->sourceProgramAmount;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('sourceProgramAmount', $copyrightqz->sourceProgramAmount, "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->firstPublicPlace;?>
                        </th>
                        <td>
                            <?php echo html::input('firstPublicPlace', $copyrightqz->firstPublicPlace, "class='form-control' placeholder='【例：北京海淀】'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->softwareType;?>
                        </th>
                        <td>
                            <?php echo html::select('softwareType', $lang->copyrightqz->softwareTypeList, $copyrightqz->softwareType, "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->isRegister;?>
                        </th>
                        <td>
                            <?php echo html::select('isRegister', $lang->copyrightqz->isRegisterList, $copyrightqz->isRegister, "class='form-control chosen' onchange='registerChange()'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->isOriRegisNumChanged;?>
                        </th>
                        <td>
                            <?php echo html::select('isOriRegisNumChanged', $lang->copyrightqz->isOriRegisNumChangedList, $copyrightqz->isOriRegisNumChanged, "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->oriRegisNum;?>
                        </th>
                        <td>
                            <?php echo html::select('oriRegisNum', $emisCodeList, $copyrightqz->oriRegisNum, "class='form-control chosen'");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->proveNum;?>
                        </th>
                        <td>
                            <?php echo html::input('proveNum', $copyrightqz->proveNum, "class='form-control'");?>
                        </td>
                    </tr>
                    <!-- 软件鉴别材料 -->
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->identityMaterial;?>
                        </th>
                        <td>
                            <?php echo html::select('identityMaterial', $lang->copyrightqz->identityMaterialList, $copyrightqz->identityMaterial, "class='form-control chosen'");?>
                        </td>
                        <th class="identityMaterial-99">
                            <?php echo $lang->copyrightqz->generalDeposit;?>
                        </th>
                        <td class="identityMaterial-99 required">
                            <?php echo html::select('generalDeposit', $lang->copyrightqz->generalDepositList, $copyrightqz->generalDeposit, "class='form-control chosen'");?>
                        </td>
                    </tr>
                    <tr>
                        <th class="identityMaterial-99">
                            <?php echo $lang->copyrightqz->generalDepositType;?>
                        </th>
                        <td class="identityMaterial-99 required" colspan="3">
                            <?php echo html::textarea('generalDepositType', $copyrightqz->generalDepositType, "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                    <tr>
                        <th class="identityMaterial-1">
                            <?php echo $lang->copyrightqz->exceptionalDeposit;?>
                        </th>
                        <td class="identityMaterial-1 required">
                            <?php echo html::select('exceptionalDeposit', $lang->copyrightqz->exceptionalDepositList, $copyrightqz->exceptionalDeposit, "class='form-control chosen'");?>
                        </td>
                        <th class="identityMaterial-1 exceptionalDeposit-99">
                            <?php echo $lang->copyrightqz->pageNum;?>
                        </th>
                        <td class="identityMaterial-1 exceptionalDeposit-99 required">
                            <?php echo html::number('pageNum', $copyrightqz->pageNum, "class='form-control'");?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->copyrightqz->filelist;?></th>
                        <td>
                            <div class='detail'>
                                <div class='detail-content article-content'>
                                    <?php
                                    if($copyrightqz->files){
                                        echo $this->fetch('file', 'printFiles', array('files' => $copyrightqz->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                                    }else{
                                        echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->file;?>
                            <i title="<?php echo $lang->copyrightqz->fileTip;?>" class="icon icon-help"></i>
                        </th>
                        <td colspan="3" class='required'>
                            <?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->devLanguage;?>
                        </th>
                        <td>
                            <?php echo html::select('devLanguage[]', $lang->copyrightqz->devLanguageList, $copyrightqz->devLanguage, "class='form-control chosen' multiple");?>
                        </td>
                        <th>
                            <?php echo $lang->copyrightqz->techFeatureType;?>
                        </th>
                        <td>
                            <?php echo html::select('techFeatureType[]', $lang->copyrightqz->techFeatureTypeList, $copyrightqz->techFeatureType, "class='form-control chosen' multiple");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->devHardwareEnv;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('devHardwareEnv', $copyrightqz->devHardwareEnv, "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->opsHardwareEnv;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('opsHardwareEnv', $copyrightqz->opsHardwareEnv, "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->devOS;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('devOS', $copyrightqz->devOS, "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->devEnv;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('devEnv', $copyrightqz->devEnv, "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->operatingPlatform;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('operatingPlatform', $copyrightqz->operatingPlatform, "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->operationSupportEnv;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('operationSupportEnv', $copyrightqz->operationSupportEnv, "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->devPurpose;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('devPurpose', $copyrightqz->devPurpose, "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->industryOriented;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('industryOriented', $copyrightqz->industryOriented, "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->mainFunction;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('mainFunction', $copyrightqz->mainFunction, "class='form-control' rows='4' placeholder=''");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->techFeature;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('techFeature', $copyrightqz->techFeature, "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang->copyrightqz->others;?>
                        </th>
                        <td colspan="3">
                            <?php echo html::textarea('others', $copyrightqz->others, "class='form-control' rows='4'");?>
                        </td>
                    </tr>
                   <!-- <tr>
                        <th><?php /*echo $lang->copyrightqz->consumed;*/?></th>
                        <td colspan="3" class="required">
                            <?php /*echo html::input('consumed', end($copyrightqz->consumed)->consumed, "class='form-control'");*/?>
                        </td>
                    </tr>-->
                    <tr class="nodes">
                        <th>
                            <?php echo $lang->copyrightqz->reviewNodes;?>
                            <i title="<?php echo $lang->copyrightqz->reviewNodesTip;?>" class="icon icon-help"></i>
                        </th>
                        <td colspan='3'>
                        <?php
                        foreach($lang->copyrightqz->reviewerList as $key => $nodeName):
                            $currentAccounts = '';
                            if(isset($nodesReviewers[$key]) && !empty($nodesReviewers[$key])):
                                $currentAccounts = implode(',', $nodesReviewers[$key]);
                            endif;
                        ?>
                        <div class='input-group node-item node<?php echo $key;?>'>
                            <span class='input-group-addon'><?php echo $nodeName;?></span>
                            <?php echo html::select("nodes[$key][]", $reviewers[$key], $currentAccounts, "class='form-control chosen' required multiple");?>
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
            var saveUrl = locUrl.replace('.html','-1.html')
            $('#dataform').attr('action',saveUrl)
            $('#dataform').submit();
        });
        $('#submit').click(function() {
            var saveUrl = locUrl.replace('.html','-0.html')
            $('#dataform').attr('action',saveUrl)
            $('#dataform').submit();
        });
        $('#reset').click(function() {
            $('input').val('')
            $('textarea').text('')
            $('textarea').val('')
            $('select').not('#applicant').val('').trigger("chosen:updated");
        })
        $('#identityMaterial').change()
        $('#exceptionalDeposit').change()
    })
    function registerChange(){
        var _val = $("#isRegister option:selected").val();
        if (_val == 99){
            $("#oriRegisNum").parent().addClass('required');
        }else{
            $("#oriRegisNum").parent().removeClass('required');
        }
    }
    registerChange();
    $('#emisCode').change(function(){
        var val = $(this).val();
        $.get(createLink('copyrightqz', 'ajaxGetProduct', "id=" + val), function(data)
        {
            data = JSON.parse(data);
            $('#fullname').val(data.dynacommCn);
            $('#version').val(data.versionNum);
        });
    })
    $('#identityMaterial').change(function(){
        var val = $(this).val();
        if(val=='99'){
            $('.identityMaterial-1').addClass('hidden')
            $('.identityMaterial-99').removeClass('hidden')
        }else{
            $('.identityMaterial-99').addClass('hidden')
            $('.identityMaterial-1').removeClass('hidden')
            $('#exceptionalDeposit').change()
        }
    })
    $('#exceptionalDeposit').change(function(){
        var val = $(this).val();
        if(val&&val.includes('99')){
            $('.exceptionalDeposit-99').removeClass('hidden')
        }else{
            $('.exceptionalDeposit-99').addClass('hidden')
        }
    })
</script>