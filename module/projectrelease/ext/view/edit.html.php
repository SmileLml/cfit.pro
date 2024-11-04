<?php
/**
 * The edit view of release module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: edit.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='prefix'><?php echo html::icon($lang->icons['release']);?> <strong><?php echo $release->id;?></strong></span>
        <strong><?php echo html::a(inlink('view', "release=$release->id"), $release->name);?></strong>
        <small><?php echo $lang->arrow . ' ' . $lang->release->edit;?></small>
      </h2>
    </div>
    <form class='load-indicator main-form form-ajax' method='post' target='hiddenwin' id='dataform' enctype='multipart/form-data'>
      <table class='table table-form'> 
        <tbody>
          <tr>
            <th><?php echo $lang->release->name;?></th>
            <td colspan='4'><?php echo html::input('name', $release->name, "class='form-control' required");?></td>
            <td>
              <?php $checked = !empty($release->marker) ? "checked='checked'" : '';?>
              <div class='checkbox-primary'>
                <input id='marker' name='marker' value='1' type='checkbox' <?php echo $checked;?> />
                <label for='marker'><?php echo $lang->release->marker;?></label>
              </div>
            </td>
          </tr>  
          <tr>
            <th><?php echo $lang->projectrelease->buildname;?></th>
            <td colspan='4'><?php echo html::select('build', $builds, $release->build, "class='form-control chosen' required"); ?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->release->date;?></th>
            <td colspan='4'><?php echo html::input('date', $release->date, "class='form-control form-date' required");?></td><td></td>
          </tr>  
          <tr>
            <th><?php echo $lang->release->path;?></th>
            <td colspan='4'><?php echo html::input('path', $release->path, "class='form-control' required");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->release->mailto;?></th>
            <td colspan='4'><?php echo html::select('mailto', $users, $release->mailto, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->release->status;?></th>
            <td colspan='4'><?php echo html::select('status', $lang->release->statusList, $release->status, "class='form-control'");?></td><td></td>
          </tr>  
          <?php $this->printExtendFields($release, 'table');?>
          <tr>
            <th><?php echo $lang->release->desc;?></th>
            <td colspan='4'><?php echo html::textarea('desc', htmlspecialchars($release->desc), "rows=10 class='form-control kindeditor disabled-ie-placeholder' hidefocus='true' placeholder='" . htmlspecialchars($lang->projectrelease->plateTip ) . "'");?></td>
          </tr>  
          <tr>
            <th><?php echo $lang->release->uploadedFiles;?></th>
            <td colspan='4'>
              <div class="detail">
                <div class="detail-content article-content">
                  <?php
                  if($release->files)
                  {
                      echo $this->fetch('file', 'printFiles', array('files' => $release->files, 'fieldset' => 'false', 'object' => null));
                  }else{
                      echo $lang->noData;
                  }
                 /* elseif($release->filePath)
                  {
                      echo $lang->release->filePath . html::a($release->filePath, $release->filePath, '_blank');
                  }
                  elseif($release->scmPath)
                  {
                      echo $lang->release->scmPath . html::a($release->scmPath, $release->scmPath, '_blank');
                  }*/
                  ?>
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->files;?></th>
            <td colspan='4'><?php echo $this->fetch('file', 'buildform');?></td>
          </tr>  
          <tr>
            <td colspan='5' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
              <?php echo html::hidden('product', $release->product);?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>  
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
