<?php
/**
 * The product view file of custom module of ZenTaoPMS.
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Liyuchun <liyuchun@cnezsoft.com>
 * @package     custom
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include 'header.html.php';?>
<!--自定义产品菜单-->
<div id='mainContent' class='main-row'>
    <div class="side-col" id="sidebar">
        <div class="cell">
            <div class="list-group">
                <?php echo html::a($this->createLink("custom", "product", ""), "已关闭产品修改", 'self', 'id="sourceModeListTab" class="active"');?>
                <?php echo html::a($this->createLink("custom", "set", "module=productplan&section=osTypeList"), "支持平台列表", 'self', 'id="osTypeListTab"');?>
                <?php echo html::a($this->createLink("custom", "set", "module=productplan&section=archTypeList"), "硬件平台列表", 'self', 'id="archTypeListTab"');?>
                </div>
        </div>
    </div>
<div id='mainContent' class='main-content'>
  <form class="load-indicator main-form form-ajax" method='post'>
    <table class='table table-form'>
      <tr>
        <th class='w-150px'><?php echo $lang->custom->closedProduct;?></th>
        <td class='w-300px text-left'>
          <?php $checkedKey = isset($config->CRProduct) ? $config->CRProduct : 0;?>
          <?php foreach($lang->custom->CRProduct as $key => $value):?>
          <label class="radio-inline"><input type="radio" name="product" value="<?php echo $key?>"<?php echo $key == $checkedKey ? " checked='checked'" : ''?> id="product<?php echo $key;?>"><?php echo $value;?></label>
          <?php endforeach;?>
        </td>
        <td><span class='alert alert-info no-margin'><?php echo $lang->custom->notice->readOnlyOfProduct;?></span></td>
      </tr>
      <tr>
        <th></th>
        <td class='form-actions'>
          <?php echo html::submitButton();?>
        </td>
      </tr>
    </table>
  </form>
</div>
</div>
<script>
$(function()
{
    $('#mainMenu #productTab').addClass('btn-active-text');
})
</script>
<?php include '../../common/view/footer.html.php';?>
