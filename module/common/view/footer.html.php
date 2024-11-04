</div><?php /* end '.outer' in 'header.html.php'. */ ?>
<style>
    .user-help{width: 20px;padding: 5px 0;position: fixed;right: 3px;bottom: 100px;background-color: #fff;border-radius: 6px;text-align: center}
    .icon-help-mask{display:;width:80px;height: 80px;background-color: #fff;position: fixed;right: -136px;bottom: 150px;opacity: 0.6;border: 1px solid #cbd0db}
    .icon-help-mask .close{position:absolute;right: 4px;top:1px;font-size: 22px;opacity: 0.8}
    .icon-help-mask .close i{font-weight: bold;}
</style>
<?php
$res = $this->loadModel('my')->getDocTypes();
$modulesKey = array_keys($res['options']);
$flowModuleName = '';
//工作流模块
if ($moduleName == 'flow'){
    $pathInfo = explode('-',$_SERVER['PATH_INFO']);
    $flowModuleName = str_replace('/','',$pathInfo[0]);
}
?>
<?php if(common::hasPriv('my','showHelpDoc') && $onlybody != 'yes' && (in_array($moduleName,$modulesKey) || in_array($flowModuleName,$modulesKey))):?>
    <div class="user-help">
    <?php echo $lang->userDoc;?>
</div>

<div class="icon-help-mask">
    <button class="close"><i class="icon icon-arrow-right"></i></button>
    <i title="<?php echo $lang->userDoc;?>" onclick="showHelpDoc()" class="icon" style="font-size: 48px;display: block;margin: 0 auto;text-align: center;margin-top: 10px;cursor: pointer"></i>
    <div style="margin-top: 5px;text-align: center"><?php echo $lang->userDoc;?></div>
</div>
<?php endif;?>
<script>
$.initSidebar();
</script>
<?php if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}?>

<iframe frameborder='0' name='hiddenwin' id='hiddenwin' scrolling='no' class='debugwin hidden'></iframe>

<?php if($onlybody != 'yes'):?>
</main><?php /* end '#wrap' in 'header.html.php'. */ ?>
<div id="noticeBox"><?php echo $this->loadModel('score')->getNotice(); ?></div>
<script>
    //用户手册
    $(".user-help").mouseenter(function () {
        $(".icon-help-mask").animate({'right':'36px'});
        $(".icon-help-mask i").addClass('icon-help');
        $(this).animate({'right':'-80px'});
    })
    //用户手册弹窗配置
    var batchAddModalTrigger = new $.zui.ModalTrigger(
        {
            width: '90%',
            type: 'iframe',
            waittime: 3000
        });
    function showHelpDoc(){
        var module_name = "<?php echo $this->moduleName?>";
        var flowMoudeleName = "<?php echo $flowModuleName;?>";
        if (flowMoudeleName != '') module_name = flowMoudeleName;

        var url = '<?php echo $this->createLink('my', 'showHelpDoc', 'moduleName=module_name', '', true);?>';

        batchAddModalTrigger.show({url: url.replace('module_name', module_name)})
    }

    $(".icon-help-mask .close").click(function () {
        $(".icon-help-mask i").removeClass('icon-help');
        $(".icon-help-mask").animate({'right':'-136px'});
        $('.user-help').animate({'right':'3px'});
    });
<?php $this->app->loadConfig('message');?>
<?php if($config->message->browser->turnon):?>
/* Alert got messages. */
needPing = false;
$(function()
{
    var windowBlur = false;
    if(window.Notification)
    {
        window.onblur  = function(){windowBlur = true;}
        window.onfocus = function(){windowBlur = false;}
    }
    setInterval(function()
    {
        if(!windowBlur){
            $.get(createLink('message', 'ajaxGetMessage', "windowBlur=" + (windowBlur ? '1' : '0')), function(data)
            {
                if(!windowBlur)
                {
                    $('#noticeBox').append(data);
                    adjustNoticePosition();
                }
                else
                {
                    if(data)
                    {
                        if(typeof data == 'string') data = $.parseJSON(data);
                        if(typeof data.message == 'string') notifyMessage(data);
                    }
                }
            });
        }
    }, <?php echo $config->message->browser->pollTime * 1000;?>);
})
<?php endif;?>

<?php if(!empty($config->sso->redirect)):?>
<?php
$ranzhiAddr = $config->sso->addr;
$ranzhiURL  = substr($ranzhiAddr, 0, strrpos($ranzhiAddr, '/sys/'));
?>
<?php if(!empty($ranzhiURL)):?>
$(function(){ redirect('<?php echo $ranzhiURL?>', '<?php echo $config->sso->code?>'); });
<?php endif;?>
<?php endif;?>
</script>

<?php endif;?>
<?php
if($this->loadModel('cron')->runable()) js::execute('startCron()');
if(isset($pageJS)) js::execute($pageJS);  // load the js for current page.

/* Load hook files for current page. */
$extPath      = $this->app->getModuleRoot() . '/common/ext/view/';
$extHookRule  = $extPath . 'footer.*.hook.php';
$extHookFiles = glob($extHookRule);
if($extHookFiles) foreach($extHookFiles as $extHookFile) include $extHookFile;
?>
</body>
<script>
$('td.c-actions').find('.disabled.btn').attr({'class':'disabled btn', 'style': 'pointer-events: unset;'});
</script>
</html>
