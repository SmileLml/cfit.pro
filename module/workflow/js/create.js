$(document).ready(function()
{
    $('#app').change();

    $.setAjaxForm('#createForm', function(response)
    {
        if(response.result == 'success')
        {
            $('#triggerModal .close').click();

            bootbox.dialog(
            {
                title: '&nbsp;',
                message: createTips,
                buttons:
                {
                    no:
                    {
                        label: notNow,
                        className: 'btn-secondary',
                        callback: function(){location.reload();}
                    },
                    yes:
                    {
                        label: toDesign,
                        className: 'btn-primary',
                        callback: function(){location.href = createLink('workflow', 'flowchart', 'module=' + response.module);}
                    }
                }
            });
        }
    });
});
