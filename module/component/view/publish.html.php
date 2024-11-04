<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <?php if($component->type == 'thirdParty'): ?>
                <h2><?php echo $lang->component->thirdpublish;?></h2>
            <?php elseif($component->type == 'public'): ?>
                <h2><?php echo $lang->component->publicpublish;?></h2>
            <?php endif; ?>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform-submit'>
            <table class="table table-form">
                <tbody>
                <?php if($component->type == 'thirdParty'): ?>
                    <tr>
                        <th><?php echo $lang->component->name;?></th>
                        <td><?php echo $component->name;?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->version;?></th>
                        <td><?php echo $component->version;?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->licenseType;?></th>
                        <td><?php echo $component->licenseType;?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->developLanguage;?></th>
                        <td><?php echo zget($lang->component->developLanguageList, $component->developLanguage);?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->category;?></th>
                        <td class='required'><?php echo html::select('category', $categoryList, '', "class='form-control chosen'");?></td>
                    </tr>
                    <!--<tr>
                        <th><?php /*echo $lang->component->chineseClassify;*/?></th>
                        <td class='required'><?php /*echo html::select('chineseClassify', array('' => '请选择') + $lang->component->chineseClassifyList, '', "class='form-control chosen'"); */?></td>
                    </tr>-->
                    <tr>
                        <th><?php echo $lang->component->englishClassify;?></th>
                        <td class='required'><?php echo html::select('englishClassify', array('' => '请选择') + $lang->component->englishClassifyList, '', "class='form-control chosen'"); ?></td>
                    </tr>
                <?php elseif($component->type == 'public'): ?>
                    <tr>
                        <th><?php echo $lang->component->name;?></th>
                        <td><?php echo $component->name;?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->level;?></th>
                        <td><?php echo zget($lang->component->levelList, $component->level);?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->functionDesc;?></th>
                        <td><?php echo $component->functionDesc;?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->location;?></th>
                        <td><?php echo $component->location;?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->createdDept;?></th>
                        <td><?php echo zget($depts,$component->createdDept)?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->maintainer;?></th>
                        <td><?php echo zget($users,$component->maintainer)?></td>
                    </tr>
                    <tr>
                        <th class='w-150px'><?php echo $lang->component->relationgit; ?></th>
                        <?php
                        $gitlabname = '';
                        $gitlablist = json_decode($component->gitlab);
                        if($gitlablist){
                            foreach($gitlablist as $key=>$gitval){
                                $gitlabname .= $gitval.'<br />';
                            }
                        }

                        ?>
                        <td colspan="3"><?php echo $gitlabname; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->developLanguage;?></th>
                        <td><?php echo zget($lang->component->developLanguageList, $component->developLanguage);?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->fileTitle;?></th>
                        <td><?php foreach ($component->files as $key => $file) {
                                echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $component, 'canOperate' => $file->addedBy == $this->app->user->account));
                            }; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->version;?></th>
                        <td><?php echo $component->version;?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->category;?></th>
                        <td class='required'><?php echo html::select('category', $categoryList, '', "class='form-control chosen'");?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->publishStatus;?></th>
                        <td class='required'><?php echo html::select('publishStatus', $publishStatusList, '', "class='form-control chosen'");?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->component->publishType;?></th>
                        <td class='required'><?php echo html::select('publishType', $lang->component->publishList, '', "class='form-control chosen' onchange='cidtrtrigger(this.value)'");?></td>
                    </tr>

                    <tr id="cidtr" style="display:none;">
                        <th><?php echo $lang->component->name;?></th>
                        <td class="required"><?php echo html::select('cid', $componentList, $component->cid, "class='form-control chosen' "); ?></td>
                    </tr>

                <?php endif; ?>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton($lang->component->publish);?>
                        <?php echo html::backButton();?>
                    </td>
                </tr>
                <tr style="height: 100px">

                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    function cidtrtrigger(value){
 
        if(value == 'incorporate'){
            $("#cidtr").show();
        }else{
            $("#cidtr").hide();
        }
    }
/*    $("#category").change(function (){
       let categoryval = $("#category").val();
       console.log(categoryval)
       if(categoryval == 'incorporate'){
            $("#cidtr").show();
       }else{
           $("#cidtr").hide();
       }
    })*/
</script>
<?php include '../../common/view/footer.html.php';?>
