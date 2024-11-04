function addItem(clickedButton)
{
    $(clickedButton).parent().parent().after(itemRow);
    var newItem =  $(clickedButton).parent().parent().after();
    console.log(newItem);
    //判断是否有某标签
    if($('#belongPlatformTable').find('.extendInfo_productmanager').length > 0){
        $('#belongPlatformTable').find('.extendInfo_productmanager').attr('class','form-control chosen extendInfoRow extendInfo_productmanager');
        $('#belongPlatformTable').find('.extendInfo_productmanager').chosen();
        $('#belongPlatformTable').find('.extendInfo_productmanager').removeClass('extendInfo_productmanager');
    }
}

function delItem(clickedButton)
{
    $(clickedButton).parent().parent().remove();
}

$(function()
{
    //在产品计划页面时 产品tab高亮 （产品计划tab已在页面隐藏）
    if(module == 'productplan'){
        $('#' + 'product' + 'Tab').addClass('btn-active-text');
    }
    $('#' + module + 'Tab').addClass('btn-active-text');
    $('#' + field + 'Tab').addClass('active');
})

$('[name*=unitList]').change(function()
{
    var defaultCurrency = $('#defaultCurrency').val();
    $('#defaultCurrency').empty().append('<option></option>');
    $('[name*=unitList]').each(function()
    {
        if($(this).prop('checked'))
        {
            var text     = $(this).parent().html();
            var firstStr = $(this).val() + '">';

            text = text.substring(text.lastIndexOf(firstStr) + firstStr.length, text.lastIndexOf('<'));
            $('#defaultCurrency').append("<option value='" + $(this).val() + "'>" + text + '</option>');
                                                                                }
    });

     $('#defaultCurrency').val(defaultCurrency);
     $("#defaultCurrency").trigger("chosen:updated");
});

$('[name*=unitList]').change();

$('[name*=roleList]').change(function()
{
    var defaultRole = $('#defaultRole').val();
    $('#defaultRole').empty().append('<option></option>');
    $('[name*=roleList]').each(function()
    {
        if($(this).prop('checked'))
        {
            var text     = $(this).parent().html();
            var firstStr = $(this).val() + '">';

            text = text.substring(text.lastIndexOf(firstStr) + firstStr.length, text.lastIndexOf('<'));
            $('#defaultRole').append("<option value='" + $(this).val() + "'>" + text + '</option>');
                                                                                }
    });

     $('#defaultRole').val(defaultRole);
     $("#defaultRole").trigger("chosen:updated");
});

$('[name*=roleList]').change();
