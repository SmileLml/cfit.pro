<?php
/**
 * The create view of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: create.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<?php js::set('noProject', false);?>
<div id="mainContent" class="main-content">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->product->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" id="createForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->product->app;?></th>
            <td colspan='11'><?php echo html::select('app', $apps, $oneapp, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->product->line;?></th>
            <td colspan='11'><?php echo html::select('line', $lines, '', "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->product->name;?></th>
            <td colspan='11'><?php echo html::input('name', '', "class='form-control input-product-title' required");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->product->code;?></th>
              <td colspan='11'>
                  <div class='table-row'>
                      <div class='table-col'">
                          <div class='input-group w-p140'>
                              <span class='input-group-addon' ><?php echo $lang->product->code;?></span>
                              <?php echo html::input('codes[]', '', "class='form-control input-product-code' required");?>
                          </div>
                      </div>
                      <div class='table-col '  >
                          <div class='input-group '>
                              <span class='input-group-addon'><?php echo $lang->product->enableTime;?></span>
                              <?php echo html::input('enableTime[]', '', "class='form-control form-datetime'  required");?>
                          </div>
                      </div>
                      <div class='table-col '  >
                          <div class='input-group  '>
                              <span class='input-group-addon'><?php echo $lang->product->comment;?></span>
                              <?php echo html::input('comment[]', '', "class='form-control input-product-comment' required");?>
                          </div>
                      </div>
                      <div class='table-col actionCol' style="width: 90px;">
                          <div class='btn-group'>
                              <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem(this)'");?>
                              <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem(this)'");?>
                          </div>
                      </div>
                  </div>
              </td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->product->osName;?></th>
              <td colspan='11' class='w-p40-f'><?php echo html::select('os', $selects['osTypeList'], '', "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->product->archName;?></th>
              <td  colspan='11' class='w-p40-f'><?php echo html::select('arch', $selects['archTypeList'], '', "class='form-control chosen'");?></td><td></td>
          </tr>

          <tr>
            <th><?php echo $lang->product->PO;?></th>
            <td colspan='11' ><?php echo html::select('PO', $poUsers, '', "class='form-control chosen'");?></td><td></td>
          </tr>
        <tr>
            <th><?php echo $lang->product->belongDeptIds;?></th>
            <td colspan='11' ><?php echo html::select('belongDeptIds[]', $depts, '', "class='form-control input-code chosen' multiple");?></td><td></td>
        </tr>

          <tr class='hidden'>
            <th><?php echo $lang->product->type;?></th>
            <td colspan='11'>
              <?php
              $proudctTypeList = array();
              foreach($lang->product->typeList as $key => $type) $productTypeList[$key] = $type . zget($lang->product->typeTips, $key, '');
              ?>
              <?php echo html::select('type', $productTypeList, 'normal', "class='form-control'");?>
            </td>
            <td></td>
          </tr>
          <tr>
              <th><?php echo $lang->product->codebasePath;?></th>
              <td colspan='11'><?php echo html::input('codebasePath', '', "class='form-control input-product-title'");?></td><td></td>
          </tr>
          <tr>
              <th><?php echo $lang->product->piplinePath;?></th>
              <td colspan='11'>
                  <div class='table-row'>
                       <div class="table-col w-p80">
                          <div class='input-group '>
                              <?php echo html::input('piplinePath', '', "class='form-control ' ");?>
                          </div>
                       </div>
                      <div class="table-col" >
                          <div class='input-group'>
                              <span class='input-group-addon''><?php echo html::checkbox('skipBuild',['1' => $lang->product->skipBuild],'');?></span>
                          </div>
                      </div>
                  </div>
            </td>
          </tr>
          <tr class='hide'>
            <th><?php echo $lang->product->status;?></th>
            <td colspan='11'><?php echo html::hidden('status', 'normal');?></td>
            <td></td>
          </tr>
          <?php $this->printExtendFields('', 'table');?>
          <tr>
            <th><?php echo $lang->product->desc;?></th>
            <td colspan='11'>
              <?php echo $this->fetch('user', 'ajaxPrintTemplates', "type=product&link=desc");?>
              <?php echo html::textarea('desc', '', "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->product->acl;?></th>
            <td colspan='11'><?php echo nl2br(html::radio('acl', $lang->product->aclList, 'private', "onclick='setWhite(this.value);'", 'block'));?></td>
          </tr>
          <tr id="whitelistBox">
            <th><?php echo $lang->whitelist;?></th>
            <td colspan='11'><?php echo html::select('whitelist[]', $users, '', 'class="form-control chosen" multiple');?></td>
            <td></td>
          </tr>
          <tr>
            <td colspan='11' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
