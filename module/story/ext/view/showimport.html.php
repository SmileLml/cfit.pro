<?php include '../../../common/view/header.html.php';?>
<?php if(isset($suhosinInfo)):?>
<div class='alert alert-info'><?php echo $suhosinInfo?></div>
<?php elseif(empty($maxImport) and $allCount > $this->config->file->maxImport):?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->story->import;?></h2>
  </div>
  <p><?php echo sprintf($lang->file->importSummary, $allCount, html::input('maxImport', $config->file->maxImport, "style='width:50px'"), ceil($allCount / $config->file->maxImport));?></p>
  <p><?php echo html::commonButton($lang->import, "id='import'", 'btn btn-primary');?></p>
</div>
<script>
$(function()
{
    $('#maxImport').keyup(function()
    {
        if(parseInt($('#maxImport').val())) $('#times').html(Math.ceil(parseInt($('#allCount').html()) / parseInt($('#maxImport').val())));
    });
    $('#import').click(function(){location.href = createLink('story', 'showImport', "productID=<?php echo $productID;?>&branch=<?php echo $branch?>&type=<?php echo $type?>&pageID=1&maxImport=" + $('#maxImport').val())})
});
</script>
<?php else:?>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->story->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post'>
    <table class='table table-form' id='showData'>
      <thead>
        <tr>
          <th class='w-60px'><?php echo $lang->story->id?></th>
          <th class='w-150px'><?php echo $lang->story->title?></th>
          <?php if(!empty($branches)):?>
          <th class='w-150px'><?php echo $lang->story->branch?></th>
          <?php endif;?>
          <th class='w-150px'><?php echo $lang->story->module?></th>
          <th class='w-150px'><?php echo $lang->story->plan?></th>
          <th class='w-100px'><?php echo $lang->story->source?></th>
          <th class='w-100px'><?php echo $lang->story->sourceNote?></th>
          <th class='w-70px'><?php echo $lang->story->pri?></th>
          <th class='w-80px'><?php echo $lang->story->estimate?></th>
          <th class='w-80px'><?php echo $lang->story->keywords?></th>
          <th><?php echo $lang->story->spec?></th>
          <th><?php echo $lang->story->verify?></th>
          <?php if(!$this->story->checkForceReview()):?>
          <th class='w-80px'><?php echo $lang->story->needNotReview;?></th>
          <?php endif;?>
          <?php if(!empty($appendFields)):?>
          <?php foreach($appendFields as $appendField):?>
          <th class='w-100px'><?php echo $lang->story->{$appendField->field}?></th>
          <?php endforeach;?>
          <?php endif;?>
        </tr>
      </thead>
      <tbody>
        <?php
        $insert = true;
        $addID  = 1;
        ?>
        <?php foreach($storyData as $key => $story):?>
        <tr class='text-top'>
          <td>
            <?php
            if(!empty($story->id))
            {
                echo $story->id . html::hidden("id[$key]", $story->id);
                $insert = false;
            }
            else
            {
                echo $addID++ . " <sub style='vertical-align:sub;color:gray'>{$lang->story->new}</sub>";
            }
            echo html::hidden("product[$key]", $productID);
            ?>
          </td>
          <td><?php echo html::input("title[$key]", htmlspecialchars($story->title, ENT_QUOTES), "class='form-control'")?></td>
          <?php if(!empty($branches)):?>
          <td style='overflow:visible'><?php echo html::select("branch[$key]", $branches, !empty($story->branch) ? $story->branch : ((!empty($story->id) and isset($stories[$story->id])) ? $stories[$story->id]->branch : ''), "class='form-control chosen'")?></td>
          <?php endif;?>
          <td style='overflow:visible'><?php echo html::select("module[$key]", $modules, !empty($story->module) ? $story->module : ((!empty($story->id) and isset($stories[$story->id])) ? $stories[$story->id]->module : ''), "class='form-control chosen'")?></td>
          <td style='overflow:visible'><?php echo html::select("plan[$key]", $plans, !empty($story->plan) ? $story->plan : ((!empty($story->id) and isset($stories[$story->id])) ? $stories[$story->id]->plan : ''), "class='form-control chosen'")?></td>
          <td><?php echo html::select("source[$key]", $lang->story->sourceList, !empty($story->source) ? $story->source : ((!empty($story->id) and isset($stories[$story->id])) ? $stories[$story->id]->source : ''), "class='form-control'")?></td>
          <td><?php echo html::input("sourceNote[$key]", !empty($story->sourceNote) ? $story->sourceNote : ((!empty($story->id) and isset($stories[$story->id])) ? $stories[$story->id]->sourceNote : ''), "class='form-control'")?></td>
          <td><?php echo html::select("pri[$key]", $lang->story->priList, !empty($story->pri) ? $story->pri : ((!empty($story->id) and isset($stories[$story->id])) ? $stories[$story->id]->pri : ''), "class='form-control'")?></td>
          <td><?php echo html::input("estimate[$key]", !empty($story->estimate) ? $story->estimate : ((!empty($story->id) and isset($stories[$story->id])) ? $stories[$story->id]->estimate : ''), "class='form-control' autocomplete='off'")?></td>
          <td><?php echo html::input("keywords[$key]", !empty($story->keywords) ? $story->keywords : ((!empty($story->id) and isset($stories[$story->id])) ? $stories[$story->id]->keywords : ''), "class='form-control'")?></td>
          <td><?php echo html::textarea("spec[$key]", $story->spec, "class='form-control' cols='35' rows='1'")?></td>
          <td><?php echo html::textarea("verify[$key]", $story->verify, "class='form-control' cols='35' rows='1'")?></td>
          <?php if(!$this->story->checkForceReview()):?>
          <td class='text-center'>
            <div class='checkbox-primary text-center'>
              <input id='needNotReview<?php echo $key;?>' name='needNotReview[<?php echo $key;?>]' value='1' type='checkbox' class='no-margin' <?php echo $needReview;?>/>
              <label for='needNotReview'></label>
            </div>
          </td>
          <?php endif;?>
          <?php if(!empty($appendFields)):?>
          <?php $this->loadModel('flow');?>
          <?php foreach($appendFields as $appendField):?>
          <td><?php echo $this->flow->buildControl($appendField, zget($story, $appendField->field, ''), "{$appendField->field}[$key]");?></td>
          <?php endforeach;?>
          <?php endif;?>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='<?php echo !empty($branches) ? 11 : 10;?>' class='text-center form-actions'>
            <?php
            $submitText = $isEndPage ? $this->lang->save : $this->lang->file->saveAndNext;
            if(!$insert and $dataInsert === '')
            {
                echo "<button type='button' data-toggle='modal' data-target='#importNoticeModal' class='btn btn-primary btn-wide'>{$submitText}</button>";
            }
            else
            {
                echo html::submitButton($submitText);
                if($dataInsert !== '') echo html::hidden('insert', $dataInsert);
            }
            echo html::hidden('isEndPage', $isEndPage ? 1 : 0);
            echo html::hidden('pagerID', $pagerID);
            echo ' &nbsp; ' . html::backButton();
            echo ' &nbsp; ' . sprintf($lang->file->importPager, $allCount, $pagerID, $allPager);
            ?>
          </td>
        </tr>
      </tfoot>
    </table>
    <?php if(!$insert and $dataInsert === '') include '../../../common/view/noticeimport.html.php';?>
  </form>
</div>
<?php endif;?>
<?php echo js::set('storyType', $type);?>
<script>
$(function()
{
    $.fixedTableHead('#showData');
});
</script>
<?php include '../../../common/view/footer.html.php';?>
