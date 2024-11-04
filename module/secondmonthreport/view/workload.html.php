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
                            <th class='w-120px'>人时</th>
                            <th class='w-120px'>人天</th>
                            <th class='w-120px'>人月</th>
                            <th class='w-120px'>人年</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($quarterReport as $key => $report): ?>
                            <tr>
                                <td><?php echo sprintf(zget($this->lang->secondmonthreport->quarterDateNameList[$staticType], $key), ceil($curWholeReport->month / 3)); ?></td>
                                <td><?php echo $report->hour; ?></td>
                                <td><?php echo $report->day; ?></td>
                                <td><?php echo $report->month; ?></td>
                                <td><?php echo $report->year; ?></td>
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
                                <ul class="dropdown-menu  pull-right" id='exportActionMenu'>
                                    <?php
                                    if($searchtype == 'history') {
                                        $class = common::hasPriv('secondmonthreport', 'workloadExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'workloadExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'workloadExport') ? $this->getExportFormUrl('workloadExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'workloadExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'workloadExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'workloadExport') ? $this->getExportFormUrl('workloadExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                            <th class='w-120px'><?php echo $lang->secondmonthreport->workloadShowTypeList['secondproblem']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->workloadShowTypeList['seconddemand']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->workloadShowTypeList['secondorder']; ?></th>
                            <?php if (!$quarterReport): ?>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->workloadShowTypeList['secondcustom']; ?></th>
                            <?php endif; ?>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->countPeopleMonth; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $secondproblemNum = 0;
                        $seconddemandNum = 0;
                        $secondorderNum = 0;
                        $secondcustomNum = 0;
                        $countPeopleMonthNum = 0;

                        foreach ($detailReports as $key => $detailReport) { ?>
                        <tr>
                            <td><?php echo  zget($depts, $key); ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->secondproblem,'secondproblem',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $secondproblemNum += $detailReport->detail->secondproblem; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->seconddemand,'seconddemand',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $seconddemandNum += $detailReport->detail->seconddemand; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->secondorder,'secondorder',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $secondorderNum += $detailReport->detail->secondorder; ?></td>
                            <?php if (!$quarterReport): ?>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->secondcustom,'secondcustom',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $secondcustomNum += $detailReport->detail->secondcustom; ?></td>
                            <?php endif; ?>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->countPeopleMonth,'countPeopleMonth',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $countPeopleMonthNum += $detailReport->detail->countPeopleMonth; ?></td>

                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php echo $this->getFormColumnFormat($secondproblemNum,'secondproblem',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($seconddemandNum,'seconddemand',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($secondorderNum,'secondorder',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                        <?php if (!$quarterReport): ?>
                            <td><?php echo $this->getFormColumnFormat($secondcustomNum,'secondcustom',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                        <?php endif; ?>
                            <td><?php echo $this->getFormColumnFormat($countPeopleMonthNum,'countPeopleMonth',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>

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
