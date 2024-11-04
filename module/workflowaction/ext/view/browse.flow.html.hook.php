<?php if($flow->buildin):?>
<style>
#actionListTable thead tr th:nth-of-type(2){width:75px !important;}
#actionListTable thead tr th:nth-of-type(3){width:75px !important;}
#actionListTable thead tr th:nth-of-type(4){width:50px !important;}
#actionListTable thead tr th:nth-of-type(5){width:80px !important;}
</style>
<script>
$(function()
{
    $('#actionList tr[data-buildin=1][data-extensiontype=override] a.condition').addClass('disabled');
})
</script>
<?php endif;?>
<script>
$('a[disabled=disabled]').addClass('disabled');

$(".select-action").each(function(){
    $(this).attr("title",$(this).text());
});

$(function()
{
    var html = $('#actionList tr td.actions a.edit:first').html();
    $('#actionList tr td.actions a.edit').attr('title', html).addClass('btn').html("<i class='icon icon-edit'></i>");

    var html = $('#actionList tr td.actions a.layout:first').html();
    $('#actionList tr td.actions a.layout').attr('title', html).addClass('btn').html("<i class='icon icon-layout'></i>");

    var html = $('#actionList tr td.actions a.condition:first').html();
    $('#actionList tr td.actions a.condition').attr('title', html).addClass('btn').html("<i class='icon icon-trigger'></i>");

    var html = $('#actionList tr td.actions a.verification:first').html();
    $('#actionList tr td.actions a.verification').attr('title', html).addClass('btn').html("<i class='icon icon-audit'></i>");

    var html = $('#actionList tr td.actions a.moreActions:first').text();
    $('#actionList tr td.actions a.moreActions').attr('title', html).addClass('btn').html("<i class='icon icon-more-circle'></i><span class='caret'></span>");
})
</script>
