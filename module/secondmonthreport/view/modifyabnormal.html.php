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
        <div class="page-title"><h4><?php echo $title; ?></h4></div>
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
                            <th class='w-120px'>一级变更</th>
                            <th class='w-120px'>二级变更</th>
                            <th class='w-120px'>三级变更</th>
                            <th class='w-120px'>合计</th>
                            <th class='w-120px'>实施异常单数</th>
                            <th class='w-120px'>实施异常率</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($quarterReport as $key => $report): ?>
                            <tr>
                                <td><?php echo sprintf(zget($this->lang->secondmonthreport->quarterDateNameList[$staticType], $key), ceil($curWholeReport->month / 3)); ?></td>
                                <td><?php echo $report->first; ?></td>
                                <td><?php echo $report->second; ?></td>
                                <td><?php echo $report->third; ?></td>
                                <td><?php echo $report->total; ?></td>
                                <td><?php echo $report->exceed; ?></td>
                                <td><?php echo $report->exceedRate; ?></td>
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
                                        $class = common::hasPriv('secondmonthreport', 'modifyabnormalExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'modifyabnormalExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'modifyabnormalExport') ? $this->getExportFormUrl('modifyabnormalExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'modifyabnormalExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'modifyabnormalExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'modifyabnormalExport') ? $this->getExportFormUrl('modifyabnormalExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                            <th class='w-120px'><?php echo $lang->secondmonthreport->abnormalNum; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->modifyCountNum; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->banormalrate; ?></th>





                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $abnormalNum = 0;
                        $modifyCountNum = 0;

                        $banormalrateNum = 0;

                        foreach ($detailReports as $key => $detailReport) { ?>
                            <tr>
                                <td><?php echo  zget($depts, $key); ?></td>
                                <td><?php
                                    echo $this->getFormColumnFormat($detailReport->detail->abnormalNum,'abnormalNum',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $abnormalNum += $detailReport->detail->abnormalNum; ?></td>
                                <td><?php
                                    echo $this->getFormColumnFormat($detailReport->detail->modifyCountNum,'modifyCountNum',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $modifyCountNum += $detailReport->detail->modifyCountNum; ?></td>
                                <td><?php echo $detailReport->detail->banormalrate.'%'; ?></td>


                            </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php echo $this->getFormColumnFormat($abnormalNum,'abnormalNum',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($modifyCountNum,'modifyCountNum',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>

                            <td><?php if($modifyCountNum > 0){ echo sprintf("%0.2f",($abnormalNum/$modifyCountNum)*100);}else{ echo '0.00';} ?>%</td>



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
