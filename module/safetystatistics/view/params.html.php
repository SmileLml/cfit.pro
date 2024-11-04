<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .tableWrap {
        overflow: auto;
    }
    .table td, .table th {
        padding: 8px 8px;
        line-height: 1.42857143;
        vertical-align: top;
        border-bottom: 1px solid #cbd0db;
        -webkit-transition: background .2s cubic-bezier(.175,.885,.32,1);
        -o-transition: background .2s cubic-bezier(.175,.885,.32,1);
        transition: background .2s cubic-bezier(.175,.885,.32,1);
    }
    table>tbody>tr>th {
        width: 100px;
        font-weight: 700;
        text-align: right;
    }
    .table tbody>tr>td, .table tbody>tr>th {
        vertical-align: middle;
    }
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        cursor: not-allowed;
        background-color: #f5f5f5;
        width: 200px;
    }
</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="page-title">
            <span class="text"><?php echo $lang->safetystatisstics->title; ?></span>
        </div>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='side-col' id='sidebar'><?php include 'blockreportlist.html.php'; ?></div>
    <div class='main-col'>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <div class='panel'>
                    <div class='main-header'><h2><?php echo $lang->safetystatisstics->tableTitle[0]; ?></h2></div>
                    <div data-ride='table' class="tableWrap">
                        <table class='table'>
                            <tbody>
                            <td><?php echo html::input("", '', "class='form-control'"); ?></td>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class='panel'>
                    <div class='main-header'><h2><?php echo $lang->safetystatisstics->tableTitle[1]; ?></h2></div>
                    <div data-ride='table' class="tableWrap">
                        <table class='table'>
                            <?php foreach ($lang->safetystatistics->targetTwoList as $key => $val): ?>
                                <tr>
                                    <th colspan="4" style="border-bottom: 0;"><?php echo $val; ?></th>
                                    <?php var_dump($targetTwoList);die; ?>
                                    <td colspan="4" style="border-bottom: 0; width: 50px"><?php echo html::input("$key", $targetTwoList[$key]['weightNum'] ?? '', "class='form-control' disabled=true"); ?></td>
                                </tr>
                                <tr>
                                    <?php foreach ($lang->safetystatistics->targetThreeList as $k => $v): ?>
                                        <td style="text-align: right;"><?php echo $v; ?></td>
                                        <td><?php echo html::input("target[$key][$k]", $targetTwoList[$key]['child'][$k] ?? '', "class='form-control'"); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
                <div class='panel'>
                    <div class='main-header'><h2><?php echo $lang->safetystatisstics->tableTitle[2]; ?></h2></div>
                    <div data-ride='table' class="tableWrap">
                        <table class='table'>
                            <tbody>
                            <?php foreach ($lang->safetystatistics->calibration as $key => $value):?>
                            <td style="text-align: right;border-bottom: 0; width: 150px"><?php echo $value; ?></td>
                            <td style="display:block; border-bottom: 0; width: 150px"><?php echo html::input("calibration[$key]", '', "class='form-control'"); ?></td>
                            <?php endforeach;?>
                            <tr>
                                <td class='form-actions text-center' colspan='12' style="height: 120px"><?php echo html::submitButton();?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>

