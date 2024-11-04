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
            <div class='w-300px col-md-4 col-sm-8'>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->report->product;?></span>
                  <?php echo html::select('productId', $productList, $productId,"class='form-control chosen ' ");?>
              </div>
            </div>

            <div class='col-md-3 col-sm-6'><?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?></div>
          </div>
        </form>
      </div>
    </div>
    <?php if(empty($bugData)):?>
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
            <!--
          <nav class="panel-actions btn-toolbar">
            <?php if(common::hasPriv('report', 'exportQualityGateCheckResult')) echo html::a(inLink('exportQualityGateCheckResult', array('projectID' => $projectID, 'param' => $param)), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
          </nav>
          -->
        </div>
        <div data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
            <thead>
              <tr class='text-center'>
                <th class='w-180px' rowspan="2"><?php echo $lang->reportList->qualityGateBug->product;?></th>
                <th class='w-180px' rowspan="2"><?php echo $lang->reportList->qualityGateBug->productVersion;?></th>
                <th class='w-240px' colspan="4"><?php echo $lang->reportList->qualityGateBug->projectRangeBug;?></th>
                <th class="w-100px"  rowspan="2"><?php echo $lang->reportList->qualityGateBug->statisticsResult;?></th>
              </tr>
                  <tr class='text-center'>
                      <th class='w-60px'><?php echo $lang->reportList->qualityGateBug->childType;?></th>
                      <th class='w-60px'><?php echo $lang->reportList->qualityGateBug->severity;?></th>
                      <th class='w-40px'><?php echo $lang->reportList->qualityGateBug->bugCount;?></th>
                      <th class='w-40px'><?php echo $lang->reportList->qualityGateBug->blackBugCount;?></th>
                  </tr>
            </thead>
            <tbody>
            <?php  foreach($bugData as $productId => $productBug): //产品id
                $productBugData = $productBug['data'];
                $productCount   = $productBug['count']; //每个产品的数量
                $productVersionKeys = array_keys($productBugData);
                $firstProductVersionKey = array_shift($productVersionKeys);
                $productVersion = $firstProductVersionKey;
                $productVersionBug = $productBugData[$firstProductVersionKey];
                unset($productBugData[$firstProductVersionKey]);

                $productVersionBugData = $productVersionBug['data'];
                $productVersionCount   = $productVersionBug['count'];
                $isNotAllowPass = $productVersionBug['isNotAllowPass'];
                $childTypeKeys = array_keys($productVersionBugData); //bug二级分类
                $firstChildTypeKey = array_shift($childTypeKeys);
                $childType = $firstChildTypeKey;
                $firstChildTypeBug = $productVersionBugData[$firstChildTypeKey];
                unset($productVersionBugData[$firstChildTypeKey]);

                $childTypeBugData = $firstChildTypeBug['data'];
                $childTypeCount   = count($childTypeBugData);
                $blackBugCount    = $firstChildTypeBug['blackBugCount']; //黑名单数量
                $severityKeys = array_keys($childTypeBugData);
                //第一个问题级别
                $firstSeverityKey   = array_shift($severityKeys);
                $severity = $firstSeverityKey;
                $firstSeverityCount = $childTypeBugData[$firstSeverityKey];
                $severityCount = $firstSeverityCount;
                unset($childTypeBugData[$firstSeverityKey]);
                ?>
                <tr class="text-center">
                    <td rowspan="<?php echo $productCount;?>" title="<?php echo zget($productList, $productId);?>"><strong><?php echo zget($productList, $productId);?></strong></td>
                    <td rowspan="<?php echo $productVersionCount;?>" title="<?php echo zget($productPlanList, $productVersion);?>"><strong><?php echo zget($productPlanList, $productVersion);?></strong></td>
                    <td rowspan="<?php echo $childTypeCount;?>"><?php echo zget($childTypeList, $firstChildTypeKey);?></td>
                    <td><?php echo zget($this->lang->bug->severityList, $firstSeverityKey);?></td>
                    <td>
                        <?php if(common::hasPriv('report','qualityGateBugDetail') && $severityCount > 0): ?>
                            <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=severity&severity=$severity", '', true), $severityCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                        <?php else:?>
                            <?php echo $severityCount;?>
                        <?php endif;?>
                    </td>
                    <!--黑名单数量-->
                    <td rowspan="<?php echo $childTypeCount;?>">
                        <?php if(common::hasPriv('report','qualityGateBugDetail') && $blackBugCount > 0): ?>
                            <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=blackList&severity=null", '', true), $blackBugCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                        <?php else:?>
                            <?php echo $blackBugCount;?>
                        <?php endif;?>
                    </td>
                    <td rowspan="<?php echo $productVersionCount;?>" ><?php echo $isNotAllowPass ? '不能通过': '' ?></td>
                </tr>

                <?php
                if($childTypeBugData):
                    foreach($childTypeBugData as   $severity => $severityCount):
                        ?>
                        <tr class='text-center'>
                            <td><?php echo zget($this->lang->bug->severityList, $severity);?></td>
                            <td>
                                <?php if(common::hasPriv('report','qualityGateBugDetail')  && $severityCount > 0): ?>
                                    <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=severity&severity=$severity", '', true), $severityCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                                <?php else:?>
                                    <?php echo $severityCount;?>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                endif;
                ?>

                <?php
                if($productVersionBugData):
                    foreach($productVersionBugData as   $childType => $childTypeBug): //安全缺陷分类下的二级分类
                        $childTypeBugData = $childTypeBug['data'];
                        $childTypeCount   = count($childTypeBugData);
                        $blackBugCount    = $childTypeBug['blackBugCount']; //黑名单数量
                        $severityKeys = array_keys($childTypeBugData);
                        //第一个问题级别
                        $firstSeverityKey   = array_shift($severityKeys);
                        $severity = $firstSeverityKey;
                        $firstSeverityCount = $childTypeBugData[$firstSeverityKey];
                        $severityCount = $firstSeverityCount;
                        unset($childTypeBugData[$firstSeverityKey]);
                        ?>
                        <tr class='text-center'>
                            <td rowspan="<?php echo $childTypeCount;?>"><?php echo zget($childTypeList, $childType);?></td>
                            <td><?php echo zget($this->lang->bug->severityList, $firstSeverityKey);?></td>
                            <td>
                                <?php if(common::hasPriv('report','qualityGateBugDetail')  && $severityCount > 0): ?>
                                    <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=severity&severity=$severity", '', true), $severityCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                                <?php else:?>
                                    <?php echo $severityCount;?>
                                <?php endif;?>
                            </td>
                            <!--黑名单数量-->
                            <td rowspan="<?php echo $childTypeCount;?>">
                                <?php if(common::hasPriv('report','qualityGateBugDetail') && $blackBugCount > 0): ?>
                                    <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=blackList&severity=null", '', true), $blackBugCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                                <?php else:?>
                                    <?php echo $blackBugCount;?>
                                <?php endif;?>

                            </td>
                        </tr>
                        <?php
                        if($childTypeBugData):
                            foreach($childTypeBugData as   $severity => $severityCount):
                                ?>
                                <tr class='text-center'>
                                    <td><?php echo zget($this->lang->bug->severityList, $severity);?></td>
                                    <td>
                                        <?php if(common::hasPriv('report','qualityGateBugDetail') && $severityCount > 0): ?>
                                            <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=severity&severity=$severity", '', true), $severityCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                                        <?php else:?>
                                            <?php echo $severityCount;?>
                                        <?php endif;?>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                        endif;
                        ?>
                    <?php
                    endforeach;
                endif;
                ?>

              <?php
                if($productBugData):
                    foreach($productBugData as $productVersion => $productVersionBug): //产品版本
                    $productVersionBugData = $productVersionBug['data'];
                    $productVersionCount   = $productVersionBug['count'];
                    $isNotAllowPass = $productVersionBug['isNotAllowPass'];
                    $childTypeKeys = array_keys($productVersionBugData);
                    $firstChildTypeKey = array_shift($childTypeKeys);
                    $childType = $firstChildTypeKey;
                    $firstChildTypeBug = $productVersionBugData[$firstChildTypeKey];
                    unset($productVersionBugData[$firstChildTypeKey]);

                    $childTypeBugData = $firstChildTypeBug['data'];
                    $childTypeCount   = count($childTypeBugData);
                    $blackBugCount    = $firstChildTypeBug['blackBugCount']; //黑名单数量
                    $severityKeys = array_keys($childTypeBugData);
                    //第一个问题级别
                    $firstSeverityKey   = array_shift($severityKeys);
                    $severity = $firstSeverityKey;
                    $firstSeverityCount = $childTypeBugData[$firstSeverityKey];
                    $severityCount = $firstSeverityCount;
                    unset($childTypeBugData[$firstSeverityKey]);
                  ?>
                    <tr class="text-center">
                        <td rowspan="<?php echo $productVersionCount;?>" title="<?php echo zget($productPlanList, $productVersion);?>"><strong><?php echo zget($productPlanList, $productVersion);?></strong></td>
                        <td rowspan="<?php echo $childTypeCount;?>"><?php echo zget($childTypeList, $firstChildTypeKey);?></td>
                        <td><?php echo zget($this->lang->bug->severityList, $firstSeverityKey);?></td>
                        <td>
                            <?php if(common::hasPriv('report','qualityGateBugDetail') && $severityCount > 0): ?>
                                <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=severity&severity=$severity", '', true), $severityCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                            <?php else:?>
                                <?php echo $severityCount;?>
                            <?php endif;?>
                        </td>
                        <!--黑名单数量-->
                        <td rowspan="<?php echo $childTypeCount;?>">
                            <?php if(common::hasPriv('report','qualityGateBugDetail') && $blackBugCount > 0): ?>
                                <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=blackList&severity=null", '', true), $blackBugCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                            <?php else:?>
                                <?php echo $blackBugCount;?>
                            <?php endif;?>
                        </td>
                        <td rowspan="<?php echo $productVersionCount;?>" ><?php echo $isNotAllowPass ? '不能通过': '' ?></td>
                    </tr>

                    <?php
                    if($childTypeBugData):
                        foreach($childTypeBugData as   $severity => $severityCount):
                            ?>
                            <tr class='text-center'>
                                <td><?php echo zget($this->lang->bug->severityList, $severity);?></td>
                                <td>
                                    <?php if(common::hasPriv('report','qualityGateBugDetail')  && $severityCount > 0): ?>
                                        <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=severity&severity=$severity", '', true), $severityCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                                    <?php else:?>
                                        <?php echo $severityCount;?>
                                    <?php endif;?>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>

                      <?php
                        if($productVersionBugData):
                            foreach($productVersionBugData as   $childType => $childTypeBug): //安全缺陷分类下的二级分类
                                $childTypeBugData = $childTypeBug['data'];
                                $childTypeCount   = count($childTypeBugData);
                                $blackBugCount    = $childTypeBug['blackBugCount']; //黑名单数量
                                $severityKeys = array_keys($childTypeBugData);
                                //第一个问题级别
                                $firstSeverityKey   = array_shift($severityKeys);
                                $severity = $firstSeverityKey;
                                $firstSeverityCount = $childTypeBugData[$firstSeverityKey];
                                $severityCount = $firstSeverityCount;
                                unset($childTypeBugData[$firstSeverityKey]);
                        ?>
                      <tr class='text-center'>
                          <td rowspan="<?php echo $childTypeCount;?>"><?php echo zget($childTypeList, $childType);?></td>
                          <td><?php echo zget($this->lang->bug->severityList, $firstSeverityKey);?></td>
                          <td>
                              <?php if(common::hasPriv('report','qualityGateBugDetail')  && $severityCount > 0): ?>
                                  <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=severity&severity=$severity", '', true), $severityCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                              <?php else:?>
                                  <?php echo $severityCount;?>
                              <?php endif;?>
                          </td>
                          <!--黑名单数量-->
                          <td rowspan="<?php echo $childTypeCount;?>">
                              <?php if(common::hasPriv('report','qualityGateBugDetail') && $blackBugCount > 0): ?>
                                  <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=blackList&severity=null", '', true), $blackBugCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                              <?php else:?>
                                  <?php echo $blackBugCount;?>
                              <?php endif;?>

                          </td>
                      </tr>
                            <?php
                              if($childTypeBugData):
                                    foreach($childTypeBugData as   $severity => $severityCount):
                                        ?>
                                        <tr class='text-center'>
                                            <td><?php echo zget($this->lang->bug->severityList, $severity);?></td>
                                            <td>
                                                <?php if(common::hasPriv('report','qualityGateBugDetail') && $severityCount > 0): ?>
                                                    <?php echo html::a($this->createLink('report', 'qualityGateBugDetail', "dataSource=$bugDataSource&projectId=$projectID&productId=$productId&productVersion=$productVersion&buildId=$buildId&childType=$childType&sourceType=severity&severity=$severity", '', true), $severityCount, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?>
                                                <?php else:?>
                                                    <?php echo $severityCount;?>
                                                <?php endif;?>
                                            </td>
                                        </tr>
                            <?php
                                    endforeach;
                                endif;
                            ?>
                    <?php
                            endforeach;
                    endif;
                    ?>
              <?php
                    endforeach;
              endif;
              ?>
           <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
