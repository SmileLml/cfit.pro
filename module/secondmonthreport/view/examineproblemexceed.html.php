<?php include '../../common/view/header.html.php'; ?>
<style>
    table{
        border-collapse: collapse !important;
    }
    table,th,td{
        border: 1px solid #b0bac1 !important;
        text-align: center;
    }
</style>


<div id="mainMenu" class="clearfix">
    <?php include 'reportheader.html.php'; ?>
    <div class="btn-toolbar pull-left">
        <div class="page-title">
            <span class="text"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="btn-toolbar pull-right">

    </div>
</div>
<div id='mainContent' class='main-row'>

    <div class='side-col' id='sidebar'><?php include 'examinereportlist.html.php'; ?></div>
    <div class='main-col'>
        <div class="page-title"><h4><?php echo $title; ?></h4></div>
        <?php if (empty($wholeReportList)) { ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php } else { ?>
            <form class='main-table' id='secondorderForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
                <?php $vars = "orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"; ?>
                <table class='table table-fixed has-sort-head table-bordered'>
                    <thead>
                    <tr>
                        <th class='w-120px'><?php echo $lang->secondmonthreport->id; ?></th>
                        <th class='w-120px'><?php echo $lang->secondmonthreport->year; ?></th>
                        <th class='w-120px'><?php echo $lang->secondmonthreport->statisticalInterval; ?></th>
                        <th class='w-120px'><?php echo $lang->secondmonthreport->createdDate; ?></th>
                        <th class='w-120px'><?php echo $lang->actions; ?></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php $num = 1; foreach ($wholeReportList as $key => $item) { ?>
                       <tr>
                           <td><?php echo ($pager->pageID - 1) * $pager->recPerPage + $num; ?></td>
                           <td><?php echo $item->year; ?></td>
                           <td><?php echo $item->startday.' ~ '.$item->endday; ?></td>
                           <td><?php echo $item->createdDate; ?></td>
                           <td class='c-actions'>
                               <?php
                                common::printIcon('secondmonthreport', 'browse', "wholeID={$item->id}&" . $vars, $item, 'list', 'eye', '', '', '', '', $lang->secondmonthreport->detailTip);
                                ?>
                               <?php if($isExecutive){ ?>
                               <a href="<?php echo  $item->fileUrl; ?>" class="btn " title="<?php echo $lang->secondmonthreport->fileTip ?>" data-app="report"><i class="icon-secondmonthreport-browse icon-export"></i></a>
                               <?php } ?>
                           </td>
                       </tr>
                    <?php $num++; } ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs'); ?>
                </div>
            </form>
        <?php } ?>
            <div class="cell" style="margin-top:30px;">
                <form class='main-table'  method='post' data-ride='table' data-nested='true' >
                <div class="row" id='conditions'>
                    <div class='w-220px col-md-3 col-sm-6'>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->secondmonthreport->deptName; ?></span>
                            <?php echo html::select('deptID', $searchdepts, $deptID, "class='form-control chosen' "); ?>
                        </div>
                    </div>
                    <div class='col-md-3 col-sm-6'><?php echo html::submitButton($lang->secondmonthreport->seqrchQuery, '', 'btn btn-primary'); ?></div>
                </div>
                    <div  class="clearfix" style="margin-top:30px;" >
                        <div class="btn-toolbar pull-left">
                            <div class="page-title">
                                <span class="text"><?php echo $detailTitle; ?></span>
                            </div>
                        </div>
                        <div class="btn-toolbar pull-right">
                            <?php if($isExecutive){ ?>
                            <div class='btn-group'>
                                <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export; ?></span> <span class="caret"></span></button>
                                <ul class="dropdown-menu" id='exportActionMenu'>
                                    <?php
                                    $class = common::hasPriv('secondmonthreport', 'browseExport') ? '' : 'class=disabled';
                                    $misc  = common::hasPriv('secondmonthreport', 'browseExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                    $link  = common::hasPriv('secondmonthreport', 'browseExport') ? $this->createLink('secondmonthreport', 'browseExport', 'wholeId='.$wholeID.'&&deptId='.$deptID) : '#';
                                    echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                    if($curWholeReport->fileUrl2){
                                        echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                    }

                                    ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                    if ($detailReports) {
                        ?>
                    <table class='table table-fixed has-sort-head table-bordered'>
                        <thead>
                        <tr>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->deptName; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->waitAllocation; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->waitSolve; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->alreadySolve; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->total; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->solveRate; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $waitAllocation = 0;
                        $waitSolve      = 0;
                        $alreadySolve   = 0;
                        $total          = 0;
                        foreach ($detailReports as $key => $detailReport) {
                            $detailReport->deptID = 0 < $detailReport->deptID ? $detailReport->deptID : -1;
                            ?>
                        <tr>
                            <td><?php echo zget($depts, $detailReport->deptID); ?></td>
                            <td><?php
                                if( $detailReport->detail->waitAllocation > 0 ){
                                echo html::a($this->createLink("secondmonthreport",'historyDataShow'),$detailReport->detail->waitAllocation,'_self',"class='btn btn-link iframe'");

                                }else { echo $detailReport->detail->waitAllocation;}

                            $waitAllocation += $detailReport->detail->waitAllocation; ?></td>
                            <td><?php
                                if( $detailReport->detail->waitSolve > 0 ){
                                echo html::a($this->createLink("secondmonthreport",'historyDataShow',"wholeID={$wholeID}&deptID={$detailReport->deptID}&columKey=waitSolve",'',true),$detailReport->detail->waitSolve,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

                                }else {
                                echo $detailReport->detail->waitSolve;}
                                $waitSolve += $detailReport->detail->waitSolve; ?></td>
                            <td><?php
                                if( $detailReport->detail->alreadySolve > 0 ){
                                    echo html::a($this->createLink("secondmonthreport",'historyDataShow',"wholeID={$wholeID}&deptID={$detailReport->deptID}&columKey=alreadySolve",'',true),$detailReport->detail->alreadySolve,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

                                }else {
                                    echo $detailReport->detail->alreadySolve;}
                                 $alreadySolve += $detailReport->detail->alreadySolve; ?></td>
                            <td><?php
                                if( $detailReport->detail->total > 0 ){
                                    echo html::a($this->createLink("secondmonthreport",'historyDataShow',"wholeID={$wholeID}&deptID={$detailReport->deptID}&columKey=total",'',true),$detailReport->detail->total,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

                                }else {
                                    echo $detailReport->detail->total;
                                } $total += $detailReport->detail->total; ?></td>
                            <td><?php echo $detailReport->detail->solveRate . '%'; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php echo $waitAllocation; ?></td>
                            <td><?php echo $waitSolve; ?></td>
                            <td><?php echo $alreadySolve; ?></td>
                            <td><?php echo $total; ?></td>
                            <td><?php echo ($total > 0 ? number_format(($alreadySolve / $total) * 100, 2) : "0.00") . '%'; ?></td>
                        </tr>
                        </tbody>
                    </table>
                        <?php
                    } else {
                        ?>
                        <div class="table-empty-tip">
                            <p>
                                <span class="text-muted"><?php echo $lang->noData; ?></span>
                            </p>
                        </div>
                    <?php
                    }
                    ?>
                </form>
            </div>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
