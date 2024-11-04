$(document).ready(function(){
    /* Change the operating system. */
    $('#osName').change(function(){
        var selectVal = $('#osName').val();
        $.get(createLink('host', 'ajaxGetOSVersion', 'field=' + selectVal), function(data)
        {
            $('select#osVersion').replaceWith(data);
            $('#osVersion_chosen').remove();
            $("select#osVersion").chosen();
        });
    });
});
