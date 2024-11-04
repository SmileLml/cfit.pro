var submitobject = new $.zui.ModalTrigger(
    {
        // width: '80%',
        type: 'iframe',
        waittime: 2000
    });
function submitData() {
    var status = $("#status").val();
    var id = $("#id").val();
    var abnormalId = $("#abnormalCode option:selected").val()
    if (abnormalId != ''){
        $("#problemId").removeAttr('disabled');
        $("#demandId").removeAttr('disabled');
        $("#secondorderId").removeAttr('disabled');
    }
    var formdata = $("#dataform").serialize();
    $.post(createLink('modify', 'edit', 'id='+id),formdata,function (response) {
        var submitButton = $(formID).find('.submitBtn')
        var formID = "#dataform"
        if (response.result == 'success' && $("[name='issubmit']").val() == 'submit' && response.iframeUrl != '' && response.iframeUrl != undefined){
            submitobject.show({url:response.iframeUrl})
        }
        if ($("[name='issubmit']").val() == 'save' && response.result == 'success'){
            var submitButton = $(formID).find('.saveBtn');
            submitButton.popover($.extend(
                {
                    trigger: 'manual',
                    content: response.message,
                    tipClass: 'popover-success popover-form-result',
                    placement: response.placement ? response.placement : 'right'
                }, submitButton.data())).popover('show');
            setTimeout(function(){
                submitButton.popover('destroy')
                var reloadUrl = response.locate == 'reload' ? location.href : response.locate;
                location.href = reloadUrl;
            }, 2000);
        }
        if (response.result == 'fail'){
            // if (abnormalId != ''){
            //     $("#problemId").attr('disabled','true');
            //     $("#demandId").attr('disabled','true');
            // }
            var alertMsg = '';
            $.each(response.message, function(key, value)
            {
                /* Define the id of the error objecjt and it's label. */
                var errorOBJ   = '#' + key;
                var errorLabel = key + 'Label';

                /* Create the error message. */
                var errorMSG = '<span id="' + errorLabel + '" for="' + key  + '"  class="text-error red">';
                if($.type(value) == 'string')
                {
                    errorMSG += value;
                }
                else
                {
                    $.each(value, function(subKey, subValue)
                    {
                        errorMSG += subKey != value.length - 1 ? subValue.replace(/[\。|\.]/, ';') : subValue;
                    })
                }
                if (!document.getElementById(key)){
                    alertMsg += value + ' ';
                }
                errorMSG += '</span>';

                /* Append error message, set style and set the focus events. */
                $('#' + errorLabel).remove();
                var $errorOBJ = $(formID).find(errorOBJ);
                if($errorOBJ.closest('.input-group').length > 0)
                {
                    $errorOBJ.closest('.input-group').after(errorMSG)
                }
                else
                {
                    $errorOBJ.parent().append(errorMSG);
                }
                $errorOBJ.css('margin-bottom', 0);
                $errorOBJ.css('border-color','#ff5d5d')
                $errorOBJ.css('box-shadow','0 0 6px #ffc3c3')
                $errorOBJ.change(function()
                {
                    $errorOBJ.css('margin-bottom', 0);
                    $errorOBJ.css('border-color','')
                    $('#' + errorLabel).remove();
                });
            })
            if (alertMsg != ''){
                $.zui.messager.danger(alertMsg);
            }
            setTimeout(function(){
                $(".saveBtn").removeAttr("disabled")
            }, 3000);

        }
    },'json')
}