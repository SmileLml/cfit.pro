<?php
/**
 * The export view file of file module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     file
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>





<main id="main" style="height:400px;">
    <div class="container">
        <div id="mainContent" class='main-content load-indicator'>
            <div class='main-header'>
                <h2><?php echo $lang->secondmonthreport->customTimeInterval;?></h2>
            </div>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform' >
                <table class="table table-form">
                    <tbody>


                    <tr>
                        <th><?php echo $lang->secondmonthreport->statisticalTimeInterval;?></th>
                        <td><?php echo html::input('startTime', '', "class='form-control form-date' readonly='readonly'  style='background: #FFFFFF;' ");?></td>
                        <td style="width:20px">~</td>
                        <td><?php echo html::input('endTime', '', "class='form-control form-date' readonly='readonly' style='background: #FFFFFF;' ");?></td>
                    </tr>
                    <tr>
                        <td><?php echo $lang->secondmonthreport->generateModule;?></td>
                        <td colspan="3"><?php echo html::select('generateType', $lang->secondmonthreport->generateTypeList, '', " class='form-control chosen' ");?> </td>
                    </tr>

                    <tr>

                        <td colspan="4" class="text-center">
                            <?php echo html::submitButton($lang->secondmonthreport->generateSnapshot, "", 'btn btn-primary');?>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <button  class="btn" data-dismiss="modal"><?php echo $lang->secondmonthreport->cancellation;?></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</main>
<?php include '../../common/view/footer.html.php';?>

