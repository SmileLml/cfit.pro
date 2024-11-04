<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
td p {margin-bottom: 0;}
.w-175px {width: 175px;}
.task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
.task-toggle .icon{display: inline-block; transform: rotate(90deg);}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php $browseLink = inlink('browse');?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php printf('%03d', $demandcollection->id);?></span>
      <span class="text" title='<?php echo $demandcollection->title;?>'><?php echo $demandcollection->title;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->demandcollection->title;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($demandcollection->title) ? $demandcollection->title : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->demandcollection->desc;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($demandcollection->desc) ? $demandcollection->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->demandcollection->analysis;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($demandcollection->analysis) ? $demandcollection->analysis : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
        <?php if(in_array($this->session->user->account,$this->demandcollection->getScheme('viewerList'))): ?>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->demandcollection->scheme;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($demandcollection->scheme) ? $demandcollection->scheme : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <?php endif;?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->demandcollection->fileList;?></div>
        <div class="detail-content article-content">
        <?php
        if($demandcollection->files)
        {
            echo $this->fetch('file', 'printFiles', array('files' => $demandcollection->files, 'fieldset' => 'false', 'object' => null));
        }else
        {
            echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
        }
        ?>
        </div>
      </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->demandcollection->commConfirmRecord;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($demandcollection->commConfirmRecord) && $demandcollection->commConfirmRecord != '<p><br /></p>' ? $demandcollection->commConfirmRecord : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
    </div>

    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
          <?php common::printBack($browseLink);?>
          <?php common::printIcon('demandcollection', 'edit', "demandcollectionId=$demandcollection->id", $demandcollection, 'list');
          common::printIcon('demandcollection', 'deal', "demandcollectionId=$demandcollection->id", $demandcollection, 'list','time');
          common::printIcon('demandcollection', 'confirmed', "demandcollectionId=$demandcollection->id", $demandcollection, 'list','ok', '', 'iframe', true);
          common::printIcon('demandcollection', 'closed', "demandcollectionId=$demandcollection->id", $demandcollection, 'list', 'off','', 'iframe', true);
          if (common::hasPriv('demandcollection', 'syncDemand') && $this->demandcollection->isClickable($demandcollection, 'syncDemand')){
              echo '<button type="button" class="btn" title="' . $this->lang->demandcollection->syncDemand . '" onclick="isClickable('.$demandcollection->id.', \'syncDemand\')"><i class="icon-common-suspend icon-exchange"></i></button>';
              common::printIcon('demandcollection', 'syncDemand', "id=$demandcollection->id", $demandcollection, 'list', 'exchange','', 'hidden', false, 'id=isClickable_syncDemand' . $demandcollection->id);
          }else{
              common::printIcon('demandcollection', 'syncDemand', "id=$demandcollection->id", $demandcollection, 'list', 'exchange','', '', false, 'id=isClickable_syncDemand' . $demandcollection->id);
          }
          ?>
      </div>
  </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->demandcollection->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->id;?></th>
                <td><?php echo $demandcollection->id;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->storyId;?></th>
                <td><?php echo $demandcollection->storyId;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->dept;?></th>
                <td><?php echo zget($depts,$demandcollection->dept,'')?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->submitter?></th>
                <td><?php echo zget($users,$demandcollection->submitter,'');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->Implementation?></th>
                <td><?php echo zget($depts,$demandcollection->Implementation,'')?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->priority;?></th>
                <td><?php echo $demandcollection->priority;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->type;?></th>
                <td><?php echo zget($lang->demandcollection->typeList,$demandcollection->type,'')?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->state;?></th>
                <td><?php echo zget($lang->demandcollection->statusList,$demandcollection->state,'')?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->feedbackResult;?></th>
                <td><?php echo $demandcollection->feedbackResult;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->developstate;?></th>
                <td><?php echo $demandcollection->developstate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->productmanager?></th>
                <td><?php echo zget($users,$demandcollection->productmanager,'');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->dealUser?></th>
                <td><?php echo zget($users,$demandcollection->dealuser,'');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->processingDate?></th>
                <td><?php echo $demandcollection->processingDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->handoverDate?></th>
                <td><?php echo $demandcollection->handoverDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->feedbackDate?></th>
                <td><?php echo $demandcollection->feedbackDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->scheduledDate?></th>
                <td><?php echo $demandcollection->scheduledDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->launchDate?></th>
                <td><?php echo $demandcollection->launchDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->Expected?></th>
                <td><?php echo zmget($productPlanList,$demandcollection->Expected, '', '<br/>');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->Actual?></th>
                <td><?php echo zmget($productPlanList,$demandcollection->Actual, '', '<br/>');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->Developer?></th>
                <td><?php echo zget($users,$demandcollection->Developer,'');?></td>
              </tr>
              <tr>
                  <th class='w-100px'><?php echo $lang->demandcollection->bPlatform?></th>
                  <td><?php echo zget($this->lang->demandcollection->belongPlatform,$demandcollection->belongPlatform);?></td>
              </tr>
              <tr>
                  <th class='w-100px'><?php echo $lang->demandcollection->bModel?></th>
                  <td><?php echo zget($this->demandcollection->getChildTypeList($demandcollection->belongPlatform),$demandcollection->belongModel,'');?></td>
              </tr>

              <tr>
                  <th class='w-100px'><?php echo $lang->demandcollection->product?></th>
                  <td>
                      <?php
                        if($demandcollection->product):
                            $productIds = array_filter(explode(',', $demandcollection->product));
                                foreach ($productIds as $productId):
                                    echo html::a($this->createLink('product', 'view', 'id=' . $productId, '', true), zget($productList, $productId), '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                endforeach;
                            endif;
                       ?>
                  </td>
              </tr>

              <tr>
                  <th class='w-100px'><?php echo $lang->demandcollection->correctionReason?></th>
                  <td><?php echo zget($correctionReasonList,$demandcollection->correctionReason,'');?></td>
              </tr>
              <tr>
                  <th class='w-100px'><?php echo $lang->demandcollection->commConfirmBy?></th>
                  <td><?php echo zmget($users,$demandcollection->commConfirmBy,$demandcollection->commConfirmBy);?></td>
              </tr>
              <tr>
                  <th class='w-100px'><?php echo $lang->demandcollection->demandId; ?></th>
                  <td><?php
                      if(!empty($demandcollection->demandId)){
                          echo html::a(
                                  $this->createLink('demandinside', 'view', 'id=' . $demandcollection->demandId, '', true),
                                  zget($demandList, $demandcollection->demandId),
                                  '',
                                  "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"
                              ) . '<br/>';
                      }
                      ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->demandcollection->operation;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->createBy?></th>
                <td><?php echo zget($users,$demandcollection->createBy,'');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->createDate?></th>
                <td><?php echo $demandcollection->createDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->updateBy?></th>
                <td><?php echo zget($users,$demandcollection->updateBy,'');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->updateDate?></th>
                <td><?php echo $demandcollection->updateDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->handoverBy?></th>
                <td><?php echo zget($users,$demandcollection->handoverBy,'');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->handoverDate?></th>
                <td><?php echo $demandcollection->handoverDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->processingBy?></th>
                <td><?php echo zget($users,$demandcollection->processingBy,'');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->processingDate?></th>
                <td><?php echo $demandcollection->processingDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->confirmBy?></th>
                <td><?php echo zget($users,$demandcollection->confirmBy,'');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->confirmDate?></th>
                <td><?php echo $demandcollection->confirmDate;?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->closedBy?></th>
                <td><?php echo zget($users,$demandcollection->closedBy,'');?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->demandcollection->closedDate?></th>
                <td><?php echo $demandcollection->closedDate;?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    </div>
  </div>
</div>
<script>
</script>
<?php include '../../common/view/footer.html.php';?>
