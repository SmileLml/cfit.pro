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

                    <div  class="clearfix" >
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
                                    if($searchtype == 'history'){
                                    $class = common::hasPriv('secondmonthreport', 'problemExceedExport') ? '' : 'class=disabled';
                                    $misc  = common::hasPriv('secondmonthreport', 'problemExceedExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                    $link  = common::hasPriv('secondmonthreport', 'problemExceedExport') ? $this->getExportFormUrl('problemExceedExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                    echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'problemExceedExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'problemExceedExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'problemExceedExport') ? $this->getExportFormUrl('problemExceedExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                    $alreadySolve = 0;
                    $waitSolve    = 0;
                    $sum          = 0;
                    $total        = 0;
                    if ($detailReports) {
                        ?>
                    <table class='table table-fixed has-sort-head table-bordered myformtable'>
                        <thead>
                        <tr>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->deptName; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->exceed->alreadySolve; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->exceed->waitSolve; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->exceed->sum; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->exceed->total; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->exceed->exceedRate; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($detailReports as $key => $detailReport) {
                            ?>
                        <tr>
                            <td><?php echo zget($depts, $detailReport->deptID); ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->alreadySolve,'alreadySolve',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $alreadySolve += $detailReport->detail->alreadySolve; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->waitSolve,'waitSolve',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $waitSolve += $detailReport->detail->waitSolve; ?></td>
                            <td><?php echo $this->getFormColumnFormat($detailReport->detail->sum,'sum',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <?php $sum += $detailReport->detail->sum; ?>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->total,'total',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $total += $detailReport->detail->total; ?></td>
                            <td><?php echo $detailReport->detail->exceedRate . '%'; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php echo $this->getFormColumnFormat($alreadySolve,'alreadySolve',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($waitSolve,'waitSolve',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);; ?></td>
                            <td><?php echo $this->getFormColumnFormat($sum,'sum',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($total,'total',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo ($total > 0 ? number_format($sum / $total * 100, 2) : '0.00') . '%'; ?></td>
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
