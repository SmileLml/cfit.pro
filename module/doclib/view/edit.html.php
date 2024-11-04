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
        <h2><?php echo $lang->doclib->edit;?></h2>
      </div>
      <form id='doclibForm' method='post' class='form-ajax'>
        <table class='table table-form'>
          <tr>
            <th><?php echo $lang->doclib->name; ?></th>
            <td class='required'><?php echo html::input('name', $lib->name, "class='form-control'"); ?></td>
            <td></td>
          </tr>
          <tr class='hide-gitlab'>
            <th><?php echo $lang->doclib->path; ?></th>
            <td class='required'><?php echo html::input('path', $lib->path, "class='form-control'"); ?></td>
            <td class='muted'>
                <span class="tips-svn"><?php echo $lang->doclib->example->path->svn;?></span>
            </td>
          </tr>
          <tr class='hide-gitlab'>
            <th><?php echo $lang->doclib->client;?></th>
            <td class='required'><?php echo html::input('client', $lib->client, "class='form-control'")?></td>
            <td class='muted'>
              <span class="tips-svn"><?php echo $lang->doclib->example->client->svn;?></span>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->doclib->encoding; ?></th>
            <td class='required'><?php echo html::input('encoding', $lib->encoding, "class='form-control'"); ?></td>
            <td class='muted'><?php echo $lang->doclib->encodingsTips; ?></td>
          </tr>
          <tr class="account-fields">
            <th><?php echo $lang->doclib->account;?></th>
            <td><?php echo html::input('account', $lib->account, "class='form-control'");?></td>
          </tr>
          <tr class="account-fields">
            <th><?php echo $lang->doclib->password;?></th>
            <td>
              <div class='input-group'>
                <?php echo html::password('password', $lib->password, "class='form-control'");?>
                <span class='input-group-addon fix-border fix-padding'></span>
                <?php echo html::select('encrypt', $lang->doclib->encryptList, 'base64', "class='form-control'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->doclib->acl;?></th>
            <td class='acl'>
              <div class='input-group mgb-10'>
                <span class='input-group-addon'><?php echo $lang->doclib->group?></span>
                <?php echo html::select('acl[groups][]', $groups, empty($lib->acl->groups) ? '' : join(',', $lib->acl->groups), "class='form-control chosen' multiple")?>
              </div>
              <div class='input-group'>
                <span class='input-group-addon user-addon'><?php echo $lang->doclib->user?></span>
                <?php echo html::select('acl[users][]', $users, empty($lib->acl->users) ? '' : join(',', $lib->acl->users), "class='form-control chosen' multiple")?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->doclib->desc; ?></th>
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
