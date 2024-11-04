function seturl(project,id){
     $.ajaxSettings.async = false;
     $.post(createLink('problem', 'ajaxGetProjectId', "project="+ project,''), function(data)
     {

     });
     $.ajaxSettings.async = true;
     var taskurl = createLink('task', 'view', 'id=' + id);
     $('#taskurl').attr('href',taskurl);
     $('#taskurl')[0].click();
}
 function addurl(project,id){
     $.ajaxSettings.async = false;
     $.post(createLink('problem', 'ajaxGetProjectSession', "project="+ project,''), function(data)
     {

     });
     $.ajaxSettings.async = true;
     var releaseurl = createLink('projectrelease', 'view', 'id=' + id);
     $('#problemreleaseurl').attr('href',releaseurl);
     $('#problemreleaseurl')[0].click();
}
 function newurl(project,id){
      $.ajaxSettings.async = false;
      $.post(createLink('problem', 'ajaxGetProjectBuild', "project="+ project,''), function(data)
      {

      });
      $.ajaxSettings.async = true;
      var buildurl = createLink('build', 'view', 'id=' + id);
      $('#problembuildurl').attr('href',buildurl);
      $('#problembuildurl')[0].click();
}


