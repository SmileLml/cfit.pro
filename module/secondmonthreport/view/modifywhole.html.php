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
                                        $class = common::hasPriv('secondmonthreport', 'modifywholeExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'modifywholeExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'modifywholeExport') ? $this->getExportFormUrl('modifywholeExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }
                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'modifywholeExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'modifywholeExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'modifywholeExport') ? $this->getExportFormUrl('modifywholeExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                            <th class='w-120px'><?php echo $lang->secondmonthreport->modifyShowmodeList['modifyandcncca1']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->modifyShowmodeList['modifyandcncca2']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->modifyShowmodeList['modifyandcncca3']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->modifyShowmodeList['modifyandcncca4']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->total; ?></th>




                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $modifyandcncca1Num = 0;
                        $modifyandcncca2Num = 0;
                        $modifyandcncca3Num = 0;
                        $modifyandcncca4Num = 0;
                        $banormalrateNum = 0;
                        $totalNum = 0;
                        foreach ($detailReports as $key => $detailReport) { ?>
                        <tr>
                            <td><?php echo  zget($depts, $key); ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->modifyandcncca1,'modifyandcncca1',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $modifyandcncca1Num += $detailReport->detail->modifyandcncca1; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->modifyandcncca2,'modifyandcncca2',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $modifyandcncca2Num += $detailReport->detail->modifyandcncca2; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->modifyandcncca3,'modifyandcncca3',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $modifyandcncca3Num += $detailReport->detail->modifyandcncca3; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->modifyandcncca4,'modifyandcncca4',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $modifyandcncca4Num += $detailReport->detail->modifyandcncca4; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->total,'total',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $totalNum += $detailReport->detail->total; ?></td>



                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php
                            echo $this->getFormColumnFormat($modifyandcncca1Num,'modifyandcncca1',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($modifyandcncca2Num,'modifyandcncca2',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($modifyandcncca3Num,'modifyandcncca3',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($modifyandcncca4Num,'modifyandcncca4',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($totalNum,'total',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            ?></td>




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
