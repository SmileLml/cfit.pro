<?php
/**
 * The side logs view file of doclib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2014 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     doclib
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php
$pathInfo = '&root=' . $this->doclib->encodePath(empty($path) ? '/' : $path);
if(isset($entry)) $pathInfo .= '&type=file';
?>
<form id='logForm' class='main-table' data-ride='table' method='post'>
  <table class='table table-fixed'>
    <thead>
      <tr>
        <th class='w-40px'></th>
        <th class='w-80px'><?php echo $lang->doclib->revisionA?></th>
        <th class='w-80px'><?php echo $lang->doclib->time?></th>
        <th class='w-100px'><?php echo $lang->doclib->committer?></th>
        <th><?php echo $lang->doclib->comment?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($revisions as $log):?>
      <tr>
        <td>
          <div class='checkbox-primary'>
            <input type='checkbox' name='revision[]' value="<?php echo $log->revision?>" />
            <label></label>
          </div>
        </td>
        <td class='versions'><span class="revision"><?php echo html::a($this->doclib->createLink('revision', "libID=$libID&revision={$log->revision}" . $pathInfo), $log->revision, '', "data-app='{$this->app->openApp}'");?></span></td>
        <td><?php echo substr($log->time, 0, 10);?></td>
        <td><?php echo $log->committer;?></td>
        <?php $comment = htmlspecialchars($log->comment, ENT_QUOTES);?>
        <td title='<?php echo $comment?>' class='comment'><?php echo $log->comment?></td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
  <div class='table-footer'>
    <?php if(common::hasPriv('doclib', 'diff')) echo html::submitButton($lang->doclib->diff, '', count($revisions) < 2 ? 'disabled btn btn-primary' : 'btn btn-primary')?>
    <?php //echo html::a($this->doclib->createLink('log', "libID=$libID&entry=" . $this->doclib->encodePath($path) . "&revision=HEAD&type=$logType"), $lang->doclib->allLog, '', "class='allLogs'");?>
    <div class='pull-right'>
      <div class='btn-group'>
        <?php
        $prePage  = $pager->pageID == 1 ? 1 : $pager->pageID - 1;
        $nextPage = $pager->pageID == $pager->pageTotal ? $pager->pageID : $pager->pageID + 1;
        $params   = "libID=$libID&path=" . $this->doclib->encodePath($path) . "&type=$logType&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";
        $preLink  = $this->doclib->createLink('ajaxSideCommits', "$params&pageID=$prePage");
        $nextLink = $this->doclib->createLink('ajaxSideCommits', "$params&pageID=$nextPage");
        echo html::commonButton("<i class='icon icon-angle-left'></i>",  "data-href='$preLink'",  "ajaxPager btn" . ($prePage == $pager->pageID ? ' disabled' : ''));
        echo html::commonButton("<i class='icon icon-angle-right'></i>", "data-href='$nextLink'", "ajaxPager btn" . ($nextPage == $pager->pageID ? ' disabled' : ''));
        ?>
      </div>
    </div>
  </div>
</form>
<script>
if($("input:checkbox[name='revision[]']:checked").length < 2)
{
    $("input:checkbox[name='revision[]']:lt(2)").attr('checked', 'checked');
}
$("input:checkbox[name='revision[]']").each(function(){ if(!$(this).is(':checked')) $(this).attr("disabled","disabled")});
$("input:checkbox[name='revision[]']").click(function(){
    var checkNum = $("input:checkbox[name='revision[]']:checked").length;
    if (checkNum >= 2)
    {
        $("input:checkbox[name='revision[]']").each(function(){ if(!$(this).is(':checked')) $(this).attr("disabled","disabled")});
    }
    else
    {
        $('#diffRepo').remove();
        $("input:checkbox[name='revision[]']").each(function(){$(this).attr("disabled", false)});
    }
});
</script>
