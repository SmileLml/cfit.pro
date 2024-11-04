
<?php include '../../common/view/header.html.php'; ?>
<style>.w-170px {
        width: 170px;
    }</style>
<div id="mainMenu" class="clearfix">
    <?php include 'menu.html.php'; ?>
    <div class="btn-toolbar pull-left">

       <!-- <a class="btn btn-link querybox-toggle" id='bysearchTab'><i
                class="icon icon-search muted"></i> <?php /*echo $lang->searchAB; */?></a>-->
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" >
                <?php

                $class = common::hasPriv('secondmonthdata', 'exportTemplate') ? '' : "class='disabled'";
                $link = common::hasPriv('secondmonthdata', 'exportTemplate') ? $this->createLink('secondmonthdata', 'exportTemplate',"type={$type}","",true) : '#';
                $misc = common::hasPriv('secondmonthdata', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='importdata'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->secondmonthdata->exportTemplate, '', $misc) . '</li>';
                ?>
            </ul>

        </div>
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-import muted"></i> <span
                        class="text"><?php echo $lang->import ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" >
                <?php

                $class = common::hasPriv('secondmonthdata', 'importdata') ? '' : "class='disabled'";
                $link = common::hasPriv('secondmonthdata', 'importdata') ? $this->createLink('secondmonthdata', 'importdata',"type={$type}","",true) : '#';
                $misc = common::hasPriv('secondmonthdata', 'importdata') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='importdata'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->secondmonthdata->importdata, '', $misc) . '</li>';

                ?>
            </ul>

        </div>
        <?php if (common::hasPriv('secondmonthdata', 'create')) echo html::a($this->createLink('secondmonthdata', 'create',"type={$type}","",true), "<i class='icon-plus'></i> {$lang->secondmonthdata->create}", '', "class='btn btn-primary' data-toggle='modal' data-type='iframe'"); ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='projectplan'></div>
        <?php if (empty($dataList)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='projectplanForm'   method='post' data-ride='table' data-nested='true'
                  data-checkable='true'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='projectplans'>
                    <thead>
                    <tr>
                        <th class='w-40px'><?php echo $lang->secondmonthdata->id; ?></th>
                        <th class='w-50px'><?php echo $lang->secondmonthdata->sourceyear; ?></th>
                        <th style="width: 120px"><?php echo $lang->secondmonthdata->demandlang->code; ?></th>
                        <th class='w-80px'><?php echo $lang->secondmonthdata->demandlang->status; ?></th>
                        <th class='w-80px'><?php echo $lang->secondmonthdata->demandlang->app; ?></th>
                        <th class='w-120px'><?php echo $lang->secondmonthdata->demandlang->title; ?></th>
                        <th class='w-110px'> <?php echo $lang->secondmonthdata->demandlang->newPublishedTime; ?></th>
                        <th class='w-110px'><?php echo $lang->secondmonthdata->demandlang->solvedTime; ?></th>
                        <th class='w-80px'><?php echo $lang->secondmonthdata->demandlang->acceptDept; ?></th>
                        <th class='w-80px'><?php echo $lang->secondmonthdata->demandlang->acceptUser; ?></th>
                        <th class='w-100px'> <?php echo $lang->secondmonthdata->demandlang->createdBy; ?></th>
                        <th class='w-100px'><?php echo $lang->secondmonthdata->demandlang->createdDate; ?></th>
                        <th class='w-100px'><?php echo $lang->secondmonthdata->demandlang->fixType; ?></th>
                        <th class='w-100px'><?php echo $lang->secondmonthdata->demandlang->actualMethod; ?></th>

                        <th class='text-center w-50px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dataList as $data): ?>
                        <tr>
                            <td>
                                <?php    echo $data->id;     ?>

                            </td>
                            <td ><?php echo $data->sourceyear; ?></td>

                            <td ><?php echo $data->code; ?></td>
                            <td class='text-ellipsis' >  <?php echo zget($lang->demand->statusList, $data->status); ?>  </td>
                            <?php
                            $as = [];
                            foreach (explode(',', $data->app) as $app) {
                                if (!$app) continue;
                                $as[] = zget($apps, $app);
                            }
                            $app = implode(', ', $as);
                            ?>

                            <td title='<?php echo $app; ?>' class='text-ellipsis'><?php echo $app; ?></td>

                            <td title='<?php echo $data->title; ?>' class='text-ellipsis'><?php echo $data->title; ?></td>
                            <td><?php echo $data->newPublishedTime; ?></td>

                            <td><?php if (!helper::isZeroDate($data->solvedTime)) echo $data->solvedTime; ?></td>
                            <td><?php echo zget($depts,$data->acceptDept); ?></td>
                            <td><?php echo zget($users,$data->acceptUser); ?></td>
                            <td><?php echo zget($users,$data->createdBy); ?></td>
                            <td><?php echo $data->createdDate; ?></td>
                            <td class='text-ellipsis' >
                                <?php echo zget($lang->demand->fixTypeList, $data->fixType); ?>
                            </td>
                            <td class='text-ellipsis' >
                                <?php echo zmget($lang->requirement->actualMethodList, $data->actualMethod, '');; ?>
                            </td>

                            <td class='c-actions'>
                                <?php

                                common::printIcon('secondmonthdata', 'delete', "id=$data->id&confirm=no&type={$type}", $data, 'list', 'trash', 'hiddenwin');
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="table-footer">

                    <?php
                    $pager->show('right', 'pagerjs');
                    ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>


