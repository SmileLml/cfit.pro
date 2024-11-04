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
            $types = [
                    'consult' => $lang->secondmonthreport->secondorderTypeList['consult'],
                    'plan' => $lang->secondmonthreport->secondorderTypeList['plan'],
                    'test' => $lang->secondmonthreport->secondorderTypeList['test'],
                    'script' => $lang->secondmonthreport->secondorderTypeList['script'],
                    'support' => $lang->secondmonthreport->secondorderTypeList['support'],
                    'other' => $lang->secondmonthreport->secondorderTypeList['other'],
                    'sum' => '合计',
            ];
        ?>
        <div class="cell" >
            <form class='main-table'  method='post' data-nested='true' >

                    <table class='table table-fixed has-sort-head table-bordered myformtable'>
                        <thead>
                        <tr>
                            <th class='w-120px'>统计周期</th>
                            <?php foreach ($types as $key => $type): ?>
                            <th class='w-120px'><?php echo $type; ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($quarterReport as $key => $report): ?>
                            <tr>
                                <td><?php echo sprintf(zget($this->lang->secondmonthreport->quarterDateNameList[$staticType], $key), ceil($curWholeReport->month / 3)); ?></td>
                                <?php foreach ($types as $k => $type): ?>
                                    <td><?php echo $report->$k; ?></td>
                                <?php endforeach; ?>
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
                                        $class = common::hasPriv('secondmonthreport', 'secondorderclassExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'secondorderclassExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'secondorderclassExport') ? $this->getExportFormUrl('secondorderclassExport', $searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
                                        echo "<li {$class}>" . html::a($link, $lang->secondmonthreport->export, '', $misc) . '</li>';
                                        if(isset($curWholeReport->fileUrl2) && $curWholeReport->fileUrl2){
                                            echo "<li >" . html::a($curWholeReport->fileUrl2, $lang->secondmonthreport->exportSnapshot, '', '') . '</li>';
                                        }
                                        if(isset($curWholeReport->fileUrl) && $curWholeReport->fileUrl){
                                            echo "<li >" . html::a($curWholeReport->fileUrl, $lang->secondmonthreport->fileTip, '', '') . '</li>';
                                        }

                                    }else{
                                        $class = common::hasPriv('secondmonthreport', 'secondorderclassExport') ? '' : 'class=disabled';
                                        $misc  = common::hasPriv('secondmonthreport', 'secondorderclassExport') ? "data-toggle='modal' data-type='iframe' class='export'" : 'class=disabled';
                                        $link  = common::hasPriv('secondmonthreport', 'secondorderclassExport') ? $this->getExportFormUrl('secondorderclassExport', $searchtype,0,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData) : '#';
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
                            <th class='w-120px'><?php echo $lang->secondmonthreport->secondorderTypeList['consult']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->secondorderTypeList['plan']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->secondorderTypeList['test']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->secondorderTypeList['script']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->secondorderTypeList['support']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->secondorderTypeList['other']; ?></th>
                            <th class='w-120px'><?php echo $lang->secondmonthreport->total; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $consultNum = 0;
                        $testNum = 0;
                        $scriptNum = 0;
                        $planNum = 0;
                        $supportNum = 0;
                        $otherNum = 0;
                        $totalNum = 0;
                        foreach ($detailReports as $key => $detailReport) { ?>
                        <tr>
                            <td><?php echo  zget($depts, $key); ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->consult,'consult',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $consultNum += $detailReport->detail->consult; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->plan,'plan',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $planNum += $detailReport->detail->plan; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->test,'test',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $testNum += $detailReport->detail->test; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->script,'script',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $scriptNum += $detailReport->detail->script; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->support,'support',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $supportNum += $detailReport->detail->support; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->other,'other',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                                $otherNum += $detailReport->detail->other; ?></td>
                            <td><?php
                                echo $this->getFormColumnFormat($detailReport->detail->total,'total',$searchtype,$wholeID,$detailReport->deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist);
                            $totalNum += $detailReport->detail->total; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $this->lang->secondmonthreport->total; ?></td>
                            <td><?php echo $this->getFormColumnFormat($consultNum,'consult',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($testNum,'test',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($scriptNum,'script',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($planNum,'plan',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($supportNum,'support',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
                            <td><?php echo $this->getFormColumnFormat($otherNum,'other',$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist); ?></td>
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
