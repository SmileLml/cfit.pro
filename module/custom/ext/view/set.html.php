<?php
/**
 * The set view file of custom module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     custom
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php
  $oldDir = getcwd();
  chdir(dirname(dirname(dirname(__FILE__))) . '/view');
  include './header.html.php';
  chdir($oldDir);
?>
<?php
$orderStr = '';
$extendInfoStr = '';
if ($field == 'navOrderList'){
    $orderStr = '<td><input type="text" class="form-control" value="0" autocomplete="off" name="orders[]"></td>';
}
if($module == 'demandcollection' && $field == 'belongPlatform'){
    $orderStr = '<td><input type="text" class="form-control" value="0" autocomplete="off" name="orders[]"></td>';
    $extendInfoStr = '<td class="text-left">'.html::select("productmanager[]", $users,  '', "class='form-control chosen extendInfoRow extendInfo_productmanager'").'</td>';
}
$itemRow = <<<EOT
  <tr class='text-center'>
    <td>
      <input type='text' class="form-control" autocomplete="off" value="" name="keys[]">
      <input type='hidden' value="0" name="systems[]">
    </td>
    <td>
      <input type='text' class="form-control" value="" autocomplete="off" name="values[]">
    </td>$orderStr$extendInfoStr
    <td class='c-actions'>
      <a href="javascript:void(0)" class='btn btn-link' onclick="addItem(this)"><i class='icon-plus'></i></a>
      <a href="javascript:void(0)" class='btn btn-link' onclick="delItem(this)"><i class='icon-close'></i></a>
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
        foreach($lang->custom->{$module}->fields as $key => $value)
        {
            if($module == 'productplan' && $key == 'closedEdit'){ //跳转到产品选项
                echo html::a(inlink('product', ""), $value, '', " id='{$key}Tab'");
            } else {
                echo html::a(inlink('set', "module=$module&field=$key"), $value, '', " id='{$key}Tab' title='$value'");
            }
        }
        ?>
      </div>
    </div>
  </div>
  <div class='main-col main-content'>
    <form class="load-indicator main-form form-ajax" method='post'>
      <div class='main-header'>
        <div class='heading'>
          <strong><?php echo $lang->custom->object[$module] . $lang->arrow . $lang->custom->$module->fields[$field]?></strong>
        </div>
      </div>
      <?php  if($module == 'project' and $field == 'unitList'):?>
      <table class='table table-form'>
        <tr>
          <th class='<?php echo strpos($this->app->getClientLang(), 'zh') === false ? 'w-120px' : 'w-70px';?> text-left'><?php echo $lang->custom->project->currencySetting;?></th>
        </tr>
        <tr>
          <td colspan='5'><?php echo html::checkbox('unitList', $lang->project->unitList, $unitList);?></td>
        </tr>
        <tr>
          <th class='text-left'><?php echo $lang->custom->project->defaultCurrency;?></th>
          <td><?php echo html::select('defaultCurrency', $lang->project->unitList, $defaultCurrency, "class='form-control chosen' required");?></td>
        </tr>
        <tr>
          <td colspan='4' class='text-center'><?php echo html::submitButton();?></td>
        </tr>
      </table>

      <?php elseif($module == 'project' && $field == 'setShWhiteList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          unset($users['']);
                          echo html::select('values[]', $users,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
              <tr>
                  <?php if($module == 'project' && $field == 'setShWhiteList'):?>
                      <td></td>
                      <td style="color: red">
                          <b><?php echo $lang->custom->project->setShWhiteListTip?></b>
                      </td>
                  <?php endif;?>
              </tr>
          </table>
      <?php elseif($module == 'project' && $field == 'projectSetList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          unset($users['']);
                          echo html::select('values[]', $users,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif(($module == 'story' or $module == 'testcase') and $field == 'review'):?>
      <table class='table table-form mw-800px'>
        <tr>
          <th class='thWidth'><?php echo $lang->custom->storyReview;?></th>
          <td><?php echo html::radio('needReview', $lang->custom->reviewList, $needReview);?></td>
          <td></td>
        </tr>
        <tr <?php if($needReview and $module == 'testcase') echo "class='hidden'"?>>
          <th><?php echo $lang->custom->forceReview;?></th>
          <td><?php echo html::select('forceReview[]', $users, $forceReview, "class='form-control chosen' multiple");?></td>
          <td style='width:300px'><?php printf($lang->custom->notice->forceReview, $lang->$module->common);?></td>
        </tr>
        <?php if($module == 'testcase'):?>
        <tr <?php if(!$needReview) echo "class='hidden'"?>>
          <th><?php echo $lang->custom->forceNotReview;?></th>
          <td><?php echo html::select('forceNotReview[]', $users, $forceNotReview, "class='form-control chosen' multiple");?></td>
          <td style='width:300px'><?php printf($lang->custom->notice->forceNotReview, $lang->$module->common);?></td>
        </tr>
        <?php endif;?>
        <tr>
          <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
        </tr>
      </table>
      <?php elseif($module == 'task' and $field == 'hours'):?>
      <table class='table table-form mw-600px'>
        <tr>
          <th class='w-150px'><?php echo $lang->custom->workingHours;?></th>
          <td><?php echo html::input('defaultWorkhours', $workhours, "class='form-control w-80px'");?></td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->custom->weekend;?></th>
          <td><?php echo html::radio('weekend', $lang->custom->weekendList, $weekend);?></td>
        </tr>
        <tr>
          <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
        </tr>
      </table>
      <?php elseif($module == 'task' and $field == 'workThreshold'):?>
      <table class='table table-form mw-600px'>
        <tr>
          <th class='w-150px'><?php echo $lang->custom->workThreshold;?></th>
          <td><?php echo html::input('workThreshold', $workThreshold, "class='form-control w-80px'");?></td>
          <td></td>
        </tr>
        <tr>
          <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
        </tr>
      </table>
      <?php elseif($module == 'task' and $field == 'workBuffer'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->custom->workBuffer;?></th>
                  <td><?php echo html::input('workBuffer', $workBuffer, "class='form-control'");?></td>
                  <td><?php echo $lang->custom->workBufferTip?></td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>

      <?php elseif($module == 'task' && ($field == 'stageList' or $field == 'jobList' or $field == 'stageSecondList' or $field == 'threeTaskList')): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-200px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>

              </tr>
              <?php  foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          echo html::input('values[]',  $value, "class='form-control'");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');

                      ?>
                  </td>
              </tr>
          </table>

      <?php elseif($module == 'localesupport' && $field == 'projectList' ): ?>
      <span style="color:red"><?php echo $lang->custom->localesupport->fieldsTips?></span>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-200px'><strong><?php echo $lang->custom->key.'（部门ID）';?></strong></td>
                  <td class='w-300px'><strong><?php echo $lang->custom->value.'(二线项目)';?></strong></td>

              </tr>
              <?php  foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          echo html::select("projectList[]", $projects,$value, "class='form-control chosen'");
                          ?>
                      </td>
                      <?php if($canAdd):?>
                          <td class='c-actions'>
                              <a href="javascript:void(0)" onclick="addreview(this)" class='btn btn-link'><i class='icon-plus'></i></a>
                              <?php if($key !== ''):?>
                                  <a href="javascript:void(0)" onclick="delItem(this)" class='btn btn-link'><i class='icon-close'></i></a>
                              <?php endif;?>
                          </td>
                      <?php endif;?>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');

                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'localesupport' &&  $field == 'limitDaySwitch'):?>
          <table class='table table-form mw-750px'>
              <tr>
                  <th class='w-200px'><?php echo$lang->custom->$module->fields[$field];?></th>
                  <td><?php echo html::radio('limitDaySwitch', $lang->custom->changeStatus, $limitDaySwitch);?></td>
              </tr>
              <tr>
                  <td colspan='2' style="padding-left: 66px;">
                      <strong>备注：开关打开，创建现场支持时将限制次月多少工作日内可以报上个月的，超过指定工作日将不允许填报。<br/>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;开关关闭时不限制次月多少工作日可以填报上个月的。</strong>
                  </td>
              </tr>
              <tr>
                  <td></td>
                  <td><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'localesupport' &&  $field == 'reportWorkLimitDay'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-200px'><?php echo$lang->custom->$module->fields[$field];?></th>
                  <td><?php echo html::input('reportWorkLimitDay', $reportWorkLimitDay, "class='form-control'");?></td>
                  <td></td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'bug' and $field == 'longlife'):?>
      <table class='table table-form mw-600px'>
        <tr>
          <th class='w-100px'><?php echo $lang->custom->bug->fields['longlife'];?></th>
          <td class='w-100px'>
            <div class='input-group'>
              <?php echo html::input('longlife', $longlife, "class='form-control'");?>
              <span class='input-group-addon'><?php echo $lang->day?></span>
            </div>
          </td>
          <td><?php echo html::submitButton();?></td>
        </tr>
      </table>
      <div class='alert alert-info alert-block'><?php echo $lang->custom->notice->longlife;?></div>
      <?php elseif($module == 'bug' and $field == 'allowDeptList'):?>
          <table class='table table-form mw-600px'>
              <tr>
              <th class='w-100px'><?php echo $lang->custom->bug->fields['allowDeptList'];?></th>
              <td>
                  <?php
                  echo html::select('allowDeptList[]', $deptList, $allowDeptList, "class='form-control chosen' multiple"); ?>
              </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'review' and $field == 'startTimeOut'):?>
      <table class='table table-form mw-600px'>
        <tr>
          <th class='w-100px'><?php echo $lang->custom->review->fields['startTimeOut'];?></th>
          <td>
            <?php
            echo html::select('typeList[]', $typeList, $this->config->review->startTimeOut, "class='form-control chosen' multiple"); ?>
          </td>
        </tr>
        <tr>
          <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
        </tr>
      </table>
      <?php elseif($module == 'review' and $field == 'singleReviewDeal'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->custom->review->fields['singleReviewDeal'];?></th>
                  <td>
                      <?php
                      echo html::select('singleReviewDeal[]', $users, $this->config->review->singleReviewDeal, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>

      <?php elseif($module == 'review' and $field == 'manageReviewDefExperts'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->custom->review->fields['manageReviewDefExperts'];?></th>
                  <td>
                      <?php
                      echo html::select('manageReviewDefExperts[]', $users, $this->config->review->manageReviewDefExperts, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>

      <?php elseif($module == 'review' && in_array($field, $lang->custom->shanghai->reviewUser)):
          if($field == 'shanghaiReviewOwnerList'){
              $findStr = 'Owner';
          }else{
              $findStr = 'Reviewer';
          }
          ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-200px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>

              </tr>
          <?php
              foreach($fieldList as $key => $value):
                  $reviewType = str_replace($findStr, '', $key);
                  $reviewTypeName = zget($reviewTypeList, $reviewType)
                  ?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1; ?>
                      <td><?php echo $key === '' ? 'NULL' : $reviewTypeName; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          echo html::select('values[]', $users,  $value, "class='form-control chosen'");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>

        <?php elseif($module == 'reviewqz' and $field == 'liasisonOfficer'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->custom->reviewqz->fields['liasisonOfficer'];?></th>
                  <td>
                      <?php
                      echo html::select('liasisonOfficerList[]', $users, $this->config->reviewqz->liasisonOfficer, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'modify' and $field == 'branchManagerList'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->custom->modify->fields['branchManagerList'];?></th>
                  <td>
                      <?php
                      echo html::select('branchManagerList[]', $users, $this->config->modify->branchManagerList, "class='form-control chosen'"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'osspchange' and $field == 'interfacePerson'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->custom->osspchange->fields['interfacePerson'];?></th>
                  <td>
                      <?php
                      echo html::select('interfacePersonList[]', $users, $this->config->osspchange->interfacePerson, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'closingitem' and $field == 'assemblyPerson'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->custom->closingitem->fields['assemblyPerson'];?></th>
                  <td>
                      <?php
                      echo html::select('assemblyPersonList[]', $users, $this->config->closingitem->assemblyPerson, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'closingitem' and $field == 'toolsPerson'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->custom->closingitem->fields['toolsPerson'];?></th>
                  <td>
                      <?php
                      echo html::select('toolsPersonList[]', $users, $this->config->closingitem->toolsPerson, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'closingitem' and $field == 'knowledgePerson'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->custom->closingitem->fields['knowledgePerson'];?></th>
                  <td>
                      <?php
                      echo html::select('knowledgePersonList[]', $users, $this->config->closingitem->knowledgePerson, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'closingitem' and $field == 'preResearchPerson'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->custom->closingitem->fields['preResearchPerson'];?></th>
                  <td>
                      <?php
                      echo html::select('preResearchPersonList[]', $users, $this->config->closingitem->preResearchPerson, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'secondorder' and $field == 'ccDeptList'):?>
          <table class='table table-form mw-600px'>
              <?php foreach ($deptList as $key=>$value):?>
                  <tr>
                      <th class='w-100px'><?php echo $value;?></th>
                      <td>
                          <?php
                          echo html::select('ccDeptList['.$key.'][]', $users, $ccDeptList->$key, "class='form-control chosen' multiple"); ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'secondorder' && $field == 'noFeedBackCloseDate'): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-200px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-left'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td>
                          <?php echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?>
                          <?php
                          switch ($key){
                              case 'JX' :
                                  echo "金信未反馈关闭期限";
                                  break;
                              case 'QZ'   :
                                  echo "清总未反馈关闭期限";
                                  break;
                              default :
                                  echo "";
                          }
                          ?>
                      </td>
                      <td>
                          <?php echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "class='form-control ".$classStr."' " . (empty($key) ? 'readonly' : ''));?>
                      </td>
                      <td>
                          <?php
                          switch ($key){
                              case 'QZ' :
                                  echo "(自然日)";
                                  break;
                              case 'JX'     :
                                  echo "(工作日)";
                                  break;
                              default :
                                  echo "";
                          }
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'demand' and $field == 'deptLeadersList'):?>
          <table class='table table-form mw-600px'>
              <?php foreach ($deptList as $key=>$value):?>
                  <tr>
                      <th class='w-100px'><?php echo $value;?></th>
                      <td>
                          <?php
                          echo html::select('deptLeadersList['.$key.'][]', $users, $deptLeadersList->$key, "class='form-control chosen' multiple"); ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'block' and $field == 'closed'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->custom->block->fields['closed'];?></th>
                  <td>
                      <?php
                      if(empty($blockPairs))
                      {
                          echo $lang->custom->notice->noClosedBlock;
                      }
                      else
                      {
                          echo html::select('closed[]', $blockPairs, $closedBlock, "class='form-control chosen' multiple");
                      }
                      ?>
                  </td>
              </tr>
              <tr>
                  <?php if(!empty($blockPairs)):?>
                      <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
                  <?php endif;?>
              </tr>
          </table>
      <?php elseif($module == 'user' and $field == 'contactField'):?>
      <?php
      $this->app->loadConfig('user');
      $this->app->loadLang('user');
      ?>
      <table class='table table-form mw-800px'>
        <tr>
          <th class='w-150px'><?php echo $lang->custom->user->fields['contactField'];?></th>
          <td><?php echo html::select('contactField[]', $lang->user->contactFieldList, $config->user->contactField, "class='form-control chosen' multiple");?></td>
        </tr>
        <tr>
          <td></td>
          <td>
            <?php echo html::submitButton();?>
            <?php if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=user&field=contactField"), 'hiddenwin', '', 'btn btn-wide');?>
          </td>
        </tr>
      </table>
      <?php elseif($module == 'user' and $field == 'deleted'):?>
      <table class='table table-form mw-600px'>
        <tr>
          <th class='w-150px'><?php echo $lang->custom->user->fields['deleted'];?></th>
          <td><?php echo html::radio('showDeleted', $lang->custom->deletedList, $showDeleted);?></td>
        </tr>
        <tr>
          <td></td>
          <td><?php echo html::submitButton();?></td>
        </tr>
      </table>
      <?php elseif($module == 'opinion' && $field == 'apiDealUserList'): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php  foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);
                      ?></td>
                      <td>
                          <?php
                            $users = $this->loadModel('user')->getPairs('nodeleted');
                            unset($users['']); //不允许选空
                            echo html::select('values[]', $users,  $value, "class='form-control chosen'");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'residentsupport' && $field == 'secondReviews'):
            $this->app->loadConfig('residentsupport');
            $this->app->loadLang('residentsupport');
            $users = $this->loadModel('user')->getPairs('nodeleted');
            unset($users['']); //不允许选空
        ?>
        <table class='table table-form mw-800px'>
            <tr>
                <th class='w-150px'><?php echo $lang->custom->residentsupport->fields['secondReviews'];?></th>
                <td><?php echo html::select('secondReviews[]', $users, $config->residentsupport->secondReviews, "class='form-control chosen' multiple");?></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?php echo html::submitButton();?>
                    <?php if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=residentsupport&field=secondReviews"), 'hiddenwin', '', 'btn btn-wide');?>
                </td>
            </tr>
        </table>
      <?php elseif($module == 'localesupport' &&  $field == 'reportWorkLimitDay'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->custom->reportWorkLimitDay;?></th>
                  <td><?php echo html::input('reportWorkLimitDay', $reportWorkLimitDay, "class='form-control'");?></td>
                  <td><?php echo $lang->custom->workBufferTip?></td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'putproduction' and $field == 'syncFailList'):?>
          <table class='table table-form mw-600px'>
                  <tr>
                      <th class='w-100px'><?php echo $value;?></th>
                      <td>
                          <?php
                          echo html::select('syncFailList[]', $users, $syncFailList, "class='form-control chosen' multiple"); ?>
                      </td>
                  </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'residentsupport' && $field == 'schedulingIntervalDay'):
          $this->app->loadConfig('residentsupport');
          $this->app->loadLang('residentsupport');
          ?>
          <table class='table table-form mw-800px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->custom->residentsupport->fields['schedulingIntervalDay'];?></th>
                  <td><?php echo html::input('schedulingIntervalDay', $config->residentsupport->schedulingIntervalDay, "class='form-control'");?></td>
              </tr>
              <tr>
                  <td></td>
                  <td>
                      <?php echo html::submitButton();?>
                      <?php if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=residentsupport&field=schedulingIntervalDay"), 'hiddenwin', '', 'btn btn-wide');?>
                  </td>
              </tr>
          </table>
      <?php elseif(($module == 'residentsupport' && $field == 'setCcList') || ($module == 'project' && $field == 'setWhiteList')):
          if ($module == 'residentsupport'){
              $this->app->loadConfig('residentsupport');
              $this->app->loadLang('residentsupport');
          }else{
              $this->app->loadConfig('project');
              $this->app->loadLang('project');
          }
          $users = $this->loadModel('user')->getPairs('nodeleted');
          unset($users['']); //不允许选空
          if ($module == 'residentsupport' && isset($this->config->residentsupport->setCcList)){
              $setCclist = json_decode($this->config->residentsupport->setCcList);
          }
          if ($module == 'project' && isset($this->config->project->setWhiteList)){
              $setCclist = json_decode($this->config->project->setWhiteList);
          }
          ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>

              </tr>
              <?php if (!empty($setCclist)){?>
                  <?php foreach($setCclist as $key => $value):?>
                      <tr class='text-center'>
                          <?php $system = isset($value->systems) ? $value->systems : 1;?>
                          <td><?php echo $value->keys === '' ? 'NULL' : $value->keys; echo html::hidden('keys[]', $value->keys) . html::hidden('systems[]', $value->systems);?></td>
                          <td>
                              <?php
                              echo html::select('values'.$value->keys.'[]', $users,  $value->values, "class='form-control chosen' multiple");
                              ?>
                          </td>
                      </tr>
                  <?php endforeach;?>
              <?php }else{?>
                  <?php foreach($fieldList as $key => $value):?>
                      <tr class='text-center'>
                          <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                          <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                          <td>
                              <?php
                              $users = $this->loadModel('user')->getPairs('nodeleted');
                              unset($users['']); //不允许选空
                              echo html::select('values'.$key.'[]', $users,  $value, "class='form-control chosen' multiple");
                              ?>
                          </td>
                      </tr>
                  <?php endforeach;?>
              <?php }?>

              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
              <tr>
                  <?php if($module == 'project' && $field == 'setWhiteList'):?>
                  <td></td>
                      <td style="color: red">
                          <b><?php echo $lang->custom->project->setWhiteListTip?></b>
                      </td>
                  <?php endif;?>
              </tr>
          </table>
      <?php elseif($module == 'api' && $field == 'mediaCheckList'): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system); ?>
                          <?php echo $key == 'release'? "发布校验": "对外交付校验";?></td>
                      <td>
                          <?php
                          $users = [0=>"关",1=>"开"];
                          echo html::select('values[]', $users,  $value, "class='form-control chosen'");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'requirement' and $field == 'overDateInfoVisible'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->custom->requirement->fields['overDateInfoVisible'];?></th>
                  <td>
                      <?php
                      echo html::select('overDateInfoVisible[]', $users, $this->config->requirement->overDateInfoVisible, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'demand' && $field == 'changeSwitchList'): ?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->custom->changeUnlock;?></th>
                  <td><?php echo html::radio('changeSwitch', $lang->custom->changeStatus, $checked);?></td>
              </tr>
              <tr>
                  <td></td>
                  <td><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'demand' && $field == 'demandOutTime'): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-200px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-left'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td>
                          <?php echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?>
                          <?php
                          switch ($key){
                              case 'demandOutTime' :
                                  echo "需求条目超时时间";
                                  break;
                              case 'demandToOutTime'   :
                                  echo "需求条目即将超时时间";
                                  break;
                              case 'demandToOutReferPlanEnd_1' :
                                  echo "需求条目依照任务计划完成时间即将超期提醒第一次";
                                  break;
                              case 'demandToOutReferPlanEnd_2'   :
                                  echo "需求条目依照任务计划完成时间即将超期提醒第二次";
                                  break;
                              case 'demandToOutReferPlanEndIsCreateUser'   :
                                  echo "需求条目依照任务计划完成时间即将超期提醒是否发送创建人";
                                  break;

                              case 'demandToOutReferPlanEndIsManagerUser'   :
                                  echo "需求条目依照任务计划完成时间即将超期提醒是否发送部门负责人";
                                  break;

                              case 'requireOutTime'     :
                                  echo "需求任务内部-超时时间";
                                  break;
                              case 'requireToOutTime'    :
                                  echo "需求任务内部-即将超时时间";
                                  break;
                              case 'requireOut'     :
                                  echo "需求任务外部-超时时间";
                                  break;
                              case 'requireToOut'    :
                                  echo "需求任务外部-即将超时时间";
                                  break;
                              default :
                                  echo "";
                          }
                          ?>
                      </td>
                      <td>
                          <?php if(in_array($key, array('demandToOutReferPlanEndIsCreateUser', 'demandToOutReferPlanEndIsManagerUser'))):?>
                          <?php
                              echo html::select('values[]', $lang->custom->extra->enableTypeList,  $value, "class='form-control chosen'");
                          ?>

                          <?php else: ?>
                            <?php echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "class='form-control' " . (empty($key) ? 'readonly' : ''));?>
                          <?php endif;?>
                      </td>
                      <td>
                          <?php
                          switch ($key){
                              case 'demandToOutTime':
                                  echo "(自然日)";
                                  break;
                              case 'demandOutTime':
                                  echo "(自然月)";
                                  break;

                              case 'demandToOutReferPlanEnd_1':
                              case 'demandToOutReferPlanEnd_2':
                                  echo "(工作日)";
                                  break;

                              case 'requireOutTime':
                              case 'requireToOutTime':
                              case 'requireOut':
                              case 'requireToOut':
                                  echo "(工作日)";
                                  break;
                              default :
                                  echo "";
                          }
                          ?>
                      </td>
                  </tr>
                  <tr class="w-1000px">
                      <?php
                      switch ($key){
                          case 'demandToOutTime' :
                              echo "<td colspan='3' class='w-1000px'>该配置项为判断即将超期提醒配置项，配置n天后按【问题解决超时提醒时间】向前推n天</td>";
                              break;
                          case 'demandOutTime' :
                              echo "<td colspan='3'>该配置项为问题解决超时邮件提醒，是否超期判断使用</td>";
                              break;
                          case 'requireOutTime':
                          case 'requireToOutTime':
                          case 'requireOut':
                          case 'requireToOut':
                          default :
                              echo "";
                      }
                      ?>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'demand' && $field == 'singleUsage'): ?>
          <table class='table table-form mw-750px'>
              <tr>
                  <th class='w-150px'><?php echo $this->lang->custom->demand->fields['singleUsage'];?></th>
                  <td><?php echo html::radio('singleUsage', $lang->custom->singleUsageList, $checked);?></td>
              </tr>
              <tr>
                  <td colspan='2' style="padding-left: 66px;">
                      <strong>备注：开关打开，表示清总对外交付或金信生产变更关联需求条目后，另一个不能关联相同的需求条目。<br/>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;开关关闭，表示清总对外交付或金信生产变更关联需求条目后，另一个可以关联相同的需求条目。</strong>
                  </td>
              </tr>
              <tr>
                  <td></td>
                  <td><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'modify' && $field == 'changeCloseSwitchList'): ?>
          <table class='table table-form mw-750px'>
              <tr>
                  <th class='w-150px'><?php echo $this->lang->custom->modify->fields['changeCloseSwitchList'];?></th>
                  <td><?php echo html::radio('changeCloseSwitch', $lang->custom->changeStatus, $checked);?></td>
              </tr>
              <tr>
                  <td colspan='2' style="padding-left: 66px;">
                      <strong>备注：开关打开，表示清总和金信生产变更在同步出去后，如果状态在内部，支持取消，并将取消的状态同步到外部。<br/>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;开关关闭，表示生产变更同步出去后，如果状态在内部不支持用户取消。</strong>
                  </td>
              </tr>
              <tr>
                  <td></td>
                  <td><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'problem' && ($field == 'apiDealUserList' or $field == 'headOfficeApiDealUserList')): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>

              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                            $users = $this->loadModel('user')->getPairs('nodeleted');
                            unset($users['']); //不允许选空
                            echo html::select('values[]', $users,  $value, "class='form-control chosen'");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>

      <?php elseif($module == 'problem' && $field == 'closePersonList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>

              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          unset($users['']); //不允许选空
                          echo html::select("values[$key][]", $users,  $value, "class='form-control chosen' multiple");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'problem' && $field == 'OverDateList'): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system); ?>
                          <?php echo $key == 'openType'? "开关": "";?></td>
                      <td>
                          <?php
                          $users = [0=>"关",1=>"开"];
                          echo html::select('values[]', $users,  $value, "class='form-control chosen'");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'problem' && $field == 'problemOutTime'): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-200px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-left'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td>
                          <?php echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?>
                          <?php
                          switch ($key){
                              case 'problemToOutTime' :
                                  echo "问题解决即将超时超时提醒时间";
                                  break;
                              case 'problemOutTime'   :
                                  echo "问题解决超时提醒时间";
                                  break;
                              case 'inQzFBToTime'     :
                                  echo "内部清总反馈即将超时提醒时间";
                                  break;
                              case 'inQzFBOutTime'    :
                                  echo "内部清总反馈超时提醒时间";
                                  break;
                              case 'inJxFBToTime'     :
                                  echo "内部金信反馈即将超时提醒时间";
                                  break;
                              case 'inJxFBOutTime'    :
                                  echo "内部金信反馈超时提醒时间";
                                  break;
                              case 'outQzFBToTime'    :
                                  echo "外部清总反馈即将超时提醒时间";
                                  break;
                              case 'outQzFBOutTime'   :
                                  echo "外部清总反馈超时提醒时间";
                                  break;
                              case 'outJxFBToTime'    :
                                  echo " 外部金信反馈即将超时提醒时间";
                                  break;
                              case 'outJxFBOutTime'   :
                                  echo "外部金信反馈超时提醒时间";
                                  break;
                              case 'ToOutByPlannedTime_1'   :
                                  echo "依照计划解决（变更）时间即将超期提醒第一次";
                                  break;
                              case 'ToOutByPlannedTime_2'   :
                                  echo "依照计划解决（变更）时间即将超期提醒第二次";
                                  break;
                              case 'ToOutByPlannedTimeIsCreateUser'   :
                                  echo "依照计划解决（变更）时间即将超期提醒是否发送创建人";
                                  break;
                              case 'ToOutByPlannedTimeIsManagerUser'   :
                                  echo "依照计划解决（变更）时间即将超期提醒是否发送部门负责人";
                                  break;
                              default :
                                  echo "";
                          }
                          ?>
                      </td>
                      <td>
                          <?php if(in_array($key, array('ToOutByPlannedTimeIsCreateUser', 'ToOutByPlannedTimeIsManagerUser'))):?>
                          <?php
                            echo html::select('values[]', $lang->custom->extra->enableTypeList,  $value, "class='form-control chosen'");
                          ?>
                          <?php else:?>
                            <?php echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "class='form-control' " . (empty($key) ? 'readonly' : ''));?>
                          <?php endif;?>
                      </td>
                      <td>
                          <?php
                          switch ($key){
                              case 'inJxFBToTime':
                              case 'inJxFBOutTime':
                              case 'outJxFBOutTime':
                              case 'outJxFBToTime':
                              case 'problemToOutTime' :
                                  echo "(自然日)";
                                  break;
                              case 'problemOutTime'   :
                                  echo "(自然月)";
                                  break;
                              case 'inQzFBOutTime':
                              case 'outQzFBToTime':
                              case 'outQzFBOutTime':
                              case 'inQzFBToTime':
                              case 'ToOutByPlannedTimeOfChange_1':
                              case 'ToOutByPlannedTimeOfChange_2':
                                  echo "(工作日)";
                                  break;
                              default :
                                  echo "";
                          }
                          ?>
                      </td>
                  </tr>
              <tr class="w-1000px">
                  <?php
                  switch ($key){
                      case 'problemToOutTime' :
                          echo "<td colspan='3' class='w-1000px'>该配置项为判断即将超期提醒配置项，配置n天后按【问题解决超时提醒时间】向前推n天</td>";
                          break;
                      case 'problemOutTime'   :
                          echo "<td colspan='3'>该配置项为问题解决超时邮件提醒，是否超期判断使用</td>";
                          break;
                      case 'inQzFBToTime'     :
                      case 'inQzFBOutTime'    :
                      case 'inJxFBToTime'     :
                      case 'inJxFBOutTime'    :
                      case 'outQzFBToTime'    :
                      case 'outQzFBOutTime'   :
                      case 'outJxFBToTime'    :
                      case 'outJxFBOutTime'   :
                      default :
                          echo "";
                  }
                  ?>
              </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'problem' and $field == 'deptLeadersList'):?>
          <table class='table table-form mw-600px'>
              <?php foreach ($deptList as $key=>$value):?>
                  <tr>
                      <th class='w-100px'><?php echo $value;?></th>
                      <td>
                          <?php
                          echo html::select('deptLeadersList['.$key.'][]', $users, $deptLeadersList->$key, "class='form-control chosen' multiple"); ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>

      <?php elseif($module == 'problem' && $field == 'expireDaysList'): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>

              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "oninput=\"value=value.replace(/[^\d]/g,'')\" class='form-control' " . (empty($key) ? 'readonly' : ''));?>

                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'problem' && $field == 'statusYearSwitch'): ?>
          <table class='table table-form mw-750px'>
              <tr>
                  <th class='w-150px'><?php echo $this->lang->custom->problem->fields['statusYearSwitch'];?></th>
                  <td><?php echo html::radio('statusYearSwitch', $lang->custom->changeStatus, $checked);?></td>
              </tr>
              <tr>
                  <td colspan='2' style="padding-left: 66px;">
                      <strong>备注：开关打开，表示只联动一年以内的问题单。<br/>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;开关关闭，表示联动所有问题单。</strong>
                  </td>
              </tr>
              <tr>
                  <td></td>
                  <td><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'projectplan' && $field == 'shProductAndarchList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>

              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          echo html::select("values[$key][]", $users,  $value, "class='form-control chosen'");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'demand' && ($field == 'expireDaysList' or $field == 'closePersonList')): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->custom->value;?></strong></td>

              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "oninput=\"value=value.replace(/[^\d]/g,'')\" class='form-control' " . (empty($key) ? 'readonly' : ''));?>

                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'demand' and $field == 'overDateInfoVisible'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->custom->demand->fields['overDateInfoVisible'];?></th>
                  <td>
                      <?php
                      echo html::select('overDateInfoVisible[]', $users, $this->config->demand->overDateInfoVisible, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'opinion' and $field == 'groupList'):?>
      <table class='table table-form active-disabled table-condensed mw-800px'>
        <tr class='text-center'>
          <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
          <td><strong><?php echo $lang->custom->value;?></strong></td>
          <th class='w-200px text-left'><?php echo $lang->opinion->owner;?></th>
          <th class='w-90px'></th>
        </tr>
        <?php foreach($fieldList as $key => $value):?>
        <tr class='text-center'>
          <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
          <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
          <td>
            <?php echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "class='form-control' " . (empty($key) ? 'readonly' : ''));?>
          </td>
          <td><?php echo empty($key) ? html::input('ownerList[]', '', "class='form-control' readonly") : html::select('ownerList[]', $users, zget($ownerList, $key, ''), "class='form-control chosen'");?></td>
          <td class='c-actions'>
            <a href="javascript:void(0)" onclick="addOpinion(this)" class='btn btn-link'><i class='icon-plus'></i></a>
            <a href="javascript:void(0)" onclick="delItem(this)" class='btn btn-link'><i class='icon-close'></i></a>
          </td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td colspan='4' class='text-center form-actions'>
          <?php
          echo html::submitButton();
          if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');
          ?>
          </td>
        </tr>
      </table>
     <!-- --><?php /*else:*/?>

      <?php elseif($module == 'review' and $field == 'fileSize'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-100px'><?php echo $lang->review->fileSize;?></th>
                  <td ><?php echo html::input('fileSize', $fileSize, "class='form-control'");?></td>
                  <td ><?php echo $lang->custom->review->fileSizeTip;?></td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'review' and $field == 'reviewConsumed'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->review->reviewConsumed;?></th>
                  <td><?php echo html::input('reviewConsumed',$this->config->review->reviewConsumed->reviewConsumed, "class='form-control w-80px'");?></td>
                  <td></td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'review' and $field == 'reviewerList'):?>
      <?php elseif(($module == 'review' and $field == 'reviewerList')|| ($module == 'demandcollection' and ($field == 'writerList' ||$field == 'viewerList' ||$field == 'copyForList'))): ?>
          <table class='table table-form active-disabled table-condensed mw-800px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->review->reviewer;?></strong></td>
                  <?php if($canAdd):?><th class='w-90px'></th><?php endif;?>
              </tr>
              <?php  foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <?php if($field == 'reviewerList'):?>
                          <td><?php echo empty($key) ? html::input('reviewerList[]', '', "class='form-control' readonly") : html::select('reviewerList[]', $users, zget($reviewerList, $key, ''), "class='form-control chosen'");?></td>
                      <?php elseif($field == 'writerList'):?>
                      <td><?php echo empty($key) ? html::input('writerList[]', '', "class='form-control' readonly") : html::select('writerList[]', $users, zget($reviewerList, $key, ''), "class='form-control chosen'");?></td>
                      <?php elseif($field == 'copyForList'):?>
                          <td><?php echo empty($key) ? html::input('copyForList[]', '', "class='form-control' readonly") : html::select('copyForList[]', $users, zget($reviewerList, $key, ''), "class='form-control chosen'");?></td>
                      <?php else:?>
                      <td><?php echo empty($key) ? html::input('viewerList[]', '', "class='form-control' readonly") : html::select('viewerList[]', $users, zget($reviewerList, $key, ''), "class='form-control chosen'");?></td>
                      <?php endif;?>
                      <td class='c-actions'>
                          <a href="javascript:void(0)" onclick="addreview(this)" class='btn btn-link'><i class='icon-plus'></i></a>
                          <a href="javascript:void(0)" onclick="delItem(this)" class='btn btn-link'><i class='icon-close'></i></a>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'review' and $field == 'endDates'):?>
          <table class='table table-form active-disabled table-condensed mw-800px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td><strong><?php echo $lang->review->endDateLevel;?></strong></td>
                  <?php if($canAdd):?><th class='w-90px'></th><?php endif;?>
              </tr>
              <?php
              foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php
                      $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : zget($lang->review->endDateList, $key, ''); echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td><?php echo empty($key) ? html::input('endDates[]', '', "class='form-control' readonly") : html::select('endDates[]', $lang->review->level,$value, "class='form-control chosen' ");?></td>
                     <!-- <td class='c-actions'>
                          <a href="javascript:void(0)" onclick="addItem(this)" class='btn btn-link'><i class='icon-plus'></i></a>
                          <a href="javascript:void(0)" onclick="delItem(this)" class='btn btn-link'><i class='icon-close'></i></a>
                      </td>-->
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');
                      ?>
                  </td>
              </tr>
          </table>
          <?php elseif($module == 'workreport' && $field == 'leaderList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          unset($users['']);
                          echo html::select('values[]', $users,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'workreport' && $field == 'deptList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          echo html::select('values[]', $dept,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'projectplan' && $field == 'shProjectPlanDeptList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          echo html::select('values[]', $dept,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');

                      ?>
                  </td>
              </tr>
          </table>
        <?php elseif($module == 'requestlog' && $field == 'userList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          unset($users['']);
                          echo html::select('values[]', $users,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'build' && $field == 'leaderList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          unset($users['']);
                          echo html::select('values[]', $users,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'problem' && $field == 'examinationResultUpdateList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-600px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          echo html::select('values[]', $users,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'issue' && $field == 'leaderList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-800px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center '>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          unset($users['']);
                          echo html::select('values[]', $users,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                      <td> <?php echo $lang->custom->issue->fieldsTips ?></td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'issue' && $field == 'assignToList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-800px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          unset($users['']);
                          echo html::select('values[]', $users,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                      <td> <?php echo $lang->custom->issue->fieldsTips ?></td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'issue' && $field == 'frameworkToList' ): ?>
          <table class='table table-form active-disabled table-condensed mw-800px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td  class='w-500px'><strong><?php echo $lang->custom->value;?></strong></td>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php
                          $users = $this->loadModel('user')->getPairs('nodeleted');
                          unset($users['']);
                          echo html::select('values[]', $users,  $value, "class='form-control chosen' multiple ");
                          ?>
                      </td>
                      <td> <?php echo $lang->custom->issue->fieldsTips ?></td>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'credit' and $field == 'confirmResultUsers'):?>
          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->custom->credit->fields['confirmResultUsers'];?></th>
                  <td>
                      <?php
                      echo html::select('confirmResultUsers[]', $users, $this->config->credit->confirmResultUsers, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>
      <?php elseif($module == 'qualitygate' && $field == 'allowQualityGateDeptIds'):?>

          <table class='table table-form mw-600px'>
              <tr>
                  <th class='w-150px'><?php echo $lang->custom->qualitygate->fields['allowQualityGateDeptIds'];?></th>
                  <td>
                      <?php
                      echo html::select('allowQualityGateDeptIds[]', $deptList, $this->config->qualitygate->allowQualityGateDeptIds, "class='form-control chosen' multiple"); ?>
                  </td>
              </tr>
              <tr>
                  <td colspan='2' class='text-center'><?php echo html::submitButton();?></td>
              </tr>
          </table>

      <?php elseif(($module == 'review' and $field == 'objectList') || ($module == 'helpdoc' and $field == 'navOrderList')):?>
          <table class='table table-form active-disabled table-condensed mw-800px'>
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td class='w-400px'><strong><?php echo $lang->custom->value;?></strong></td>
                  <td class='w-120px'><strong><?php echo $lang->custom->order;?></strong></td>
                  <?php  if($canAdd):?><th class='w-90px'></th><?php endif;?>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <?php
                  $classStr = '';

                  if ($field == 'workHours' && $key == 'effectiveDate'){
                      $classStr = 'form-date';
                  }
                  ?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "class='form-control ".$classStr."' " . (empty($key) ? 'readonly' : ''));?>
                      </td>
                      <td>
                          <?php echo html::input("orders[]", isset($dbFields[$key]) ? $dbFields[$key]->order : $value, "class='form-control ".$classStr."' " . (empty($key) ? 'readonly' : ''));?>
                      </td>
                      <?php if($canAdd):?>
                          <td class='c-actions'>
                              <a href="javascript:void(0)" onclick="addItem(this)" class='btn btn-link'><i class='icon-plus'></i></a>
                              <?php if($key !== ''):?>
                                  <a href="javascript:void(0)" onclick="delItem(this)" class='btn btn-link'><i class='icon-close'></i></a>
                              <?php endif;?>
                          </td>
                      <?php endif;?>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');
                      ?>
                  </td>
              </tr>
          </table>
      <?php elseif($module == 'demandcollection' &&  $field == 'belongPlatform'):?>
          <table class='table table-form active-disabled table-condensed mw-800px' id="belongPlatformTable">
              <tr class='text-center'>
                  <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
                  <td class='w-400px'><strong><?php echo $lang->custom->value;?></strong></td>
                  <td class='w-120px'><strong><?php echo $lang->custom->order;?></strong></td>
                  <td class='w-200px'><strong><?php echo $lang->custom->extendInfo->productmanager;?></strong></td>
                  <?php  if($canAdd):?><th class='w-90px'></th><?php endif;?>
              </tr>
              <?php foreach($fieldList as $key => $value):?>
                  <?php
                  $classStr = '';

                  if ($field == 'workHours' && $key == 'effectiveDate'){
                      $classStr = 'form-date';
                  }

                  ?>
                  <tr class='text-center'>
                      <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
                      <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
                      <td>
                          <?php echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "class='form-control ".$classStr."' " . (empty($key) ? 'readonly' : ''));?>
                      </td>
                      <td>
                          <?php echo html::input("orders[]", isset($dbFields[$key]) ? $dbFields[$key]->order : 0, "class='form-control ".$classStr."' " . (empty($key) ? 'readonly' : ''));?>
                      </td>
                      <td  class='text-left'>
                          <?php if(empty($key)):?>
                              <select name="" readonly="readonly" class='form-control readonly extendInfoRow ">
                              <option vaue = ''></option>
                              </select>
                              <span style="display:none;">
                                  <?php echo html::select("productmanager[]", $users,  isset($dbFields[$key]->extendInfo->productmanager) ? $dbFields[$key]->extendInfo->productmanager : '', "class='form-control chosen extendInfoRow ".$classStr."'" ); ?>
                              </span>
                          <?php else:?>
                            <?php echo html::select("productmanager[]", $users,  isset($dbFields[$key]->extendInfo->productmanager) ? $dbFields[$key]->extendInfo->productmanager : '', "class='form-control chosen extendInfoRow ".$classStr."'" ); ?>
                          <?php endif;?>
                      </td>
                      <?php if($canAdd):?>
                          <td class='c-actions'>
                              <a href="javascript:void(0)" onclick="addItem(this)" class='btn btn-link'><i class='icon-plus'></i></a>
                              <?php if($key !== ''):?>
                                  <a href="javascript:void(0)" onclick="delItem(this)" class='btn btn-link'><i class='icon-close'></i></a>
                              <?php endif;?>
                          </td>
                      <?php endif;?>
                  </tr>
              <?php endforeach;?>
              <tr>
                  <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
                      <?php
                      $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
                      echo html::radio('lang', $appliedTo, $lang2Set);
                      echo html::submitButton();
                      if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');
                      ?>
                  </td>
              </tr>
          </table>

      <?php else:?>
      <table class='table table-form active-disabled table-condensed mw-600px'>
        <tr class='text-center'>
          <td class='w-120px'><strong><?php echo $lang->custom->key;?></strong></td>
          <td><strong><?php echo $lang->custom->value;?></strong></td>
          <?php  if($canAdd):?><th class='w-90px'></th><?php endif;?>
        </tr>
        <?php foreach($fieldList as $key => $value):?>
            <?php
                $classStr = '';

                if ($field == 'workHours' && $key == 'effectiveDate'){
                    $classStr = 'form-date';
                }
            ?>
            <tr class='text-center'>
              <?php $system = isset($dbFields[$key]) ? $dbFields[$key]->system : 1;?>
              <td><?php echo $key === '' ? 'NULL' : $key; echo html::hidden('keys[]', $key) . html::hidden('systems[]', $system);?></td>
              <td>
                <?php echo html::input("values[]", isset($dbFields[$key]) ? $dbFields[$key]->value : $value, "class='form-control ".$classStr."' " . (empty($key) ? 'readonly' : ''));?>
              </td>
              <?php if($canAdd):?>
              <td class='c-actions'>
                <a href="javascript:void(0)" onclick="addItem(this)" class='btn btn-link'><i class='icon-plus'></i></a>
                <?php if($key !== ''):?>
                <a href="javascript:void(0)" onclick="delItem(this)" class='btn btn-link'><i class='icon-close'></i></a>
                <?php endif;?>
              </td>
              <?php endif;?>
            </tr>
        <?php endforeach;?>
          <?php if ($module == 'problem' && $field == 'delayCCUserList'): ?>
          <tr class="text-center w-1000px">
              <td colspan='2' class='w-1000px'>该配置适用于【需求池/问题池】延期审批流程，延期申请通过后的抄送人</td>
          </tr>
          <?php endif; ?>
        <tr>
          <td colspan='<?php $canAdd ? print(3) : print(2);?>' class='text-center form-actions'>
          <?php
          $appliedTo = array($currentLang => $lang->custom->currentLang, 'all' => $lang->custom->allLang);
          echo html::radio('lang', $appliedTo, $lang2Set);
          echo html::submitButton();
          if(common::hasPriv('custom', 'restore')) echo html::linkButton($lang->custom->restore, inlink('restore', "module=$module&field=$field"), 'hiddenwin', '', 'btn btn-wide');
          ?>
          </td>
        </tr>
      </table>

          <?php if($module == 'infoqz' && in_array($field,['demandUnitList1','demandUnitList2','demandUnitList3'])):?>
              <div class='alert alert-warning alert-block'>键值请按照以上格式添加</div>
          <?php endif;?>
      <?php if(!$canAdd):?>
      <div class='alert alert-warning alert-block'><?php echo $lang->custom->notice->canNotAdd;?></div>
      <?php endif;?>
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
<?php if($module == 'opinion' and $field == 'groupList'):?>
<?php
$owners = html::select('ownerList[]', $users, '', "class='form-control chosen'");
$opinionRow = <<<EOT
  <tr class='text-center'>
    <td>
      <input type='text' class="form-control" autocomplete="off" value="" name="keys[]">
      <input type='hidden' value="0" name="systems[]">
    </td>
    <td>
      <input type='text' class="form-control" value="" autocomplete="off" name="values[]">
    </td>
    <td>
      $owners
    </td>
    <td class='c-actions'>
      <a href="javascript:void(0)" class='btn btn-link' onclick="addItem(this)"><i class='icon-plus'></i></a>
      <a href="javascript:void(0)" class='btn btn-link' onclick="delItem(this)"><i class='icon-close'></i></a>
    </td>
  </tr>
EOT;
?>
<?php js::set('opinionRow', $opinionRow);?>
<script>
function addOpinion(clickedButton)
{
    $(clickedButton).parent().parent().after(opinionRow);
    $(clickedButton).closest('tr').next().find("select[name*='ownerList']").chosen();
}
</script>
<?php endif;?>

<?php if(($module == 'review' and $field == 'reviewerList') || ($module == 'demandcollection' and ($field == 'writerList' ||$field == 'viewerList' ||$field == 'copyForList'))|| ($module == 'localesupport' and $field == 'projectList')) :?>
    <?php
    if($field == 'reviewerList'){
        $reviewers = html::select('reviewerList[]', $users, '', "class='form-control chosen w-250px'");
    }elseif($field == 'writerList'){
        $reviewers = html::select('writerList[]', $users, '', "class='form-control chosen w-250px'");
    }elseif($field == 'copyForList'){
        $reviewers = html::select('copyForList[]', $users, '', "class='form-control chosen w-250px'");
    }elseif($field == 'projectList'){
        $reviewers = html::select('projectList[]', $projects, '', "class='form-control chosen w-250px'");
    }else{
        $reviewers = html::select('viewerList[]', $users, '', "class='form-control chosen w-250px'");
    }
    $reviewRow = <<<EOT
  <tr class='text-center'>
    <td>
      <input type='text' class="form-control" autocomplete="off" value="" name="keys[]">
      <input type='hidden' value="0" name="systems[]">
    </td>
  
    <td>
      $reviewers
    </td>
    <td class='c-actions'>
      <a href="javascript:void(0)" class='btn btn-link' onclick="addreview(this)"><i class='icon-plus'></i></a>
      <a href="javascript:void(0)" class='btn btn-link' onclick="delItem(this)"><i class='icon-close'></i></a>
    </td>
  </tr>
EOT;
    ?>
    <?php js::set('reviewRow', $reviewRow);?>
    <?php js::set('field', $field);?>
    <script>
        function addreview(clickedButton)
        {
            $(clickedButton).parent().parent().after(reviewRow);
            if(field == 'reviewerList'){
                $(clickedButton).closest('tr').next().find("select[name*='reviewerList']").chosen();
            }else if(field == 'writerList'){
                $(clickedButton).closest('tr').next().find("select[name*='writerList']").chosen();
            }else if(field == 'copyForList'){
                $(clickedButton).closest('tr').next().find("select[name*='copyForList']").chosen();
            }else if(field == 'projectList'){
                $(clickedButton).closest('tr').next().find("select[name*='projectList']").chosen();
            }else{
                $(clickedButton).closest('tr').next().find("select[name*='viewerList']").chosen();
            }
        }
    </script>
<?php endif;?>

<?php include '../../../common/view/footer.html.php';?>
