$(document).ready(function()
{
    $('#menu .container li').removeClass('active');
    if(config.requestType == 'GET')
    {
        $('#menu .container li a[href*=' + config.moduleVar + '\\=' + module + '\\&' + config.methodVar + '\\=report]').parent('li').addClass('active');
    }   
    else
    {
        $('#menu .container li a[href*=' + module + '-report-]').parent('li').addClass('active');
    }   

    var percentageOption = {scaleLabel: "<%=value%>%", tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>%",  multiTooltipTemplate: "<%if (datasetLabel){%><%=datasetLabel%>: <%}%><%= value %>%"};

    $('.text-top').each(function()
    {
        var options  = {};
        var $canvas  = $(this).find('canvas');
        var canvasId = $canvas.attr('id');
        var type     = $canvas.data('type');
        var legend   = $canvas.data('legend');
        var data     = chartData[canvasId];

        if($canvas.data('displaytype') == 'percent') options = percentageOption;

        options["responsive"] = true;
        if(type == 'pie')
        {
            options["scaleShowLabels"] = true;
            options["animation"]       = false;

            var pieChart = $("#" + canvasId).pieChart(data, options);
            $(this).find('.legend').append(pieChart.generateLegend());

            var maxHeight = $(this).height();
            var css       = {'max-height': maxHeight};
            $(this).find('.legend ul').css('max-height', maxHeight - 10);
            if(maxHeight <= $(this).find('.legend ul').height() + 10) $(this).find('.legend ul').addClass('scrollbar-hover').css('overflow-y', 'scroll');
            $('.legend .pie-legend li').each(function()
            {
                $(this).attr('title', $(this).text()); 
            });
        }
        else
        {
            var chart = {};
            if(type == 'line') chart = $("#" + canvasId).lineChart(data, options);
            if(type == 'bar')
            {
                options["barValueSpacing"] = 20;
                chart = $("#" + canvasId).barChart(data, options);
            }

            if(legend) $(this).find('.legend').append(chart.generateLegend());
        }
    });
});
