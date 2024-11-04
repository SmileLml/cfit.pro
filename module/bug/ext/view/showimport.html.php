<?php include '../../../common/view/header.html.php';?>
<style>.chosen-container-multi .chosen-choices li.search-choice{width: 100%;}</style>
<?php if(isset($suhosinInfo)):?>
<div class='alert alert-info'><?php echo $suhosinInfo?></div>
<?php elseif(empty($maxImport) and $allCount > $this->config->file->maxImport):?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->bug->import;?></h2>
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
    $('#import').click(function(){location.href = createLink('bug', 'showImport', "applicationID=<?php echo $applicationID;?>&productID=<?php echo $productID;?>&branch=<?php echo $branch?>&pageID=1&maxImport=" + $('#maxImport').val())})
});
</script>
<?php else:?>
<?php js::set('requiredFields', $requiredFields);?>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->bug->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post'>
    <table class='table table-form' id='showData'>
      <thead>
        <tr>
          <th class='w-50px'><?php echo $lang->idAB?></th>

          <th class='w-120px' id='product'>    <?php echo $lang->bug->product;?></th>
          <th class='w-120px' id='linkPlan'>    <?php echo $lang->bug->linkPlan;?></th>
          <?php if($this->app->openApp != 'project'):?>
          <th class='w-120px' id='project'>    <?php echo $lang->bug->project;?></th>
          <?php endif;?>
          <th class='w-120px' id='module'>     <?php echo $lang->bug->module?></th>
          <th class='w-150px' id='title'><?php echo $lang->bug->title?></th>
          <th                 id='steps'>      <?php echo $lang->bug->steps?></th>
          <th class='w-80px'><?php echo $lang->bug->type;?></th>
          <th class='w-80px'><?php echo $lang->bug->childType;?></th>
          <th class='w-80px'><?php echo $lang->bug->severity?></th>
          <th class='w-70px'  id='pri'>        <?php echo $lang->bug->pri?></th>
          <th class='w-80px'  id='keywords'>   <?php echo $lang->bug->keywords?></th>
          <th class='w-120px' id='openedbuild'><?php echo $lang->bug->openedBuild?></th>
          <th class='w-100px' id='deadline'>   <?php echo $lang->bug->deadline?></th>
          <th class='w-120px' id='story'>  <?php echo $lang->bug->story;?></th>
          <th class='w-160px'><?php echo $lang->bug->lblSystemBrowserAndHardware?></th>
          <?php if(!empty($appendFields)):?>
          <?php foreach($appendFields as $appendField):?>
          <th class='w-100px'><?php echo $lang->bug->{$appendField->field}?></th>
          <?php endforeach;?>
          <?php endif;?>
        </tr>
      </thead>
      <tbody>
        <?php
        $insert = true;
        $addID  = 1;
        ?>
        <?php foreach($bugData as $key => $bug):?>
        <tr class='text-top'>
          <td>
            <?php
            if(!empty($bug->id))
            {
                echo '#' . $bug->id . html::hidden("id[$key]", $bug->id);
                $insert = false;
            }
            else
            {
                echo $addID++ . " <sub style='vertical-align:sub;color:gray'>{$lang->bug->new}</sub>";
            }
            ?>
          </td>

          <td style='overflow:visible'>
          <?php
          echo html::hidden("applicationID[$key]", $applicationID);
          if($this->app->openApp == 'project') echo html::hidden("project[$key]", $bug->project);
          echo html::select("product[$key]", $products, $bug->product, "class='form-control chosen control-product' onchange='loadProductOnlist(this,$branch)'");
          ?>
          </td>
          
          <td style='overflow:visible'>
          <?php
          echo html::select("linkPlan[$key][]", $linkPlan, $bug->linkPlan, "class='form-control chosen control-plan' multiple=multiple  disabled=disabled");
          ?>
          </td>

          <?php if($this->app->openApp != 'project'):?>
          <td style='overflow:visible'>
          <?php
          echo html::select("project[$key]", $projects, $bug->project, "class='form-control chosen'");
          ?>
          </td>
          <?php endif;?>

          <td style='overflow:visible'><?php echo html::select("module[$key]", $modules, $bug->module, "class='form-control chosen'")?></td>
          <td><?php echo html::input("title[$key]", htmlspecialchars($bug->title, ENT_QUOTES), "class='form-control'")?></td>
          <td><?php echo html::textarea("steps[$key]", $bug->steps, "class='form-control bug-area'")?></td>
          <td>
            <?php echo html::select("type[$key]", $lang->bug->typeList, $bug->type, "class='form-control' onchange='loadChildTypeList(this.value, $key, this)'");?>
          </td>
          <td>
            <?php echo html::select("childTypes[$key]", $bug->type ? $parentChildTypeList[$bug->type] : $bug->type, $bug->childType, "class='form-control'");?>
          </td>
          <td>
            <?php echo html::select("severity[$key]", $lang->bug->severityList, $bug->severity, "class='form-control'");?>
          </td>
          <td><?php echo html::select("pri[$key]", $lang->bug->priList, $bug->pri, "class='form-control'")?></td>
          <td><?php echo html::input("keywords[$key]", $bug->keywords, "class='form-control'")?></td>
          <?php
          if(!empty($bug->openedBuild) and !array_key_exists($bug->openedBuild, $builds))
          {
              $openedBuilds     = explode(';', $bug->openedBuild);
              $bug->openedBuild = array();
              foreach($openedBuilds as $openedBuild)
              {
                  $openedBuild = trim($openedBuild);
                  if($openedBuild == 'trunk')
                  {
                      $bug->openedBuild[] = $openedBuild;
                      continue;
                  }
                  if(isset($flipBuilds[$openedBuild])) $bug->openedBuild[] = $flipBuilds[$openedBuild];
              }
              $bug->openedBuild = join(',', $bug->openedBuild);
          }
          ?>
          <td style='overflow:visible'><?php echo html::select("openedBuild[$key][]", $builds, $bug->openedBuild, "multiple=multiple class='form-control chosen control-build' onchange='loadPlansOnList(this,{$branch})'");?></td>
          <td><?php echo html::input("deadline[$key]", preg_match('/[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/', $bug->deadline) ? $bug->deadline : '', "class='form-control form-date'");?></td>
          <td style='overflow:visible'>
            <?php echo html::select("story[$key]", $stories, $bug->story, "class='form-control chosen'")?>
          </td>
          <td>
            <div class='input-group'>
              <?php echo html::select("os[$key]", $lang->bug->osList, $bug->os, "class='form-control'");?>
              <span class='input-group-addon'></span>
              <?php echo html::select("browser[$key]", $lang->bug->browserList, $bug->browser, "class='form-control'");?>
            </div>
          </td>
          <?php if(!empty($appendFields)):?>
          <?php $this->loadModel('flow');?>
          <?php foreach($appendFields as $appendField):?>
          <td><?php echo $this->flow->buildControl($appendField, zget($bug, $appendField->field, ''), "{$appendField->field}[$key]");?></td>
          <?php endforeach;?>
          <?php endif;?>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='<?php echo !empty($branches) ? 12 : 11;?>' class='text-center form-actions'>
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
            echo html::backButton();
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
<script>
$(function()
{
    $.fixedTableHead('#showData');
    $("#showData th").each(function()
    {
        if(requiredFields.indexOf(this.id) !== -1) $("#" + this.id).addClass('required');
    });
});

function loadChildTypeList(type, currentIndex)
{
    var link = createLink('bug', 'ajaxGetChildTypeList', 'type=' + type + '&currentIndex=' + currentIndex);
    $.get(link, function(data)
    {
        if(data)
        {
            $('#childTypes' + currentIndex).replaceWith(data);
            //$('#childTypes' + currentIndex + '_chosen').remove();
            //$('#childTypes' + currentIndex).chosen();
        }
    });
}
</script>
<?php include '../../../common/view/footer.html.php';?>
