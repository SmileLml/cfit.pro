$(document).ready(function(){
    $('[name="files[]"]').attr('id','files');
    getreviewer();
    getowner();
});
$('#type').change(function()
{
    var Type = $(this).val();
    $.get(createLink('review', 'ajaxGetReviewer', "type=" + Type + "&bearDept=" + bearDept), function(data)
    {
        $('#reviewer_chosen').remove();
        $('#reviewer').replaceWith(data);
        $('#reviewer').chosen();
        $('#reviewerLabel').remove();
    });
    $.get(createLink('review', 'ajaxGetOwner', "type=" + Type + "&bearDept=" + bearDept), function(data)
    {
        $('#owner_chosen').remove();
        $('#owner').replaceWith(data);
        $('#owner').chosen();
        $('#ownerLabel').remove();
    });
    //新需求 管理评审 评审专家选所有部门负责人
    $.get(createLink('review', 'ajaxGetExpert',"type=" + Type), function(data)
    {
        $('#expert_chosen').remove();
        $('#expert').replaceWith(data);
        $('#expert').chosen();
    });
    //部门评审时,将外部专家隐去----需求收集1284
    if(Type=='dept'){
        $('#reviewedBy').closest('tr').addClass('hidden');
        $('#outside').closest('tr').addClass('hidden');
    }else {
        $('#reviewedBy').removeClass('hidden');
        $('#outside').removeClass('hidden');
    }

});
$('#deadline').datetimepicker({
   
    format : 'yyyy-mm-dd',
    minView : "month"
});

function getreviewer(){
    var type = $('#type').val();
    $.get(createLink('review', 'ajaxGetReviewer', "type=" + type + "&bearDept=" + bearDept), function(data) {
        $('#reviewer_chosen').remove();
        $('#reviewer').replaceWith(data);
        $('#reviewer').val(reviewer);
        $('#reviewer').chosen();
        $('#reviewerLabel').remove();
    })
}

function getowner(){
    var type = $('#type').val();
    $.get(createLink('review', 'ajaxGetOwner', "type=" + type + "&bearDept=" + bearDept), function(data) {
        $('#owner_chosen').remove();
        $('#owner').replaceWith(data);
        $('#owner').val(owner);
        $('#owner').chosen();
        $('#ownerLabel').remove();
    })
}
//新需求  bug 号8895
$('#reviewedit').submit(function () {
    var expert = $('#expert').val();
    var outside = $('#outside').val();
    var type = $('#type').val();
    if(type == 'pro'){
        if(expert == null ||expert == "" ){
            js:alert('管理评审或专家评审时，内部专家不能为空');
            return false;
        }
       /* if(outside == null || outside == ""){
            js:alert('专业评审时,评审专家(金科内部专家,研发部门,产创部,架构部等),外部人员(成方金信,CBP专家等,若不涉及写"无")不能为空');
            return false;
        }*/
    }
    //新需求 bug 9076
    if(type == 'manage'){
        if(expert == null ||expert == "" ){
            js:alert('管理评审或专家评审时，内部专家不能为空');
            return false;
        }
    }
    return  true;
});
//新需求  bug 号8902
$('#object').change(function()
{
    var title = '';
    var object = document.getElementById('object');
    for(var i = 0; i < object.length ;i++ ){
        if(object.options[i].selected == true){
            title +=  object.options[i].text + "_" ;
        }
    }

    title = mark + title;
    title = title.substr(0,title.length-1);
    $('#title').attr('value', title);
});