<?php
/**
 * The showSyncCommit view file of doclib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     doclib
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content">
  <div class='cell'>
    <div class='alert with-icon'>  
      <i class="icon-check-sign"></i>
      <div class='content'>
        <h3><?php echo $lang->doclib->notice->syncing;?></h3>
        <hr>
        <p><?php echo $lang->doclib->notice->syncedCount?><span id='commits'><?php echo $version?></span></p>
      </div>
    </div>
  </div>
</div>
<script language='Javascript'>
$(function(){
    var link = createLink('doclib', 'ajaxSyncCommit', "libID=<?php echo $libID?>");
    function syncComments()
    {
        $.get(link, function(data)
        {
            if(data == 'finish')
            {
                $('#caption').text('<?php echo $lang->doclib->notice->syncComplete?>');
                return self.location = '<?php echo $browseLink;?>';
            }
            $('#commits').html(parseInt($('#commits').html()) + parseInt(data));
            setTimeout(syncComments, 10);
        });
    }
    setTimeout(syncComments, 500);
})
</script>
<?php include '../../common/view/footer.html.php';?>
