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
                                        $class = common::hasPriv('secondmonthreport', 'supportExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'supportExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'supportExport') ? $this->getExportFormUrl('supportExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'supportExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'supportExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'supportExport') ? $this->getExportFormUrl('supportExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                            <th class=''><?php echo $lang->secondmonthreport->deptName; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->supportShowStypeList['supporta1']; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->supportShowStypeList['supporta3']; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->supportShowStypeList['supporta4']; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->supportShowStypeList['supporta5']; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->supportShowStypeList['supporta6']; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->supportShowStypeList['supporta7']; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->supportShowStypeList['supporta8']; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->supportShowStypeList['supporta11']; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->supportShowStypeList['supporta12']; ?></th>
                            <th class=''><?php echo $lang->secondmonthreport->total; ?></th>


                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $supporta1Num = 0;

                        $supporta3Num = 0;
                        $supporta4Num = 0;
                        $supporta5Num = 0;
                        $supporta6Num = 0;
                        $supporta7Num = 0;

                        $supporta11Num = 0;
                        $supporta12Num = 0;

                        $totalNum = 0;
                        foreach ($detailReports as $key => $detailReport) { ?>
                        <tr>
                            <td><?php echo  zget($depts, $key); ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->supporta1,'supporta1',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $supporta1Num += $detailReport->detail->supporta1; ?></td>

                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->supporta3,'supporta3',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $supporta3Num += $detailReport->detail->supporta3; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->supporta4,'supporta4',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $supporta4Num += $detailReport->detail->supporta4; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->supporta5,'supporta5',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $supporta5Num += $detailReport->detail->supporta5; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->supporta6,'supporta6',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $supporta6Num += $detailReport->detail->supporta6; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->supporta7,'supporta7',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $supporta7Num += $detailReport->detail->supporta7; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->supporta8,'supporta8',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $supporta8Num += $detailReport->detail->supporta8; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->supporta11,'supporta11',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $supporta11Num += $detailReport->detail->supporta11; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->supporta12,'supporta12',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $supporta12Num += $detailReport->detail->supporta12; ?></td>

                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->total,'total',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $totalNum += $detailReport->detail->total; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php echo $this->getFormColumnFormat($supporta1Num,'supporta1',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($supporta3Num,'supporta3',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($supporta4Num,'supporta4',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($supporta5Num,'supporta5',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($supporta6Num,'supporta6',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($supporta7Num,'supporta7',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($supporta8Num,'supporta8',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($supporta11Num,'supporta11',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($supporta12Num,'supporta12',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>


                            <td><?php echo $this->getFormColumnFormat($totalNum,'total',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>

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
