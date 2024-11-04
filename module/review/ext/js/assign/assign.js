/**
 * 选择部门时，抄送人同步负责人多
 * @param deptId
 */
function ajaxGetManage(deptId){

    var arrays =[];
    $('[name="depts[]"]:checked').each(function (){
        arrays.push($(this).val());
    })

    $.get(createLink('review', 'ajaxgetmanagers', "deptId=" + arrays), function(data)
    {
        $('#mailto_chosen').remove();
        $('#mailto').replaceWith(data);
        $('#mailto').chosen();
        $('#mailtoLabel').remove();
    });
}
$('#type').change(function()
{
    var Type = $(this).val();
    //部门评审时,将外部专家隐去----需求收集1284
    if(Type=='dept'){
        $('#reviewedBy').closest('tr').addClass('hidden');
        $('#outside').closest('tr').addClass('hidden');
    }else {
        $('#reviewedBy').removeClass('hidden');
        $('#outside').removeClass('hidden');
    }

});

/**
 评审主席变化时，抄送人跟着一起变化
 * @param reviewId
 */
function ajaxgetmailto(reviewId){
    var  reviewId =$(".label-id")[0].textContent;
    $.get(createLink('review', 'ajaxgetmailto', "reviewId=" + reviewId), function(data)
    {
        $('#mailto_chosen').remove();
        $('#mailto').replaceWith(data);
        $('#mailto').chosen();
        $('#mailtoLabel').remove();
        $('#mailto').nextAll()[1] .remove();
    });
}




