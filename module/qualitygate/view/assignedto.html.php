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
    .table-form>tbody>tr>td, .table-form>tbody>tr>th, .table-form>tfoot>tr>td, .table-form>thead>tr>th {
        padding: 10px 5px 20px;
        vertical-align: middle;
        border-bottom: none;
    }
    .btn {
        min-width: 80px;
    }
    .backbtn {
        color: #3c4353;
        background-color: #fff;
        border-color: #d6dae3;
    }
</style>
<div id='mainContent' class='main-content' style="height: 380px">
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <?php echo $lang->qualitygate->assignedTo;?>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' target='hiddenwin'>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->qualitygate->assignTo;?></th>
                    <td colspan='2' class="required"><?php echo html::select('dealUser', $assignToUsers, $qualitygate->dealUser, "class='form-control chosen'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->qualitygate->comment;?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
                </tr>
                <tr>
                    <td colspan='3' class='text-center form-actions'>
                        <button type='button' class ='btn btn-primary backbtn' data-dismiss='modal' aria-hidden='true' id='closeModal' onclick="refresh()">返回</button>
                        <?php echo html::submitButton();?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
