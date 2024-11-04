<?php
/**
 * The suspend file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang<wwccss@gmail.com>
 * @package     project
 * @version     $Id: suspend.html.php 935 2013-01-16 07:49:24Z wwccss@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2>
                <span class='label label-id'><?php echo $project->id;?></span>
                <small><?php echo $lang->arrow . $project->name;?></small>
                <small><?php echo $lang->arrow . $lang->project->edit;?></small>
            </h2>
        </div>

        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-120px'><?php echo $lang->project->$field;?></th>
                    <td><?php
                        $dataList = $field. 'List';
                        echo html::select($field, $lang->project->$dataList, $project->$field, "class='form-control chosen'");
                        ?>
                    </td>
                    <td></td>
                </tr>

                <tr>
                    <th colspan="3">&nbsp;&nbsp;&nbsp;</th>
                </tr>

                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton() . html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>

    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
