<?php
/**
 * The feedback view file of custom module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     custom
 * @version     $Id$
 * @link        http://www.zentao.net
 */
$oldDir = getcwd();
chdir('../../view/');
include './header.html.php';
chdir($oldDir);
?>
<div id='mainContent' class='main-row'>
  <div class='side-col' id='sidebar'>
    <div class='cell'>
      <div class='list-group'>
        <?php echo html::a(inlink('feedback'), $lang->custom->doclibConfig, '', "class='active'");?>
      </div>
    </div>
  </div>
  <div class='main-col main-content'>
    <form class="load-indicator main-form form-ajax" method='post'>
      <div class='main-header'>
        <div class='heading'>
          <strong><?php echo $lang->custom->doclib . $lang->arrow . $lang->custom->doclibConfig;?></strong>
        </div>
      </div>
      <table class='table table-form mw-900px'>
        <tr>
          <th><?php echo $lang->doclib->client;?></th>
          <td class='w-350px'><?php echo html::input('client', $config->doclib->client, "class='form-control'")?></td>
          <td class='muted'>
            <span class="tips-svn"><?php echo $lang->doclib->example->client->svn;?></span>
          </td>
        </tr>
        <tr class="account-fields">
          <th><?php echo $lang->doclib->account;?></th>
          <td><?php echo html::input('account', $config->doclib->account, "class='form-control'");?></td>
        </tr>
        <tr class="account-fields">
          <th><?php echo $lang->doclib->password;?></th>
          <td>
            <?php echo html::password('password', $config->doclib->password, "class='form-control'");?>
          </td>
        </tr>
        <tr>
          <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<script>
$('#doclibTab').addClass('btn-active-text');
</script>
<?php include '../../../common/view/footer.html.php';?>
