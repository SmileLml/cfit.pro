<?php
/**
 * The create view file of doclib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @author      Wang Yidong, Zhu Jinyong 
 * @package     doclib
 * @version     $Id: create.html.php $
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::import($jsRoot . 'misc/base64.js');?>
<div id='mainContent' class='main-row'>
  <div class='main-col main-content'>
    <div class='center-block'>
      <div class='main-header'>
        <h2><?php echo $lang->projectdoc->edit;?></h2>
      </div>
      <form id='doclibForm' method='post' class='form-ajax'>
        <table class='table table-form'>
          <tr>
            <th><?php echo $lang->projectdoc->name; ?></th>
            <td class='required'><?php echo html::input('name', $lib->name, "class='form-control'"); ?></td>
            <td></td>
          </tr>
          <tr class='hide-gitlab'>
            <th><?php echo $lang->projectdoc->path; ?></th>
            <td class='required'><?php echo html::input('path', $lib->path, "class='form-control'"); ?></td>
            <td class='muted'>
                <span class="tips-svn"><?php echo $lang->projectdoc->example->path->svn;?></span>
            </td>
          </tr>
          <tr class='hide-gitlab'>
            <th><?php echo $lang->projectdoc->client;?></th>
            <td class='required'><?php echo html::input('client', $lib->client, "class='form-control'")?></td>
            <td class='muted'>
              <span class="tips-svn"><?php echo $lang->projectdoc->example->client->svn;?></span>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectdoc->encoding; ?></th>
            <td class='required'><?php echo html::input('encoding', $lib->encoding, "class='form-control'"); ?></td>
            <td class='muted'><?php echo $lang->projectdoc->encodingsTips; ?></td>
          </tr>
          <tr class="account-fields">
            <th><?php echo $lang->projectdoc->account;?></th>
            <td><?php echo html::input('account', $lib->account, "class='form-control'");?></td>
          </tr>
          <tr class="account-fields">
            <th><?php echo $lang->projectdoc->password;?></th>
            <td>
              <div class='input-group'>
                <?php echo html::password('password', $lib->password, "class='form-control'");?>
                <span class='input-group-addon fix-border fix-padding'></span>
                <?php echo html::select('encrypt', $lang->projectdoc->encryptList, 'base64', "class='form-control'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectdoc->desc; ?></th>
            <td colspan='2'><?php echo html::textarea('desc', $lib->desc, "rows='3' class='form-control'"); ?></td>
          </tr>
          <tr>
            <th></th>
            <td colspan='2' class='text-center form-actions'>
              <?php echo html::submitButton(); ?>
              <?php echo html::backButton(); ?>
            </td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
