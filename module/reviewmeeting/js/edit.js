$(document).ready(function(){

    $('#reviewIds').on('change', function() {
        var experts = ',';
        $("#reviewIds option:selected").each(function () {
          var reviewId = $(this).val();
          console.log(reviewId);
          $.get(createLink('review', 'ajaxgetmeetingexperts', "reviewId=" + reviewId), function(data) {
                  experts= experts+','+data;
            });
        });
        setTimeout(function (){
            $.get(createLink('review', 'ajaxsetmeetingexperts', "experts=" + experts), function(data) {
                $('#meetingPlanExport_chosen').remove();
                $('#meetingPlanExport').replaceWith(data);
                $('#meetingPlanExport').chosen();
               // $('#meetingPlanExportLabel').remove();
                if($('#meetingPlanExport').nextAll()[1]){
                    $('#meetingPlanExport').nextAll()[1] .remove();
                }
            });
            },1000);
    });
});