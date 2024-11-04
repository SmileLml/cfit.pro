<?php
/**
 * The create view file of domain module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Jiangxiu Peng <pengjiangxiu@cnezsoft.com>
 * @package     domain
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.modal.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<form id='ajaxForm' method='post' action="<?php echo inlink('edit', "id=$domain->id");?>">
  <table class='table table-form'>
    <tr>
      <th class='w-100px'><?php echo $lang->domain->domain?></th>
      <td class='required'><?php echo html::input('domain', $domain->domain, "class='form-control'")?></td>
    </tr>
    <tr>
      <th><?php echo $lang->domain->adminURI?></th>
      <td class='required'><?php echo html::input('adminURI', $domain->adminURI, "class='form-control'")?></td>
    </tr>
    <tr>
      <th><?php echo $lang->domain->resolverURI?></th>
      <td class='required'><?php echo html::input('resolverURI', $domain->resolverURI, "class='form-control'")?></td>
    </tr>
    <tr>
      <th><?php echo $lang->domain->register?></th>
      <td class='required'><?php echo html::input('register', $domain->register, "class='form-control'")?></td>
    </tr>
    <tr>
      <th><?php echo $lang->domain->expiredDate;?></th>
      <td class='required'><?php echo html::input('expiredDate', substr($domain->expiredDate, 0, 10), "class='form-control form-date'")?></td>
    </tr>
    <tr>
      <th><?php echo $lang->domain->renew;?></th>
      <td class='required'><?php echo html::select('renew', $lang->domain->renewMethod, $domain->renew, "class='form-control chosen'")?></td>
    </tr>
    <tr>
      <th><?php echo $lang->domain->account;?></th>
      <td><?php echo html::select('account', $users, $domain->account, "class='form-control chosen'")?></td>
    </tr>
    <tr>
      <td colspan='2' class='text-center form-actions'>
        <?php echo html::submitButton();?>
        <?php echo html::backButton('', '', 'btn btn-wide');?>
      </td>
    </tr>
  </table>
</form>
<?php include '../../common/view/footer.modal.html.php';?>
