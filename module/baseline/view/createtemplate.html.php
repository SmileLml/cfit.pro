<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php include '../../common/view/markdown.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->baseline->createTemplate;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-110px'><?php echo $lang->baseline->templateType;?></th>
            <td><?php echo html::select('templateType', $lang->baseline->templateTypeList, '', "class='form-control chosen'");?></td>
            <td></td>
          </tr>
          <tr>
            <th class='w-110px'><?php echo $lang->baseline->templateTitle;?></th>
            <td><?php echo html::input('title', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <th class='w-110px'><?php echo $lang->baseline->docType;?></th>
            <td><?php echo html::radio('type', $lang->baseline->docTypeList, 'html', "onchange=toggleEditor(this.value)");?></td>
          </tr>
          <tr id='contentBox'>
            <th class='w-110px'><?php echo $lang->baseline->docContent;?></th>
            <td colspan='2'>
              <div class='contenthtml'><?php echo html::textarea('content', '', "style='width:100%;height:200px'");?></div>
              <div class='contentmarkdown hidden'><?php echo html::textarea('contentMarkdown', '', "style='width:100%;height:200px'");?></div>
              <?php echo html::hidden('contentType', 'html');?>
            </td>
          </tr>
          <tr id='urlBox' class='hidden'>
            <th><?php echo $lang->baseline->url;?></th>
            <td colspan='2'><?php echo html::input('url', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
              <?php echo html::submitButton() . ' ' . html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>

<script>
function toggleEditor(type)
{
    if(type == 'html')
    {   
        $('#contentBox').removeClass('hidden');
        $('#urlBox').addClass('hidden');
        $('.contenthtml').removeClass('hidden');
        $('.contentmarkdown').addClass('hidden');
    }   
    else if(type == 'markdown')
    {   
        $('#contentBox').removeClass('hidden');
        $('#urlBox').addClass('hidden');
        $('.contenthtml').addClass('hidden');
        $('.contentmarkdown').removeClass('hidden');
    }   
    else if(type == 'url')
    {   
        $('#contentBox').addClass('hidden');
        $('#urlBox').removeClass('hidden');
    }   
    else if(type == 'book')
    {   
        $('#contentBox').addClass('hidden');
        $('#urlBox').addClass('hidden');
    }   
    $('#contentType').val(type);
    return false;
}

$('#templateType').change(function()
{
    var text = $(this).find("option:selected").text();
    $('#title').val(text);
})
</script>
<?php include '../../common/view/footer.html.php';?>
