<?php include '../../common/view/header.html.php'; ?>

<style>
    .myformtable{
        border-collapse: collapse !important;
    }
    .myformtable th,.myformtable td{
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
<?php js::import($this->app->getWebRoot() . "js/" . 'datatable/min.js'); ?>
<?php css::import($this->app->getWebRoot() . "js/" . 'datatable/min.css'); ?>
<div id='mainContent' class='main-row'>

    <div class='side-col' id='sidebar'><?php include 'blockreportlist.html.php'; ?></div>
    <div class='main-col'>
        <?php include 'search.html.php'; ?>
        <?php
        if ($quarterReport) {
        ?>
        <div class="cell" >
            <form class='main-table'  method='post' data-nested='true' >

                    <table class='table table-fixed has-sort-head table-bordered myformtable'>
                        <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align: middle;">统计周期</th>
                            <th rowspan="2" style="vertical-align: middle;">问题单数</th>
                            <th colspan="3">问题来源情况</th>
                            <th colspan="5">问题解决情况</th>
                        </tr>
                        <tr>
                            <th class='w-120px' title="成方金信">成方金信</th>
                            <th class='w-120px' title="清算总中心">清算总中心</th>
                            <th class='w-120px' title="研发自建">研发自建</th>
                            <th class='w-120px' title="未解决">未解决</th>
                            <th class='w-120px' title="已解决">已解决</th>
                            <th class='w-120px' title="解决率">解决率</th>
                            <th class='w-120px' title="平均解决周期（天）">平均解决周期（天）</th>
                            <th class='w-120px' title="最大解决周期（天）">最大解决周期（天）</th>
                        </tr>
                        </tbody>
                        <tbody>
                            <?php foreach ($quarterReport as $key => $report): ?>
                            <tr>
                                <td><?php echo sprintf(zget($this->lang->secondmonthreport->quarterDateNameList[$staticType], $key), ceil($curWholeReport->month / 3)); ?></td>
                                <?php if('quarter' == $key): ?>
                                    <td><?php echo $report->sum; ?></td>
                                <?php else: ?>
                                    <td><?php echo $report->sum . '(' . $report->sumRate . ')'; ?></td>
                                <?php endif;?>
                                <td><?php echo $report->guestjx . '(' . $report->guestjxRate . ')'; ?></td>
                                <td><?php echo $report->guestcn . '(' . $report->guestcnRate . ')'; ?></td>
                                <td><?php echo $report->self . '(' . $report->selfRate . ')'; ?></td>
                                <td><?php echo $report->noSolve; ?></td>
                                <td><?php echo $report->solved; ?></td>
                                <td><?php echo $report->solveRate; ?></td>
                                <td><?php echo $report->averagePeriod; ?></td>
                                <td><?php echo $report->maxPeriod; ?></td>

                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

            </form>
        </div>
            <?php
        }
        ?>
            <div class="cell" >
                <form class='main-table'  method='post' data-ride='table' data-nested='true' >

                    <div  class="clearfix"  >
                        <div class="btn-toolbar pull-left">
                            <div class="page-title">
                                <span class="text"><?php echo $detailTitle; ?></span>
                            </div>
                        </div>
                        <div class="btn-toolbar pull-right">
                            <?php if($isExecutive){ ?>
                            <div class='btn-group'>
                                <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export; ?></span> <span class="caret"></span></button>
                                <ul class="dropdown-menu pull-right" id='exportActionMenu'>
                                    <?php
                                    if($searchtype == 'history'){
                                        $class = common::hasPriv('secondmonthreport', 'browseExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'browseExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'browseExport') ? $this->getExportFormUrl('browseExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';

                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'browseExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'browseExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'browseExport') ? $this->getExportFormUrl('browseExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        echo "<li >" . html::a($this->getrealtimebasicexportUrl('realtimebasicexport','form',$realstarttime,$realendtime,$deptID,$staticType,$isuseHisData), $lang->secondmonthreport->exportSnapshot, '', "data-toggle='modal' data-type='iframe' class='export'") . '</li>';
                                        echo "<li >" . html::a($this->getrealtimebasicexportUrl('realtimebasicexport','basic',$realstarttime,$realendtime,$deptID,$staticType,$isuseHisData), $lang->secondmonthreport->fileTip, '', "data-toggle='modal' data-type='iframe' class='export'") . '</li>';

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
                    <table class='table table-fixed has-sort-head table-bordered myformtable'>
                        <thead>
                        <tr>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->deptName; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->unaccepted; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->waitAllocation; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->waitSolve; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->alreadySolve; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->total; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->solveRate; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $unaccepted = 0;
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
                                echo $this->getFormColumnFormat($detailReport->detail->unaccepted,'unaccepted',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $unaccepted += $detailReport->detail->unaccepted; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->waitAllocation,'waitAllocation',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $waitAllocation += $detailReport->detail->waitAllocation; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->waitSolve,'waitSolve',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                /*if( $detailReport->detail->waitSolve > 0 ){
                                echo html::a($this->getShowDetailListUrl($searchtype,$wholeID,$detailReport->deptID,'waitSolve',$realstarttime,$realendtime),$detailReport->detail->waitSolve,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

                                }else {
                                echo $detailReport->detail->waitSolve;}*/
                                $waitSolve += $detailReport->detail->waitSolve; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->alreadySolve,'alreadySolve',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                /*if( $detailReport->detail->alreadySolve > 0 ){
                                    echo html::a($this->getShowDetailListUrl($searchtype,$wholeID,$detailReport->deptID,'alreadySolve',$realstarttime,$realendtime),$detailReport->detail->alreadySolve,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

                                }else {
                                    echo $detailReport->detail->alreadySolve;}*/
                                 $alreadySolve += $detailReport->detail->alreadySolve; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->total,'total',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                /*if( $detailReport->detail->total > 0 ){
                                    echo html::a($this->createLink("secondmonthreport",'historyDataShow',"wholeID={$wholeID}&deptID={$detailReport->deptID}&columKey=total",'',true),$detailReport->detail->total,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

                                }else {
                                    echo $detailReport->detail->total;
                                }*/
                                $total += $detailReport->detail->total; ?></td>
                            <td><?php echo $detailReport->detail->solveRate . '%'; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($unaccepted,'unaccepted',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($waitAllocation,'waitAllocation',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            ?></td>
                            <td><?php echo $this->getFormColumnFormat($waitSolve,'waitSolve',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($alreadySolve,'alreadySolve',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($total,'total',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
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
