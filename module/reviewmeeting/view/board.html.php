<?php include '../../common/view/header.html.php'; ?>
<style>
    .calendar .cell-day.with-plus-sign .day>.heading:before{
        content: none;
    }
    .eventreview{
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
        width: auto!important;
        padding: 0 5px;
        margin: 0 0 1px;
        font-size: 12px;
        line-height: 18px;
        background-color: transparent;
        cursor: pointer;
    }

</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php foreach($lang->reviewmeeting->meetingMenu as $key => $label):?>
            <?php $active = $status == $key ? 'btn-active-text' : '';?>
            <?php $label = "<span class='text'>$label</span>";?>
            <?php if($status == $key) {$label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";}else{$label .= " <span class='label label-light label-badge'>{$meetCount[$key]}</span>";}?>
            <?php echo html::a(inlink('reviewmeeting', "type=$key"), $label, '', "class='btn btn-link $active'");?>
        <?php endforeach;?>
    </div>
</div>

<?php include '../../common/view/footer.html.php';?>