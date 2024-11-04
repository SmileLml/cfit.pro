function checkSubmit(obj){
  if(confirm('请确认评审问题/意见已通过线下反馈给清总?')){
      var id = $(obj).attr('node-val');
      $('#submit_'+id).click();
  }else {
      return false;
  }
}