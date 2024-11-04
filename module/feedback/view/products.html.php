<?php
/**
 * The admin view file of feedback module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     feedback
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <span class='label label-info'><?php echo $lang->feedback->hasPrivUser;?></span>
</div>
<div id='mainContent' class='main-table'>
  <table class='table table-fixed tablesorter table-datatable'>
    <thead>
      <tr>
        <th class='w-160px'>
          <?php echo $lang->productCommon;?>
        </th>
        <th><?php echo $lang->user->common;?></th>
        <th class='w-60px c-actions text-center'><?php echo $lang->actions?></th>
      </tr>
    </thead>
    <?php if($products):?>
    <tbody>
      <?php foreach($products as $productID => $productName):?>
      <tr class='text-left'>
        <td title='<?php echo $productName;?>'><?php echo $productName;?></td>
        <td class='text-left'>
          <?php if(isset($feedbackView[$productID])):?>
          <?php foreach($feedbackView[$productID] as $account => $view):?>
          <?php if(!isset($users[$account])) continue;;?>
          <?php $user = $users[$account];?>
          <?php echo empty($user->realname) ? $account : $user->realname;?>
          <?php endforeach;?>
          <?php endif;?>
        </td>
        <td class='c-actions'>
          <?php common::printLink('feedback', 'manageProduct', "product=$productID", "<i class='icon icon-link'></i>", '', "class='iframe btn' title='{$lang->feedback->manageProduct}'", '', true);?>
        </td>
      </tr>
      <?php endforeach;?>
    </tbody>
    <?php endif;?>
  </table>
  <div class='table-footer'><?php $pager->show('right', 'pagerjs');?></div>
</div>
<?php include '../../common/view/footer.html.php';?>
