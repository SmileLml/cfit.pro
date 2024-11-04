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
            <div class="cell" style="margin-top:30px;">
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
                                        $class = common::hasPriv('secondmonthreport', 'demandunrealizedExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'demandunrealizedExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'demandunrealizedExport') ? $this->getExportFormUrl('demandunrealizedExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)  : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'demandunrealizedExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'demandunrealizedExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'demandunrealizedExport') ? $this->getExportFormUrl('demandunrealizedExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                            <th rowspan="2"><?php echo $lang->secondmonthreport->deptName; ?></th>
                            <th colspan="4"><?php echo $lang->secondmonthreport->demandunrealizedNotice; ?></th>

                        </tr>
                        <tr>

                            <th class='w-120px'><?php echo $lang->secondmonthreport->demandletwoMonth; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->demandlesixMonth; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->demandletwelveMonth; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->demandgttwelveMonth; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $demandletwoMonth = 0;
                        $demandlesixMonth = 0;
                        $demandletwelveMonth = 0;
                        $demandgttwelveMonth = 0;
                        foreach ($detailReports as $key => $detailReport) {
                            $detailReport->deptID = 0 < $detailReport->deptID ? $detailReport->deptID : -1;
                            ?>
                        <tr>
                            <td><?php echo zget($depts, $detailReport->deptID); ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->demandletwoMonth,'demandletwoMonth',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $demandletwoMonth += $detailReport->detail->demandletwoMonth; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->demandlesixMonth,'demandlesixMonth',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $demandlesixMonth += $detailReport->detail->demandlesixMonth; ?></td>
                            <td><?php if(isset($detailReport->detail->demandletwelveMonth)){
                                    echo $this->getFormColumnFormat($detailReport->detail->demandletwelveMonth,'demandletwelveMonth',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                    $demandletwelveMonth += $detailReport->detail->demandletwelveMonth; }else{echo '0';} ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->demandgttwelveMonth,'demandgttwelveMonth',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $demandgttwelveMonth += $detailReport->detail->demandgttwelveMonth; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($demandletwoMonth,'demandletwoMonth',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($demandlesixMonth,'demandlesixMonth',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($demandletwelveMonth,'demandletwelveMonth',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($demandgttwelveMonth,'demandgttwelveMonth',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
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
