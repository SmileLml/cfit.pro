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

                                        $class = common::hasPriv('secondmonthreport', 'demandExceedExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'demandExceedExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'demandExceedExport') ? $this->getExportFormUrl('demandExceedExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'demandExceedExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'demandExceedExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'demandExceedExport') ? $this->getExportFormUrl('demandExceedExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                            <th class='w-120px'><?php echo $lang->secondmonthreport->realizedTwoMonthMore; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->unrealizedTwoMonth; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->amount; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->totalDemand; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->overdueRate; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $realizedNum = 0;
                        $twoMonthNum = 0;
                        $amount = 0;
                        $totalDemand = 0;
                        foreach ($detailReports as $key => $detailReport) { ?>
                        <tr>
                            <td><?php echo $detailReport->detail->deptID == '合计' ? $detailReport->detail->deptID : zget($depts, $detailReport->detail->deptID); ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->realizedNum,'realizedNum',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $realizedNum += $detailReport->detail->realizedNum;
                            ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->twoMonthNum,'twoMonthNum',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $twoMonthNum += $detailReport->detail->twoMonthNum;?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->amount,'amount',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $amount += $detailReport->detail->amount;?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->totalDemand,'totalDemand',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $totalDemand += $detailReport->detail->totalDemand; ?></td>
                            <td><?php echo $detailReport->detail->overdueRate.'%'; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($realizedNum,'realizedNum',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($twoMonthNum,'twoMonthNum',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($amount,'amount',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($totalDemand,'totalDemand',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php echo ($totalDemand > 0 ? number_format(($amount / $totalDemand) * 100, 2) : "0.00") . '%'; ?></td>
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
