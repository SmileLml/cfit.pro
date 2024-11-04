<?php
/**
 * The browse view file of dept module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dept
 * @version     $Id: browse.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->dept->batchUpdate;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->dept->user;?></th>
                    <td><?php echo html::select('user', $users, '', "class='form-control chosen'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->edit;?></th>
                    <td><?php echo html::select('dept', $depts, '', "class='form-control chosen'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->starttime;?></th>
                    <td><?php echo html::input('starttime', '', "class='form-control form-date' ");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->endtime;?></th>
                    <td><?php echo html::input('endtime', '', "class='form-control form-date' ");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='2'><?php echo html::submitButton() . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
