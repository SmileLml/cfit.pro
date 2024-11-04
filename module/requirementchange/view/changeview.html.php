<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    /*.detail-title-bold{font-size:14px;lisne-height:20px;font-weight:bold;}*/
    .desc>div{float:left}
    .detail-content{margin-top: 0px !important;}
    .detail-title{width:130px;text-align: left}
    .main-change>div{margin-bottom: 10px}
</style>
</div>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px; max-height: 500px;">
    <div class="main-header">
        <div class="page-title">
            <span class="text" title='<?php echo $this->lang->requirementchange->common; ?>'><?php echo $this->lang->requirementchange->common; ?></span>
        </div>
    </div>
    <div id="" class="main-row">
        <div class="main-col col-8 main-change">
            <div class="desc clearfix">
                <div class="detail-title"><?php echo $lang->requirementchange->isType; ?>：</div>
                <div class="detail-content article-content"><?php echo $lang->requirementchange->isTypeArray[$info->missedDemolition]?></div>
            </div>
<!--            <div class="desc clearfix">-->
<!--                <div class="detail-title">--><?php //echo $lang->requirementchange->deptManager; ?><!--：</div>-->
<!--                <div class="detail-content article-content">--><?php //echo $info->generalManager;?><!--</div>-->
<!---->
<!--            </div>-->
            <div>
                <div class="detail-title"><?php echo $lang->requirementchange->changeBackground; ?>：</div>
                <div class="detail-content article-content" style="text-indent: 2em"><?php echo $info->changeBackground;?></div>
            </div>
            <div>
                <div class="detail-title"><?php echo $lang->requirementchange->changeConfirm; ?>：</div>
                <div class="detail-content article-content" style="text-indent: 2em"><?php echo $info->circumstance;?></div>
            </div>
            <div>
                <div class="detail-title"><?php echo $lang->requirementchange->changeCotent; ?>：</div>
                <div class="detail-content article-content" style="text-indent: 2em"><?php echo $info->changeContent;?></div>
            </div>
            <div>
                <div class="detail-title"><?php echo $lang->requirementchange->includeAssign; ?>：</div>
                <div class="detail-content article-content">
                    <table class="table ops" style="text-align: center">
                        <tr>
                            <th class="w-100px" style="text-align: center"><?php echo $lang->requirementchange->sortAB; ?></th>
                            <th class="w-200px" style="text-align: center"><?php echo $lang->requirementchange->requirementTitle; ?></th>
                            <th class="w-200px" style="text-align: center"><?php echo $lang->requirementchange->QzRequirementCode; ?></th>
                        </tr>
                        <?php $num = 1;
                        foreach ($requirements as $val): ?>
                            <tr>
                                <td><?php echo $val->code; ?></td>
                                <td>

                                    <a class="iframe" data-width="900" href='<?php echo $this->createLink('requirementchange', 'assigndetail', "changeID=$val->id",'',true)?>'><?php echo htmlspecialchars_decode($val->name); ?></a>
                                </td>
                                <td>
                                    <?php echo $val->entriesCode; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
