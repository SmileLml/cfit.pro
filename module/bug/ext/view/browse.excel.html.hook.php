<?php
$linkHtml = '';
if((!empty($this->config->CRProduct) or $product->status != 'closed') and common::hasPriv('bug', 'import'))
{
    $linkHtml = html::a($this->createLink('bug', 'import', "applicationID=$applicationID&productID=$productID&branch=$branch"), '<i class="icon-import muted"></i> ' . $lang->bug->import, '', "class='btn btn-link import'");
}

$class = common::hasPriv('bug', 'exportTemplet') ? '' : "class='disabled'";
$link  = common::hasPriv('bug', 'exportTemplet') ? $this->createLink('bug', 'exportTemplet', "applicationID=$applicationID&productID=$productID&branch=$branch") : '#';
$misc  = common::hasPriv('bug', 'exportTemplet') ? "class='exportTemplet'" : "class='disabled'";
$exportHtml = "<li $class>" . html::a($link, $lang->bug->exportTemplet, '', $misc) . '</li>';
?>
<script>
$(function(){
    $('#exportActionMenu').closest('.btn-group').after(<?php echo json_encode($linkHtml)?>);
    $('#exportActionMenu').append(<?php echo json_encode($exportHtml)?>);
    $(".import").modalTrigger({width:650, type:'iframe'});
    $(".exportTemplet").modalTrigger({width:650, type:'iframe'});
})
</script>
