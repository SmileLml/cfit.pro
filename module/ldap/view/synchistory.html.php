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
        echo html::a($this->createLink('ldap', 'noticeConf'), $lang->ldap->noticeConf);
        echo html::a($this->createLink('ldap', 'syncHistory'), $lang->ldap->syncHistory, '', "class='active'");
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
        <table class="table table-condensed table-bordered active-disabled table-fixed">
          <tr>
            <th class='w-80px'><?php echo $lang->ldap->id;?></th>
            <th class='w-160px'><?php echo $lang->ldap->syncAccount;?></th>
            <th><?php echo $lang->ldap->syncResult;?></th>
            <th class='w-180px'><?php echo $lang->ldap->syncTime;?></th>
          </tr>
          <?php foreach($historyList as $history):?>
          <tr>
            <td><?php echo $history->id;?></td>
            <td><?php echo $history->ldapAccount;?></td>
            <td><?php echo zget($lang->ldap->syncResultList, $history->result, '');?></td>
            <td><?php echo $history->addTime;?></td>
          </tr>
          <?php endforeach;?>
        </table>
        <div class="table-footer">
          <?php $pager->show('right', 'pagerjs');?>
        </div>
      </form>
    </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
