//保存不需要校验数据
// $(".saveBtn").click(function () {
//     var formValue = {};
//     var url = '';
//     //添加
//     url = createLink('osspchange', 'save')
//
//     var formdata = $("#dataform").serialize();
//     console.log(formdata)
//     console.log(url)
//     $.post(url,formdata,function (response) {
//         console.log(response)
//         var submitButton = $(formID).find('.submitBtn')
//         var formID = "#dataform"
//         if (response.result == 'success'){
//             if ($("[name='issubmit']").val() == 'save'){
//                 var submitButton = $(formID).find('.saveBtn');
//                 submitButton.popover($.extend(
//                     {
//                         trigger: 'manual',
//                         content: response.message,
//                         tipClass: 'popover-success popover-form-result',
//                         placement: response.placement ? response.placement : 'right'
//                     }, submitButton.data())).popover('show');
//             }
//         }
//         if (response.result == 'fail'){
//             var alertMsg = '';
//             $.each(response.message, function(key, value)
//             {
//                 /* Define the id of the error objecjt and it's label. */
//                 var errorOBJ   = '#' + key;
//                 var errorLabel = key + 'Label';
//
//                 /* Create the error message. */
//                 var errorMSG = '<span id="' + errorLabel + '" for="' + key  + '"  class="text-error red">';
//                 if($.type(value) == 'string')
//                 {
//                     errorMSG += value;
//                 }
//                 else
//                 {
//                     $.each(value, function(subKey, subValue)
//                     {
//                         errorMSG += subKey != value.length - 1 ? subValue.replace(/[\。|\.]/, ';') : subValue;
//                     })
//                 }
//                 if (!document.getElementById(key)){
//                     alertMsg += value + ' ';
//                 }
//                 errorMSG += '</span>';
//
//                 /* Append error message, set style and set the focus events. */
//                 $('#' + errorLabel).remove();
//                 var $errorOBJ = $(formID).find(errorOBJ);
//                 if($errorOBJ.closest('.input-group').length > 0)
//                 {
//                     $errorOBJ.closest('.input-group').after(errorMSG)
//                 }
//                 else
//                 {
//                     $errorOBJ.parent().append(errorMSG);
//                 }
//                 $("[name='"+key+"']").css('border-color','rgb(255, 93, 93) !important');
//                 $errorOBJ.css('margin-bottom', 0);
//                 $errorOBJ.css('border-color','#ff5d5d')
//                 $errorOBJ.css('box-shadow','0 0 6px #ffc3c3')
//                 $errorOBJ.change(function()
//                 {
//                     $errorOBJ.css('margin-bottom', 0);
//                     $errorOBJ.css('border-color','')
//                     $('#' + errorLabel).remove();
//                 });
//             })
//             if (alertMsg != ''){
//                 $.zui.messager.danger(alertMsg);
//             }
//         }
//     },'json')
// })