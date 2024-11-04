<?php
if($this->app->openApp == 'project') die();
$linkHtml = '';
$openModule = $this->app->rawModule == 'projectstory' ? 'project' : 'product';
if((!empty($this->config->CRProduct) or $product->status != 'closed') and common::hasPriv('story', 'import')) $linkHtml = html::a($this->createLink('story', 'import', "productID=$productID&branch=$branch&type=$storyType"), '<i class="icon-import muted"></i> <span class="text">' . $lang->story->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe' data-app='$openModule'");

$class = common::hasPriv('story', 'exportTemplet') ? '' : "class='disabled'";
$link  = common::hasPriv('story', 'exportTemplet') ? $this->createLink('story', 'exportTemplet', "productID=$productID&branch=$branch&type=$storyType") : '#';
$misc  = common::hasPriv('story', 'exportTemplet') ? "data-toggle='modal' data-type='iframe' class='exportTemplet' data-app='$openModule'" : "class='disabled'";
$exportHtml = "<li $class>" . html::a($link, $lang->story->exportTemplet, '', $misc) . '</li>';
?>
<script>
$(function()
{
    $('#exportActionMenu').closest('.btn-group').after(<?php echo json_encode($linkHtml)?>);
    $('#exportActionMenu').append(<?php echo json_encode($exportHtml)?>);
    $('.import').modalTrigger({width:650, type:'iframe'})
    $(".exportTemplet").modalTrigger({width:650, type:'iframe'});
    $('#exportActionMenu a.export').attr('href', createLink('story', 'export', "<?php echo "productID=$productID&orderBy=$orderBy&executionID=0&browseType=$browseType&type=$storyType";?>"));
})
</script>
