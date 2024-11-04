<?php
/**
 * The create view of build module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: create.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
    <div class='center-block'>
        <div class='main-header'>
            <h2><?php echo $lang->build->rebuild;?></h2>
        </div>
        <form class='load-indicator main-form form-ajax' id='dataform' method='post' enctype='multipart/form-data'>
            <table class='table table-form'>
                <tr>
                    <th><?php echo $lang->comment;?></th>
                    <td colspan='3'><?php echo html::textarea('desc', '', "rows='10' class='form-control kindeditor' hidefocus='true'");?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center form-actions">
                        <?php echo html::submitButton();?>
                        <?php echo html::backButton();?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
