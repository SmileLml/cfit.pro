<?php include '../../common/view/header.html.php'; ?>
<style>.w-170px {
        width: 170px;
    }</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        foreach ($lang->projectplan->labelList as $label => $labelName) {
            $active = '';
            if($browseType == $label && $isSecondline == 0 && $shanghaipart == 0){ $active = 'btn-active-text';}
            echo html::a($this->createLink('projectplan', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
            $i++;
            if ($i >= 11) break;
        }

        $active = '';

        if($isSecondline == 1 && $shanghaipart == 0){ $active = 'btn-active-text';}
        echo html::a($this->createLink('projectplan', 'browse', "browseType=second&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&page=1&secondline=1"), '<span class="text">二线/部门项目</span>', '', "class='btn btn-link $active'");
        $active = '';
        if($shanghaipart == 1 && $isSecondline == 0){ $active = 'btn-active-text';}
/*        echo html::a($this->createLink('projectplan', 'browse', "browseType=all&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&page=1&secondline=0&shanghaipart=1"), '<span class="text">'.$lang->projectplan->shanghaipart.'</span>', '', "class='btn btn-link $active'");*/

        if ($i >= 11) {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach ($lang->projectplan->labelList as $label => $labelName) {
                $i++;
                if ($i <= 11) continue;
                $active = '';
                if($browseType == $label && $isSecondline == 0 ){ $active = 'btn-active-text';}
                echo '<li>' . html::a($this->createLink('projectplan', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
            }
            echo '</ul></div>';
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i
                    class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span
                        class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('projectplan', 'export') ? '' : "class=disabled";
                $misc = common::hasPriv('projectplan', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link = common::hasPriv('projectplan', 'export') ? $this->createLink('projectplan', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->projectplan->export, '', $misc) . "</li>";

                $class = common::hasPriv('projectplan', 'exportTemplate') ? '' : "class='disabled'";
                $link = common::hasPriv('projectplan', 'exportTemplate') ? $this->createLink('projectplan', 'exportTemplate') : '#';
                $misc = common::hasPriv('projectplan', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->projectplan->exportTemplate, '', $misc) . '</li>';

                $class = common::hasPriv('projectplan', 'exportHistory') ? '' : "class='disabled'";
                $link = common::hasPriv('projectplan', 'exportHistory') ? $this->createLink('projectplan', 'exportHistory') : '#';
                $misc = common::hasPriv('projectplan', 'exportHistory') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportHistory'" : "class='disabled'";
                echo "<li $class>" . html::a($link, $lang->projectplan->exportHistory, '', $misc) . '</li>';
                ?>
            </ul>
            <?php if (common::hasPriv('projectplan', 'import')) echo html::a($this->createLink('projectplan', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->projectplan->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'"); ?>
        </div>
        <?php if (common::hasPriv('projectplan', 'create')) echo html::a($this->createLink('projectplan', 'create',"secondLine=$isSecondline"), "<i class='icon-plus'></i> {$lang->projectplan->create}", '', "class='btn btn-primary'"); ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='projectplan'></div>
        <?php if (empty($plans)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='projectplanForm'   method='post' data-ride='table' data-nested='true'
                  data-checkable='true'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='projectplans'>
                    <thead>
                    <tr>
                        <th class='w-90px'><div class="checkbox-primary checkall" onclick="checkall()" title="<?php echo $lang->selectAll?>">
                                <label></label>
                            </div><?php common::printOrderLink('id', $orderBy, $vars, $lang->projectplan->id); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('planCode', $orderBy, $vars, $lang->projectplan->planCode); ?></th>
                        <th style="width: 180px"><?php common::printOrderLink('name', $orderBy, $vars, $lang->projectplan->name); ?></th>

                        <th class='w-80px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->projectplan->code); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('mark', $orderBy, $vars, $lang->projectplan->mark); ?></th>
                        <th class='w-70px'> <?php common::printOrderLink('year', $orderBy, $vars, $lang->projectplan->year); ?></th>
                        <th class='w-120px'><?php echo $lang->projectplan->bearDept; ?></th>
                        <th class='w-100px'><?php common::printOrderLink('begin', $orderBy, $vars, $lang->projectplan->begin); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->projectplan->end); ?></th>
<!--                        <th class='w-120px'>--><?php //echo $lang->projectplan->outsides; ?><!--</th>-->
                        <th class='w-120px'><?php echo $lang->projectplan->workload; ?></th>
                        <th class='w-180px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->projectplan->status); ?></th>
                        <th class='w-100px'><?php echo $lang->projectplan->insideStatus; ?></th>
                        <th class='w-100px'><?php echo $lang->projectplan->pending; ?></th>
                        <th class='text-center w-200px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($plans as $plan): ?>
                        <tr>
                            <td>
                                <?php
                                if($plan->status == 'yearreviewing' && $plan->reviewers && in_array($this->app->user->account,explode(',',$plan->reviewers))){
                                    echo html::checkbox('plans', array($plan->id => '')) . sprintf('%03d', $plan->id);
                                }else{
                                    echo html::checkbox('plans', array($plan->id => ''),'',"disabled readonly").sprintf('%03d', $plan->id);
                                }
                                ?>
                                <?php ?>
                            </td>
                            <td title='<?php echo $plan->planCode; ?>'><?php echo $plan->planCode; ?></td>
                            <td class='text-ellipsis' title='<?php echo $plan->name; ?>'><?php echo common::hasPriv('projectplan', 'view') ? html::a(inlink('view', "projectplanID=$plan->id"), $plan->name) : $plan->name; ?></td>

                            <td title='<?php echo $plan->code; ?>' class='text-ellipsis'><?php echo $plan->code; ?></td>
                            <td title='<?php echo $plan->mark; ?>' class='text-ellipsis'><?php echo $plan->mark; ?></td>
                            <td><?php echo $plan->year; ?></td>
                            <?php
                            $bearDeptTitle = '';
                            if (!empty($plan->bearDept)) {
                                foreach (explode(',', $plan->bearDept) as $bearDept) {
                                    if (!empty($bearDept)) $bearDeptTitle .= zget($depts, $bearDept, $bearDept) . ',';
                                }
                            }
                            $bearDeptTitle = trim($bearDeptTitle, ',');
                            ?>
                            <td title='<?php echo $bearDeptTitle; ?>' class='text-ellipsis'><?php echo $bearDeptTitle; ?></td>
                            <td><?php if (!helper::isZeroDate($plan->begin)) echo $plan->begin; ?></td>
                            <td><?php if (!helper::isZeroDate($plan->end)) echo $plan->end; ?></td>
                            <td><?php  echo $plan->workload; ?></td>
                            <!--<td class='text-ellipsis'
                                title=<?php
/*                            $outsides = [];
                            $outPlanIds = [];
                            if(!empty($plan->outsideProject))
                                $outPlanIds = explode(',',$plan->outsideProject);
                            foreach ($outPlanIds as $outPlanId)
                            {
                                if(empty($outPlanId)) continue;
                                $outsides[] = zget($outsidePlans, $outPlanId);
                            }
                            echo $outsidesNames = implode(',',$outsides);
                                    */?>>
                                <?php /* echo $outsidesNames; */?>
                            </td>-->
                            <?php
                            $projectplanstatusstr = '';
                           // if($plan->status==$lang->projectplan->statusEnglishList['yearpass'] && $plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['pending']){
                            if(in_array($plan->status,array($lang->projectplan->statusEnglishList['yearpass'],$lang->projectplan->statusEnglishList['start'])) && $plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['pending']){
                                $projectplanstatusstr = $lang->projectplan->changeing;
                            }else{
                                $projectplanstatusstr = zget($lang->projectplan->statusList, $plan->status, '');
                                if($plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['pass']){
                                    $projectplanstatusstr .= $lang->projectplan->changePass;
                                }else if($plan->changeStatus == $lang->projectplan->ChangestatusEnglishList['reject']){
                                    $projectplanstatusstr .= $lang->projectplan->changeReject;
                                }
                            }


                            ?>
                            <td class='text-ellipsis' title="<?php echo $projectplanstatusstr;?>">
                                <?php echo $projectplanstatusstr;?>
                            </td>
                            <td class='text-ellipsis'
                                title=<?php echo zget($lang->projectplan->insideStatusList, $plan->insideStatus, ''); ?>>
                                <?php echo zget($lang->projectplan->insideStatusList, $plan->insideStatus, ''); ?>
                            </td>
                            <?php
                            $reviewersTitle = '';
                            if (!empty($plan->reviewers)) {
                                foreach (explode(',', $plan->reviewers) as $reviewers) {
                                    if (!empty($reviewers)) $reviewersTitle .= zget($users, $reviewers, $reviewers) . ',';
                                }
                            }
                            $reviewersTitle = trim($reviewersTitle, ',');
                            ?>
                            <td title='<?php echo $reviewersTitle; ?>' class='text-ellipsis'>
                               <?php echo $reviewersTitle; ?>
                            </td>
                            <td class='c-actions'>
                                <?php
                                common::printIcon('projectplan', 'initProject', "projectplanID=$plan->id&creationID=$plan->creationID", $plan, 'list', 'file-text');

                                // 判断是否审批年度计划
                                if (in_array($plan->status, array('yearstart', 'yearwait', 'yearreviewing', 'yearreject'))) {
                                    common::printIcon('projectplan', 'yearReview', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true, '', $this->lang->projectplan->yearReview);
                                    common::printIcon('projectplan', 'yearReviewing', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->yearReviewing);

                                    echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->planChange . " '><i class='icon-common-feedback disabled icon-feedback'></i></button>\n";

                                } else if(in_array($plan->status, array('yearpass','start'))) {
                                    if ($plan->changeStatus != 'pending') {
                                        common::printIcon('projectplan', 'submit', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true);
                                        echo "<button type='button' class='disabled btn' title='".$this->lang->projectplan->review."' style='pointer-events: unset;'><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
//                                        echo "<button type='button' class='disabled btn' title='".$this->lang->projectplan->submit."' style='pointer-events: unset;'><i class='icon-common-start disabled icon-start'></i></button>\n";
                                        common::printIcon('projectplan', 'planChange', "id=$plan->id", $plan, 'list', 'feedback', '', '', '', $lang->projectplan->planChange);
                                    } else {
                                        if(strpos($plan->reviewers,$this->app->user->account) !== false){
                                            common::printIcon('projectplan', 'changeReview', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $this->lang->projectplan->changeReview);
                                        }else{
                                            echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->changeReview . " '><i class='icon-common-glasses disabled icon-glasses'></i></button>\n";
                                        }
                                        common::printIcon('projectplan', 'submit', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true);
                                        echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->planChange . " '><i class='icon-common-feedback disabled icon-feedback'></i></button>\n";
                                    }
                                }else{
                                    common::printIcon('projectplan', 'submit', "projectplanID=$plan->id", $plan, 'list', 'start', '', 'iframe', true);
                                    common::printIcon('projectplan', 'review', "projectplanID=$plan->id", $plan, 'list', 'glasses', '', 'iframe', true, '', $plan->reviewStage == 2 ? $this->lang->projectplan->involved : $this->lang->projectplan->review);
                                    echo "<button type='button' class='disabled btn' title=' " . $lang->projectplan->planChange . " '><i class='icon-common-feedback disabled icon-feedback'></i></button>\n";

                                }
                                if ($plan->status == 'pass') {
                                    common::printIcon('projectplan', 'exec', "projectplanID=$plan->id", $plan, 'list', 'run', '', 'iframe', true);
                                } else {
                                    common::printIcon('projectplan', 'edit', "projectplanID=$plan->id", $plan, 'list');
                                }
                                common::printIcon('projectplan', 'execEdit', "id=$plan->id", $plan, 'list','change','','','','',$lang->projectplan->execEdit);
                                common::printIcon('projectplan', 'delete', "projectplanID=$plan->id", $plan, 'list', 'trash', 'hiddenwin');
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <div class="checkbox-primary checkall" onclick="checkall()"><label><?php echo $lang->selectAll?></label></div>
                    <div class="table-actions btn-toolbar"><a id="batchedit" onclick="setbatchediturl()"  class="btn iframe" title="批量审批年度计划" data-app="platform">批量审批</a></div>
                    <?php
                        $pager->show('right', 'pagerjs');
                        ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
<script>
    /* $(".check-all").click(function (){
         $("#projectplans tbody").attr("checked",true)
     })*/
    cleardischeckbox();
function cleardischeckbox(){

    $("#projectplans tbody input[name='plans[]']").each(function (){
        var isdisabled = $(this).attr("disabled");

        if(isdisabled){

            if($(this).is(":checked")){
                $(this).removeAttr("checked");
            }

        }
    });

}

        function checkall(){

            var checkflag = false;
            var hascheck = $(".checkall").eq(0).hasClass("checked");

            if(hascheck){
                $("#projectplans tbody input[name='plans[]']").each(function (){
                    var isdisabled = $(this).attr("disabled");

                    if(!isdisabled){
                        $(this).removeAttr("checked")

                    }else{
                        $(this).removeAttr("checked")
                    }
                });
                $(".checkall").removeClass("checked")
                $("#projectplanForm").removeClass("has-row-checked")
            }else{
                $("#projectplans tbody input[name='plans[]']").each(function (){
                    var isdisabled = $(this).attr("disabled");

                    if(!isdisabled){
                        $(this).attr("checked",true)
                        checkflag = true
                    }else{
                        $(this).removeAttr("checked")
                    }
                });
                if(checkflag){
                    $(".checkall").addClass("checked")
                    $("#projectplanForm").addClass("has-row-checked")
                }

            }
           /* return;
            $("#projectplans tbody input[name='plans[]']").each(function (){
                var isdisabled = $(this).attr("disabled");

                if(!isdisabled){

                    if($(this).is(":checked")){
                        $(this).removeAttr("checked")
                        // $(this).prop("checked",false)
                    }else{
                        $(this).attr("checked",true)
                        checkflag = true;
                    }

                }else{
                    $(this).removeAttr("checked")
                }
            });

            if(checkflag){
                $(".checkall").addClass("checked")
                $("#projectplanForm").addClass("has-row-checked")

                // $(".table-actions").css({visibility:"visible",opacity:1});
            }else{
                $(".checkall").removeClass("checked")
                $("#projectplanForm").removeClass("has-row-checked")
                // $(".table-actions").css({visibility:"hidden",opacity:0});
            }*/
        }
        $("#projectplans tbody input[name='plans[]']").change(
            function (){
                if(!($(this).is(":checked"))){
                    $(".checkall").removeClass("checked")
                }
               /* var checkflag = true;

                $("#projectplans tbody input[name='plans[]']:checked").each(function (){
                    checkflag=false;
                });
                if(checkflag){
                    $(".checkall").removeClass("checked")
                }else{
                    $(".checkall").addClass("checked")
                }*/
            }
        )
        function setbatchediturl(){
            var planidArr = [];
            $("#projectplans tbody input[name='plans[]']:checked").each(function (){
                planidArr.push($(this).val());
            });
            if(planidArr.length == 0){
                alert("请选择要审批的年度计划");

                return false;
            }


            planidIdstr = planidArr.join(",");

            $("#batchedit").attr("href",createLink("projectplan","yearBatchReviewing","planID="+planidIdstr)+"?onlybody=yes")
        }
    /* $("#batchedit").click(function (

     ))*/
</script>
