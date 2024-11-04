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
                            <th class='w-120px'>统计周期</th>
                            <th class='w-120px'>未按计划解决</th>
                            <th class='w-120px'>按计划解决</th>
                            <th class='w-120px'>合计</th>
                            <th class='w-120px'>按计划解决率</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quarterReport as $key => $report): ?>
                            <tr>
                                <td><?php echo sprintf(zget($this->lang->secondmonthreport->quarterDateNameList[$staticType], $key), ceil($curWholeReport->month / 3)); ?></td>
                                <td><?php echo $report->noPlan; ?></td>
                                <td><?php echo $report->plan; ?></td>
                                <td><?php echo $report->total; ?></td>
                                <td><?php echo $report->planRate; ?></td>

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
                                        $class = common::hasPriv('secondmonthreport', 'problemCompletedPlanExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'problemCompletedPlanExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'problemCompletedPlanExport') ? $this->getExportFormUrl('problemCompletedPlanExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';

                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'problemCompletedPlanExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'problemCompletedPlanExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'problemCompletedPlanExport') ? $this->getExportFormUrl('problemCompletedPlanExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                            <th class='w-120px'>未按计划解决</th>
                            <th class='w-120px'>按计划解决</th>
                            <th class='w-120px'>合计</th>
                            <th class='w-120px'>按计划解决率</th>
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
                                echo $this->getFormColumnFormat($detailReport->detail->noPlan,'noPlan',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $noPlan += $detailReport->detail->noPlan; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->plan,'plan',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $plan += $detailReport->detail->plan; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->total,'total',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $total += $detailReport->detail->total; ?></td>
                            <td><?php
                                echo $detailReport->detail->planRate . '%';
                                ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($noPlan,'noPlan',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($plan,'plan',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            ?></td>
                            <td><?php echo $this->getFormColumnFormat($total,'total',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo ($total > 0 ? number_format(($plan / $total) * 100, 2) : "0.00") . '%'; ?></td>
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
