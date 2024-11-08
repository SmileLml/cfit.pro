<?php
/**
 * The mail file of issue module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     issue
 * @version     $Id: sendmail.html.php 4626 2013-04-10 05:34:36Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php 
$mailTitle  = $this->lang->projectplan->projectCreation . '#' . $plan->id . ' ' . $plan->name . '-';
$mailTitle .= $type ? $this->lang->projectplan->labelList[$type] : $this->lang->projectplan->waitReview;
?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<tr>
  <td>
    <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
      <tr>
        <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'>
          <?php echo html::a(zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('projectplan', 'view', "planID=$plan->id") . '#app=platform', $mailTitle, '', "data-app='platform' text-decoration: underline;'");?>
        </td>
      </tr>
    </table>
  </td>
</tr>
<?php if(!$type):?>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'><?php echo $this->lang->projectplan->currentNode;?></legend>
      <div style='padding:5px;'><?php echo zget($this->lang->projectplan->reviewList, $grade, '');?></div>
    </fieldset>
  </td>
</tr>
<?php endif;?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
