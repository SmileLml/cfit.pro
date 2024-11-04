<?php include '../../common/view/header.html.php';?>
<style>
    /*.main-table .table{ background-color:unset !important;}*/
    .main-table tbody>tr:nth-child(odd){
        background-color: #fff;
    }
    .main-table tbody>tr:nth-child(even){
        background-color: #fbfafc;
    }
</style>


<div style="background-color: white;padding: 10px;border-radius: 15px">
<div id="mainContent" class="main-row fade"  >
    <div class="side-col" id="sidebar" >
        <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
        <div class="cell" style="background-color: #f5f5f5;border-radius: 25px;height: 500px">
           <!-- --><?php /*$vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID&type=$type"; */?>
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
                        <?php common::printLink('myauthority','browse',"type=$key",$item,'',"class='block-statistion-nav-item $active'")?>
                    </li>
                </menu>
                <hr class="space-sm" />
            </div>
            <?php endforeach;?>
            <?php endif;?>
        </div>
    </div>
    <div class="main-col" style="background-color: white;border-radius: 15px">
        <div class='cell'>
            <div class="clearfix">
                <div class="btn-toolbar pull-left" >
                <form method='post'  id="authorityForm" action="<?php echo inLink('browse',"type=$type");?>">
                    <div class="table-row" id='conditions'>
                        <div class='w-220px col-md-3 col-sm-6  <?php echo  !in_array($type,array('gitlab','svn','jenkins')) ? 'hidden' : "" ;?>' >
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $type != 'jenkins' ? $lang->myauthority->repository : $lang->myauthority->JenkinsUrl;?></span>
                                <?php echo html::input('projectOrRepository', $projectOrRepository, "class='form-control'");?>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-6 <?php echo  !in_array($type,array('gitlab','svn')) ? 'hidden' : "" ;?>' >
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->myauthority->permsissionName;?></span>
                               <?php echo html::input('permsissionName', $permsissionName, "class='form-control'");?>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-6 <?php echo  !in_array($type,array('dpmp')) ? 'hidden' : "" ;?>' >
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->myauthority->role;?></span>
                                <?php echo html::input('role', $role, "class='form-control'");?>
                            </div>
                        </div>

                        <div class='w-220px col-md-3 col-sm-6 <?php echo  !in_array($type,array('jenkins')) ? 'hidden' : "" ;?>' >
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->myauthority->permsission;?></span>
                                <?php echo html::input('permsission', $permsission, "class='form-control '");?>
                            </div>
                        </div>
                        <div class='col-md-3 col-sm-6 <?php echo  !in_array($type,array('dpmp','gitlab','svn','jenkins')) ? 'hidden' : "" ;?>' >
                            <input type="hidden" name="browseType" value="bySearch">
                            <?php echo html::submitButton($lang->myauthority->query , '', 'btn btn-primary');?>
                            <?php echo html::commonButton($lang->myauthority->reset , 'id=reset', 'btn ');?>
                        </div>
                    </div>
                </form>
                </div>
                <div class="btn-toolbar pull-right" style=" <?php echo  in_array($type,array('dpmp','gitlab','svn','jenkins')) ? 'position:relative;top:-35px;height:5px' : "" ;?>">
                    <?php if (common::hasPriv('authorityapply', 'create')) echo html::a($this->createLink('authorityapply', 'create'), "<i class='icon-plus'></i> {$lang->myauthority->create}", '', "class='btn btn-primary'"); ?>

                </div>
            </div>
        </div>
        <?php if(empty($authority)):?>
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
                            <th style="width: 30%"><?php echo $lang->myauthority->role;?></th>
                            <th style="width: 70%"><?php echo $lang->myauthority->roleDesc;?></th>
                        <?php elseif(in_array($type,array('gitlab','svn'))):?>
                            <th style="width: 50%"><?php echo $lang->myauthority->repository;?></th>
                           <!-- <th ><?php /*echo $lang->myauthority->applyPermsission;*/?></th>-->
                            <th ><?php echo $lang->myauthority->permsissionName;?></th>
                            <th ><?php  echo $type == 'svn' ?  $lang->myauthority->role :'';?></th>
                            <th ><?php  echo $type == 'gitlab' ?  $lang->myauthority->expires :'';?></th>
                        <?php else:?>
                            <th style="width: 50%"><?php echo $lang->myauthority->JenkinsUrl;?></th>
                            <!--<th ><?php /*echo $lang->myauthority->applyPermsission;*/?></th>-->
                            <th style="width: 50%"><?php echo $lang->myauthority->permsission;?></th>
                        <?php endif;?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($authority as $item):?>
                    <tr>
                        <?php if($type == 'dpmp'):?>
                            <td class='text-ellipsis' title="<?php echo $item->name?>"><?php echo $item->name?></td>
                            <td class='text-ellipsis' title="<?php echo $item->desc?>"><?php echo $item->desc?></td>
                        <?php elseif(in_array($type,array('gitlab','svn'))):?>
                        <td class='text-ellipsis' title="<?php echo $item->projectOrRepository;?>"><?php echo $item->projectOrRepository;?></td>
                        <td><?php echo zget($lang->myauthority->svnAuthority,$item->permsission);?></td>
                        <td><?php echo $type == 'svn' ? $item->role : '';?></td>
                            <td><?php echo $type == 'gitlab'&& $item->expires ? date('Y-m-d',strtotime($item->expires)) : '';?></td>
                        <?php else:?>
                            <td><?php echo $item->projectOrRepository?></td>

                            <td class='text-ellipsis' title="<?php echo $item->permsission?>"><?php echo $item->permsission?></td>
                        <?php endif;?>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </form>
            <div class="table-footer">
                <?php $pager->show('right', 'pagerjs');?>
            </div>
            <?php endif;?>
        </div>
    </div>
</div>
</div>
<script>
   /*重置*/
        $('#reset').click(function() {
            $("#authorityForm input").each(function(){
                $(this).val("");
           });
    // var url = "<?php echo "type=$type&browseType=all&param=$param&orderBy=$orderBy&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=1"?>";
    //location.href =  createLink('myauthority', 'browse',url);
})
</script>
<?php include '../../common/view/footer.html.php';?>
