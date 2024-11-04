<?php
/**
 * The set view file of custom module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     custom
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php
$orderStr = '';
if ($field == 'navOrderList'){
    $orderStr = '<td><input type="text" class="form-control" value="0" autocomplete="off" name="orders[]"></td>';
}
$itemRow = <<<EOT
  <tr class='text-center'>
    <td>
      <input type='text' class="form-control" autocomplete="off" value="" name="keys[]">
      <input type='hidden' value="0" name="systems[]">
    </td>
    <td>
      <input type='text' class="form-control" value="" autocomplete="off" name="values[]">
    </td>$orderStr
    <td class='c-actions'>
      <a href="javascript:void(0)" class='btn btn-link' onclick="addItem(this)"><i class='icon-plus'></i></a>
      <a href="javascript:void(0)" class='btn btn-link' onclick="delItem(this)"><i class='icon-close'></i></a>
    </td>
  </tr>
EOT;
?>
<div id="mainMenu" class="clearfix">
    <?php include 'reportheader.html.php'; ?>
    <div class="btn-toolbar pull-left">
        <div class="page-title">
            <span class="text"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="btn-toolbar pull-right">

    </div>
</div>
<style>
    .checkbox-primary {width: 170px; margin: 0 10px 10px 0; display: inline-block;}
</style>
<div id='mainContent' class='main-row'>
    <div class='side-col' id='sidebar'>

    </div>
    <div class='main-col main-content'>
        <form class="load-indicator main-form form-ajax" method='post'>
            <div class='main-header'>
                <div class='heading'>
                    <strong><?php echo $lang->secondmonthreport->secondmonthreport . $lang->arrow . $lang->secondmonthreport->cycleconfiguration?></strong>
                </div>
            </div>
            <div class="red">
                配置说明: 2$:代表上一年； 1$:代表当年；1$和2$相对于”考核年份“转换
            </div>
                <table class='table table-form active-disabled table-condensed mw-600px'>
                    <tr class='text-center'>
                        <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                        <td><strong><?php echo $lang->custom->value;?></strong></td>
                        <?php  if($canAdd):?><th class='w-90px'></th><?php endif;?>
                    </tr>

                        <?php
                        $classStr = '';
                        foreach ($lang->secondmonthreport->examinecycleConfigMapList as $keyfield=>$tablename){

                        ?>
                        <tr class='text-center'>
                            <?php $system = 0;?>
                            <td><?php echo $tablename; ?><?php  echo html::hidden('keys[]', $keyfield) . html::hidden('systems[]', $system);?></td>
                            <td>
                                <?php echo html::input("values[]", isset($dbFields[$keyfield]->value) ? $dbFields[$keyfield]->value : '', "class='form-control ".$classStr."' ");?>
                            </td>
                        </tr>
                    <?php
                        }
                    ?>


                    <tr>
                        <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                            <?php

                            echo html::submitButton();

                            ?>
                        </td>
                    </tr>
                </table>

        </form>
    </div>
</div>


<?php include '../../common/view/footer.html.php';?>
