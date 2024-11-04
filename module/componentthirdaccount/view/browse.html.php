<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        foreach ($lang->componentthirdaccount->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo html::a($this->createLink('componentthirdaccount', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
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
                $class = common::hasPriv('componentthirdaccount', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('componentthirdaccount', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('componentthirdaccount', 'export') ? $this->createLink('componentthirdaccount', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->componentthirdaccount->export, '', $misc) . "</li>";
                ?>
            </ul>
            <?php if (common::hasPriv('componentthirdaccount', 'create')) echo html::a($this->createLink('componentthirdaccount', 'create'), "<i class='icon-plus'></i> {$lang->componentthirdaccount->create}", '', "class='btn btn-primary'"); ?>
        </div>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='componentthirdaccount'></div>
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
                        <th class='w-30px'><?php echo $lang->componentthirdaccount->code; ?></th>
                        <th class='w-160px'><?php common::printOrderLink('appname', $orderBy, $vars, $lang->componentthirdaccount->appname); ?></th>
                        <th class='w-160px'><?php common::printOrderLink('productname', $orderBy, $vars, $lang->componentthirdaccount->productname); ?></th>
                        <th class='w-90px'><?php common::printOrderLink('productversion', $orderBy, $vars, $lang->componentthirdaccount->productversion); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('productdept', $orderBy, $vars, $lang->componentthirdaccount->productdept); ?></th>
                        <th class='w-70px'><?php common::printOrderLink('productconnect', $orderBy, $vars, $lang->componentthirdaccount->productconnect); ?></th>
                        <th class='w-200px'><?php common::printOrderLink('componentname', $orderBy, $vars, $lang->componentthirdaccount->componentname); ?></th>
                        <th class='w-70px'><?php common::printOrderLink('componentversion', $orderBy, $vars, $lang->componentthirdaccount->componentversion); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('vulnerabilityLevel', $orderBy, $vars, $lang->componentthirdaccount->vulnerabilityLevel); ?></th>
                        <th class='w-140px'><?php echo $lang->componentthirdaccount->comment; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data): ?>
                        <tr>
                            <td class='text-ellipsis' title="<?php echo $data->code;?>"><?php echo $data->code;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->appname;?>"><?php echo $data->appname;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->productname;?>"><?php echo $data->productname;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->productversion; ?>"><?php echo $data->productversion;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->productdept; ?>"><?php echo $data->productdept; ?></td>
                            <td class='text-ellipsis' title="<?php echo $data->productconnect;?>"><?php echo $data->productconnect;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->componentname;?>"><?php echo $data->componentname;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->componentversion; ?>"><?php echo $data->componentversion; ?></td>
                            <td class='text-ellipsis' title="<?php echo $data->vulnerabilityLevel; ?>"><?php echo $data->vulnerabilityLevel; ?></td>
                            <td class='text-ellipsis' title="<?php echo $data->comment; ?>"><?php echo $data->comment; ?></td>
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
    $(function () {
        //系统-产品搜索功能联动
        $('#value1,#value2,#value3,#value4,#value5,#value6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");
            if($('#field'+index).val() == 'appname'){
                for(var i = 1; i<=6; i++){
                    if($('#field'+i).val() == 'productname'){
                        $.get(createLink('componentthirdaccount', 'ajaxGetproductByApp', 'id=' + $('#value'+index).val()+'&index='+i), function(data)
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
        });
        //产品-版本搜索功能联动
        $('#field1,#field2,#field3,#field4,#field5,#field6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");
            if($('#field'+index).val() == 'productname'){
                for(var i = 1; i<=6; i++){
                    if($('#field'+i).val() == 'appname'){
                        $.get(createLink('componentthirdaccount', 'ajaxGetproductByApp', 'id=' + $('#value'+i).val()+'&index='+index), function(data)
                        {
                            var pose = data.indexOf('value');
                            var p = data.charAt(pose+5);
                            $('#value'+p).nextAll().remove();
                            $('#value'+p).replaceWith(data);
                            $('#value'+p).chosen();
                            $('#value'+p).change();
                        });
                        break;
                    }
                }
            }
        });

        //产品-版本搜索功能联动
        $('#value1,#value2,#value3,#value4,#value5,#value6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");
            if($('#field'+index).val() == 'productname'){
                for(var i = 1; i<=6; i++){
                    if($('#field'+i).val() == 'productversion'){
                        $.get(createLink('componentthirdaccount', 'ajaxGetVersionByProduct', 'id=' + $('#value'+index).val()+'&index='+i), function(data)
                        {
                            var pose = data.indexOf('value');
                            var p = data.charAt(pose+5);
                            //$('#value'+p+'_chosen').remove();
                            $('#value'+p).nextAll().remove();
                            $('#value'+p).replaceWith(data);
                            $('#value'+p).chosen();
                        });
                    }
                }
            }
        });
        //产品-版本搜索功能联动
        $('#field1,#field2,#field3,#field4,#field5,#field6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");
            if($('#field'+index).val() == 'productversion'){
                for(var i = 1; i<=6; i++){
                    if($('#field'+i).val() == 'productname'){
                        $.get(createLink('componentthirdaccount', 'ajaxGetVersionByProduct', 'id=' + $('#value'+i).val()+'&index='+index), function(data)
                        {
                            var pose = data.indexOf('value');
                            var p = data.charAt(pose+5);
                            $('#value'+p).nextAll().remove();
                            $('#value'+p).replaceWith(data);
                            $('#value'+p).chosen();
                        });
                        break;
                    }
                }
            }
        });


        //组件-版本搜索功能联动
        $('#value1,#value2,#value3,#value4,#value5,#value6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");
            if($('#field'+index).val() == 'componentname'){
                for(var i = 1; i<=6; i++){
                    if($('#field'+i).val() == 'componentversion'){
                        $.get(createLink('componentthirdaccount', 'ajaxGetVersionByComponent', 'id=' + $('#value'+index).val()+'&index='+i), function(data)
                        {
                            var pose = data.indexOf('value');
                            var p = data.charAt(pose+5);
                            $('#value'+p).nextAll().remove();
                            $('#value'+p).replaceWith(data);
                            $('#value'+p).chosen();
                        });
                    }
                }
            }
        });
        //组件-版本搜索功能联动
        $('#field1,#field2,#field3,#field4,#field5,#field6').live("change", function () {
            var id = $(this).attr("id");
            var index = id.replace(/[^\d]/g, "");
            if($('#field'+index).val() == 'componentversion'){
                for(var i = 1; i<=6; i++){
                    if($('#field'+i).val() == 'componentname'){
                        $.get(createLink('componentthirdaccount', 'ajaxGetVersionByComponent', 'id=' + $('#value'+i).val()+'&index='+index), function(data)
                        {
                            var pose = data.indexOf('value');
                            var p = data.charAt(pose+5);
                            $('#value'+p).nextAll().remove();
                            $('#value'+p).replaceWith(data);
                            $('#value'+p).chosen();
                        });
                        break;
                    }
                }
            }
        });


    });
</script>

<?php include '../../common/view/footer.html.php'; ?>
