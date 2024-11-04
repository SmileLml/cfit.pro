<?php include '../../common/view/header.html.php'; ?>
<style>.w-170px {
        width: 170px;
    }</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>

    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                    class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('projectplanactiontrigger', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('projectplanactiontrigger', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('projectplanactiontrigger', 'export') ? $this->createLink('projectplanactiontrigger', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->projectplanactiontrigger->export, '', $misc) . "</li>";

                ?>
            </ul>

        </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='projectplanactiontrigger'></div>
        <?php if (empty($relationList)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='projectplanactiontriggerForm'   method='post' data-ride='table' >
<!--                --><?php //$vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='projectplans'>
                    <thead>
                    <tr>
                        <th class='w-90px'><?php echo $lang->projectplanactiontrigger->id; ?></th>
                        <th class='w-100px'><?php echo $lang->projectplanactiontrigger->actionDay; ?></th>
                        <th class="w-230px"><?php echo $lang->projectplanactiontrigger->planName; ?></th>
                        <th class='w-100px'><?php echo $lang->projectplanactiontrigger->actionUser; ?></th>
                        <th class='w-100px'><?php echo $lang->projectplanactiontrigger->status; ?></th>
                        <th class='w-100px'><?php echo $lang->projectplanactiontrigger->snapshotVersion; ?></th>

                        <th class='text-center w-80px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($relationList as $planaction): ?>
                        <tr>
                            <td>
                                <?php
                                echo $planaction->id;
                                ?>

                            </td>
                            <td  ><?php echo $planaction->actionDay; ?></td>
                            <td >
                                <?php echo html::a(helper::createLink('projectplan','view', "projectplanID=$planaction->planID"), $planaction->planName); ?>
                            </td>
                            <td title='<?php echo $planaction->actionUser; ?>' class='text-ellipsis'><?php echo zget($users,$planaction->actionUser); ?></td>



                            <td ><?php echo zget($lang->projectplanactiontrigger->statusList,$planaction->status); ?></td>
                            <td title='<?php echo $planaction->snapshotVersion; ?>' class='text-ellipsis'><?php echo $planaction->snapshotVersion; ?></td>

                            <td class='c-actions text-center'>
                                <?php

                                common::printIcon('projectplanactiontrigger', 'acttagging', "id=$planaction->id", $planaction, 'list','flag', '', 'iframe', true, '');


//                                common::printIcon('projectplanactiontrigger', 'delete', "projectplanID=$planaction->id", $planaction, 'list', 'trash', 'hiddenwin');
                                ?>
                                <?php if($planaction->fileUrl && common::hasPriv('projectplanactiontrigger', 'downloadSnap')){
                                    ?>
                                    <a href="<?php echo $planaction->fileUrl; ?>" class="btn " title="<?php echo $lang->projectplanactiontrigger->fileTip; ?>" ><i class="icon-projectplanactiontrigger-browse icon-export"></i></a>

                                <?php
                                }?>
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

