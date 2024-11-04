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
</style>
<div style="background-color: white;padding: 10px;border-radius: 15px">
    <div id="mainContent" class="main-row fade"  >
        <div class="side-col" id="sidebar" >
            <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
            <div class="cell" style="background-color: #f5f5f5;border-radius: 25px;height: 500px">
                <?php if(!$subSystem):?>
                    <hr class="space">
                    <div class="text-center text-muted"><?php echo $lang->noData;?></div>
                    <hr class="space">
                <?php else :?>
                    <?php foreach ($subSystem as $key=>$item):?>
                        <?php $subs = array_keys($subSystem);  $active = (in_array($type,$subs) && $type == $key) || ($subs && $key == reset($subs) && !$type)  ? 'active' :   '';?>
                        <div class="text-center ">
                            <menu class="nav nav-stacked">
                                <li class=" list-group nav-item item group <?php echo $active?>">
                                    <?php common::printLink('authoritysystemviewpoint','browse',"type=$key",$item,'',"class='block-statistion-nav-item $active'")?>
                                </li>
                            </menu>
                            <hr class="space-sm" />
                        </div>
                    <?php endforeach;?>
                <?php endif;?>
                <?php if(common::hasPriv('authoritysystemviewpoint', 'dataAccessConfig')) echo html::a($this->createLink('authoritysystemviewpoint', 'dataAccessConfig').'?onlybody=yes', "<i class='icon-cog-outline'></i> {$lang->authoritysystemviewpoint->dataAccessConfig}", '', "class='  bottom-button iframe' data-width='1200px'");?>

            </div>
        </div>


        <div class="main-col" style="background-color: white;border-radius: 15px">
            <div class='cell'>
                <div class="clearfix">
                    <div class="btn-toolbar pull-left" >
                        <form method='post'  id="authorityForm"  action="<?php echo inLink('browse',"type=$type");?>">
                            <div class="table-row" id='conditions'>
                                <div class='w-220px col-md-3 col-sm-6  <?php echo  !in_array($type,array('dpmp')) ? 'hidden' : "" ;?>' >
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo  $lang->authoritysystemviewpoint->groupName;?></span>
                                        <?php echo html::input('name', $groupName, "class='form-control'");?>
                                    </div>
                                </div>
                               <!-- <div class='w-220px col-md-3 col-sm-6 <?php /*echo  !in_array($type,array('dpmp')) ? 'hidden' : "" ;*/?>' >
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php /*echo $lang->authoritysystemviewpoint->account;*/?></span>
                                        <?php /*echo html::input('realName', $realName, "class='form-control'");*/?>
                                    </div>
                                </div>-->

                                <div class='w-220px col-md-3 col-sm-6  <?php echo  !in_array($type,array('gitlab','svn','jenkins')) ? 'hidden' : "" ;?>' >
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo  $type != 'jenkins' ? $lang->authoritysystemviewpoint->repository : $lang->authoritysystemviewpoint->project;?></span>
                                        <?php echo html::input('projectOrRepository', $projectOrRepository, "class='form-control'");?>
                                    </div>
                                </div>
                                <div class='w-220px col-md-3 col-sm-6  <?php echo  !in_array($type,array('gitlab','svn','jenkins')) ? 'hidden' : "" ;?>' >
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo  $lang->authoritysystemviewpoint->roleName;?></span>
                                        <?php echo html::input('role', $role, "class='form-control'");?>
                                    </div>
                                </div>
                                <div class='w-220px col-md-3 col-sm-6  <?php echo  !in_array($type,array('gitlab','svn','jenkins','dpmp')) ? 'hidden' : "" ;?>' >
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo  $lang->authoritysystemviewpoint->account;?></span>
                                        <?php echo html::input('realName', $realName, "class='form-control'");?>
                                    </div>
                                </div>

                                <div class='col-md-3 col-sm-6 <?php echo  !in_array($type,array('gitlab','svn','jenkins','dpmp')) ? 'hidden' : "" ;?>' >
                                    <input type="hidden" name="browseType" value="bySearch">
                                    <?php echo html::submitButton($lang->authoritysystemviewpoint->query , '', 'btn btn-primary');?>
                                    <?php echo html::commonButton($lang->authoritysystemviewpoint->reset , 'id=reset', 'btn ');?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php if(empty($list)):?>
                <div class="cell">
                    <div class="table-empty-tip">
                        <p>
                            <span class="text-muted"><?php echo $lang->noData;?></span>
                        </p>
                    </div>
                </div>
            <?php else :?>
            <div class="cell">
                <form class=" main-table table-nest-collapsed">
                    <table class='table table-fixed has-sort-head' id='programList' style="background-color: #f5f5f5;border-radius: 25px">
                        <thead>
                        <tr >
                            <?php if($type == 'dpmp'):?>
                                <th class="w-50px"><?php echo $lang->idAB;?></th>
                                <th ><?php echo $lang->authoritysystemviewpoint->groupName;?></th>
                                <th ><?php echo $lang->authoritysystemviewpoint->groupDesc;?></th>
                                <th ><?php echo $lang->authoritysystemviewpoint->userList;?></th>
                                <th class="w-60px"><?php echo $lang->actions;?></th>
                            <?php elseif(in_array($type,array('gitlab','svn','jenkins'))):?>
                                <th class="w-50px"><?php echo $lang->authoritysystemviewpoint->number;?></th>
                                <th style="width:25%"><?php echo $type == 'jenkins' ? $lang->authoritysystemviewpoint->project : $lang->authoritysystemviewpoint->repository;?></th>
                                <th style="width:<?php echo $type == 'jenkins' ? '40%' :'10%' ;?>"><?php echo  $lang->authoritysystemviewpoint->roleName ;?></th>
                                <th ><?php echo  $type == 'svn' ?  $lang->authoritysystemviewpoint->role  : '';?></th>
                                <th style="width:45%"><?php echo $lang->authoritysystemviewpoint->userList;?></th>
                                <th class="w-60px"><?php echo $lang->actions;?></th>
                            <?php endif;?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 0;?>
                        <?php foreach ($list as $key => $item):
                            $i++;
                            ?>
                            <tr>
                                <?php if($type == 'dpmp'):?>
                                    <td class="text-ellipsis" ><?php echo $item->id ;?></td>
                                    <td class="text-ellipsis" title="<?php echo $item->name ;?>"><?php echo $item->name ;?></td>
                                    <td class="text-ellipsis" title="<?php echo $item->desc ;?>"><?php echo $item->desc ;?></td>
                                    <td class="text-ellipsis" title="<?php echo $item->users ;?>"><?php echo $item->users ;?></td>
                                    <td class="c-actions"><?php echo common::printIcon('authoritysystemviewpoint','groupUsers',"groupID=$item->id",$item,'list','persons', '', 'iframe ', true,'data-width="100%"');?></td>
                                <?php elseif(in_array($type,array('gitlab','svn','jenkins'))):?>
                                <?php $permsission = trim($item->permsission);?>
                                    <td class="text-ellipsis" ><?php echo $i ;?></td>
                                    <td class="text-ellipsis"  title="<?php echo $item->projectOrRepository?>"><?php echo $item->projectOrRepository?></td>
                                    <td class="text-ellipsis"title="<?php  echo zget($lang->myauthority->svnAuthority,$permsission) ;?>"><?php  echo zget($lang->myauthority->svnAuthority,$permsission) ;?></td>
                                    <td class="text-ellipsis"title="<?php   echo  $type == 'svn' ?  $item->role  : '';?>"><?php  echo $type == 'svn' ? $item->role  : '';?></td>
                                    <td class="text-ellipsis" title="<?php echo $item->users; ?>"><?php echo $item->users;?></td>
                                <?php $projects = base64_encode($item->projectOrRepository);
                                       //$permsissions = base64_encode(trim($item->permsission));
                                       $roles = base64_encode(trim($item->role));?>
                                    <td class="c-actions"><?php echo common::printIcon('authoritysystemviewpoint','authorityUsers',"type=$type&id=$item->id",$item,'list','list-alt', '', 'iframe ', true,'data-width="100%"');?></td>
                                  <?php endif;?>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
                <?php endif;?>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs');?>
                </div>
        </div>
    </div>
    </div>

</div>
<script>
    /*重置*/
    $('#reset').click(function() {
        $("#authorityForm input").each(function(){
            $(this).val("");
        })
       // var url = "<?php echo "type=$type&browseType=all&param=$param&orderBy=$orderBy&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=1"?>";
      //  location.href =  createLink('authoritysystemviewpoint', 'browse',url);
    })
</script>
<?php include '../../common/view/footer.html.php';?>
