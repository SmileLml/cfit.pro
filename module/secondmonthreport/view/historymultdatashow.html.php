<?php include '../../common/view/header.html.php'; ?>
<!--<style>
    table{
        border-collapse: collapse !important;
    }
    table,th,td{
        border: 1px solid #b0bac1 !important;
        text-align: center;
    }
</style>-->
<!--<div id="mainMenu" class="clearfix">-->
<!--    <div class="btn-toolbar pull-left">-->
<!--        <div class="page-title">-->
<!--            <span class="text">--><?php //echo $title; ?><!--</span>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="btn-toolbar pull-right">-->
<!---->
<!--    </div>-->
<!--</div>-->
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="page-title"><h4><?php echo $title; ?></h4></div>

    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('secondmonthreport', 'exportDataList') ? '' : "class=disabled";
                $misc = common::hasPriv('secondmonthreport', 'exportDataList') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('secondmonthreport', 'exportDataList') ? $this->createLink('secondmonthreport', 'exportDataList', "wholeID={$wholeID}&deptID={$deptID}&columnKey={$columnKey}",'',true) : '#';
                echo "<li $class>" . html::a($link, $lang->secondmonthreport->exportDataList, '', $misc) . "</li>";
                ?>
            </ul>

        </div>

    </div>
</div>
<div id='mainContent' class='main-row'>

    <div class='main-col'>

        <?php if (empty($historyDataList)) { ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php } else { ?>
            <form class='main-table' id='secondorderForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
<!--                --><?php //$vars = "orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"; ?>
                <table class='table table-fixed has-sort-head table-bordered'>
                    <thead>
                    <tr>
                        <?php
                        foreach ($useFieldArr as $field){
                            ?>
                            <th ><?php echo $destlang->$field; ?></th>
                        <?php
                        }

                        ?>

                        <th class='w-60px'><?php echo $lang->actions; ?></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php $num = 1; foreach ($historyDataList as $key => $historyData) {

                        ?>
                       <tr>
                           <?php
                            foreach ($historyData as $item){
                                ?>
                                <td title='<?php echo $item; ?>' class='text-ellipsis'><?php echo $item; ?></td>
                                <?php
                            }
                           ?>



                           <td class='c-actions'>
                               <?php
//                                common::printIcon('secondmonthreport', 'views', "wholeID={$item->id}&" . $vars, $item, 'list', 'eye', '', '', '', '', $lang->secondmonthreport->detailTip);
//                               echo html::a($this->createLink($lang->secondmonthreport->reportTomodules[$wholeReport->type],'view',"ID={$historyData['id']}"),$lang->secondmonthreport->detailTip,'_self',"class='btn btn-link icon-eye'");
//                               echo html::a($this->createLink($lang->secondmonthreport->reportTomodules[$wholeReport->type],'view',"ID={$historyData->id}",$linkviewtype,false), "<i class='{$lang->secondmonthreport->reportTomodules[$wholeReport->type]}-view icon-eye'></i>", "_blank", "class='' title='{$lang->secondmonthreport->detailTip}' ", false);;
                               echo html::a($this->createLink($lang->secondmonthreport->multreportTomodules[$historyData->exybtjsource],'view',"ID={$historyData->id}",$linkviewtype,false), "<i class='{$lang->secondmonthreport->multreportTomodules[$historyData->exybtjsource]}-view icon-eye'></i>", "_blank", "class='' title='{$lang->secondmonthreport->detailTip}' ", false);;

                               ?>
                           </td>
                       </tr>
                    <?php $num++; } ?>
                    </tbody>
                </table>
                <!--<div class="table-footer">
                    <?php /*$pager->show('right', 'pagerjs'); */?>
                </div>-->
            </form>
        <?php } ?>

    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
