<?php
/**
 * The mail file of testtesttask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: sendmail.html.php 3717 2012-12-10 00:37:07Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php $mailTitle = 'TESTTASK #' . $testtask->id . ' ' . $testtask->name;?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<style>
    .infoName {
        width: 140px; border: 1px solid #e5e5e5; background-color: #f5f5f5; padding: 5px;text-align: center;
    }
    .infoContent{
        padding: 5px; border: 1px solid #e5e5e5;
    }
</style>
<tr>
  <td>
    <table cellpadding='0' cellspacing='0' width='600' style='border: none; border-collapse: collapse;'>
      <tr>
        <td style='padding: 10px; background-color: #F8FAFE; border: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #e5e5e5;'>
          <?php $color = empty($testtask->color) ? '#333' : $testtask->color;?>
          <?php echo html::a(zget($this->config->mail, 'domain', common::getSysURL()) . helper::createLink('testtask', 'view', "testtaskID=$testtask->id", 'html'), $mailTitle, '', "style='color: {$color}; text-decoration: underline;'");?>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <fieldset style='border: 1px solid #e5e5e5'>
            <legend style='color: #114f8e'><?php echo $this->lang->custommail->tips;?></legend>
            <div style='padding:5px;'><?php echo $mailConf;?></div>
        </fieldset>
    </td>
</tr>
<tr>
    <td style='padding: 10px; border: none;'>
        <table cellpadding='0' cellspacing='0' style='width: 100%; border: 1px solid #e5e5e5; margin-bottom: 15px; border-collapse: collapse; font-size: 13px; text-align: left;'>
            <tr>
                <th class="infoName"><?php echo $this->lang->testtask->id ;?></th>
                <td class="infoContent"><?php echo $testtask->id;?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->testtask->name;?></th>
                <td class="infoContent"><?php echo $testtask->name;?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->testtask->status;?></th>
                <td class="infoContent"><?php echo zget($this->lang->testtask->statusList,$testtask->status)?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->testtask->owner;?></th>
                <td class="infoContent"><?php echo zget($users,$testtask->owner);?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->testtask->applicationID;?></th>
                <td class="infoContent"><?php echo zget($products, $testtask->product, '无') == '无'?'无':zget($applicationList, $testtask->applicationID, '无');?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->testtask->product;?></th>
                <td class="infoContent"><?php echo zget($products, $testtask->product, '无');?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->testtask->productVersion;?></th>
                <td class="infoContent"><?php echo zget($version,$build->version,'无');?></td>
            </tr>
            <tr>
                <th class="infoName"><?php echo $this->lang->testtask->createdDate;?></th>
                <td class="infoContent"><?php echo $testtask->createdDate;?></td>
            </tr>
        </table>
    </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
