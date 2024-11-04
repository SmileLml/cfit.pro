<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .inonelineTH{white-space: NOWRAP;padding-left: 40px!important;}
    .inonelineTD{padding-left: 200px!important;}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header" style="background-color: rgba(170, 170, 170, 1);">
            <h2><?php echo $lang->component->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <!--组件类型和申请方式选择-->
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-90px'><?php echo $lang->component->componentType;?></th>
                    <td class='required' colspan='2'>
                        <?php echo html::radio("type", $lang->component->type, "thirdParty","onclick=\"radioShow()\"");?>
                    </td>
                    <th class='w-150px'><?php echo $lang->component->application;?></th>
                    <td class='required' colspan='2'>
                            <?php echo html::radio('applicationMethod', $lang->component->applicationMethod, 'new');?>
                    </td>
                </tr>
                </tbody>
            </table>
                <div>
                    <!-- 组件类型：第三方组件 && 申请方式：新引入-->
                    <div id="newThirdParty">
                        <table class="table table-form">
                            <tbody>
                                <tr>
                                    <th><?php echo $lang->component->name;?></th>
                                    <td class='required' colspan='2'><?php echo html::input('newThirdPartyName',"", "maxlength='60' class='form-control' required");?></td>
                                    <th><?php echo $lang->component->version;?></th>
                                    <td class='required' colspan='2'><?php echo html::input('newThirdPartyVersion', "", "class='form-control' required");?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->component->developLanguage;?></th>
                                    <td class='required' colspan='2'><?php echo html::select('newThirdPartyDevelopLanguage', $lang->component->developLanguageList, '', "class='form-control chosen'");?></td>
                                    <th><?php echo $lang->component->licenseType;?></th>
                                    <td class='required' colspan='2'><?php echo html::input('licenseType', "", "maxlength='40' class='form-control'");?></td>
                                </tr>
                               <!-- <tr>
                                    <th><?php /*echo $lang->component->artifactId;*/?></th>
                                    <td colspan='2'><?php /*echo html::input('artifactId',"", "class='form-control'");*/?></td>
                                    <th><?php /*echo $lang->component->groupId;*/?></th>
                                    <td colspan='2'><?php /*echo html::input('groupId', "", "class='form-control'");*/?></td>
                                </tr>-->
                                <tr>
                                    <th><?php echo $lang->component->project;?></th>
                                    <td class='required' colspan='2'><?php echo html::select('newThirdPartyProjectId', $projectList, '', "class='form-control chosen'");?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->component->applicationReason;?></th>
                                    <td class='required' colspan='5'>
                                        <?php echo html::textarea('applicationReason', '', "class='form-control' maxlength='1000' placeholder='可以在编辑器直接贴图，最多支持输入1000个字符'");?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->component->evidence;?></th>
                                    <td class='required' colspan='5'><?php echo html::textarea('evidence', '', "class='form-control'  maxlength='5000' placeholder='可以在编辑器直接贴图，最多支持输入5000个字符'");?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->component->files;?><i title="<?php echo $lang->component->filesTip;?>" class="icon icon-help"></i></th>
                                    <td colspan='2'><?php echo $this->fetch('file', 'buildform');?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- 组件类型：公共组件 && 申请方式：新引入-->
                    <div id ="newPublic" class="hidden">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <th><?php echo $lang->component->level;?></th>
                                <td class='required' colspan='2'><?php echo html::select('level', $lang->component->levelList, '',"class='form-control chosen'");?></td>
                                <th class='inonelineTH'><?php echo $lang->component->hasProfessionalReview;?></th>
                                <td class='inonelineTD required' colspan='2' ><?php echo html::radio("hasProfessionalReview", $lang->component->professionalReviewResult);?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->component->name;?></th>
                                <td class='required' colspan='2'><?php echo html::input('newPublicName',"", "maxlength='60' class='form-control' required");?></td>
                                <th><?php echo $lang->component->version;?></th>
                                <td class='required' colspan='2'><?php echo html::input('newPublicVersion', "", "class='form-control' required");?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->component->developLanguage;?></th>
                                <td class='required' colspan='2'><?php echo html::select('newPublicDevelopLanguage', $lang->component->developLanguageList, '', "class='form-control chosen'");?></td>
                                <th><?php echo $lang->component->project;?></th>
                                <td class='required' colspan='2'><?php echo html::select('newPublicProjectId', $projectList, '', "class='form-control chosen'");?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->component->relationgit;?></th>
                                <td  colspan='5'>
                                    <div class='table-row'>
                                        <div class='table-col '  >
                                            <div class='input-group '>
                                                <?php echo html::input('gitlab[]', '', "class='form-control' maxlength='200' placeholder='最多200字符' ");?>
                                            </div>
                                        </div>

                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addGitlab(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeGitlab(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                            <tr>
                                <th><?php echo $lang->component->maintainer;?></th>
                                <td class='required' colspan='2'><?php echo html::select('maintainer',$maintainers, '',"class='form-control chosen'");?></td>
                                <th><?php echo $lang->component->location;?><i title="<?php echo $lang->component->locationTip;?>" class="icon icon-help"></i></th>
                                <td class='required' colspan='2'><?php echo html::input('location', "", "class='form-control' required");?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->component->functionDesc;?></th>
                                <td class='required' colspan='5'>
                                    <?php echo html::textarea('functionDesc', '', "class='form-control' maxlength='1000' placeholder='可以在编辑器直接贴图，最多支持输入1000个字符'");?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->component->files;?><i title="<?php echo $lang->component->filesTip;?>" class="icon icon-help"></i></th>
                                <td colspan='2'><?php echo $this->fetch('file', 'buildform');?></td>
                            </tr>
<!--                            <tr>-->
<!--                                <th>--><?php //echo $lang->component->files;?><!--<i title="--><?php //echo $lang->component->filesTip;?><!--" class="icon icon-help"></i></th>-->
<!--                                <td colspan='2'>--><?php //echo $this->fetch('file', 'buildform','id=1111');?>
<!--                                    <div style="position: relative">-->
<!--                                        <div style="position: absolute;width: 400px;height: 32px;line-height: 38px;top:-32px;left: 168px;font-size: 13px;color: #222;">-->
<!--                                            （文件需小于200M）　-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </td>-->
<!--                            </tr>-->
                            </tbody>
                        </table>
                    </div>
                </div>
            <table class="table table-form">
                <tbody>
                    <tr>
                        <td class='form-actions text-center' colspan='5'>
                            <?php echo html::submitButton();?>
                            <?php echo html::backButton();?>
<!--                            --><?php //echo html::a(inlink('submit'), $lang->component->submit, "", "class='btn btn-back btn-wide'");?><!--</td>-->
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    function addGitlab(obj)
    {
        stageNum = $(obj).closest('td').find('.table-row').size();
        if(stageNum >= 200) { alert("最多加200个git库"); return; }

        $row = $(obj).closest('.table-row');
        $row.after($row.clone());
        $next = $row.next();

        $next.find('.form-control').val('');

    }

    function removeGitlab(obj)
    {
        if($(obj).closest('td').find('.table-row').size() == 1) {
            $(obj).closest('td').find('.form-control').val('');
            return false;
        }
        $(obj).closest('.table-row').remove();

    }
    function radioShow(){
        nowType = document.getElementsByName('type');
        console.log()
        if (nowType[0].checked){
            $('#newThirdParty').removeClass('hidden')
        }else {
            $('#newThirdParty').addClass('hidden')
        }
        if (nowType[1].checked){
            $('#newPublic').removeClass('hidden')
        }else {
            $('#newPublic').addClass('hidden')
        }
    }

    $(function(){
        radioShow();
        window.editor['applicationReason'].edit.afterChange(function (){
            var limitNum = 1000;  //设定限制字数
            window.editor['applicationReason'].sync();
            var strValue = $("#applicationReason").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var oldLength = strValue.length;
                var strValueOld = $("#applicationReason").val();
                var isTag = 0;
                for(var i = strValueOld.length-1; i>=0&&oldLength>limitNum; i--){
                    if(strValueOld.charAt(i) == '>'){
                        isTag = 1;
                    }else if(strValueOld.charAt(i) == '<'){
                        isTag = 0;
                    }else{
                        if(isTag == 0){
                            strValueOld = strValueOld.slice(0,i)+strValueOld.slice(i+1);
                            oldLength--;
                        }
                    }
                }
                window.editor['applicationReason'].html(strValueOld);
                window.editor['applicationReason'].focus();
                window.editor['applicationReason'].appendHtml('');
            }
        });

        window.editor['evidence'].edit.afterChange(function (){
            var limitNum = 5000;  //设定限制字数
            window.editor['evidence'].sync();
            var strValue = $("#evidence").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var oldLength = strValue.length;
                var strValueOld = $("#evidence").val();
                var isTag = 0;
                for(var i = strValueOld.length-1; i>=0&&oldLength>limitNum; i--){
                    if(strValueOld.charAt(i) == '>'){
                        isTag = 1;
                    }else if(strValueOld.charAt(i) == '<'){
                        isTag = 0;
                    }else{
                        if(isTag == 0){
                            strValueOld = strValueOld.slice(0,i)+strValueOld.slice(i+1);
                            oldLength--;
                        }
                    }
                }
                window.editor['evidence'].html(strValueOld);
                window.editor['evidence'].focus();
                window.editor['evidence'].appendHtml('');
            }
        });

        window.editor['functionDesc'].edit.afterChange(function (){
            var limitNum = 1000;  //设定限制字数
            window.editor['functionDesc'].sync();
            var strValue = $("#functionDesc").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var oldLength = strValue.length;
                var strValueOld = $("#functionDesc").val();
                var isTag = 0;
                for(var i = strValueOld.length-1; i>=0&&oldLength>limitNum; i--){
                    if(strValueOld.charAt(i) == '>'){
                        isTag = 1;
                    }else if(strValueOld.charAt(i) == '<'){
                        isTag = 0;
                    }else{
                        if(isTag == 0){
                            strValueOld = strValueOld.slice(0,i)+strValueOld.slice(i+1);
                            oldLength--;
                        }
                    }
                }
                window.editor['functionDesc'].html(strValueOld);
                window.editor['functionDesc'].focus();
                window.editor['functionDesc'].appendHtml('');
            }
        });
    });
</script>


<?php include '../../common/view/footer.html.php';?>
