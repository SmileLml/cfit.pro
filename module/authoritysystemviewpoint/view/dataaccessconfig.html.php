<?php include '../../common/view/header.html.php';?>
<style>
    /*.main-table .table{ background-color:unset !important;}*/
    .main-table tbody>tr:nth-child(odd){
        background-color: #fff;
    }
    .main-table tbody>tr:nth-child(even){
        background-color: #fbfafc;
    }
    .bottom-button {
        position:absolute;
        bottom: 10px;
        left: 30px;

    }
    .main-row{
        display:unset;;
    }
    #closeModal{
     background-color:gainsboro;
    }
</style>
<div  class ="load-indicator loading" data-loading="正在加载..." style="background-color: white;padding: 20px;margin-top: 50px;border-radius: 15px" id="modaltest">
    <div id="mainContent" class='main-row fade '  >
        <div class="main-header">
            <h2><?php echo $lang->authoritysystemviewpoint->dataAccessConfig;?></h2>
        </div>
        <div class="side-col center-block" id="sidebar" >
            <div class="sidebar-toggle "><i class="icon icon-angle-left"></i></div>
            <div class="cell" style="background-color: #f5f5f5;border-radius: 25px;min-height: 350px">
                <?php if(!$subSystem):?>
                    <hr class="space">
                    <div class="text-center text-muted"><?php echo $lang->nodata;?></div>
                    <hr class="space">
                <?php else :?>
                    <?php foreach ($subSystem as $key=>$item):?>
                        <?php  $subs = array_keys($subSystem);  $active = (in_array($type,$subs) && $type == $key) || ($subs && $key == reset($subs) && !$type)  ? 'active' :   '';?>
                        <div class="text-center ">
                            <menu class="nav nav-stacked">
                                <li class=" list-group nav-item item group <?php echo $active?>">
                                    <?php common::printLink('authoritysystemviewpoint','dataaccessconfig',"type=$key",$item,'',"class='block-statistion-nav-item $active'")?>
