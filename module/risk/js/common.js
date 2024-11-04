$(function()
    {
        $('#rate').attr('readonly', true);
        $('#pri').attr('disabled', true);
        computeIndex();
        function computeIndex()
        {   
            var impact      = $('#impact').val();
            var probability = $('#probability').val();
            var timeFrame   = $('#timeFrame').val();
            var rate        = parseInt(impact * probability * timeFrame);
            var pri         = ''; 
            var priColor    = ''; 
            if(0 < rate && rate <= 8)    pri = 'low';
            if(8 < rate && rate <= 17)   pri = 'middle';
            if(rate > 17) pri = 'high';

            if(pri == 'low')    priColor = 'pri-low';
            if(pri == 'middle') priColor = 'pri-middle';
            if(pri == 'high')   priColor = 'pri-high';

            $('#rate').val(rate);
            $('#pri').val(pri);
            $('#pri').trigger("chosen:updated")
            $('#pri').chosen();
            $('#pri').attr('disabled', true);
            $('#priValue .chosen-container-single .chosen-single>span').attr("class", priColor);
            $('input[name="pri"]').remove();
            $('#pri').after("<input type='hidden' name='pri' value='" + pri + "'/>");
        }   

        $('#impact, #probability, #timeFrame').change(function(){computeIndex()});
    })
