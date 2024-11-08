<?php
/**
 * The admin view file of conference module of XXB.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZOSL (https://zpl.pub/page/zoslv1.html)
 * @author      Wenrui LI <liwenrui@easycorp.ltd>
 * @package     conference
 * @version     $Id$
 * @link        https://xuanim.com
 */
?>
<?php
include $app->getModuleRoot() . 'common/view/header.html.php';
?>
<div class='panel-content'>
  <ul class="nav nav-tabs">
    <?php foreach($this->config->conference->owtTabList as $tabIndex):?>
    <li class="<?php if($type == $tabIndex || ($type == 'edit' && $tabIndex == 'server')) echo 'active';?>">
      <a href="<?php echo '#' . $tabIndex . 'Content'?>" data-toggle="tab"><?php echo $lang->conference->$tabIndex;?></a>
    </li>
    <?php endforeach;?>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade <?php echo $type == 'server' || $type == 'edit' ? 'active in' : '';?>" id="serverContent">
      <form method='post' id='conference-admin-form' class='form-ajax<?php if($enabled) echo ' conference-enabled';?><?php if(!empty($backendType)) echo " $backendType-selected";?>'>
        <table class='table table-form'>
          <tr>
            <th class="w-150px"><?php echo $lang->conference->enabled;?></th>
            <td class="w-400px">
              <?php if($type != 'edit'): ?>
              <div class="checkbox-primary disabled <?php if($enabled) echo 'checked';?>">
                <label><?php echo $lang->conference->enabledTip;?></label>
              </div>
              <?php else: ?>
              <div class="checkbox-primary">
                <input type="checkbox" name="enabled" id='enabled' value="true" <?php if($enabled) echo 'checked';?> <?php if($type != 'edit') echo 'disabled';?>>
                <label for='enabled'><?php echo $lang->conference->enabledTip;?></label>
              </div>
              <?php endif; ?>
            </td>
            <td></td>
          </tr>
          <?php if($type == 'edit' || $enabled): ?>
          <tr class='edit-row common-row'>
            <th class="w-120px"><?php echo $lang->conference->backend->type;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'):?>
                <div class='required required-wrapper'></div>
                <?php echo html::radio('backendType', $lang->conference->backend->types, $backendType);?>
              <?php else: echo html::radio('backendType', $lang->conference->backend->types, $backendType, 'disabled'); endif; ?>
            </td>
            <td><?php echo $lang->conference->backendTypeTip;?></td>
          </tr>
          <tr class='edit-row common-row'>
            <th class="w-120px"><?php echo $lang->conference->serverAddr;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'): ?>
                <div class='required required-wrapper'></div>
                <?php echo html::input('serverAddr', $serverAddr, "class='form-control'");?>
              <?php else: echo empty($serverAddr) ? $lang->conference->notset : $serverAddr; endif; ?>
            </td>
            <td><?php echo $lang->conference->serverAddrTip;?></td>
          </tr>
          <tr class='edit-row common-row'>
            <th class="w-120px"><?php echo $lang->conference->https;?></th>
            <td class="w-400px">
              <?php if($type != 'edit'): ?>
              <div class="checkbox-primary disabled <?php if($https) echo 'checked';?>">
                <label><?php echo $lang->conference->httpsTip;?></label>
              </div>
              <?php else: ?>
              <div class="checkbox-primary">
                <input type="checkbox" name="https" id='https' value="true" <?php if($https) echo 'checked';?> <?php if($type != 'edit') echo 'disabled';?>>
                <label for='https'><?php echo $lang->conference->httpsTip;?></label>
              </div>
              <?php endif; ?>
            </td>
            <td></td>
          </tr>
          <tr class='edit-row common-row'>
            <th class="w-120px"><?php echo $lang->conference->apiPort;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'):?>
                <div class='required required-wrapper'></div>
                <input type="number" name="apiPort" id="apiPort" <?php echo empty($apiPort) ? '' : "value='$apiPort'";?> min="1" max="65535" class="form-control">
              <?php else: echo empty($apiPort) ? $lang->conference->notset : $apiPort; endif; ?>
            </td>
            <td><?php echo $type == 'edit' ? $lang->conference->apiPortTip : '';?></td>
          </tr>
          <tr class='edit-row owt-row'>
            <th class="w-120px"><?php echo $lang->conference->mgmtPort;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'): ?>
                <div class='required required-wrapper'></div>
                <input type="number" name="mgmtPort" id="mgmtPort" <?php echo empty($mgmtPort) ? '' : "value='$mgmtPort'";?> min="1" max="65535" class="form-control">
              <?php else: echo empty($mgmtPort) ? $lang->conference->notset : $mgmtPort; endif; ?>
            </td>
            <td><?php echo $type == 'edit' ? $lang->conference->mgmtPortTip : '';?></td>
          </tr>
          <tr class='edit-row srs-row'>
            <th class="w-120px"><?php echo $lang->conference->rtcPort;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'): ?>
                <div class='required required-wrapper'></div>
                <input type="number" name="rtcPort" id="rtcPort" <?php echo empty($rtcPort) ? '' : "value='$rtcPort'";?> min="1" max="65535" class="form-control">
              <?php else: echo empty($rtcPort) ? $lang->conference->notset : $rtcPort; endif; ?>
            </td>
            <td><?php echo $type == 'edit' ? $lang->conference->rtcPortTip : '';?></td>
          </tr>
          <tr class='edit-row owt-row'>
            <th class="w-120px"><?php echo $lang->conference->serviceId;?></th>
            <td class="w-400px code">
              <?php if($type == 'edit'): ?>
                <div class='required required-wrapper'></div>
                <?php echo html::input('serviceId', $serviceId, "class='form-control'");?>
              <?php else: echo empty($serviceId) ? $lang->conference->notset : $serviceId; endif; ?>
            </td>
            <td><?php echo $type == 'edit' ? $lang->conference->serviceIdTip : '';?></td>
          </tr>
          <tr class='edit-row owt-row'>
            <th class="w-120px vtop"><?php echo $lang->conference->serviceKey;?></th>
            <td class="w-400px code wrapper">
              <?php if($type == 'edit'): ?>
                <div class='required required-wrapper'></div>
                <?php echo html::textarea('serviceKey', $serviceKey, "class='form-control' style='height:100px;'");?>
              <?php else: echo empty($serviceKey) ? $lang->conference->notset : $serviceKey; endif; ?>
            </td>
            <td><?php echo $type == 'edit' ? $lang->conference->serviceKeyTip : '';?></td>
          </tr>
          <?php endif; ?>
          <tr>
            <th></th>
            <td colspan='2'>
              <?php if($type == 'edit') echo html::submitButton();?>
              <?php if($type != 'edit') echo '<a class="btn btn-primary" href="' . helper::createLink('conference', 'admin', 'type=edit') . '">' . $lang->edit;?>
            </td>
          </tr>
        </table>
      </form>
    </div>
    <div class="tab-pane fade <?php echo $type == 'video' ? 'active in' : '';?>" id="videoContent">
      <form method='post' id='ajaxForm' class='form-ajax' action=<?php echo $this->createLink("conference", 'admin', 'type=video');?>>
        <table class='table table-form'>
          <tr>
            <th class="w-120px"><?php echo $lang->conference->resolutionWidth;?></th>
            <td class="code w-100px">
              <input type="number" name="resolutionWidth" id="resolutionWidth" <?php echo "placeholder='{$lang->conference->placeholder->resolutionWidth}'" ;echo empty($resolutionWidth) ? '' : "value='$resolutionWidth'";?> min="320" max="1280" class="form-control">
            </td>
            <td><?php echo $lang->conference->resolutionWidthTip;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->conference->resolutionHeight;?></th>
            <td class="code w-100px">
              <input type="number" name="resolutionHeight" id="resolutionHeight" <?php echo "placeholder='{$lang->conference->placeholder->resolutionHeight}'" ;echo empty($resolutionHeight) ? '' : "value='$resolutionHeight'";?> min="240" max="720" class="form-control">
            <td><?php echo $lang->conference->resolutionHeightTip;?></td>
          </tr>
          <tr><th></th><td></td></tr>
          <tr>
            <th></th>
            <td colspan='2'>
              <?php echo html::submitButton();?>
            </td>
          </tr>
        </table>
      </form>


    </div>
  </div>
</div>
<style>
.edit-row {display: none}
#conference-admin-form.conference-enabled .edit-row {display: table-row}
.srs-selected .owt-row {display: none!important}
.owt-selected .srs-row {display: none!important}
</style>
<script>
$(function()
{
    $.setAjaxForm('#conference-admin-form');
    $('#enabled').on('change', function()
    {
        $('#conference-admin-form').toggleClass('conference-enabled', $('#enabled').is(':checked'));
    });
    $('input[type="radio"][name="backendType"]').on('change', function(e)
    {
        if(e.target.value == 'owt')
        {
            $('#conference-admin-form').removeClass('srs-selected');
            $('#conference-admin-form').addClass('owt-selected');
        }
        if(e.target.value == 'srs')
        {
            $('#conference-admin-form').removeClass('owt-selected');
            $('#conference-admin-form').addClass('srs-selected');
        }
    });

});
</script>
<?php include $app->getModuleRoot() . 'common/view/footer.html.php';?>
