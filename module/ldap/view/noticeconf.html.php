<?php
/**
 * The index view file of ldap module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     ldap
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div class="main-row">
  <div class='side-col' id='sidebar'>
    <div class='cell'>
      <div class='list-group'>
        <?php
        echo html::a($this->createLink('ldap', 'set'), $lang->ldap->basicConf);
        echo html::a($this->createLink('ldap', 'dept'), $lang->ldap->deptConf);
        echo html::a($this->createLink('ldap', 'noticeConf'), $lang->ldap->noticeConf, '', "class='active'");
        echo html::a($this->createLink('ldap', 'syncHistory'), $lang->ldap->syncHistory);
        ?>
      </div>
    </div>
  </div>

  <div id='mainContent' class='main-col main-content'>
    <?php if(!extension_loaded('ldap')):?>
    <div class='center-block alert alert-danger'>
      <h4><?php echo $lang->ldap->noldap->header?></h4>
      <hr />
      <div class='box-content'><?php echo $lang->ldap->noldap->content?></div>
    </div>
    <?php else:?>
    <div class='center-block'>
      <div class='main-header'>
        <h2><?php echo $lang->ldap->common?></h2>
      </div>
      <form class="load-indicator main-form form-ajax" method='post'>
        <div class='detail-title'><?php echo $lang->ldap->deptConf;?></div>
        <table class="table table-form">
          <tr>
            <th class='w-120px'><?php echo $lang->ldap->sendUser;?></th>
            <td><?php echo html::select('sendUser[]', $users, $mailConf->sendUser, "class='form-control chosen' autocomplete='off' multiple='multiple'")?></td>
            <td></td>
          </tr>
          <tr>
            <th class='w-120px'><?php echo $lang->ldap->mailTitle;?></th>
            <td><?php echo html::input('mailTitle', $mailConf->mailTitle, "class='form-control' autocomplete='off'")?></td>
            <td></td>
          </tr>
          <tr>
            <th class='w-120px'><?php echo $lang->ldap->mailContent;?></th>
            <td><?php echo html::textarea('mailContent', $mailConf->mailContent, "raws='6'", "class='form-control'")?></td>
            <td></td>
          </tr>
          <tr>
            <th class='w-120px'></th>
            <td class='text-left form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::commonButton($lang->ldap->preview, 'data-type="ajax" data-title="' . $lang->ldap->preview . '" data-remote="' . $this->createLink('custommail', 'ajaxPreview', 'browseType=ldap') . '" data-toggle="modal"', 'btn btn-wide btn-info triggerButton');?>
            </td>
          </tr>
        </table>
      </form>
    </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
