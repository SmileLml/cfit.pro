<?php
/**
 * The set view file of custom module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     custom
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php
  $oldDir = getcwd();
  chdir(dirname(dirname(dirname(__FILE__))) . '/view');
  include './header.html.php';
  chdir($oldDir);
?>
<?php
$itemRow = <<<EOT
  <tr class='text-center'>
    <td>
      <input type='text' class="form-control" autocomplete="off" value="" name="keys[]">
      <input type='hidden' value="0" name="systems[]">
    </td>
    <td>
      <input type='text' class="form-control" value="" autocomplete="off" name="values[]">
    </td>
    <td class='c-actions'>
      <a href="javascript:void(0)" class='btn btn-link' onclick="addItem(this)"><i class='icon-plus'></i></a>
      <a href="javascript:void(0)" class='btn btn-link' onclick="delItem(this)"><i class='icon-close'></i></a>
    </td>
  </tr>
EOT;
?>
<?php js::set('itemRow', $itemRow)?>
<?php js::set('module',  $module)?>
<?php js::set('field',   $field)?>
<style>
.checkbox-primary {width: 170px; margin: 0 10px 10px 0; display: inline-block;}
</style>
<div id='mainContent' class='main-row'>
  <div class='side-col' id='sidebar'>
    <div class='cell'>
      <div class='list-group'>
        <?php
        foreach($lang->custom->{$module}->fields as $key => $value)
        {
            echo html::a(inlink('set', "module=$module&field=$key"), $value, '', " id='{$key}Tab'");
        }
        ?>
      </div>
    </div>
  </div>
  <div class='main-col main-content'>
    <form class="load-indicator main-form form-ajax" enctype='multipart/form-data' method='post'>
      <div class='main-header'>
        <div class='heading'>
          <strong><?php echo $lang->custom->object[$module] . $lang->arrow . $lang->custom->$module->fields[$field]?></strong>
        </div>
      </div>
      <table class='table table-form'>
        <tr>
          <th class='w-120px text-left'><?php echo $lang->custom->extra->guide;?></th>
        </tr>
        <tr>
          <th class='w-80px text-left'><?php echo $lang->custom->extra->guideUpload;?></th>
          <?php if($file):?>
          <?php
          $sessionString  = $config->requestType == 'PATH_INFO' ? '?' : '&';
          $sessionString .= session_name() . '=' . session_id();

          if(common::hasPriv('file', 'download'))
          {
              $uploadDate = $lang->file->uploadDate . substr($file->addedDate, 0, 10);
              $fileTitle  = "<i class='icon icon-file-text'></i> &nbsp;" . $file->title;
              if(strpos($file->title, ".{$file->extension}") === false && $file->extension != 'txt') $fileTitle .= ".{$file->extension}";
              $imageWidth = 0;
              if(stripos('jpg|jpeg|gif|png|bmp', $file->extension) !== false)
              {
                  $imageSize  = $this->file->getImageSize($file);
                  $imageWidth = $imageSize ? $imageSize[0] : 0;
              }

              $fileSize = 0;
              /* Show size info. */
              if($file->size < 1024)
              {
                  $fileSize = $file->size . 'B';
              }
              elseif($file->size < 1024 * 1024)
              {
                  $file->size = round($file->size / 1024, 2);
                  $fileSize = $file->size . 'K';
              }
              elseif($file->size < 1024 * 1024 * 1024)
              {
                  $file->size = round($file->size / (1024 * 1024), 2);
                  $fileSize = $file->size . 'M';
              }
              else
              {
                  $file->size = round($file->size / (1024 * 1024 * 1024), 2);
                  $fileSize = $file->size . 'G';
              }
          }?>

          <td>
          <?php echo $file->title;?>
          <?php
          if(common::hasPriv('file', 'download'))
          {
            echo html::a($this->createLink('file', 'download', "fileID=$file->id") . $sessionString, $lang->view, '_blank', "class='text-primary' onclick=\"return downloadFile($file->id, '$file->extension', $imageWidth, '$file->title')\"");
          }?>
          </td>
          <?php endif;?>
        </tr>
        <tr>
          <td class='w-120px text-left'><?php echo $lang->custom->extra->guideFile;?><input type="file" name="guideFileName"></td>
        </tr>
        <tr>
          <td class='text-left'><?php echo html::submitButton();?></td>
          <td><?php echo html::hidden('hidden', 1);?></td>
          <td></td>
        </tr>
      </table>
    </form>
    <div class="alert alert-info alert-block"><?php echo $lang->custom->extra->guideFileTip;?></div>
  </div>
</div>
<script>
$(function()
{
    $('#' + module + 'Tab').addClass('btn-active-text');
    $('#' + field + 'Tab').addClass('active');
})

function downloadFile(fileID, extension, imageWidth, fileTitle)
{
    if(!fileID) return;
    var fileTypes      = 'txt,jpg,jpeg,gif,png,bmp';
    var sessionString  = '<?php echo $sessionString;?>';
    var windowWidth    = $(window).width();
    var url            = createLink('file', 'download', 'fileID=' + fileID + '&mouse=left') + sessionString;
    var width          = (windowWidth > imageWidth) ? ((imageWidth < windowWidth * 0.5) ? windowWidth * 0.5 : imageWidth) : windowWidth;
    var checkExtension = fileTitle.lastIndexOf('.' + extension) == (fileTitle.length - extension.length - 1);
    if(fileTypes.indexOf(extension) >= 0 && checkExtension)
    {
        $('<a>').modalTrigger({url: url, type: 'iframe', width: width}).trigger('click');
    }
    else
    {
        window.open(url, '_blank');
    }
    return false;
}
</script>
<?php include '../../../common/view/footer.html.php';?>
