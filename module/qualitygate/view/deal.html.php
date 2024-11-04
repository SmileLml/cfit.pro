<?php
/**
 * The complete file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     task
 * @version     $Id: complete.html.php 935 2010-07-06 07:49:24Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php
include '../../common/view/header.html.php';
include '../../common/view/kindeditor.html.php';
?>
<style>
    table {
        border-collapse: separate; /* 必须设置为 separate 才能应用 border-spacing */
        border-spacing: 10px 15px; /* 第一个值是水平间距，第二个值是垂直间距 */
    }
    .warning {
        color: #ee5050;
        display: none;
    }
    tbody>tr>td>a {
        line-height: 28px;
        color: #0c60e1;
    }
</style>
<div id='mainContent' class='main-content' style="height: 380px">
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <?php echo $lang->qualitygate->dealTitle;?>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' target='hiddenwin'>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->qualitygate->qualitygate;?></th>
                    <td colspan='2'><?php echo $this->qualitygate->diffSeverityGateResult($qualitygate->severityGate);?></td>
                    <td colspan='1'><?php echo html::a($this->createLink('report', 'qualityGateCheckResult', "projectId=$qualitygate->projectId&productId=$qualitygate->productId&productVersion=$qualitygate->productVersion&buildId=$qualitygate->buildId", '', true).'#app=project', $lang->qualitygate->clickCheckDetail, '_blank', " id='qualityGateResultDetail'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->qualitygate->severityTest;?></th>
                    <td colspan='3'><?php echo html::select('status', $lang->qualitygate->statusList, $qualitygate->status, "id='mySelect' class='form-control chosen' onchange='showWarning()'");?></td>
                </tr>
                <tr>
                    <th></th>
                    <td colspan='3' style="height: 50px"><p id="warning" class="warning"><?php echo $lang->qualitygate->statusTipMsg ?></p></td>
                </tr>
                <tr>
                    <td colspan='4' class='text-center form-actions'>
                        <?php echo html::submitButton();?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<?php js::set('buildId', $qualitygate->buildId);?>
<script>
    function showWarning() {
        var selectedValue = $("#mySelect").val()
        var warning = document.getElementById("warning");
        if (selectedValue == "finish") {
            warning.style.display = "block";
        } else {
            warning.style.display = "none";
        }
    }
</script>
