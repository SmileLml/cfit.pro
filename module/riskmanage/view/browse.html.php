<?php include '../../common/view/header.html.php'; ?>
<?php include '../../riskmanage/lang/zh-cn.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toobar pull-left">
        <?php
        $menus = customModel::getFeatureMenu($this->moduleName, $this->methodName);

        foreach($menus as $menuItem)
        {
            $active = $menuItem->name == $browseType ? ' btn-active-text' : '';
            echo html::a($this->createLink('riskmanage', 'browse', "browseType=$menuItem->name"), "<span class='text'>{$menuItem->text}</span>", '', "class='btn btn-link $active'");
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->riskmanage->byQuery;?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('riskmanage', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('riskmanage', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('riskmanage', 'export') ? $this->createLink('riskmanage', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->riskmanage->export, '', $misc) . "</li>";


                ?>
            </ul>

        </div>
    </div>
</div>
<!--<div class="cell--><?php //if($browseType == 'bysearch') echo ' show';?><!--" id="queryBox" data-module='riskmanage'></div>-->
<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ('bysearch' == $browseType) {
            echo ' show';
        } ?>" id="queryBox" data-module='riskmanage'></div>
        <?php if (empty($riskList)) { ?>
        <div class="table-empty-tip">
            <p>
                <span class="text-muted"><?php echo $lang->noData; ?></span>
            </p>
        </div>
        <?php } else { ?>
        <form class='main-table' id='riskmanageForm' method='post' data-ride='table' data-nested='true'
              data-checkable='false'>
            <?php $vars = "browseType={$browseType}&param={$param}&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"; ?>
            <table class='table table-fixed has-sort-head' id='secondorders'>
                <thead>
                <tr>
                    <th class='w-130px'><?php echo $lang->riskmanage->projectCode; ?></th>
                    <th class='w-50px'><?php echo $lang->riskmanage->id; ?></th>

                    <th class='w-230px'><?php echo $lang->risk->name; ?></th>
                    <th class='w-60px'><?php echo $lang->risk->strategy; ?></th>
                    <th class='w-60px'><?php echo $lang->risk->status; ?></th>

                    <th class='w-60px'><?php echo $lang->risk->rate; ?></th>
                    <th class='w-60px'><?php echo $lang->risk->pri; ?></th>
                    <th class='w-80px'><?php echo $lang->risk->assignedTo; ?></th>
                    <th class='w-80px'><?php echo $lang->risk->category; ?></th>
                    <th class='w-120px' ><?php echo $lang->riskmanage->bearDept; ?></th>

<!--                    <th class='text-center w-150px'>--><?php //echo $lang->actions; ?><!--</th>-->
                </tr>
                </thead>
                <tbody>
                <?php foreach ($riskList as $risk) { ?>
                <tr>
                    <td title="<?php echo $risk->id; ?>"><?php echo $risk->code; ?></td>
                    <td title="<?php echo $risk->id; ?>"><?php echo $risk->id; ?></td>

                    <td title="<?php echo $risk->name; ?>" class='text-ellipsis'>
                        <?php /*echo html::a( $this->createLink(
                                        'risk',
                                        'view',
                                        "riskID={$risk->id}"
                        ),$risk->name); */
                        echo $risk->name;
                        ?>

                    </td>
                    <td  >
                        <?php echo zget($lang->risk->strategyList, $risk->strategy);?>
                    </td>
                    <td><?php echo zget($lang->risk->statusList,$risk->status); ?></td>

                    <td><?php echo $risk->rate; ?></td>
                    <td>
                        <?php
                                echo "<span class='pri-{$risk->pri}'>".zget($lang->risk->priList,$risk->pri)."</span>";
                        ?>
                    </td>
                    <td><?php echo zget($users,$risk->assignedTo); ?></td>
                    <td><?php echo zget($lang->risk->categoryList, $risk->category); ?></td>
                    <td class="text-ellipsis" title="<?php echo zmget($depts, $risk->bearDept); ?>"><?php
                        echo zmget($depts, $risk->bearDept); ?></td>

                    <!--<td class='c-actions text-center'>
                        <?php
/*
                        common::printIcon(
                        'risk',
                        'view',
                        "riskID={$risk->id}",
                        $risk,
                        'list',
                        'eye',
                            '_self',
                            '',
                            false,
                            "'data-app='project'"
                        );
                        
                        */?>


                    </td>-->
                </tr>
                <?php } ?>
                </tbody>
            </table>
            <div class="table-footer">
                <?php $pager->show('right', 'pagerjs'); ?>
            </div>
        </form>
        <?php } ?>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<style>body{background:white}</style>
<script>

</script>
