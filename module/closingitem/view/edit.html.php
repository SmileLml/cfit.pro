<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .input-group-addon{min-width: 30px;} .input-group{margin-bottom: 8px;}
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
    .panel{border-color: #ddd;}
</style>
<?php js::set('type', $closingitem->projectType);?>
<?php js::set('assembly', $closingitem->isAssembly);?>
<?php js::set('advise', $closingitem->assemblyAdvise);?>
<?php js::set('usage', $closingitem->toolsUsage);?>
<?php js::set('toolsAdvise', $closingitem->toolsAdvise);?>
<?php js::set('ossp', $closingitem->osspAdvise);?>
<?php js::set('platform', $closingitem->platformAdvise);?>
<?php js::set('platformAdvise', $closingitem->adviseChecklist);?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->closingitem->edit;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <td colspan="2">
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->closingitem->projectType;?></span>
                            <?php echo html::select('projectType', $typeList, isset($closingitem->projectType) ? $closingitem->projectType: '0', "class='form-control chosen' required");?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div>
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->closingitem->assemblyTitle;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <td colspan="4"  class='required'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->closingitem->isAssembly;?></span>&nbsp;
                                        &nbsp;<?php echo html::radio('isAssembly', $lang->closingitem->typeIsList, $closingitem->isAssembly, "onchange='toggleAssembly(this.value)'")?>
                                    </div>
                                </td>
                            </tr>
                            <tr id = 'assemblyNum'>
                                <td colspan="4">
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->closingitem->assemblyNum;?></span>
                                        <input type='number' name='assemblyNum' value="<?php echo $closingitem->assemblyNum;?>" step='0.01' class='form-control' />
                                    </div>
                                    <div style='color:red'><?php echo $lang->closingitem->assemblyTips;?></div>
                                </td>
                            </tr>
                            <tr id = 'assemblyContent'>
                                <td colspan="4" class = 'required'>
                                    <?php if(!empty($assemblyInfo)):?>
                                    <?php foreach($assemblyInfo as $assembly):?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group codes1'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes1[]',  $assembly['codes1'], "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col w-p20'>
                                            <div class='input-group assemblyIndex'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->assemblyAlready;?></span>
                                                <?php echo html::select('assemblyIndex[]', $components,$assembly['assemblyIndex'],"onchange='toggleAssemblyIndex(this.value,this)' class='form-control chosen'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group assemblyDesc'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->assemblyDesc;?></span>
                                                <?php echo html::input('assemblyDesc[]', $assembly['assemblyDesc'], "readonly class='form-control'  ");?>
                                            </div>
                                        </div>
                                        <div class='table-col w-p15'>
                                            <div class='input-group assemblyLevel'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->assemblyLevel;?></span>
                                                <?php echo html::select('assemblyLevel[]', $levelList, $assembly['assemblyLevel'], "disabled class='form-control chosen'");?>
                                            </div>
                                        </div>
<!--                                        <div class='table-col'>-->
<!--                                            <div class='input-group assemblyStatus'>-->
<!--                                                <span class='input-group-addon'>--><?php //echo $lang->closingitem->status;?><!--</span>-->
<!--                                                --><?php //echo html::select('assemblyStatus[]', $statusList,$assembly['assemblyStatus'], "class='form-control chosen'");?>
<!--                                            </div>-->
<!--                                        </div>-->
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group assemblyAdd'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                    <?php else:?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group codes1'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes1[]',  '1', "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col w-p20'>
                                            <div class='input-group assemblyIndex'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->assemblyAlready;?></span>
                                                <?php echo html::select('assemblyIndex[]', $components,'' ,"onchange='toggleAssemblyIndex(this.value,this)' class='form-control chosen'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group assemblyDesc'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->assemblyDesc;?></span>
                                                <?php echo html::input('assemblyDesc[]', '', "readonly class='form-control'  ");?>
                                            </div>
                                        </div>
                                        <div class='table-col w-p15'>
                                            <div class='input-group assemblyLevel'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->assemblyLevel;?></span>
                                                <?php echo html::select('assemblyLevel[]', $levelList, '', "disabled class='form-control chosen'");?>
                                            </div>
                                        </div>
<!--                                        <div class='table-col'>-->
<!--                                            <div class='input-group assemblyStatus'>-->
<!--                                                <span class='input-group-addon'>--><?php //echo $lang->closingitem->status;?><!--</span>-->
<!--                                                --><?php //echo html::select('assemblyStatus[]', $statusList,'', "class='form-control chosen'");?>
<!--                                            </div>-->
<!--                                        </div>-->
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group assemblyAdd'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"  class='required'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->closingitem->assemblyAdvise;?></span>&nbsp;
                                        &nbsp;<?php echo html::radio('assemblyAdvise', $lang->closingitem->typeHasList, $closingitem->assemblyAdvise, "onchange='toggleAssemblyAdvise(this.value)'")?>
                                    </div>
                                </td>
                            </tr>
                            <tr id = 'assemblyAdviseInput'>
                                <td colspan="4" class = 'required'>
                                    <?php if(!empty($closingitem->advises[1])):?>
                                    <?php foreach ($closingitem->advises[1] as $assemblyAdvise):?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes2[]',  "1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group advise2'>
                                                <span class='input-group-addon advise2'><?php echo $lang->closingitem->advise;?></span>
                                                <?php echo html::input('advise2[]', $assemblyAdvise->advise, "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem1(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem1(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                    <?php else: ?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes2[]',  "1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group advise2'>
                                                <span class='input-group-addon advise2'><?php echo $lang->closingitem->advise;?></span>
                                                <?php echo html::input('advise2[]', '', "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem1(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem1(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div>
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->closingitem->testTitle;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <td colspan="4"  class='required'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->closingitem->toolsUsage;?></span>&nbsp;
                                        &nbsp;<?php echo html::radio('toolsUsage', $lang->closingitem->typeHasList, $closingitem->toolsUsage, "onchange='toggleUsage(this.value)'")?>
                                    </div>
                                </td>
                            </tr>
                            <tr id = 'usageContent'>
                                <td colspan="4" class = 'required'>
                                    <?php if(!empty($toolsInfo)):?>
                                    <?php foreach ($toolsInfo as $tools):?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes3[]',$tools['codes3'], "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group toolsName'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->toolsName;?></span>
                                                <?php echo html::input('toolsName[]', $tools['toolsName'], "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group toolsVersion'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->toolsVersion;?></span>
                                                <?php echo html::input('toolsVersion[]', $tools['toolsVersion'], "class='form-control'  ");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->isTesting;?></span>
                                                <?php echo html::select('isTesting[]', $lang->closingitem->typeIsList, $tools['isTesting'], "class='form-control chosen'")?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group toolsType'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->toolsTypeName;?></span>
                                                <?php echo html::select('toolsType[]', $lang->closingitem->toolsType, $tools['toolsType'], "class='form-control chosen'")?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group toolsDesc'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->toolsDesc;?></span>
                                                <?php echo html::input('toolsDesc[]', $tools['toolsDesc'], "class='form-control'  ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem2(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem2(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                    <?php else: ?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes3[]',"1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group toolsName'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->toolsName;?></span>
                                                <?php echo html::input('toolsName[]', '', "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group toolsVersion'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->toolsVersion;?></span>
                                                <?php echo html::input('toolsVersion[]', '', "class='form-control'  ");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group isTesting'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->isTesting;?></span>
                                                <?php echo html::select('isTesting[]', $lang->closingitem->typeIsList, '', "class='form-control chosen'")?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group toolsType'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->toolsTypeName;?></span>
                                                <?php echo html::select('toolsType[]', $lang->closingitem->toolsType, '', "class='form-control chosen'")?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group toolsDesc'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->toolsDesc;?></span>
                                                <?php echo html::input('toolsDesc[]', '', "class='form-control'  ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem2(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem2(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"  class='required'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->closingitem->toolsAdvise;?></span>&nbsp;
                                        &nbsp;<?php echo html::radio('toolsAdvise', $lang->closingitem->typeHasList, $closingitem->toolsAdvise, "onchange='toggleAdvise(this.value)'")?>
                                    </div>
                                </td>
                            </tr>
                            <tr id="usageAdviseInput">
                                <td colspan="4" class = 'required'>
                                    <?php if(!empty($closingitem->advises[2])):?>
                                    <?php foreach ($closingitem->advises[2] as $toolsAdvise):?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes4[]',  "1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group advise4'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->advise;?></span>
                                                <?php echo html::input('advise4[]', $toolsAdvise->advise, "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem3(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem3(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                        <?php endforeach;?>
                                    <?php else: ?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes4[]',  "1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group advise4'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->advise;?></span>
                                                <?php echo html::input('advise4[]', '', "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem3(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem3(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div>
                <div class="panel">
                    <div class="panel-heading">
                        <?php echo $lang->closingitem->knowledgeTitle;?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <td colspan="4"  class='required'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->closingitem->osspAdvise;?></span>&nbsp;
                                        &nbsp;<?php echo html::radio('osspAdvise', $lang->closingitem->typeHasList, $closingitem->osspAdvise, "onchange='toggleOsspAdvise(this.value)'")?>
                                    </div>
                                </td>
                            </tr>
                            <tr id="osspAdvise">
                                <td colspan="4"  class = 'required'>
                                    <?php if(!empty($closingitem->advises[3])):?>
                                    <?php foreach ($closingitem->advises[3] as $osspAdvise):?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes5[]',  "1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group advise5'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->advise;?></span>
                                                <?php echo html::input('advise5[]', $osspAdvise->advise, "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem4(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem4(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                    <?php else: ?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes5[]',  "1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group advise5'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->advise;?></span>
                                                <?php echo html::input('advise5[]', '', "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem4(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem4(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"  class='required'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->closingitem->platformAdvise;?></span>&nbsp;
                                        &nbsp;<?php echo html::radio('platformAdvise', $lang->closingitem->typeHasList,  $closingitem->platformAdvise, "onchange='togglePlatformAdvise(this.value)'")?>
                                    </div>
                                </td>
                            </tr>
                            <tr id="platformAdvise">
                                <td colspan="4" class='required'>
                                    <?php if(!empty($closingitem->advises[4])):?>
                                    <?php foreach ($closingitem->advises[4] as $platformAdvise):?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes6[]',  "1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group advise6'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->advise;?></span>
                                                <?php echo html::input('advise6[]', $platformAdvise->advise, "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem5(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem5(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                        <?php endforeach;?>
                                    <?php else: ?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes6[]',  "1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group advise6'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->advise;?></span>
                                                <?php echo html::input('advise6[]', '', "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem5(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem5(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"  class='required'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->closingitem->adviseChecklist;?></span>&nbsp;
                                        &nbsp;<?php echo html::radio('adviseChecklist', $lang->closingitem->typeHasList, $closingitem->adviseChecklist, "onchange='toggleAdviseChecklist(this.value)'")?>
                                    </div>
                                </td>
                            </tr>
                            <tr id="platformAdviseList">
                                <td colspan="4">
                                    <?php if(!empty($knowledgeInfo)):?>
                                    <?php foreach ($knowledgeInfo as $knowledge):?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes7[]',  $knowledge['codes7'], "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group submitFileName'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->submitFileName;?></span>
                                                <?php echo html::input('submitFileName[]', $knowledge['submitFileName'], "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group submitReason'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->submitReason;?></span>
                                                <?php echo html::input('submitReason[]', $knowledge['submitReason'], "class='form-control'  ");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group versionCodeOSSP'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->versionCodeOSSPName;?></span>
                                                <?php echo html::select('versionCodeOSSP[]', $lang->closingitem->versionCodeOSSP, $knowledge['versionCodeOSSP'], "class='form-control chosen'")?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group comment'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->comment;?></span>
                                                <?php echo html::input('comment[]', $knowledge['advise'], "class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem6(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem6(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                        <?php endforeach;?>
                                    <?php else: ?>
                                    <div class='table-row'>
                                        <div class='table-col w-p10'>
                                            <div class='input-group'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->code;?></span>
                                                <?php echo html::input('codes7[]',  "1", "readonly class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group submitFileName'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->submitFileName;?></span>
                                                <?php echo html::input('submitFileName[]', '', "class='form-control' ");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group submitReason'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->submitReason;?></span>
                                                <?php echo html::input('submitReason[]', '', "class='form-control'  ");?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group versionCodeOSSP'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->versionCodeOSSPName;?></span>
                                                <?php echo html::select('versionCodeOSSP[]', $lang->closingitem->versionCodeOSSP, '', "class='form-control chosen'")?>
                                            </div>
                                        </div>
                                        <div class='table-col'>
                                            <div class='input-group comment'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->comment;?></span>
                                                <?php echo html::input('comment[]', '', "class='form-control'");?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem6(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem6(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->closingitem->realPoints;?></span>
                                        <input type='number' name='realPoints' value="<?php echo $closingitem->realPoints;?>" step='0.01' class='form-control' />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <?php $demandArr = explode(',',$closingitem->demandAdvise); foreach ($demandArr as $demand) { ?>
                                    <div class='table-row'>
                                        <div class='table-col'>
                                            <div class='input-group demandAdvise'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->demandAdviseName;?></span>
                                                <?php echo html::select('demandAdvise[]', $lang->closingitem->demandAdviseList, $demand, "class='form-control chosen'")?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem7(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem7(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <?php $constructionArr = explode(',',$closingitem->constructionAdvise); foreach ($constructionArr as $construction) { ?>
                                    <div class='table-row'>
                                        <div class='table-col'>
                                            <div class='input-group constructionAdvise'>
                                                <span class='input-group-addon'><?php echo $lang->closingitem->constructionAdviseName;?></span>
                                                <?php echo html::select('constructionAdvise[]', $lang->closingitem->constructionAdviseList, $construction, "class='form-control chosen'")?>
                                            </div>
                                        </div>
                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem8(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem8(this)'");?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->closingitem->filelist;?></th>
                                <td>
                                    <div class='detail'>
                                        <div class='detail-content article-content'>
                                            <?php
                                            if($closingitem->files){
                                                echo $this->fetch('file', 'printFiles', array('files' => $closingitem->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                                            }else{
                                                echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo $lang->files;?></span>
                                        <?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <table class="table table-form inputNums hidden">
                <tbody>
                <tr>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->closingitem->achievementNum;?></span>
                            <input type='number' name='achievementNum' value="<?php echo $closingitem->achievementNum;?>" step='0.01' class='form-control' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->closingitem->planNum;?></span>
                            <input type='number' name='planNum' value="<?php echo $closingitem->achievementNum;?>" step='0.01' class='form-control' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->closingitem->outPlanNum;?></span>
                            <input type='number' name='outPlanNum' value="<?php echo $closingitem->outPlanNum;?>" step='0.01' class='form-control' />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="table table-form">
                <tbody>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::a(inlink('browse', "projectID=$projectID"), $lang->goback, '', "class='btn btn-back btn-wide'");?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    $(document).ready(function()
    {
        if(type == 6){
            $('.inputNums').removeClass('hidden');
        }else{
            $('.inputNums').addClass('hidden');
        }
        if(assembly == '1')
        {
            $('#assemblyNum').removeClass('hidden');
            $('#assemblyContent').removeClass('hidden');
        }
        else
        {
            $('#assemblyNum').addClass('hidden');
            $('#assemblyContent').addClass('hidden');
        }
        if(advise == '1')
        {
            $('#assemblyAdviseInput').removeClass('hidden');
        }
        else
        {
            $('#assemblyAdviseInput').addClass('hidden');
        }
        if(usage == '1')
        {
            $('#usageContent').removeClass('hidden');
        }
        else
        {
            $('#usageContent').addClass('hidden');
        }
        if(toolsAdvise == '1')
        {
            $('#usageAdviseInput').removeClass('hidden');
        }
        else
        {
            $('#usageAdviseInput').addClass('hidden');
        }
        if(ossp == '1')
        {
            $('#osspAdvise').removeClass('hidden');
        }
        else
        {
            $('#osspAdvise').addClass('hidden');
        }
        if(platform == '1')
        {
            $('#platformAdvise').removeClass('hidden');
        }
        else
        {
            $('#platformAdvise').addClass('hidden');
        }
        if(platformAdvise == '1')
        {
            $('#platformAdviseList').removeClass('hidden');
        }
        else
        {
            $('#platformAdviseList').addClass('hidden');
        }
    });
</script>
<?php include '../../common/view/footer.html.php';?>
