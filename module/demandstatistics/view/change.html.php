<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php include '../../opinion/lang/zh-cn.php'; ?>
<?php include '../../common/view/datatable.fix.html.php';?>
<style>
    .tableWrap{
        overflow: auto;
    }
     .maxWidthTh{max-width:200px !important;}
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="page-title">
            <span class="text">全生命周期统计表</span>
        </div>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='side-col' id='sidebar'>
        <?php
        include 'blockreportlist.html.php';
        $this->app->loadLang('opinion');
        $this->app->loadConfig('opinion');
        $this->loadModel('requirement');
        $this->app->loadConfig('requirement');
        $this->app->loadLang('requirement');
        $this->loadModel('demand');
        $this->app->loadConfig('demand');
        $this->app->loadLang('demand');
        ?>

    </div>
    <div class='main-col'>
        <div class="cell">
            <div class="with-padding">
                <div class="table-row">
                    <form method="post">
                        <div class="col-md-2" style="width:250px;">
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo '所属产品'; ?></span>
                                <?php echo html::select('product', $productList, $product, "class='form-control chosen'"); ?>
                            </div>
                        </div>
                        <div class="col-md-2" style="width:250px;">
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo '所属项目'; ?></span>
                                <?php echo html::select('project', $projectList, $project, "class='form-control chosen'"); ?>
                            </div>
                        </div>
                        <div class="col-md-2" style="width:250px;">
                            <div class="input-group">
                                <span class='input-group-addon'><?php echo $lang->demandstatistics->startDate; ?></span>
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('startDate', $startDate, "class='form-control form-date'"); ?></div>
                            </div>
                        </div>
                        <div class="col-md-2" style="width:250px;">
                            <div class="input-group">
                                <span class='input-group-addon'><?php echo $lang->demandstatistics->endDate; ?></span>
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('endDate', $endDate, "class='form-control form-date'"); ?></div>
                            </div>
                        </div>
                        <div class='col-md-2'>
                            <?php echo html::submitButton($lang->searchAB, '', 'btn btn-primary'); ?>
                            <?php echo html::commonButton('重置',"onclick='resetClick(this)'",'btn btn-primary'); ?>
                        </div>
                    </form>
                    <?php if (common::hasPriv('demandstatistics', 'exportChange')): ?>
                        <div class='col-md-2'>
                            <?php
                            $startDate = strtotime($startDate);
                            $endDate = strtotime($endDate);
                            echo html::a($this->createLink('demandstatistics', 'exportChange', "startDate=$startDate&endDate=$endDate&product=$product&project=$project"), "<i class='icon-push'></i> {$lang->export}", '', "data-toggle='modal' data-type='iframe' class='btn btn-primary pull-right'", '');
                            ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->liftCycle; ?></div>
                <div class="detail-content article-content">
                    <table class="table changeInfo">
                        <tr>
                            <th class="w-300px maxWidthTh"><?php echo $this->lang->requirement->commonOpinion; ?></th>
                            <th class="w-300px maxWidthTh"><?php echo $this->lang->requirement->common; ?></th>
                            <th class="w-300px maxWidthTh"><?php echo $this->lang->requirement->commonDemand; ?></th>
                            <th class="w-150px maxWidthTh"><?php echo $this->lang->demandstatistics->requirementApp; ?></th>
                            <th class="w-150px maxWidthTh"><?php echo $this->lang->demandstatistics->demandProduct; ?></th>
                            <th class="w-150px maxWidthTh"><?php echo $this->lang->demandstatistics->demandProductPlan; ?></th>
                            <th class="w-150px maxWidthTh"><?php echo $this->lang->demandstatistics->demandFixType; ?></th>
                            <th class="w-150px maxWidthTh"><?php echo $this->lang->demandstatistics->demandProject; ?></th>
                            <th class="w-200px maxWidthTh"><?php echo $this->lang->demandstatistics->opinionStatus; ?></th>
                            <th class="w-200px maxWidthTh"><?php echo $this->lang->demandstatistics->requirementStatus; ?></th>
                            <th class="w-200px maxWidthTh"><?php echo $this->lang->demandstatistics->demandStatus; ?></th>
                        </tr>
                        <?php foreach ($opinionInfo as $lifeOpinionInfo){
                            echo "<tr>";
                            if(!$lifeOpinionInfo['requirements']){

                                echo "<td class='maxWidthTh'>".$lifeOpinionInfo['opinionCode'].'('.$lifeOpinionInfo['opinionName'].')'."</td>
                                <td class='maxWidthTh'></td>
                                <td class='maxWidthTh'></td>
                                <td class='maxWidthTh'></td>
                                <td class='maxWidthTh'></td>
                                <td class='maxWidthTh'></td>
                                <td class='maxWidthTh'></td>
                                <td class='maxWidthTh'></td>
                                <td class='maxWidthTh'></td>
                                <td class='maxWidthTh'></td>
                                <td class='maxWidthTh'></td>
                                </tr>";
                                continue;
                            }
                            echo "<td class='maxWidthTh' rowspan=\"{$lifeOpinionInfo['countAll']}\">".$lifeOpinionInfo['opinionCode'].'('.$lifeOpinionInfo['opinionName'].')'."</td>";
                            $requirementNum = 0;
                            foreach ($lifeOpinionInfo['requirements'] as $requirement){

                                if($requirementNum > 0){
                                    echo "<tr>";
                                }
                                $requirementNum++;
                                echo "<td class='maxWidthTh' rowspan=\"{$requirement['requirementCount']}\">
                                             {$requirement['requirementCode']}({$requirement['requirementName']})
     
                        </td>";
                                if(!$requirement['demands']){
                                    echo "<td class='maxWidthTh'></td>";
                                    echo "<td class='maxWidthTh'></td>";
                                    echo "<td class='maxWidthTh'></td>";
                                    echo "<td class='maxWidthTh'></td>";
                                    echo "<td class='maxWidthTh'></td>";
                                    echo "<td class='maxWidthTh'></td>";
                                    echo "<td class='maxWidthTh'>".$lifeOpinionInfo['opinionStatus']."</td>";
                                    echo "<td class='maxWidthTh'>".$requirement['requirementStatus']."</td>";
                                    echo "<td class='maxWidthTh'></td>";
                                    echo "</tr>";
                                    continue;

                                }
                                $demandNum = 0;
                                foreach ($requirement['demands'] as $demand){

                                    if($demandNum > 0){
                                        echo "<tr>";
                                    }
                                    $demandNum++;
                                    ?>

                                    <td class='maxWidthTh'>
                                        <?php
                                        echo $demand['demandCode'].'('.$demand['demandName'].')';
                                        ?>
                                    </td>
                                    <td class='maxWidthTh'><?php
                                        echo $demand['demandApp'];
                                        ?>
                                    </td>
                                    <td class='maxWidthTh'><?php
                                        echo $demand['demandProduct'];
                                        ?>
                                    </td>
                                    <td class='maxWidthTh'><?php
                                        echo $demand['demandProductPlan'];
                                        ?>
                                    </td>
                                    <td class='maxWidthTh'><?php
                                        echo $demand['demandFixType'];
                                        ?>
                                    </td>
                                    <td class='maxWidthTh'><?php
                                        echo $demand['demandProject'];
                                        ?>
                                    </td>
                                    <td class='maxWidthTh'><?php
                                        echo $lifeOpinionInfo['opinionStatus'];
                                        ?>
                                    </td>
                                    <td class='maxWidthTh'><?php
                                        echo $requirement['requirementStatus'];
                                        ?>
                                    </td>
                                    <td class='maxWidthTh'><?php
                                        echo $demand['demandStatus'];
                                        ?>
                                    </td>
                                    <?php
                                }

                            }
                            ?>
                            </tr>


                        <?php } ?>

                    </table>
                </div>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs');?>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<script>
    $("form").submit(function(event){
        var start = $('#startDate').val();
        var end = $('#endDate').val();

        if(end != '' && start > end){
            js:alert('开始日期不能大于结束日期！');
            return false;
        }
    })
    function resetClick(obj)
    {
        window.location.href = createLink('demandstatistics', 'change');
    }
</script>
