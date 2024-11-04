$(document).ready(function()
{
    initChart();
});

function changeDate()
{
    var begin = $('#begin').val();
    var end   = $('#end').val();
    if(begin.indexOf('-') != -1)
    {
        var beginarray = begin.split("-");
        var begin = '';
        for(i=0 ; i < beginarray.length ; i++)
        {
            begin = begin + beginarray[i];
        }
    }
    if(end.indexOf('-') != -1)
    {
        var endarray = end.split("-");
        var end = '';
        for(i=0 ; i < endarray.length ; i++)
        {
            end = end + endarray[i];
        }
    }

    link = createLink('report', 'projectdeviation', 'begin=' + begin + '&end=' + end);
    location.href=link;
}
