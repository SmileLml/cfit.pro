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
    <div class="btn-toolbar pull-left">
        <div class="page-title">
            <span class="text"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="btn-toolbar pull-right">

    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='side-col' id='sidebar'><?php include 'blockreportlist.html.php'; ?></div>

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
                        foreach ($detailReports as $deptIDKey => $detailReport) {
//                            $detailReport->deptID = 0 < $detailReport->deptID ? $detailReport->deptID : -1;
                            ?>
                        <tr>
                            <td><?php echo zget($depts, $deptIDKey); ?></td>
                            <td><?php
                                if( $detailReport->waitAllocation > 0 ){
                                echo html::a($this->createLink("secondmonthreport",'showrealtimedata'),$detailReport->detail->waitAllocation,'_self',"class='btn btn-link iframe'");

                                }else { echo $detailReport->waitAllocation;}

                            $waitAllocation += $detailReport->waitAllocation; ?></td>
                            <td><?php
                                if( $detailReport->waitSolve > 0 ){
                                echo html::a($this->createLink("secondmonthreport",'showrealtimedata',"deptID={$deptIDKey}&columKey=waitSolve",'',true),$detailReport->waitSolve,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

                                }else {
                                echo $detailReport->waitSolve;}
                                $waitSolve += $detailReport->waitSolve; ?></td>
                            <td><?php
                                if( $detailReport->alreadySolve > 0 ){
                                    echo html::a($this->createLink("secondmonthreport",'showrealtimedata',"deptID={$deptIDKey}&columKey=alreadySolve",'',true),$detailReport->alreadySolve,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

                                }else {
                                    echo $detailReport->alreadySolve;}
                                 $alreadySolve += $detailReport->alreadySolve; ?></td>
                            <td><?php
                                if( $detailReport->total >= 0 ){
                                    echo html::a($this->createLink("secondmonthreport",'showrealtimedata',"endtime={$endtime}&time={$time}&starttime={$starttime}&dtype={$dtype}&deptID={$deptIDKey}&columKey=total",'',true),$detailReport->total,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

                                }else {
                                    echo $detailReport->total;
                                } $total += $detailReport->total; ?></td>
                            <td><?php echo $detailReport->solveRate . '%'; ?></td>
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
