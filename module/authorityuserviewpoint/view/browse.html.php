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
        bottom: 3px;
        left: 30px;

    }
</style>
<div style="background-color: white;padding: 10px;border-radius: 15px">
    <div id="mainContent" class="main-row fade"  >
        <div class="side-col" id="sidebar" >
            <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
            <div class="cell" style="background-color: #f5f5f5;border-radius: 25px;min-height: 700px">
                <?php if(!$deptTree):?>
                    <hr class="space">
                    <div class="text-center text-muted"><?php echo $lang->nodata;?></div>
                    <hr class="space">
                <?php else :?>
                <?php echo $deptTree; ?>
                <?php endif;?>
                <?php if(common::hasPriv('authoritysystemviewpoint', 'dataAccessConfig')) echo html::a($this->createLink('authoritysystemviewpoint', 'dataAccessConfig').'?onlybody=yes', "<i class='icon-cog-outline'></i> {$lang->authoritysystemviewpoint->dataAccessConfig}", '', "class='  bottom-button iframe' data-width='1200px'");?>

            </div>
        </div>


        <div class="main-col" style="background-color: white;border-radius: 15px">
            <div class='cell'>
                <div class="clearfix">
                    <div class="btn-toolbar pull-left" >
                        <form method='post'  id="authorityForm" action="<?php echo inLink('browse',"dept=$deptID&type=$type");?>">
                            <div class="table-row" id='conditions'>
                                <div class='w-220px col-md-3 col-sm-6 ' >
                                    <div class='input-group'>
                                        <span class='input-group-addon'><?php echo  $lang->authorityuserviewpoint->authorityDistribution;?></span>
                                        <?php echo html::select('name', $subSystem, $type,"class='form-control chosen' ");?>
                                    </div>
                                </div>
                                <div class='w-220px col-md-1 col-sm-6 ' >
                                    <div class='input-group'>
                                        <?php echo html::input('search', $search, "class='form-control' placeholder={$this->lang->authorityuserviewpoint->serachTip} ");?>

                                    </div>
                                </div>
                                <div class='col-md-3 col-sm-6 ' >
                                    <input type="hidden" name="browseType" value="bySearch">
                                    <input type="hidden" name ='searchFlag' id ='searchFlag' value="1">
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
                            <th ><?php echo $lang->authorityuserviewpoint->account;?></th>
                            <th ><?php echo $lang->authorityuserviewpoint->realname;?></th>
                            <th ><?php echo $lang->authorityuserviewpoint->deptName;?></th>
                            <th ><?php echo $lang->authorityuserviewpoint->authorityDistribution;?></th>
                            <th class="w-60px"><?php echo $lang->actions;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key => $item):?>
                            <tr>
                                <td class="text-ellipsis" title="<?php echo $item->account ;?>"><?php echo $item->account ;?></td>
                                <td class="text-ellipsis" title="<?php echo $item->realname ;?>"><?php echo $item->realname ;?></td>
                                <td class="text-ellipsis" title="<?php echo $item->deptName ;?>"><?php echo $item->deptName ;?></td>
                                <td class="text-ellipsis" title="<?php echo $item->authtype ;?>"><?php echo zmget($this->lang->authorityapply->subSystemList,$item->authtype) ;?></td>
                                <td class="c-actions"><?php echo common::printIcon('authorityuserviewpoint','view',"account=".$item->account.'&type='.$type.'&browseType=all'.'&role='.$item->authtype,$item,'list','list-alt', '', 'iframe ', true,'data-width="100%"');?></td>
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
    function searchName(obj){
        var value = $.trim($(obj).val());
        var type = $("#name").val();
       link = createLink('authorityuserviewpoint', 'browse', 'dept=<?php echo $deptID ? $deptID : 0;?>&type='+type+"&browseType=bySearch&search="+encodeURIComponent(value));
       // console.log(link)
       // location.href =  link;
        $("#authorityForm").submit();
    }
    function searchType(obj){
        var value = $.trim($(obj).val());
        link = createLink('authorityuserviewpoint', 'browse', 'dept=<?php echo $deptID ? $deptID : 0;?>&type='+value+"&browseType=all");
        location.href = link;
        //$("#authorityForm").submit();
    }
    $('#dept<?php echo $deptID;?>').parent().addClass('active');

    /*重置*/
    $('#reset').click(function() {
        $("#authorityForm input").each(function(){
            $(this).val("");
        })
        $('#name').val('all').trigger("chosen:updated");
        // var url = "<?php echo "type=$type&browseType=all&param=$param&orderBy=$orderBy&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=1"?>";
        //  location.href =  createLink('authoritysystemviewpoint', 'browse',url);
    })
</script>
</script>
<?php include '../../common/view/footer.html.php';?>
