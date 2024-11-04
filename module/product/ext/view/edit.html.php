<?php
/**
 * The edit view of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: edit.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<?php js::set('noProject', false);?>
<?php js::set('oldProgramID', $product->program);?>
<?php js::set('canChangeProgram', $canChangeProgram);?>
<?php js::set('singleLinkProjects', $singleLinkProjects);?>
<?php js::set('multipleLinkProjects', $multipleLinkProjects);?>
<style>
#changeProgram .icon-project {padding-right: 5px;}
</style>
<div id="mainContent" class="main-content">
  <div class="center-block">
    <div class="main-header">
      <h2>
        <span class='label label-id'><?php echo $product->id;?></span>
        <?php echo html::a($this->createLink('product', 'view', 'product=' . $product->id), $product->name, '', "title='$product->name'");?>
        <small><?php echo $lang->arrow . ' ' . $lang->product->edit;?></small>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" id="createForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->product->app;?></th>
            <td colspan='11'><?php echo html::select('app', $apps, $product->app, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->product->line;?></th>
            <td colspan='11'><?php echo html::select('line', $lines, $product->line, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->product->name;?></th>
            <td colspan='11'><?php echo html::input('name', $product->name, "class='form-control' required");?></td><td></td>
          </tr>
          <tr>
              <th><?php echo $lang->product->code;?></th>
              <td colspan='11'>
                  <?php $i = 0; foreach ($codeinfos as $codeinfo) { $i++; ?>
                  <div class='table-row'>
                      <div class='table-col'">
                      <div class='input-group w-p140'>
                          <span class='input-group-addon' ><?php echo $lang->product->code;?></span>
                          <?php echo html::input('codes[]', $codeinfo->code, "class='form-control input-product-code' required");?>
                      </div>
                  </div>
                  <div class='table-col '  >
                      <div class='input-group '>
                          <span class='input-group-addon'><?php echo $lang->product->enableTime;?></span>
                          <?php echo html::input('enableTime[]', $codeinfo->enableTime, "class='form-control form-datetime'  required");?>
                      </div>
                  </div>
                  <div class='table-col '  >
                      <div class='input-group  '>
                          <span class='input-group-addon'><?php echo $lang->product->comment;?></span>
                          <?php echo html::input('comment[]',  $codeinfo->desc, "class='form-control input-product-comment' required");?>
                      </div>
                  </div>
                  <div class='table-col actionCol' style="width: 90px;">
                      <div class='btn-group'>
                          <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addItem(this)'");?>
                          <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeItem(this,$product->id)'");?>
                      </div>
                  </div>
                  </div>
              <?php }
              if($i == 0 ){
              ?>
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
          <?php
              }
            ?>
            </td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->product->osName;?></th>
              <td colspan='11'><?php echo html::select('os', $selects['osTypeList'], $product->os, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->product->archName;?></th>
              <td colspan='11'><?php echo html::select('arch', $selects['archTypeList'], $product->arch, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->product->PO;?></th>
            <td colspan='11'><?php echo html::select('PO', $poUsers, $product->PO, "class='form-control chosen'");?></td><td></td>
          </tr>

         <tr>
            <th><?php echo $lang->product->belongDeptIds;?></th>
            <td colspan='11' ><?php echo html::select('belongDeptIds[]', $depts, $product->belongDeptIds, "class='form-control input-code chosen' multiple");?></td><td></td>
         </tr>

          <tr class='hidden'>
            <th><?php echo $lang->product->type;?></th>
            <td colspan='11'><?php echo html::select('type', $lang->product->typeList, $product->type, "class='form-control'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->product->status;?></th>
            <td colspan='11'><?php echo html::select('status', $lang->product->statusList, $product->status, "class='form-control'");?></td><td></td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->product->codebasePath;?></th>
              <td colspan='11'><?php echo html::input('codebasePath', $product->codebasePath, "class='form-control'");?></td><td></td>
          </tr>

        <tr>
            <th><?php echo $lang->product->piplinePath;?></th>
            <td colspan='11'>
                <div class='table-row'>
                    <div class="table-col w-p80">
                        <div class='input-group '>
                            <?php echo html::input('piplinePath', $product->piplinePath, "class='form-control ' ");?>
                        </div>
                    </div>
                    <div class="table-col" >
                        <div class='input-group'>
                            <span class='input-group-addon''><?php echo html::checkbox('skipBuild',['1' => $lang->product->skipBuild] ,$product->skipBuild);?></span>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
          <?php $this->printExtendFields($product, 'table');?>
          <tr>
            <th><?php echo $lang->product->desc;?></th>
            <td colspan='11'><?php echo html::textarea('desc', htmlspecialchars($product->desc), "rows='8' class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->product->acl;?></th>
            <td colspan='11'><?php echo nl2br(html::radio('acl', $lang->product->aclList, $product->acl, "onclick='setWhite(this.value);'", 'block'));?></td>
          </tr>
          <tr class="<?php if($product->acl == 'open') echo 'hidden';?>" id="whitelistBox">
            <th><?php echo $lang->whitelist;?></th>
            <td colspan='11'><?php echo html::select('whitelist[]', $users, $product->whitelist, 'class="form-control chosen" multiple');?></td>
            <td></td>
          </tr>
          <tr>
            <td colspan='11' class='text-center form-actions'>
              <?php echo html::hidden('changeProjects', '');?>
              <?php echo html::submitButton();?>
              <?php echo html::backButton('', '', 'btn btn-wide');?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<div class="modal fade" id="changeProgram">
  <div class="modal-dialog mw-600px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-close"></i></button>
        <?php if($canChangeProgram):?>
        <h4 class="modal-title"><?php echo $lang->product->changeProgram;?></h4>
        <?php endif;?>
      </div>
      <div class="modal-body">
        <table class='table table-form'>
          <?php if(!$canChangeProgram):?>
          <tr>
            <th class='text-left'><?php echo $lang->product->notChangeProgramTip;?></th>
          </tr>
          <?php foreach($linkStoriesProjects as $project):?>
          <tr>
            <td><i class="icon icon-project"></i><?php echo $project;?></td>
          </tr>
          <?php endforeach;?>
          <?php endif;?>
          <?php if($singleLinkProjects):?>
          <tr>
            <th class='text-left'><?php echo $lang->product->programChangeTip;?></th>
          </tr>
          <?php foreach($singleLinkProjects as $project):?>
          <tr>
            <td><i class="icon icon-project"></i><?php echo $project;?></td>
          </tr>
          <?php endforeach;?>
          <?php endif;?>
          <?php if($multipleLinkProjects):?>
          <tr>
            <th class='text-left'><?php echo $lang->product->confirmChangeProgram;?></th>
          </tr>
          <tr>
            <td><?php echo html::checkbox('projects', $multipleLinkProjects);?></td>
          </tr>
          <tr>
            <td class='text-center'>
              <?php echo html::commonButton($lang->save, 'onclick = "setChangeProjects();"', 'btn btn-primary btn-wide');?>
            </td>
          </tr>
          <?php endif;?>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
