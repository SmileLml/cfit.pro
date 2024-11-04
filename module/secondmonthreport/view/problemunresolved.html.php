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
                                        $class = common::hasPriv('secondmonthreport', 'problemUnresolvedExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'problemUnresolvedExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'problemUnresolvedExport') ? $this->getExportFormUrl('problemUnresolvedExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)  : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'problemUnresolvedExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'problemUnresolvedExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'problemUnresolvedExport') ? $this->getExportFormUrl('problemUnresolvedExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                            <th  rowspan="2"><?php echo $lang->secondmonthreport->deptName; ?></th>
                            <th  colspan="4"><?php echo $lang->secondmonthreport->problemUnresolvedNotice;?></th>

                        </tr>
                        <tr>
<!--                            <th class='w-120px' rowspan="2">--><?php //echo $lang->secondmonthreport->deptName; ?><!--</th>-->
                            <th class='w-120px'><?php echo $lang->secondmonthreport->letwoMonth; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->lesixMonth; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->letwelveMonth; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->gttwelveMonth; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $letwoMonth = 0;
                        $lesixMonth = 0;
                        $letwelveMonth = 0;
                        $gttwelveMonth = 0;
                        foreach ($detailReports as $key => $detailReport) {
                            $detailReport->deptID = 0 < $detailReport->deptID ? $detailReport->deptID : -1;
                            ?>
                        <tr>
                            <td><?php echo zget($depts, $detailReport->deptID); ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->letwoMonth,'letwoMonth',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $letwoMonth += $detailReport->detail->letwoMonth; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->lesixMonth,'lesixMonth',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $lesixMonth += $detailReport->detail->lesixMonth; ?></td>
                            <td><?php if(isset($detailReport->detail->letwelveMonth)){
                                    echo $this->getFormColumnFormat($detailReport->detail->letwelveMonth,'letwelveMonth',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $letwelveMonth += $detailReport->detail->letwelveMonth; }else{echo '0';} ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->gttwelveMonth,'gttwelveMonth',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $gttwelveMonth += $detailReport->detail->gttwelveMonth; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($letwoMonth,'letwoMonth',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($lesixMonth,'lesixMonth',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($letwelveMonth,'letwelveMonth',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($gttwelveMonth,'gttwelveMonth',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);?></td>
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
