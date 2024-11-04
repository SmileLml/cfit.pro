<?php include '../../common/view/header.html.php';?>
<style>
    .main-table tbody>tr:nth-child(odd){
        background-color: #fff;
    }
    .main-table tbody>tr:nth-child(even){
        background-color: #fbfafc;
    }
</style>
<div id="mainMenu" class="clearfix">
    <div class='main-header'>
        <h2>
            <?php echo   '<span>'.$lang->authorityuserviewpoint->view. '</span>';?>
        </h2>
    </div>
</div>
<div id="mainContent" class="main-row" style="min-height:400px;display:inline-block">
    <div class="main-col col-8">
        <div class="cell" style="padding:20px;">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->authorityuserviewpoint->info;?></div>
                <div class="detail-content">
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th><?php echo $lang->authorityuserviewpoint->account;?></th>
                            <td><?php echo $account;?></td>
                            <th ><?php echo $lang->authorityuserviewpoint->realname;?></th>
                            <td><?php echo $userInfo[$account]->realname;?></td>
                            <th ><?php echo $lang->authorityuserviewpoint->gender;?></th>
                            <td><?php echo  zget($lang->user->genderList,$userInfo[$account]->gender);?></td>
                        </tr>
                        <tr>
                            <th ><?php echo $lang->authorityuserviewpoint->deptAllName;?></th>
                            <td><?php echo zget($depts,$userInfo[$account]->dept);?></td>
                            <th ><?php echo $lang->authorityuserviewpoint->userType;?></th>
                            <td><?php echo  zget($lang->user->staffTypeList,$userInfo[$account]->staffType, '');?></td>
                            <th ><?php echo $lang->authorityuserviewpoint->userStatus;?></th>
                            <td><?php echo '';?></td>
                        </tr>
                        <tr>
                            <th ><?php echo $lang->authorityuserviewpoint->job;?></th>
                            <td><?php echo zget($lang->user->roleList,$userInfo[$account]->role, '');?></td>
                            <th ><?php echo $lang->authorityuserviewpoint->notes;?></th>
                            <td><?php echo '-';?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->authorityuserviewpoint->contactWay;?></div>
                <div class="detail-content">
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th ><?php echo $lang->authorityuserviewpoint->email;?></th>
                            <td colspan="1.5"><?php echo $userInfo[$account]->email ;?></td>
                            <th ><?php echo $lang->authorityuserviewpoint->mobile;?></th>
                            <td colspan="2"><?php echo$userInfo[$account]->mobile;?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->authorityuserviewpoint->authRange;?></div>
                <div class="detail-content">
                    <div class="clearfix">
                        <div class="btn-toolbar pull-left" >
                            <form method='post'  id="authorityForm" action="<?php echo inLink('view',"account=".$account.'&type='.$type.'&browseType=all'.'&role='.$roles);?>">
                                <div class="table-row" id='conditions'>
                                    <div class='w-220px col-md-3 col-sm-6 ' >
                                        <div class='input-group'>
                                            <span class='input-group-addon'><?php echo  $lang->authorityuserviewpoint->subsystem;?></span>
                                            <?php echo html::select('viewtype', $subSystem, $viewtype,"class='form-control chosen'");?>
                                        </div>
                                    </div>
                                    <div class='w-220px col-md-3 col-sm-6 ' >
                                        <div class='input-group'>
                                            <span class='input-group-addon'><?php echo (isset($_POST['viewtype']) && in_array($_POST['viewtype'] ,array('all','dpmp')) || in_array($viewtype ,array('all','dpmp'))) ?$lang->authorityuserviewpoint->role :$lang->authorityuserviewpoint->permsissionName;?></span>
                                            <?php echo html::input('permsissionName', $permsissionName, "class='form-control'");?>
                                        </div>
                                    </div>
                                    <!--<div class='w-220px col-md-3 col-sm-6  ' >
                                        <div class='input-group'>
                                            <span class='input-group-addon'><?php /*echo $lang->authorityuserviewpoint->role;*/?></span>
                                            <?php /*echo html::input('role', $role, "class='form-control'");*/?>
                                        </div>
                                    </div>-->
                                    <div class='col-md-3 col-sm-6'>
                                        <input type="hidden" name="searchFlag" value="1">
                                        <input type="hidden" name="browseType" value="bySearch">
                                        <?php echo html::submitButton($lang->authorityuserviewpoint->query , '', 'btn btn-primary');?>
                                        <?php echo html::commonButton($lang->authorityuserviewpoint->reset , 'id=reset', 'btn ');?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cell" style="height:400px;overflow-y:scroll">
            <form class=" main-table table-nest-collapsed">
                <table class='table table-fixed has-sort-head' id='programList' style="background-color: #f5f5f5;border-radius: 25px">
                    <thead>
                    <tr >
                        <th class="w-100px"><?php echo $lang->authorityuserviewpoint->system;?></th>
                        <th class="<?php if(isset($_POST['viewtype']) && $_POST['viewtype'] == 'dpmp' || $viewtype == 'dpmp') echo 'hidden';?>"><?php echo $lang->authorityuserviewpoint->projectRepository;?></th>
                        <th class="<?php if(isset($_POST['viewtype']) && $_POST['viewtype'] == 'dpmp' || $viewtype == 'dpmp') echo 'hidden';?>"><?php echo $lang->authorityuserviewpoint->permsissionName;?></th>
                        <th class="<?php if(isset($_POST['viewtype']) && !in_array($_POST['viewtype'] ,array('all','dpmp','svn')) || !in_array($viewtype ,array('all','dpmp','svn'))) echo 'hidden';?>" ><?php echo $lang->authorityuserviewpoint->role;?></th>
                        <th class="<?php if(isset($_POST['viewtype']) &&!in_array($_POST['viewtype'],array('all','dpmp')) ||!in_array($viewtype ,array('all','dpmp'))) echo 'hidden';?>" ><?php echo $lang->authorityuserviewpoint->roleDesc;?></th>
                        <th class="<?php if(isset($_POST['viewtype']) && $_POST['viewtype'] != 'gitlab' || $viewtype != 'gitlab') echo 'hidden';?>" ><?php echo $lang->authorityuserviewpoint->expires;?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($info as $item):?>
                    <tr>
                        <td><?php echo zget($this->lang->authorityapply->subSystemList,$item->type);?></td>
                        <td class="text-ellipsis <?php if(isset($_POST['viewtype']) && $_POST['viewtype'] == 'dpmp' || $viewtype == 'dpmp') echo 'hidden';?>" title="<?php echo $item->project;?>"><?php echo $item->project;?></td>
                        <td class="text-ellipsis <?php if(isset($_POST['viewtype']) && $_POST['viewtype'] == 'dpmp' || $viewtype == 'dpmp') echo 'hidden';?>" title="<?php echo isset($item->permsission) ? zget($lang->myauthority->svnAuthority,$item->permsission) :'';?>"><?php echo isset($item->permsission) ? zget($lang->myauthority->svnAuthority,$item->permsission) :'';?></td>
                        <td class="text-ellipsis <?php if(isset($_POST['viewtype']) && !in_array($_POST['viewtype'],array('all','dpmp','svn')) ||!in_array($viewtype ,array('all','dpmp','svn'))) echo 'hidden';?>" title="<?php echo $item->name;?>"><?php echo $item->name;?></td>
                        <td class="text-ellipsis <?php if(isset($_POST['viewtype']) && !in_array($_POST['viewtype'],array('all','dpmp')) ||!in_array($viewtype ,array('all','dpmp'))) echo 'hidden';?>" title="<?php echo $item->desc;?>"><?php echo $item->type != 'svn' ? $item->desc : '-';?></td>
                        <td class="text-ellipsis <?php if(isset($_POST['viewtype']) && $_POST['viewtype'] != 'gitlab' || $viewtype != 'gitlab') echo 'hidden';?>" title="<?php echo $item->expires ? date('Y-m-d',strtotime($item->expires)) :'';?>"><?php echo $item->expires ? date('Y-m-d',strtotime($item->expires)) : '';?></td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </form>
            <div class="table-footer">
                <?php $pager->show('right', 'pagerjs');?>
            </div>
        </div>
        <div class='text-center' style="margin-top:10px">
            <button type='button'  class ='btn btn-primary' data-dismiss='modal' aria-hidden='true' id='closeModal' onclick="refresh()">关闭</button>
        </div>
    </div>
</div>
<script>
    function refresh(){
        window.parent.location.reload();
    }

    /*重置*/
    $('#reset').click(function() {
        $("#authorityForm input").each(function(){
            $(this).val("");
        })
        $('#viewtype').val('all').trigger("chosen:updated");
    })
</script>
<?php include '../../common/view/footer.html.php';?>
