$().ready(function()
{
    $('#lastBuildBtn').click(function()
    {
        $('#name').val($(this).text()).focus();
    });
    systemveif();
    /*报工改造 去掉所属任务*/
   /* $.get(createLink('build', 'ajaxGetBuildTask', "app=" + 0 + "&projectID=" + projectID +"&product=" + 0 + "&version=" + 0), function(planList)
    {
        $('#taskName_chosen').remove();
        $('#taskName').replaceWith(planList);
        $('#taskName').chosen();
        $('#taskid').val($('#taskName').val());
        $('#taskname').val('');
    });*/
});
/**
 * Load products.
 *
 * @param  int $executionID
 * @access public
 * @return void
 */
function loadProducts(executionID)
{
    $('#product').remove();
    $('#product_chosen').remove();
    $('#branch').remove();
    $('#branch_chosen').remove();
    $('#noProduct').remove();
    $.get(createLink('product', 'ajaxGetProducts', 'executionID=' + executionID), function(data)
    {
        if(data)
        {
            if(data.indexOf("required") != -1)
            {
                $('#productBox').addClass('required');
            }
            else
            {
                $('#productBox').removeClass('required');
            }

            $('#productBox').append(data);
            $('#product').chosen();
        }
    });
}

// 新增 系统部选择
function systemveif(){
    var systemverify = $("input[type='radio']:checked").val();

        if(systemverify === '1'){
            $('.test').addClass('required');
        }else{
            $('.test').removeClass('required');
        }
    $.get(createLink('build', 'ajaxGetVerifyUser', "type=" + systemverify), function(planList)
    {
        $('#verifyUser_chosen').remove();
        $('#verifyUser').replaceWith(planList);
        $('#verifyUser').chosen();
    });
}
//获取产品版本
$("#product").change(function(){
    var product = $('#product').val();
    $.get(createLink('build', 'ajaxGetProductVersion', "product=" + product), function(planList)
    {
        $('#version_chosen').remove();
        $('#version').replaceWith(planList);
        if(product == '99999') {
            $('#version').val('1');
            $('#version').chosen();
            getversion();
        }else{
            $('#version').val(planList);
            $('#version').chosen();
            /*报工改造 去掉所属任务*/
           /* $.get(createLink('build', 'ajaxGetBuildTask', "app=" + 0 + "&projectID=" + projectID +"&product=" + 0 + "&version=" + 0), function(planList)
              {
                 $('#taskName_chosen').remove();
                 $('#taskName').replaceWith(planList);
                 $('#taskName').chosen();
                 $('#taskid').val($('#taskName').val());
                 $('#taskname').val('');
               });*/
        }

    });
    var version = $('#version').val();
    var app = $('#app').val();
    getTypeList(product,version,app,'demand');
    getTypeList(product,version,app,'problem');
    getTypeList(product,version,app,'secondorder');

});
//所属任务
function getversion(){
    var product = $('#product').val();
    var version = $('#version').val();
    var app = $('#app').val();
    getTypeList(product,version,app,'demand');
    getTypeList(product,version,app,'problem');
    getTypeList(product,version,app,'secondorder');
    if($('#demandidLabel').length > 0){
        $('#demandidLabel').remove();
    }
    /*报工改造 去掉所属任务*/
   /* $.get(createLink('build', 'ajaxGetBuildTask', "app=" + app + "&projectID=" + projectID +"&product=" + product + "&version=" + version), function(planList)
    {
        $('#taskName_chosen').remove();
        $('#taskName').replaceWith(planList);
        $('#taskName').chosen();
        if(app != ''){
            $('#taskid').val($('#taskName').val());
            $('#taskname').val($('#taskName option:selected').text());
        }else{
            $('#taskid').val($('#taskName').val());
            $('#taskname').val('');
        }
    });*/
}

//所属任务
function taskNameChange(){
    var text = $('#taskName').find("option:selected").text();
    $('#taskName').attr('title',text);
    $('#taskid').val($('#taskName').val());
    $('#taskname').val($('#taskName option:selected').text());
}

$('#app').change(function()
{
    var product = $('#product').val();
    var app = $('#app').val();
    $.get(createLink('build', 'ajaxGetAppProduct', "project=" + projectID +"&application=" + app), function(planList)
    {
            $('#product_chosen').remove();
            $('#product').replaceWith(planList);
            $('#product').chosen();
    });
    $.get(createLink('build', 'ajaxGetProductVersion', "product=" + product), function(planList)
    {
        $('#version_chosen').remove();
        $('#version').replaceWith(planList);
        if(product == '99999') {
            $('#version').val('1');
            $('#version').chosen();
            getversion();
        }else{
            $('#version').val(planList);
            $('#version').chosen();
        }

    });

    var version = $('#version').val();
    getTypeList(product,version,app,'demand');
    getTypeList(product,version,app,'problem');
    getTypeList(product,version,app,'secondorder');
    if($('#demandidLabel').length > 0){
        $('#demandidLabel').remove();
    }
   /* $.get(createLink('build', 'ajaxGetBuildTask', "app=" + 0 + "&projectID=" + projectID +"&product=" + 0 + "&version=" + 0), function(planList)
    {
        $('#taskName_chosen').remove();
        $('#taskName').replaceWith(planList);
        $('#taskName').chosen();
        $('#taskid').val($('#taskName').val());
        $('#taskname').val('');
    });*/

});

function getproductversion(){
    var product = $('#product').val();
    $.get(createLink('build', 'ajaxGetProductVersion', "product=" + product), function(planList)
    {
        $('#version_chosen').remove();
        $('#version').replaceWith(planList);
        if(product == '99999') {
            $('#version').val('1');
            $('#version').chosen();
            getversion();
        }else{
            $('#version').val(planList);
            $('#version').chosen();
        }
    });
    var version = $('#version').val();
    var app = $('#app').val();
    getTypeList(product,version,app,'demand');
    getTypeList(product,version,app,'problem');
    getTypeList(product,version,app,'secondorder');
}