<!--                                    --><?php /*common::printLink('authoritysystemviewpoint','dataaccessconfig',"type=$key",$item,'',"class='block-statistion-nav-item $active' onclick='test($key)'")*/?>
                                   <!-- <div class='block-statistion-nav-item <?php /*echo $active;*/?>' onclick="test('<?php /*echo $key;*/?>')" ><?php /*echo $item*/?></div>-->
                                </li>
                            </menu>
                            <hr class="space-sm" />
                        </div>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
        </div>


        <div class="main-col" style="background-color: white;border-radius: 15px">
            <div class=''>
                <div class="clearfix">
                    <div class="btn-toolbar pull-left" >
                        <form method='post'  id="authorityForm">
                            <div class="table-row" id='conditions'>
                                <div class='w-220px col-md-3 col-sm-6 ' >
                                    <div class='input-group'>
                                        <?php echo html::input('searchName', $searchName, "class='form-control' placeholder={$this->lang->authoritysystemviewpoint->serachTip} onchange= search(this)" );?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="cell">
                <form class=" main-table table-nest-collapsed load-indicator main-form form-ajax" method='post'>
                    <table class='table ' id='programList' style="background-color: #f5f5f5;">
                        <thead>
                        <tr >
                            <th ><?php echo $lang->authoritysystemviewpoint->realname ;?></th>
                            <th ><?php echo $lang->authoritysystemviewpoint->account;?></th>
                            <th ><?php echo $lang->authoritysystemviewpoint->employeeNumber;?></th>
                            <th ><?php echo $lang->authoritysystemviewpoint->deptName ;?></th>
                            <th ><?php echo $lang->authoritysystemviewpoint->job ;?></th>
                            <th ><?php echo $lang->authoritysystemviewpoint->staffType ;?></th>
                            <th ><?php echo $lang->actions ;?></th>
                        </tr>
                        </thead>
                        <?php if(!$dataAccess):?>
                            <tbody id="dataTBody">
                            <tr id="createWorkTr">
                                <td colspan="7" style="text-align: center;">
                                    <a href="javascript:void(0)" onclick="createUser();" class="btn btn-link"   data-id='0'><i class="icon-plus"></i> 添加</a>
                                </td>
                            </tr>
                            </tbody>
                        <?php else:?>
                        <tbody id="dataTBody">
                        <tr id="createWorkTr" class="hidden">
                            <td colspan="7" style="text-align: center;">
                                <a href="javascript:void(0)" onclick="createUser();" class="btn btn-link"   data-id='0'><i class="icon-plus"></i> 添加</a>
                            </td>
                        </tr>
                            <?php foreach ($dataAccess as $key => $data):?>
                            <tr id="supportUserInfo_<?php echo $key+1;?>">
                                <td>
                                    <?php echo html::select("realname[$key]",  $users,  $data->account, " id='realname{$key}' data-index='{$key}' name='realname[{$key}]' onchange='updateItem(this)' class='form-control chosen'");?>
                                </td>
                                <td>
                                    <?php echo html::input("account[$key]",  $data->account , "class='form-control ' id='account{$key}' data-id = '' data-index='{$key}' readonly");?>
                                </td>
                                <td>
                                    <?php echo html::input("employeeNumber[$key]",  $data->employeeNumber , "class='form-control' id='employeeNumber{$key}' data-id = '' data-index='{$key}' readonly");?>
                                </td>
                                <td><?php echo html::input("deptName[$key]", $data->deptName, "class='form-control' id='deptName{$key}' data-index='{$key}' readonly");?></td>
                                <td><?php echo html::input("job[$key]", zget($lang->user->roleList,$data->role, ''), "class='form-control' id='job{$key}' data-index='{$key}' readonly");?></td>
                                <td><?php echo html::input("staffType[$key]", zget($lang->user->staffTypeList,$data->staffType, ''), "class='form-control' id='staffType{$key}' data-index='{$key}' readonly");?></td>

                                <td>
                                    <div class="input-group">
                                        <?php echo html::input("ids[$key]", "","data-index='$key' id='ids$key' name = 'ids[$key]' hidden");?>
                                        <a href="javascript:void(0)" onclick="addWork(this)" class="btn btn-link"  id="addWorkItem<?php echo $key;?>"  data-id='<?php echo $key;?>' style="color:deepskyblue">添加</a>
                                        <a href="javascript:void(0)" onclick="delWork(this)" class="btn btn-link" style="color:red">移除</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                        <?php endif;?>
                    </table>

                    <div class='text-center' style="margin-top:10px">
                      <!--  <a href="<?php /*echo helper::createLink('authoritysystemviewpoint','browse','','',false );*/?>" class='btn btn-back ' >返回</a>-->
                        <button type='button'  class ='btn btn-primary' data-dismiss='modal' aria-hidden='true' id='closeModal' onclick="refresh()">返回</button>
                        <?php echo html::submitButton('保存', '', 'btn btn-primary');?>
                    </div>
                </form>
            </div>
        </div>

        </div>
    </div>
</div>
<table class="hidden" id="usernfo">
    <tbody id="userDemo">
    <tr id="supportUserInfo_1">
        <td class="supportUserTd" id="supportUserTd0">

            <div>
                <?php echo html::select('realname[]',  $users,  '', " id='realname0' data-index='0' class='form-control chosen supportUserSelect' data-dropDirection='bottom' onchange='updateItem(this)'");?>
            </div>
        </td>
        <td>
            <?php echo html::input('account[]',  '', "class='form-control ' id='account0'  data-id = '0' readonly");?>
        </td>
        <td>
            <?php echo html::input('employeeNumber[]',  '', "class='form-control   supportDate' id='employeeNumber0'  data-id = '0' readonly");?>
        </td>
        <td><?php echo html::input('deptName[]',  '' , "class='form-control' id='deptName0' readonly");?></td>
        <td><?php echo html::input('job[]',  '' , "class='form-control' id='job0' readonly");?></td>
        <td><?php echo html::input('staffType[]',  '' , "class='form-control' id='staffType0' readonly");?></td>
        <td>
            <div class="input-group">
                <?php echo html::input("ids[]", "",'data-index="0" id="ids0" hidden');?>
                <a href="javascript:void(0)" onclick="addWork(this)" class="btn btn-link"  id='addWorkItem0'  data-id='0' style="color:deepskyblue">添加</a>
                <a href="javascript:void(0)" onclick="delWork(this)" class="btn btn-link" style="color:red">移除</a>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<?php
js::set('supportUserIndex',$dataAccess ?  count($dataAccess) : 0);
js::set('type',$type);
?>
<?php include '../../common/view/footer.html.php';?>
