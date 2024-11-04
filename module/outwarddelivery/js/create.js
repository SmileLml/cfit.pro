function submitBtn() {
    var implementationForm = $('#implementationForm option:selected').val();
    var CBPprojectId = $('#CBPprojectId option:selected').val();
    if (implementationForm == 'project' && CBPprojectId == '') {
        bootbox.confirm(confirmMsg, function (result){
            if (result){
                checkDefects();
            }
        });
    }else{
        checkDefects();
    }
}
function checkDefects() {
    var defects = new Array();
    var items = new Array();
    leaveDefectitems = $("#leaveDefect option:selected");
    for(var i = 0;i<leaveDefectitems.length;i++) {
        defects.push(leaveDefectitems[i].value);
    }
    fixDefectitems = $("#fixDefect option:selected");
    for(var i = 0;i<fixDefectitems.length;i++) {
        defects.push(fixDefectitems[i].value);
    }

    $.get(createLink('outwarddelivery', 'ajaxCheckDefects', 'defects=' + defects), function(data)
    {
        var data = JSON.parse(data)
        if(data.relatedId)
        {
            cfm = confirm(data.msg);
            if(cfm == true) {
                $.get(createLink('outwarddelivery', 'ajaxUnbinkDefect', "defectIds=" + defects), function(data){
                    submitData();
                });
            }else{
                return false;
            }
        }else {
            submitData();
        }
    });
}
var submitobject = new $.zui.ModalTrigger(
    {
        type: 'iframe',
        waittime: 2000
    });
function submitData() {
    var formValue = {};
    // var x = $("#dataform").serializeArray()
    // $.each(x,function(i,field){
    //     formValue[field.name] = field.value
    // })
    var responseid = $("#responseid").val();
    var url = '';
    if (parseInt(responseid) >0 ){
        //编辑
        url = createLink('outwarddelivery', 'edit', 'id='+responseid+'&source=create')
    }else{
        //添加
        url = createLink('outwarddelivery', 'create')
    }
    var abnormalId = $("#abnormalCode option:selected").val()
    if (abnormalId != ''){
        $("#problemId").removeAttr('disabled');
        $("#demandId").removeAttr('disabled');
        $("#secondorderId").removeAttr('disabled');
    }
    var formdata = $("#dataform").serialize();
    $.post(url,formdata,function (response) {
        var submitButton = $(formID).find('.submitBtn')
        var formID = "#dataform"
        if (response.result == 'success'){
            if ($("[name='issubmit']").val() == 'save'){
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
            if (response.id != '' && $("#responseid").val() == ''){
                $("#responseid").val(response.id)
            }
            if (response.iframeUrl != '' && response.iframeUrl != undefined){
                submitobject.show({url:response.iframeUrl})
            }
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
                // if (!document.getElementById(key)){
                    alertMsg += value + ' ';
                // }
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
                $("[name='"+key+"']").css('border-color','rgb(255, 93, 93) !important');
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
        }
    },'json')
}