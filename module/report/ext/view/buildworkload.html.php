<?php include '../../../common/view/header.html.php';?>
<?php if(common::checkNotCN()):?>
<style>#conditions .col-xs {width: 126px;}</style>
<?php endif;?>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg' id='sidebar'>
    <?php include './blockreportlist.html.php';?>
  </div>
  <div class='main-col'>
    <div class='cell'>
      <div class="with-padding">
        <form method='post'>
          <div class="table-row" id='conditions'>
            <div class='w-250px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->build->appName;?></span>
                <?php echo html::select('appName', $apps, $appName,"class='form-control chosen ' ");?>
              </div>
            </div>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->build->verifyActionDate;?></span>
                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('verifyActionDate', $verifyActionDate, "class='form-control form-date' ");?></div>
              </div>
            </div>
            <div class='w-220px col-md-3 col-sm-6'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->build->verifyDealUser;?></span>
                <?php echo html::select('verifyDealUser', $users, $verifyDealUser , "class='form-control chosen'");?>
              </div>
            </div>
            <div class='col-md-3 col-sm-6'><?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?></div>
          </div>
        </form>
      </div>
    </div>
    <?php if(empty($build)):?>
    <div class="cell">
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
      </div>
    </div>
    <?php else:?>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title">
            <div class="table-row" id='conditions'>
              <div class="col-xs"><?php echo $title;?></div>
            </div>
          </div>
          <nav class="panel-actions btn-toolbar">
            <?php if(common::hasPriv('report', 'exportBuildWorkload')) echo html::a(inLink('exportBuildWorkload', array('projectID' => $projectID, 'param' => $param)), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
          </nav>
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
            <thead>
              <tr class='text-center'>
                <th class='w-30px text-left'><?php echo $lang->build->buildID;?></th>
                <th class='w-60px text-left'><?php echo $lang->project->code;?></th>
                <th class='w-160px text-left'><?php echo $lang->project->name;?></th>
                <th class="w-80px"><?php echo $lang->build->appName;?></th>
                <th class="w-60px"><?php echo $lang->build->appNameCode;?></th>
                <th class="w-80px"><?php echo $lang->build->verifyActionDate;?></th>
                <th class="w-80px"><?php echo $lang->build->verifyDealUser;?></th>
                <th class="w-80px"><?php echo $lang->build->status;?></th>
                <th class="w-80px"><?php echo $lang->build->verifyCompleteDate;?></th>
                <th class="w-80px"><?php echo $lang->build->actualVerifyUser;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($build as $item):?>
              <tr class="text-center">
                  <td class='text-left'><?php echo $item->id;?></td>
                  <td class='text-left'><?php echo $item->projectCode;?></td>
                  <td class='text-left' title="<?php echo $item->projectName;?>"><?php echo $item->projectName;?></td>
                  <td class='text-left' title="<?php echo $item->appName;?>"><?php echo $item->appName;?></td>
                  <td class='text-left'><?php echo $item->appCode;?></td>
                  <td class='text-left'><?php echo $item->verifyActionDate;?></td>
                  <td class='text-left'><?php echo zget($users,$item->verifyDealUser,'')?></td>
                  <td><?php echo zget($lang->build->changestatus,$item->status);?></td>
                  <td><?php echo $item->actualVerifyDate;?></td>
                  <td><?php echo zmget($users,$item->actualVerifyUser,'');?></td>
              </tr>
              <?php endforeach;?>

            </tbody>
          </table>
        </div>
      </div>
    <?php endif;?>
    </div>

  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
