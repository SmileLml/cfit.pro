<?php
/**
 * The view view file of domain module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     domain
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php js::set('confirmDelete', $lang->domain->confirmDelete)?>
<div id='mainContent' class='main-content'>
  <div class='main-header'>
    <h2><?php echo $lang->domain->view;?></h2>
  </div>
  <div class='main'>
    <div class='detail'>
      <table class='table table-form'>
        <tr>
          <th class='w-100px'><?php echo $lang->domain->domain;?></th>
          <td class='w-p25-f'><?php echo $domain->domain;?></td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->domain->adminURI;?></th>
          <td><?php echo $domain->adminURI;?></td>
        </tr>
        <tr>
          <th><?php echo $lang->domain->resolverURI;?></th>
          <td><?php  echo $domain->resolverURI;?></td>
        </tr>
        <tr>
          <th><?php echo $lang->domain->register;?></th>
          <td><?php echo $domain->register;?></td>
        </tr>
        <tr>
          <th><?php echo $lang->domain->expiredDate;?></th>
          <td><?php echo $domain->expiredDate;?></td>
        </tr>
        <tr>
          <th><?php echo $lang->domain->renew;?></th>
          <td><?php echo zget($lang->domain->renewMethod, $domain->renew);?></td>
        </tr>
        <tr>
          <th><?php echo $lang->domain->account;?></th>
          <td><?php echo $domain->account;?></td>
        </tr>
      </table>
    </div>
    <?php include '../../common/view/action.html.php'?>
  </div>
  <div id='mainActions' class='main-actions'>
    <nav class='container'></nav>
    <div class='btn-toolbar'>
      <?php
      common::printLink('domain', 'edit', "id=$domain->id", "<i class='icon-edit'></i> " . $lang->edit, '', "class='btn'", '', '', $domain);
      if(!isonlybody()) common::printLink('domain', 'browse', "", "<i class='icon-goback icon-back'></i> " . $lang->goback, '', "class='btn'");
      ?>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
