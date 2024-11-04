<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/sortable.html.php';?>
<style>
    #mainMenu .pull-left .checkbox-primary {display: inline-block; margin-left: 10px;}
    .main-table tbody>tr>td:first-child, .main-table thead>tr>th:first-child {padding-left: 8px;}
    .table tbody>tr>td .dropdown {display: inline-block; line-height: 1;}
</style>
<style>
    span.sort-handler {cursor: move; color: #999;}
    #changeTableList.sortable-sorting > tr {opacity: 0.7}
    #changeTableList.sortable-sorting > tr.drag-row {opacity: 1;}
    #changeTableList > tr.drop-not-allowed {opacity: 0.1!important}
    #changeList .c-actions {overflow: visible;}
    #changeList > thead > tr > th .table-nest-toggle-global {top: 6px}
    #changeList > thead > tr > th .table-nest-toggle-global:before {color: #a6aab8;}
    #changeTableList > tr:last-child .c-actions .dropdown-menu {top: auto; bottom: 100%; margin-bottom: -5px;}
    #changeTableList .icon-project:before {content: '\e99c'; width: 22px; height: 22px; background: none; color: #16a8f8; top: 0; line-height: 22px; margin-right: 2px; font-size: 14px}
</style>
<div id="mainContent" class="main-content">
    <div class="main-header clearfix">
        <h2><?php echo $lang->programplan->batchChange;?></h2>
    </div>
    <?php $allStageID = array();?>
    <?php if(empty($stages)): ?>
        <div class="table-empty-tip">
            <p>
                <span class="text-muted"><?php echo $lang->programplan->emptyTip;?></span>
            </p>
        </div>
    <?php else:?>
        <div class="main-col">
            <form class='main-form' id='batchChangeForm' target='hiddenwin' method='post' data-ride='table' data-nested='true' data-expand-nest-child='false' data-checkable='false' data-enable-empty-nested-row='true'>
                <table class='table has-sort-head table-fixed table-form' id='changeList'>
                    <thead>
                    <tr>
                        <th class='w-80px'> <?php echo $lang->programplan->stageGrade?></th>
                        <th class='table-nest-title'><?php echo $lang->programplan->name?></th>
                        <th class='w-100px'><?php echo $lang->project->planDuration?></th>
                        <th class='w-110px'><?php echo $lang->programplan->milestone;?></th>
                        <th class='w-130px'><?php echo $lang->programplan->begin?></th>
                        <th class='w-130px'><?php echo $lang->programplan->end?></th>
                        <th class='w-130px'><?php echo $lang->programplan->realBegan?></th>
                        <th class='w-130px'><?php echo $lang->programplan->realEnd?></th>
                        <th class='w-130px'><?php echo $lang->actions?></th>
                    </tr>
                    </thead>
                    <tbody id='changeTableList'>
                    <?php foreach($stages as $id => $stage):?>
                        <?php $allStageID[$stage->id] = 'true';?>
                        <?php
                        $trClass = $stage->grade == 1 ? 'block-class' . $stage->id : 'block-class' . $stage->parent;
                        $trAttrs = "data-id='$stage->id' data-order='$stage->order' data-parent='$stage->parent'";
                        $trAttrs .= " data-nest-parent='$stage->parent' data-nest-path='$stage->path'";
                        $trAttrs .= " class='$trClass'";
                        ?>
                        <tr <?php echo $trAttrs;?> id='stageTr<?php echo $stage->id?>'>
                            <td <?php if($stage->grade == 1) echo "style='font-weight:bold;'"?>>
                                <?php if($stage->grade == 2) echo "&nbsp;&nbsp;&nbsp;&nbsp;"; echo zget($lang->programplan->objectGradeList, $stage->grade);?>
                                <?php echo html::hidden("parent[$id]", $stage->parent, "class='parent-td'");?>
                            </td>

                            <?php $readonly = $stage->source   ? 'readonly = readonly' : '';?>
                            <td><?php echo html::input("name[$id]", htmlspecialchars($stage->name, ENT_QUOTES), "class='form-control name-td' $readonly")?></td>
                            <td><?php echo html::input("planDuration[$id]", $stage->planDuration, "class='form-control duration-td ' autocomplete='off'")?></td>
                            <td><?php echo html::select("milestone[$id]", $lang->programplan->milestoneList, $stage->milestone, "class='form-control milestone-td'")?></td>
                            <td><?php echo html::input("begin[$id]", $stage->begin, "class='form-control begin-td form-date-lazy' onchange='changeComputerBegin(this)' autocomplete='off'")?></td>
                            <td><?php echo html::input("end[$id]", $stage->end, "class='form-control end-td form-date-lazy' onchange='changeComputerEnd(this)' autocomplete='off'")?></td>
                            <td><?php echo html::input("realBegan[$id]", $stage->realBegan, "class='form-control form-date-lazy realBegan-td' autocomplete='off'")?></td>
                            <td><?php echo html::input("realEnd[$id]", $stage->realEnd, "class='form-control form-date-lazy realEnd-td' autocomplete='off'")?></td>
                            <td class='c-actions'>
                                <span title="拖动排序" class="sort-handler"><i class="icon icon-move text-blue"></i></span>
                                <!-- <?php /*if($stage->grade == 1):*/?>
                                <a title="添加一级阶段" href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon icon-plus'></i></a>
                                <a title="添加子阶段" href='javascript:;' onclick='addSubItem(this)' class='btn btn-link'><i class='icon icon-split'></i></a>
                                --><?php /*endif;*/?>
                                <!-- <a title="删除" href='javascript:;' onclick='deleteItem(this)' data-id='<?php /*echo $stage->id*/?>' class='btn btn-link delete-item'><i class='icon icon-close'></i></a>-->
                                <!--数据来源系统创建 只有admin 能操作-->
                                <?php if ($stage->source ): ?>
                                    <?php if ($this->app->user->account == 'admin'): ?>
                                        <?php if ($stage->grade == 1): ?>
                                            <a title="添加一级阶段" href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon icon-plus'></i></a>
                                            <a title="添加子阶段" href='javascript:;' onclick='addSubItem(this)' class='btn btn-link'><i class='icon icon-split'></i></a>
                                        <?php endif; ?>
                                        <a title="删除" href='javascript:;' onclick='deleteItem(this)' data-id='<?php echo $stage->id ?>' class='btn btn-link delete-item'><i class='icon icon-close'></i></a>
                                <?php else: ?>
                                <!--数据来源系统创建 非admin 用户所有按钮置灰-->
                                    <?php if ($stage->grade == 1 && $flag != '2'): ?>
                                        <a title="添加一级阶段" href='javascript:;' class="icon-common-suspend disabled icon-plus"></a>
                                        <a title="添加子阶段" href='javascript:;' class="icon-common-suspend disabled icon-split"></a>
                                        <?php elseif($stage->grade == 1):?>
                                        <a title="添加一级阶段" href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon icon-plus'></i></a>
                                        <?php if(strpos($stage->name,'部门实现') === false):?>
                                        <a title="添加子阶段" href='javascript:;' onclick='addSubItem(this)' class='btn btn-link'><i class='icon icon-split'></i></a>
                                        <?php endif;?>
                                    <?php endif; ?>
                                        <a title="删除" href='javascript:;' onclick='deleteItem(this)'  disabled data-id='<?php echo $stage->id ?>' class='btn btn-link delete-item '><i class='icon icon-close'></i></a>

                               <?php endif; ?>
                               <?php else: ?>
                                   <!--数据来源不是系统创建 有权限可操作 所有按钮高亮-->
                                        <?php if ($stage->grade == 1): ?>
                                            <a title="添加一级阶段" href='javascript:;' onclick='addItem(this)' class='btn btn-link'><i class='icon icon-plus'></i></a>
                                            <a title="添加子阶段" href='javascript:;' onclick='addSubItem(this)' class='btn btn-link'><i class='icon icon-split'></i></a>
                                        <?php endif; ?>

                                        <a title="删除" href='javascript:;' onclick='deleteItem(this)' data-id='<?php echo $stage->id ?>' class='btn btn-link delete-item'><i class='icon icon-close'></i></a>
                               <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan='9' class='text-center form-actions'>
                            <?php
                            echo html::submitButton($this->lang->save);
                            echo ' &nbsp; ' . html::backButton();
                            ?>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </form>
        </div>
    <?php endif;?>
</div>
</table>
<?php js::set('orderBy', 'order_desc');?>
<?php js::set('allStageID', json_encode($allStageID));?>
<?php js::set('initIndex', '9999999');?>

<script>
    localStorage.setItem('/programplan/batchchange/table.batchChangeForm.nestConfig', allStageID);
    $(function()
    {
        var $list = $('#changeTableList');
        $list.addClass('sortable').sortable(
            {
                reverse: orderBy === 'order_desc',
                selector: 'tr',
                dragCssClass: 'drag-row',
                trigger: $list.find('.sort-handler').length ? '.sort-handler' : null,
                canMoveHere: function($ele, $target)
                {
                    var tarParent = $target.data('parent');
                    var curParent = $ele.data('parent');
                    return tarParent === curParent || curParent === tarParent.id;
                },
                start: function(e)
                {
                    e.targets.filter('[data-parent!="' + e.element.attr('data-parent') + '"]').addClass('drop-not-allowed');
                },
                finish: function(e)
                {
                    var projects = '';
                    e.list.each(function()
                    {
                        projects += $(this.item).data('id') + ',' ;
                    });

                    //$.post(createLink('programplan', 'updateOrder'), {'projects' : projects, 'orderBy' : 'order_asc'});

                    var $thead = $list.closest('table').children('thead');
                    $thead.find('.headerSortDown, .headerSortUp').removeClass('headerSortDown headerSortUp').addClass('header');
                    $thead.find('th.sort-default .header').removeClass('header').addClass('headerSortDown');

                    e.element.addClass('drop-success');
                    setTimeout(function(){e.element.removeClass('drop-success');}, 800);
                    $list.children('.drop-not-allowed').removeClass('drop-not-allowed');
                    $('#batchChangeForm').table('initNestedList')

                    $('.table-nest-icon').remove();
                }
            });

        localStorage.setItem('/programplan/batchchange/table.batchChangeForm.nestConfig', allStageID);
        $('.table-nest-icon').remove();
    });

    function milestoneChecked()
    {
        $('input[name^=milestone]').each(function()
        {
            var isChecked = $(this).attr('checked');
            var checkedValue = $(this).val();
            if(isChecked == 'checked' && checkedValue == 1)
            {
                $(this).attr('checked', 'checked');
            }
            else
            {
                $(this).removeAttr('checked');
            }
        });
    }

    function changeComputerBegin(obj)
    {
        var beginDate = $(obj).val();
        var endDate   = $(obj).parents('tr').find('.end-td').val();

        if(!endDate) return;

        var dateStart = new Date(beginDate);
        var dateEnd = new Date(endDate);
        var difValue = (dateEnd - dateStart) / (1000 * 60 * 60 * 24);
        $(obj).parents('tr').find('.duration-td').val(difValue + 1);
    }

    function changeComputerEnd(obj)
    {
        var beginDate = $(obj).parents('tr').find('.begin-td').val();
        var endDate   = $(obj).val();

        if(!beginDate) return;

        var dateStart = new Date(beginDate);
        var dateEnd = new Date(endDate);
        var difValue = (dateEnd - dateStart) / (1000 * 60 * 60 * 24);
        $(obj).parents('tr').find('.duration-td').val(difValue + 1);
    }

    function addItem(obj)
    {
        var dataID = $(obj).parents('tr').attr("data-id");
        var item   = $(obj).parents('tr').html();

        initIndex = initIndex+1;
        var blockClass = 'block-class' + initIndex;
        $(".block-class" + dataID + ':last').after("<tr data-parent='0' data-nest-parent='0' data-nest-path=',1," + initIndex + ",'  id='stageTr" + initIndex + "' data-id='" + initIndex + "' class='"+ blockClass +" has-nest-child' data-level='2'>'" + item + "'</tr>");

        var dateConf = {minView: "month", format: 'yyyy-mm-dd', todayBtn: 1, autoclose: 1};
        $("#stageTr" + initIndex).find(".begin-td").datetimepicker(dateConf);
        $("#stageTr" + initIndex).find(".end-td").datetimepicker(dateConf);
        $("#stageTr" + initIndex).find(".realBegan-td").datetimepicker(dateConf);
        $("#stageTr" + initIndex).find(".realEnd-td").datetimepicker(dateConf);

        $("#stageTr" + initIndex).find(".parent-td").attr({'name': 'parent[' + initIndex+ ']', 'id': 'parent[' + initIndex+ ']', 'value': 0});
        $("#stageTr" + initIndex).find(".name-td").attr({'name': 'name[' + initIndex+ ']', 'id': 'name[' + initIndex+ ']', 'value': '','readonly':false});
        $("#stageTr" + initIndex).find(".duration-td").attr({'name': 'planDuration[' + initIndex+ ']', 'id': 'planDuration[' + initIndex+ ']', 'value': ''});
        $("#stageTr" + initIndex).find(".milestone-td").attr({'name': 'milestone[' + initIndex+ ']', 'id': 'milestone[' + initIndex+ ']', 'value': '0'});
        $("#stageTr" + initIndex).find(".begin-td").attr({'name': 'begin[' + initIndex+ ']', 'id': 'begin[' + initIndex+ ']', 'value': ''});
        $("#stageTr" + initIndex).find(".end-td").attr({'name': 'end[' + initIndex+ ']', 'id': 'end[' + initIndex+ ']', 'value': ''});
        $("#stageTr" + initIndex).find(".realBegan-td").attr({'name': 'realBegan[' + initIndex+ ']', 'id': 'realBegan[' + initIndex+ ']', 'value': ''});
        $("#stageTr" + initIndex).find(".realEnd-td").attr({'name': 'realEnd[' + initIndex+ ']', 'id': 'realEnd[' + initIndex+ ']', 'value': ''});
        $("#stageTr" + initIndex).find(".delete-item").attr({'data-id': initIndex});
        if(initIndex){
           // $(".delete-item[data-id='10000000']").removeAttr('disabled')
            $("#stageTr" + initIndex +' td  a').removeAttr('disabled');
        }
        //添加父id默认打开
        allStageID = localStorage.getItem('/programplan/batchchange/table.batchChangeForm.nestConfig', allStageID);
        allStageID = JSON.parse(allStageID);
        allStageID[initIndex] = true;
        allStageID = JSON.stringify(allStageID);
        localStorage.setItem('/programplan/batchchange/table.batchChangeForm.nestConfig', allStageID);
    }

    function addSubItem(obj)
    {
        var dataID   = $(obj).parents('tr').attr("data-id");
        var dataPath = $(obj).parents('tr').attr("data-nest-path");
        var item     = $(obj).parents('tr').siblings('.is-nest-child').html();
        //console.log(dataID,dataPath,item)
        for(var i=0;i<3;i++)
        {
            initIndex = initIndex+1;
            var blockClass = 'block-class' + dataID;
            $(".block-class" + dataID + ':last').after("<tr data-parent='" + dataID + "' data-nest-parent='" + dataID + "' data-nest-path='" + dataPath + initIndex + ",'  id='stageTr" + initIndex + "' data-id='" + initIndex + "' class='"+ blockClass +" is-nest-child' data-level='3'>'" + item + "'</tr>");

            var dateConf = {minView: "month", format: 'yyyy-mm-dd', todayBtn: 1, autoclose: 1};
            $("#stageTr" + initIndex).find(".begin-td").datetimepicker(dateConf);
            $("#stageTr" + initIndex).find(".end-td").datetimepicker(dateConf);
            $("#stageTr" + initIndex).find(".realBegan-td").datetimepicker(dateConf);
            $("#stageTr" + initIndex).find(".realEnd-td").datetimepicker(dateConf);

            $("#stageTr" + initIndex).find(".parent-td").attr({'name': 'parent[' + initIndex+ ']', 'id': 'parent[' + initIndex+ ']', 'value': dataID});
            $("#stageTr" + initIndex).find(".name-td").attr({'name': 'name[' + initIndex+ ']', 'id': 'name[' + initIndex+ ']', 'value': '','readonly':false});
            $("#stageTr" + initIndex).find(".duration-td").attr({'name': 'planDuration[' + initIndex+ ']', 'id': 'planDuration[' + initIndex+ ']', 'value': ''});
            $("#stageTr" + initIndex).find(".milestone-td").attr({'name': 'milestone[' + initIndex+ ']', 'id': 'milestone[' + initIndex+ ']', 'value': '0'});
            $("#stageTr" + initIndex).find(".begin-td").attr({'name': 'begin[' + initIndex+ ']', 'id': 'begin[' + initIndex+ ']', 'value': ''});
            $("#stageTr" + initIndex).find(".end-td").attr({'name': 'end[' + initIndex+ ']', 'id': 'end[' + initIndex+ ']', 'value': ''});
            $("#stageTr" + initIndex).find(".realBegan-td").attr({'name': 'realBegan[' + initIndex+ ']', 'id': 'realBegan[' + initIndex+ ']', 'value': ''});
            $("#stageTr" + initIndex).find(".realEnd-td").attr({'name': 'realEnd[' + initIndex+ ']', 'id': 'realEnd[' + initIndex+ ']', 'value': ''});
            $("#stageTr" + initIndex).find(".delete-item").attr({'data-id': initIndex});
            if(initIndex){
                // $(".delete-item[data-id='10000000']").removeAttr('disabled')
                $("#stageTr" + initIndex +' td  a').removeAttr('disabled');
            }
        }
    }

    function deleteItem(obj)
    {
        var name = $(obj).parents('tr').find('.name-td').val();
        if(confirm('<?php echo $lang->execution->confirmDeleteStage;?>'.replace('%s',name)))
        {
            var id = $(obj).attr('data-id');
            $.get(createLink('programplan', 'ajaxdelete', 'executionID='+id), function(data)
            {
                if(data.result == 'success')
                {
                    $('#stageTr' + id).remove();
                }
                else
                {
                    alert(data.message);
                }
            },'json')
        }
    }

    $(document).on('click', '.form-date-lazy', function(e)
    {
        var $input = $(this);
        if($input.data('datetimepicker')) return;
        $input.datepicker().data('datetimepicker').show(e);
    });
</script>
<?php include '../../../common/view/footer.html.php';?>
