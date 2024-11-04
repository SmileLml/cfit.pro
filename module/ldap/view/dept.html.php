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
<div class="main-row">
  <div class='side-col' id='sidebar'>
    <div class='cell'>
      <div class='list-group'>
        <?php
        echo html::a($this->createLink('ldap', 'set'), $lang->ldap->basicConf);
        echo html::a($this->createLink('ldap', 'dept'), $lang->ldap->deptConf, '', "class='active'");
        echo html::a($this->createLink('ldap', 'noticeConf'), $lang->ldap->noticeConf);
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
      <form class='main-form' method='post' target='hiddenwin'>
          <div class='detail-title'><?php echo $lang->ldap->deptConf;?></div>
          <table class='table table-form'>
            <tr>
              <th><?php echo $lang->ldap->zentaoDeptName;?></th>
              <th><?php echo $lang->ldap->ldapDeptName;?></th>
              <th></th>
            </tr>
            <?php foreach($deptRelation as $dept):?>
            <tr>
              <td class='text-right'><?php echo $dept['deptName'];?></td>
              <td class='text-right'><?php echo $dept['ldapName'];?></td>
              <td></td>
            </tr>
            <?php endforeach;?>
          </table>
      </form>
    </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
