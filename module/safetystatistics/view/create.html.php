<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .panel>.panel-heading{color: #333;background-color: #f5f5f5;border-color: #ddd;}
    .panel{border-color: #ddd;}
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="page-title">
            <span class="text"><?php echo $lang->safetystatisstics->title; ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class='side-col' id='sidebar'><?php include 'blockreportlist.html.php'; ?></div>
    <div class="center-block">
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
<!--            <div>-->
<!--                <div class="panel">-->
<!--                    <div class="panel-heading">-->
<!--                        --><?php //echo $lang->safetystatisstics->tableTitle[0]; ?>
<!--                    </div>-->
<!--                    <div class="panel-body">-->
<!--                    <table class="table table-form table-bordered">-->
<!--                        <tbody>-->
<!--                        <td>--><?php //echo html::textarea("", '', "class='form-control'"); ?><!--</td>-->
<!--                        <td>--><?php //echo html::textarea("", '', "class='form-control'"); ?><!--</td>-->
<!--                        <td>--><?php //echo html::textarea("", '', "class='form-control'"); ?><!--</td>-->
<!--                        </tbody>-->
<!--                    </table>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
            <div>
                <div class="panel">
                    <div class="panel-heading">
                        <h4><?php echo $lang->safetystatisstics->tableTitle[1]; ?></h4>
                    </div>
                    <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <?php foreach ($lang->safetystatistics->targetTwoList as $key => $val): ?>
                            <tr>
                                <th colspan="4" style="width: auto"><?php echo $val; ?></th>
                                <td><?php echo html::input("$key", $targetTwoList[$key]['weightNum'] ?? '', "class='form-control' disabled=true"); ?></td>
                            </tr>
                            <tr>
                                <?php foreach ($lang->safetystatistics->targetThreeList as $k => $v): ?>
                                    <td style="text-align: right;"><?php echo $v; ?></td>
                                    <td><?php echo html::input("target[$key][$k]", $targetTwoList[$key]['child'][$k] ?? '', "class='form-control'"); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <div>
                <div class="panel">
                    <div class="panel-heading">
                        <h4><?php echo $lang->safetystatisstics->tableTitle[2]; ?></h4>
                    </div>
                    <div class="panel-body">
                    <table class="table table-form">
                        <tbody>
                        <?php foreach ($lang->safetystatistics->calibration as $key => $value):?>
                            <td width="80px"><?php echo $value; ?></td>
                            <td style="text-align: left"><?php echo html::input("calibration[$key]", $calibrationList[$key] ?? '', "class='form-control'"); ?></td>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <table class="table table-form">
                <tbody>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    $(document).ready(function()
    {
        if(type == 6){
            $('.inputNums').removeClass('hidden');
        }else{
            $('.inputNums').addClass('hidden');
        }
    });
</script>
<?php include '../../common/view/footer.html.php';?>
