<style>
#leftBar{z-index:0 !important;}
</style>
<?php if(common::hasPriv('testcase', 'submit')):?>
<?php if(($this->app->openApp == 'project') or $this->app->openApp == 'qa') die();?>
<script>
$(function()
{
    $('#mainMenu .btn-toolbar.pull-right .dropdown').before(<?php echo json_encode('<div class="btn-group">' . html::a($this->createLink('testcase', 'submit', "productID=$productID"), "<i class='icon icon-plus'></i> {$lang->testcase->submit}", '', "class='btn btn-secondary export' id='toReview'") . '</div>');?>);
    $('#toReview').modalTrigger({width:500, type:'iframe'});
})
</script>
<?php endif;?>
