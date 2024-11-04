<?php
/**
 * The create view of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: create.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('noProject', false);?>
<div id="mainContent" class="main-content" style="height:500px;">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->reviewqz->confirm;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" id="createForm" method="post" target='hiddenwin'>
            <table class="table table-form">
                <tbody>
                <?php foreach($expertList as $key => $info):?>
                <tr class="fields" style="height:60px;">
                    <th style="width:120px;"><?php if (empty($key)) echo $lang->reviewqz->expertList;?></th>
                    <?php $disabled = $info->reviewer != $this->app->user->account && $this->app->user->account != 'admin' && $show == 2 ? "disabled='disabled'" : '';?>
                    <td><?php echo $info->name;?></td>
                    <td><?php echo html::radio("status[$info->reviewer]", $lang->reviewqz->meetjoinList, $info->status, $disabled);?></td>
                </tr>
                <?php endforeach;?>
                <tr class="fields" style="height:60px;">
                    <th><?php echo $lang->reviewqz->addExpert;?></th>
                    <td colspan='2'><?php echo html::select('expertLists[]', $users, '', 'class="form-control chosen" multiple');?></td>
                </tr>
                <tr>
                    <td colspan='3' class='text-center form-actions'>
                        <?php echo html::submitButton();?>
                        <?php echo html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    function showFields(value)
    {
        $('.fields').closest('tr').addClass('hide')
    }

    function showDeployDate(value)
    {
        $('#deployDate').closest('tr').addClass('hide')
    }
</script>
<?php include '../../common/view/footer.html.php';?>
