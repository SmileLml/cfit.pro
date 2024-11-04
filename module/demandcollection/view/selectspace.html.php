
<?php include '../../common/view/headerkanban.lite.html.php';?>
<style>
    .btn-wide{border-color:#d8dbde !important;}
    .chosen-auto-max-width{width: 100% !important;}
</style>
<div id='mainContent' class='main-content importModal'>
    <div class='center-block'>
        <div class='main-header'>
            <h2><?php echo $lang->kanban->selectKanbanSpace;?></h2>
        </div>
    </div>
    <form class='form-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform' style="width: 430px;margin: 0 auto;margin-top:20px">
        <div class='input-group space' style="max-width: 430px">
            <span class='input-group-addon'><?php echo $lang->kanban->selectSpace;?></span>
            <?php echo html::select('spaceId', $space, $selectSpaceID, "onchange='changeSpace()' class='form-control chosen ' required data-drop_direction='down'");?>
        </div>
        <div class="input-group space" style="max-width: 430px">
            <span class='input-group-addon'><?php echo $lang->kanban->selectKanban;?></span>
            <?php echo html::select('kanbanId', [], '', " class='form-control chosen' required data-drop_direction='down'");?>
        </div>
        <div class="input-group space" style="max-width: 430px">
            <span class='input-group-addon'><?php echo $lang->kanban->selectedLane;?></span>
            <?php echo html::select('laneId', [], '', " class='form-control chosen' required data-drop_direction='down'");?>
        </div>
        <div class="input-group space" style="max-width: 420px">
            <?php echo html::submitButton('', 'style="margin:0 20px 0 40px"', 'btn btn-wide btn-primary ');?>
            <button type="button" class="btn btn-wide closee">取消</button>
        </div>
    </form>
</div>
<style>#product_chosen {width: 45% !important}</style>
<?php include '../../common/view/footer.lite.html.php';?>
<script>
    function changeSpace() {
        var spaceId = "";
        $("#spaceId option:selected").each(function () {
            spaceId += $(this).val()+',';
        })
        var url = '<?php echo $this->createLink('measure', 'ajaxGetkanban')?>'
        $.post(url,{spaceId:spaceId,source:'selectSpace'},function (res) {
            $('#kanbanId').next().remove();
            $('#kanbanId').replaceWith(res);
            $('#kanbanId').chosen();
            var kanbanId = $('#kanbanId option:selected').val();
            if (kanbanId){
                getGroupLane();
            }
        })
    }
    changeSpace();
    //获取泳道
    function getGroupLane() {
        var kanbanId = $('#kanbanId option:selected').val();
        var url = '<?php echo $this->createLink('kanban', 'ajaxGetLane')?>'
        $.post(url,{kanbanId:kanbanId},function (res) {
            $('#laneId').next().remove();
            $('#laneId').replaceWith(res);
            $('#laneId').chosen();
        })
    }
    $(".closee").click(function () {
        parent.$.closeModal(null, 'this');
    })
</script>
