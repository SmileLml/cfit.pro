<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        foreach ($lang->componentpublicaccount->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo html::a($this->createLink('componentpublicaccount', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i
                class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('componentpublicaccount', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('componentpublicaccount', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('componentpublicaccount', 'export') ? $this->createLink('componentpublicaccount', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->componentpublicaccount->export, '', $misc) . "</li>";
                ?>
            </ul>
            <?php if (common::hasPriv('componentpublicaccount', 'create')) echo html::a($this->createLink('componentpublicaccount', 'create'), "<i class='icon-plus'></i> {$lang->componentpublicaccount->create}", '', "class='btn btn-primary'"); ?>
        </div>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='componentpublicaccount'></div>
        <?php if (empty($datas)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='problemForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='problems'>
                    <thead>
                    <tr>
                        <th class='w-40px'><?php echo $lang->componentpublicaccount->id; ?></th>
                        <th class='w-90px'><?php common::printOrderLink('componentDept', $orderBy, $vars, $lang->componentpublicaccount->componentDept); ?></th>
                        <th class='w-130px'><?php common::printOrderLink('componentname', $orderBy, $vars, $lang->componentpublicaccount->componentname); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('componentversion', $orderBy, $vars, $lang->componentpublicaccount->componentversion); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('projectName', $orderBy, $vars, $lang->componentpublicaccount->projectName); ?></th>
                        <th class='w-70px'><?php common::printOrderLink('projectDept', $orderBy, $vars, $lang->componentpublicaccount->projectDept); ?></th>
                        <th class='w-70px'><?php common::printOrderLink('projectManager', $orderBy, $vars, $lang->componentpublicaccount->projectManager); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('componentlevel', $orderBy, $vars, $lang->componentpublicaccount->componentlevel); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('componentcategory', $orderBy, $vars, $lang->componentpublicaccount->componentcategory); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('startTime', $orderBy, $vars, $lang->componentpublicaccount->startTime); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->componentpublicaccount->createTime); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data): ?>
                        <tr>
                            <td class='text-ellipsis' title="<?php echo $data->code;?>"><?php echo $data->code;?></td>
                            <td class='text-ellipsis' title="<?php echo zget($depts, $data->maintainerDept,''); ?>"><?php echo zget($depts, $data->maintainerDept,''); ?></td>
                            <td class='text-ellipsis' title="<?php echo zget($componentNames, $data->componentId,'');?>"><?php echo html::a($this->createLink('componentpublic', 'view', 'componentpublicId=' . $data->componentId), zget($componentNames, $data->componentId,''), '');?></td>
                            <td class='text-ellipsis' title="<?php echo zget($versions, $data->componentVersion,''); ?>"><?php echo zget($versions, $data->componentVersion,''); ?></td>
                            <td class='text-ellipsis' title="<?php echo zget($projects, $data->projectName,''); ?>"><?php echo zget($projects, $data->projectName,''); ?></td>
                            <td class='text-ellipsis' title="<?php echo zget($depts, $data->projectDept,''); ?>"><?php echo zget($depts, $data->projectDept,''); ?></td>
                            <td class='text-ellipsis' title="<?php echo zget($users, $data->owner,''); ?>"><?php echo zmget($users, $data->owner,''); ?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->componentpublicaccount->levelList, $data->level,''); ?>"><?php echo zget($lang->componentpublicaccount->levelList, $data->level,''); ?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->categoryList, $data->category,''); ?>"><?php echo zget($lang->component->categoryList, $data->category,''); ?></td>
                            <td class='text-ellipsis' title="<?php echo $data->startYear.'年度--第'.$data->startQuarter.'季度'; ?>"><?php echo $data->startYear.'年度--第'.$data->startQuarter.'季度'; ?></td>
                            <td class='text-ellipsis' title="<?php echo $data->createdDate; ?>"><?php echo $data->createdDate; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs'); ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    $(function(){

            // 选择维护部门的值，联动 组件名称
            $('#value1,#value2,#value3,#value4,#value5,#value6').live("change", function () {
                var id = $(this).attr("id");
                var index = id.replace(/[^\d]/g, "");

                if($('#field'+index).val() == 'componentDept'){
                    /*$.get(createLink('componentpublicaccount', 'ajaxGetComponentByDept', 'id=' + $('#value'+index).val()+'&index='+index), function(data)
                    {
                        // window.componentpublicaccountparams = JSON.parse(data);
                        $("#boxcomponentname").html(data);
                    });*/
                    let tempvalue = $('#value'+index).val();
                    if(Number(tempvalue)){
                    for(var i = 1; i<=6; i++){
                        if($('#field'+i).val() == 'componentname'){

                                $.get(createLink('componentpublicaccount', 'ajaxGetComponentByDept', 'id=' + $('#value'+index).val()+'&index='+i), function(data)
                                {
                                    var pose = data.indexOf('value');
                                    var p = data.charAt(pose+5);
                                    $('#value'+p).nextAll().remove();
                                    $('#value'+p).replaceWith(data);
                                    $('#value'+p).chosen();
                                    $('#value'+p).change();
                                });
                            }

                        }
                    }
                }

            });
        //选择组件名称的值 联动 组件版本
        $('#value1,#value2,#value3,#value4,#value5,#value6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");



            if($('#field'+index).val() == 'componentname'){
                /*$.get(createLink('componentpublicaccount', 'ajaxGetComponentByDept', 'id=' + $('#value'+index).val()+'&index='+index), function(data)
                {
                    // window.componentpublicaccountparams = JSON.parse(data);
                    $("#boxcomponentname").html(data);
                });*/
                let tempvalue = $('#value' + index).val();
                if(Number(tempvalue)){
                for(var i = 1; i<=6; i++){
                    if($('#field'+i).val() == 'componentversion'){


                            $.get(createLink('componentpublicaccount', 'ajaxGetComponentVersionByID', 'id=' + $('#value'+index).val()+'&index='+i), function(data)
                            {
                                var pose = data.indexOf('value');
                                var p = data.charAt(pose+5);
                                $('#value'+p).nextAll().remove();
                                $('#value'+p).replaceWith(data);
                                $('#value'+p).chosen();
                                $('#value'+p).change();
                            });
                        }

                    }
                }
            }
        });
        // 选择所属部门的值，联动 项目名称展示
        $('#value1,#value2,#value3,#value4,#value5,#value6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");


            if($('#field'+index).val() == 'projectDept'){
                /*$.get(createLink('componentpublicaccount', 'ajaxGetComponentByDept', 'id=' + $('#value'+index).val()+'&index='+index), function(data)
                {
                    // window.componentpublicaccountparams = JSON.parse(data);
                    $("#boxcomponentname").html(data);
                });*/
                let tempvalue = $('#value'+index).val();
                if(Number(tempvalue)){
                    for(var i = 1; i<=6; i++){
                        if($('#field'+i).val() == 'projectName'){

                            $.get(createLink('componentpublicaccount', 'ajaxGetSearchProjects', 'id=' + $('#value'+index).val()+'&index='+i), function(data)
                            {
                                var pose = data.indexOf('value');
                                var p = data.charAt(pose+5);
                                $('#value'+p).nextAll().remove();
                                $('#value'+p).replaceWith(data);
                                $('#value'+p).chosen();
                                $('#value'+p).change();
                            });
                        }

                    }
                }
            }

        });
            // 搜索条件字段选择组件名称，则看看有没有组件部门，来联动当前组件名称应该展示的范围
            $('#field1,#field2,#field3,#field4,#field5,#field6').live("change", function () {
                var id = $(this).attr("id");
                var index = id.replace(/[^\d]/g, "");

                // console.log($('#field'+index).val());
                if($('#field'+index).val() == 'componentname'){

                    for(var i = 1; i<=6; i++) {
                        if ($('#field' + i).val() == 'componentDept') {
                            let tempvalue = $('#value' + i).val();
                            if(Number(tempvalue)){
                                $.get(createLink('componentpublicaccount', 'ajaxGetComponentByDept', 'id=' + $('#value' + i).val() + '&index=' + index), function (data) {
                                    var pose = data.indexOf('value');
                                    var p = data.charAt(pose + 5);
                                    $('#value' + p).nextAll().remove();
                                    $('#value' + p).replaceWith(data);
                                    $('#value' + p).chosen();
                                    $('#value' + p).change();
                                });
                            }


                        }
                    }

                }

            });


        //搜索字段选择组件版本，则看看已选好的条件中有没有 组件名称，如果有 则 约束组件版本选择范围
        $('#field1,#field2,#field3,#field4,#field5,#field6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");



            if($('#field'+index).val() == 'componentversion'){


                for(var i = 1; i<=6; i++) {
                    if ($('#field' + i).val() == 'componentname') {
                        let tempvalue = $('#value' + i).val();
                        if(Number(tempvalue)){
                            $.get(createLink('componentpublicaccount', 'ajaxGetComponentVersionByID', 'id=' + $('#value' + i).val() + '&index=' + index), function (data) {
                                var pose = data.indexOf('value');
                                var p = data.charAt(pose + 5);
                                $('#value' + p).nextAll().remove();
                                $('#value' + p).replaceWith(data);
                                $('#value' + p).chosen();
                                $('#value' + p).change();
                            });
                        }


                    }
                }

            }

        });

        //选择产品名称，看看有没有选好的项目所属部门，如果有 则控制展示范围
        $('#field1,#field2,#field3,#field4,#field5,#field6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");



            if($('#field'+index).val() == 'projectName'){

                for(var i = 1; i<=6; i++) {
                    if ($('#field' + i).val() == 'projectDept') {
                        let tempvalue = $('#value' + i).val();
                        if(Number(tempvalue)){
                            $.get(createLink('componentpublicaccount', 'ajaxGetSearchProjects', 'id=' + $('#value' + i).val() + '&index=' + index), function (data) {
                                var pose = data.indexOf('value');
                                var p = data.charAt(pose + 5);
                                $('#value' + p).nextAll().remove();
                                $('#value' + p).replaceWith(data);
                                $('#value' + p).chosen();
                                $('#value' + p).change();
                            });
                        }


                    }
                }

            }

        });

    });
    // $(function () {
    //     //系统-产品搜索功能联动
    //     $('#value1,#value2,#value3,#value4,#value5,#value6').live("change", function () {
    //         var id = $(this).attr("id");
    //         var index = id.replace(/[^\d]/g, "");
    //         if($('#field'+index).val() == 'appname'){
    //             for(var i = 1; i<=6; i++){
    //                 if($('#field'+i).val() == 'productname'){
    //                     $.get(createLink('componentpublicaccount', 'ajaxGetproductByApp', 'id=' + $('#value'+index).val()+'&index='+i), function(data)
    //                     {
    //                         var pose = data.indexOf('value');
    //                         var p = data.charAt(pose+5);
    //                         $('#value'+p).nextAll().remove();
    //                         $('#value'+p).replaceWith(data);
    //                         $('#value'+p).chosen();
    //                         $('#value'+p).change();
    //                     });
    //                 }
    //             }
    //         }
    //     });
    //     //产品-版本搜索功能联动
    //     $('#field1,#field2,#field3,#field4,#field5,#field6').live("change", function () {
    //         var id = $(this).attr("id");
    //         var index = id.replace(/[^\d]/g, "");
    //         if($('#field'+index).val() == 'productname'){
    //             for(var i = 1; i<=6; i++){
    //                 if($('#field'+i).val() == 'appname'){
    //                     $.get(createLink('componentpublicaccount', 'ajaxGetproductByApp', 'id=' + $('#value'+i).val()+'&index='+index), function(data)
    //                     {
    //                         var pose = data.indexOf('value');
    //                         var p = data.charAt(pose+5);
    //                         $('#value'+p).nextAll().remove();
    //                         $('#value'+p).replaceWith(data);
    //                         $('#value'+p).chosen();
    //                         $('#value'+p).change();
    //                     });
    //                     break;
    //                 }
    //             }
    //         }
    //     });
    //
    //     //产品-版本搜索功能联动
    //     $('#value1,#value2,#value3,#value4,#value5,#value6').live("change", function () {
    //         var id = $(this).attr("id");
    //         var index = id.replace(/[^\d]/g, "");
    //         if($('#field'+index).val() == 'productname'){
    //             for(var i = 1; i<=6; i++){
    //                 if($('#field'+i).val() == 'productversion'){
    //                     $.get(createLink('componentpublicaccount', 'ajaxGetVersionByProduct', 'id=' + $('#value'+index).val()+'&index='+i), function(data)
    //                     {
    //                         var pose = data.indexOf('value');
    //                         var p = data.charAt(pose+5);
    //                         //$('#value'+p+'_chosen').remove();
    //                         $('#value'+p).nextAll().remove();
    //                         $('#value'+p).replaceWith(data);
    //                         $('#value'+p).chosen();
    //                     });
    //                 }
    //             }
    //         }
    //     });
    //     //产品-版本搜索功能联动
    //     $('#field1,#field2,#field3,#field4,#field5,#field6').live("change", function () {
    //         var id = $(this).attr("id");
    //         var index = id.replace(/[^\d]/g, "");
    //         if($('#field'+index).val() == 'productversion'){
    //             for(var i = 1; i<=6; i++){
    //                 if($('#field'+i).val() == 'productname'){
    //                     $.get(createLink('componentpublicaccount', 'ajaxGetVersionByProduct', 'id=' + $('#value'+i).val()+'&index='+index), function(data)
    //                     {
    //                         var pose = data.indexOf('value');
    //                         var p = data.charAt(pose+5);
    //                         $('#value'+p).nextAll().remove();
    //                         $('#value'+p).replaceWith(data);
    //                         $('#value'+p).chosen();
    //                     });
    //                     break;
    //                 }
    //             }
    //         }
    //     });
    //
    //
    //     //组件-版本搜索功能联动
    //     $('#value1,#value2,#value3,#value4,#value5,#value6').live("change", function () {
    //         var id = $(this).attr("id");
    //         var index = id.replace(/[^\d]/g, "");
    //         if($('#field'+index).val() == 'componentname'){
    //             for(var i = 1; i<=6; i++){
    //                 if($('#field'+i).val() == 'componentversion'){
    //                     $.get(createLink('componentpublicaccount', 'ajaxGetVersionByComponent', 'id=' + $('#value'+index).val()+'&index='+i), function(data)
    //                     {
    //                         var pose = data.indexOf('value');
    //                         var p = data.charAt(pose+5);
    //                         $('#value'+p).nextAll().remove();
    //                         $('#value'+p).replaceWith(data);
    //                         $('#value'+p).chosen();
    //                     });
    //                 }
    //             }
    //         }
    //     });
    //     //组件-版本搜索功能联动
    //     $('#field1,#field2,#field3,#field4,#field5,#field6').live("change", function () {
    //         var id = $(this).attr("id");
    //         var index = id.replace(/[^\d]/g, "");
    //         if($('#field'+index).val() == 'componentversion'){
    //             for(var i = 1; i<=6; i++){
    //                 if($('#field'+i).val() == 'componentname'){
    //                     $.get(createLink('componentpublicaccount', 'ajaxGetVersionByComponent', 'id=' + $('#value'+i).val()+'&index='+index), function(data)
    //                     {
    //                         var pose = data.indexOf('value');
    //                         var p = data.charAt(pose+5);
    //                         $('#value'+p).nextAll().remove();
    //                         $('#value'+p).replaceWith(data);
    //                         $('#value'+p).chosen();
    //                     });
    //                     break;
    //                 }
    //             }
    //         }
    //     });
    //
    //
    // });
</script>

<?php include '../../common/view/footer.html.php'; ?>
