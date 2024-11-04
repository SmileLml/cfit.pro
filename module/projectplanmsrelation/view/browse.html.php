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
            <!--<button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                    class="text"><?php /*echo $lang->export */?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
/*                $class = common::hasPriv('projectplanmsrelation', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('projectplanmsrelation', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('projectplanmsrelation', 'export') ? $this->createLink('projectplan', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->projectplanmsrelation->export, '', $misc) . "</li>";

                */?>
            </ul>-->

        </div>
        <?php if (common::hasPriv('projectplanmsrelation', 'maintenanceRelation')) echo html::a($this->createLink('projectplanmsrelation', 'maintenanceRelation',"",'',true), "<i class='icon-plus'></i> {$lang->projectplanmsrelation->maintenanceRelation}", '', "class='btn btn-primary iframe'"); ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='projectplanmsrelation'></div>
        <?php if (empty($relationList)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='projectplanmsrelationForm'   method='post' data-ride='table' >
<!--                --><?php //$vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='projectplans'>
                    <thead>
                    <tr>
                        <th class='w-90px'><?php echo $lang->projectplanmsrelation->id; ?></th>
                        <th class='w-230px'><?php echo $lang->projectplanmsrelation->mainProjectPlanName; ?></th>
                        <th class="w-170px"><?php echo $lang->projectplanmsrelation->mainProjectPlanCode; ?></th>
                        <th class='w-230px'><?php echo $lang->projectplanmsrelation->slaveProjectPlanName; ?></th>
                        <th class='w-100px'><?php echo $lang->projectplanmsrelation->slaveProjectPlanCode; ?></th>
                        <th class='w-100px'><?php echo $lang->projectplanmsrelation->editedBy; ?></th>

                        <th class='text-center w-80px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($relationList as $relation): ?>
                        <tr>
                            <td>
                                <?php
                                echo $relation->id;
                                ?>

                            </td>
                            <td title='<?php echo $projectplanList[$relation->mainPlanID]->name; ?>'>
                                <a href="<?php echo $this->createLink('projectplan','view','planID='.$relation->mainPlanID); ?>" ><?php echo $projectplanList[$relation->mainPlanID]->name; ?>
                                <?php
                                if($projectplanList[$relation->mainPlanID]->deleted == '1'){
                                    echo "({$lang->projectplanmsrelation->aleadyDeleted})";
                                }
                                ?>
                                </a>
                            </td>
                            <td  title='<?php echo $projectplanList[$relation->mainPlanID]->mark; ?>'><?php echo $projectplanList[$relation->mainPlanID]->mark; ?></td>

                            <td  class='text-ellipsis'><?php

                                if ($relation->slavePlanID) {
                                    $slavePlanIDArr = explode(',', $relation->slavePlanID);
                                    foreach ( $slavePlanIDArr as $slavePlanID) {
                                        if (!empty($slavePlanID)){
                                            $tempstr = "";

                                            $tempstr  = "<a href=\"".$this->createLink('projectplan','view','planID='.$slavePlanID) ."\" >". $projectplanList[$slavePlanID]->name."</a>";

                                            if($projectplanList[$slavePlanID]->deleted == '1'){
                                                $tempstr.= "({$lang->projectplanmsrelation->aleadyDeleted})";
                                            }
                                            $tempstr.= '<br />';
                                            echo $tempstr;
//                                            echo $projectplanList[$slavePlanID]->name. '<br />';
                                        }
                                    }
                                }

                                ?></td>
                            <td  class='text-ellipsis'><?php

                                if ($relation->slavePlanID) {
                                    $slavePlanIDArr = explode(',', $relation->slavePlanID);
                                    foreach ( $slavePlanIDArr as $slavePlanID) {
                                        if (!empty($slavePlanID))  echo $projectplanList[$slavePlanID]->mark. '<br />';
                                    }
                                }

                                ?></td>
                            <td title='<?php echo $relation->editedBy; ?>' class='text-ellipsis'><?php echo zget($users,$relation->editedBy); ?></td>

                            <td class='c-actions text-center'>
                                <?php

                                common::printIcon('projectplanmsrelation', 'edit', "projectplanID=$relation->id", $relation, 'list');


                                common::printIcon('projectplanmsrelation', 'delete', "projectplanID=$relation->id", $relation, 'list', 'trash', 'hiddenwin');
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

