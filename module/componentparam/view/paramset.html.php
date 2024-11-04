<?php include '../../common/view/header.html.php'; ?>

<?php
$orderStr = '';

$itemRow = <<<EOT
  <tr class='text-center'>
    <td>
      <input type='text' class="form-control" autocomplete="off" value="" name="keys[]">
      <input type='hidden' value="0" name="systems[]">
    </td>
    <td>
      <input type='text' class="form-control" value="" autocomplete="off" name="values[]">
    </td>
    $orderStr
    <td class='c-actions'>
      <a href="javascript:void(0)" class='btn btn-link' onclick="addComponentItem(this)"><i class='icon-plus'></i></a>
      <a href="javascript:void(0)" class='btn btn-link' onclick="delComponentItem(this)"><i class='icon-close'></i></a>
    </td>
  </tr>
EOT;
?>
<?php js::set('itemRow', $itemRow)?>
<?php js::set('module',  $module)?>
<?php js::set('field',   $field)?>
<style>
.checkbox-primary {width: 170px; margin: 0 10px 10px 0; display: inline-block;}
</style>
<div id='mainContent' class='main-row'>
  <div class='side-col' id='sidebar'>
    <div class='cell'>
      <div class='list-group'>
        <?php
        foreach($lang->componentparam->fields as $key => $value)
        {

            echo html::a(inlink('paramset', "field=$key"), $value, '', " id='{$key}Tab'");

        }
        ?>
      </div>
    </div>
  </div>
  <div class='main-col main-content'>
    <form class="load-indicator main-form form-ajax" method='post'>
      <div class='main-header'>
        <div class='heading'>
          <strong><?php echo $lang->componentparam->paramset; ?></strong>
        </div>
      </div>


            <div class='cell'>
               <?php echo $lang->componentparam->useIllustrate;?>
            </div>


      <table class='table table-form active-disabled table-condensed mw-600px'>
        <tr class='text-center'>

            <td class='w-120px'><strong><?php echo $lang->componentparam->englishkey;?></strong></td>
            <td><strong><?php echo $lang->componentparam->englishvalue;?></strong></td>
          <?php  if($canAdd):?><th class='w-90px'></th><?php endif;?>
        </tr>
        <?php
        if($fieldList){


        foreach($fieldList as $key => $value):?>
            <?php
                $classStr = '';

                if ($field == 'workHours' && $key == 'effectiveDate'){
                    $classStr = 'form-date';
                }
            ?>
            <tr class='text-center'>
              <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
              <td style="word-break:break-all"><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
              <td>
                <?php echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "class='form-control ".$classStr."' " . (empty($key) ? 'readonly' : ''));?>
              </td>


              <?php if($canAdd):?>
              <td class='c-actions'>
                <a href="javascript:void(0)" onclick="addComponentItem(this)" class='btn btn-link'><i class='icon-plus'></i></a>
                <?php if($key !== '' && common::hasPriv('componentparam', 'delete')):?>
                <a href="javascript:void(0)" data-keys="<?php echo $key;?>" data-keys2="<?php echo $key2;?>" onclick="delComponentItem(this)" class='btn btn-link'><i class='icon-close'></i></a>
                <?php endif;?>
              </td>
              <?php endif;?>
            </tr>
        <?php endforeach;}else{
            echo $itemRow;
            ?>




          <?php
        }?>
        <tr>
          <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
          <?php
          //, 'all' => $lang->custom->allLang
          $appliedTo = array($currentLang => $lang->custom->currentLang);
          echo html::radio('lang', $appliedTo, $lang2Set);
          echo html::submitButton();
//          if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');
          ?>
          </td>
        </tr>
      </table>

      <?php if(!$canAdd):?>
      <div class='alert alert-warning alert-block'><?php echo $lang->custom->notice->canNotAdd;?></div>
      <?php endif;?>

    </form>
  </div>
</div>
<?php if($module == 'testcase' and $field == 'review'): ?>
<script>
$(function()
{
    $("input[name='needReview']").change(function()
    {
        if($(this).val() == 0)
        {
            $('#forceReview').closest('tr').removeClass('hidden');
            $('#forceNotReview').closest('tr').addClass('hidden');
        }
        else
        {
            $('#forceReview').closest('tr').addClass('hidden');
            $('#forceNotReview').closest('tr').removeClass('hidden');
        }
    })
})
</script>
<?php endif;?>

<script>
    function addComponentItem(clickedButton)
    {
        $(clickedButton).parent().parent().after(itemRow);
    }

    function delComponentItem(clickedButton)
    {
        var deleteNotice = '<?php echo $lang->componentparam->deleteNotice;?>';
        if(confirm(deleteNotice)){
            var keys = $(clickedButton).attr('data-keys');


            if(!keys){
                $(clickedButton).parent().parent().remove();
                new $.zui.Messager('<?php echo $lang->componentparam->successNotice;?>',{type:'success',close:true}).show();
                return;
            }

            var posturl = createLink("componentparam","delete");
            $.post(posturl,{"keys":keys},function(data){
               
                if(data.code == 200){
                    $(clickedButton).parent().parent().remove();
                    new $.zui.Messager(data.msg,{type:'success',close:true}).show();
                }else {
                    new $.zui.Messager(data.msg,{type:'danger',close:true}).show();
                }

            },'json');
        }

      
    }
    $(function()
    {
        //在产品计划页面时 产品tab高亮 （产品计划tab已在页面隐藏）
       
        $('#' + module + 'Tab').addClass('btn-active-text');
        $('#' + field + 'Tab').addClass('active');
    })
</script>

<?php include '../../common/view/footer.html.php';?>
