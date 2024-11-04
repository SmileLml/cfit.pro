function seturl(project,id){
      $.ajaxSettings.async = false;
      $.post(createLink('demand', 'ajaxGetProjectId', "project="+ project,''), function(data)
      {

      });
      $.ajaxSettings.async = true;
      var taskurl = createLink('task', 'view', 'id=' + id);
      $('#demandtaskurl').attr('href',taskurl);
      $('#demandtaskurl')[0].click();
}
 function addurl(project,id){
      $.ajaxSettings.async = false;
      $.post(createLink('demand', 'ajaxGetProjectSession', "project="+ project,''), function(data)
      {

      });
      $.ajaxSettings.async = true;
      var releaseurl = createLink('projectrelease', 'view', 'id=' + id);
      $('#demandreleaseurl').attr('href',releaseurl);
      $('#demandreleaseurl')[0].click();
}

 function newurl(project,id){
      $.ajaxSettings.async = false;
      $.post(createLink('demand', 'ajaxGetProjectBuild', "project="+ project,''), function(data)
      {

      });
      $.ajaxSettings.async = true;
      var buildurl = createLink('build', 'view', 'id=' + id);
      $('#demandbuildurl').attr('href',buildurl);
      $('#demandbuildurl')[0].click();
}

