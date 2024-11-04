<?php
/**
 * The control file of execution module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     execution
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/ext/view/gantt.html.php';?>
<style>
#ganttView {height: 600px;}
#ganttPris > span {display: inline-block; line-height: 20px; min-width: 20px; border-radius: 2px;}
.gantt_container, .gantt_tooltip {font-family: "Helvetica Neue", Helvetica, Tahoma, Arial, "PingFang SC", "Source Han Sans CN", "Source Han Sans", "Hiragino Sans GB", "WenQuanYi Micro Hei", sans-serif}
.gantt_task_line {background: #<?php echo $lang->execution->gantt->color[0]?>; border-color: #<?php echo $lang->execution->gantt->color[0]?>;}
.gantt_task_progress {background: rgba(0,0,0,.1)}
.gantt_task_line.pri-1 {background: #<?php echo $lang->execution->gantt->color[1]?>; border-color: #<?php echo $lang->execution->gantt->color[1]?>}
.gantt_task_line.pri-2 {background: #<?php echo $lang->execution->gantt->color[2]?>; border-color: #<?php echo $lang->execution->gantt->color[2]?>}
.gantt_task_line.pri-3 {background: #<?php echo $lang->execution->gantt->color[3]?>; border-color: #<?php echo $lang->execution->gantt->color[3]?>}
.gantt_task_line.pri-4 {background: #<?php echo $lang->execution->gantt->color[4]?>; border-color: #<?php echo $lang->execution->gantt->color[4]?>}
.gantt_grid_scale .gantt_grid_head_cell,.gantt_task .gantt_task_scale .gantt_scale_cell {color: #838a9d;}
.gantt_task_line.gantt_selected {box-shadow: 0 1px 1px rgba(0,0,0,.05), 0 2px 6px 0 rgba(0,0,0,.045)}
.gantt_link_arrow_right {border-left-color: #2196F3;}
.gantt_link_arrow_left {border-right-color: #2196F3;}
.gantt_task_link .gantt_line_wrapper div{background-color: #2196F3;}
.gantt_critical_link .gantt_line_wrapper>div {background-color: #e63030 !important;}
.gantt_critical_link.start_to_start .gantt_link_arrow_right {border-left-color: #e63030 !important;}
.gantt_critical_link.finish_to_start .gantt_link_arrow_right {border-left-color: #e63030 !important;}
.gantt_critical_link.start_to_finish .gantt_link_arrow_left {border-right-color: #e63030 !important;}
.gantt_critical_link.finish_to_finish .gantt_link_arrow_left {border-right-color: #e63030 !important;}
.gantt_task_link.start_to_start .gantt_line_wrapper div { background-color: #DD55EA; }
.gantt_task_link.start_to_start:hover .gantt_line_wrapper div { box-shadow: 0 0 5px 0px #DD55EA; }
.gantt_task_link.start_to_start .gantt_link_arrow_right { border-left-color: #DD55EA; }
.gantt_task_link.finish_to_start .gantt_line_wrapper div { background-color: #7576ba; }
.gantt_task_link.finish_to_start:hover .gantt_line_wrapper div { box-shadow: 0 0 5px 0px #7576ba; }
.gantt_task_link.finish_to_start .gantt_link_arrow_right { border-left-color: #7576ba; }
.gantt_task_link.finish_to_finish .gantt_line_wrapper div { background-color: #55d822; }
.gantt_task_link.finish_to_finish:hover .gantt_line_wrapper div { box-shadow: 0 0 5px 0px #55d822; }
.gantt_task_link.finish_to_finish .gantt_link_arrow_left { border-right-color: #55d822; }
.gantt_task_link.start_to_finish .gantt_line_wrapper div { background-color: #975000; }
.gantt_task_link.start_to_finish:hover .gantt_line_wrapper div { box-shadow: 0 0 5px 0px #975000; }
.gantt_task_link.start_to_finish .gantt_link_arrow_left { border-right-color: #975000; }
.gantt_critical_task{background:#e63030 !important; border-color:#9d3a3a !important;}
.weekend { background: #f4f7f4 !important; }
.gantt_grid_head_cell.gantt_grid_head_text{overflow:visible;}
.gantt_grid_head_cell.gantt_grid_head_text .btn-group .dropdown-menu{text-align:left;}

#ganttDownload, #ganttHeader {display: none;}
#mainContent:before {background: #fff;}
#mainContent.loading {overflow: hidden}
#mainContent.loading #ganttView {overflow: hidden}
#mainContent.loading #ganttHeader {display: block; padding-bottom: 20px; margin: 0; height: 46px;}
#mainContent.loading #ganttDownload {display: inline}
#mainContent.loading #ganttContainer {padding: 40px;}
#mainContent.loading .scrollVer_cell,
#mainContent.loading .scrollVer_cell {display: none;}

.gantt-fullscreen #header,
.gantt-fullscreen #mainMenu,
.gantt-fullscreen #footer {display: none!important;}
.gantt-fullscreen #mainContent {position: fixed; top: 0; right: 0; bottom: 0; left: 0}
</style>
<?php include "featurebar.html.php";?>
<div id='mainContent' class='main-content load-indicator' data-loading='<?php echo $lang->execution->gantt->exporting;?>'>
  <div id='ganttContainer'>
    <h1 id='ganttHeader'>
      <?php echo $lang->execution->gantt->common . ' - ' . $executionName;?>
      &nbsp; <small id='ganttExportDate'></small>
    </h1>
    <div class='gantt' id='ganttView'></div>
  </div>
  <a id='ganttDownload' download='gantt-export-<?php echo $executionID;?>.png'></a>
  <?php
  $typeHtml  = '<div class="btn-group">';
  $typeHtml .= '<button class="btn btn-link" data-toggle="dropdown"><span class="text">' . $lang->execution->gantt->browseType[$ganttType] . '</span> <span class="caret"></span></button>';
  $typeHtml .= '<ul class="dropdown-menu">';
  foreach($lang->execution->gantt->browseType as $browseType => $typeName)
  {
      $typeHtml .= '<li ' . ($ganttType == $browseType ? "class='active'" : '') . '>' . html::a($this->createLink('execution', 'gantt', "executionID=$executionID&type=$browseType"), $typeName) . '</li>';
  }
  $typeHtml .= '</ul></div>';
  ?>
</div>
<script>
var scriptLoadedMap = {};
var loadingPrefixText = '<?php echo $lang->execution->gantt->exporting;?>';

/**
 * Get remote script for export.
 *
 * @param  string $url
 * @param  function $sucessCallback
 * @param  function $errorCallback
 * @access public
 * @return void
 */
function getRemoteScript(url, sucessCallback, errorCallback)
{
    if(scriptLoadedMap[url]) return sucessCallback && sucessCallback();
    $.getScript(url, function()
    {
        scriptLoadedMap[url] = true;
        if(sucessCallback) sucessCallback();
    }).fail(function()
    {
        if(errorCallback) errorCallback('Cannot load "' + url + '".');
    });
}

/**
 * Update export progress.
 *
 * @param  int $progress
 * @access public
 * @return void
 */
function updateProgress(progress)
{
    var progressText = loadingPrefixText;
    if(progress < 1) progressText += Math.floor(progress * 100) + '%';
    $('#mainContent').attr('data-loading', progressText);
}

/**
 * Draw gantt to canvas.
 *
 * @param  string   $exportType
 * @param  function $sucessCallback
 * @param  function $errorCallback
 * @access public
 * @return void
 */
function drawGanttToCanvas(exportType, sucessCallback, errorCallback)
{
    updateProgress(0);

    exportType = exportType || 'image';
    var $ganttView      = $('#ganttView');
    var oldHeight       = $ganttView.css('height');
    var $ganttContainer = $('#ganttContainer');
    var $ganttDataArea  = $ganttView.find('.gantt_data_area');
    var $ganttDridData  = $ganttView.find('.gantt_grid_data');
    var ganttHeight     = $ganttView.find('.gantt_task_bg').outerHeight() + $ganttView.find('.gantt_grid_scale').outerHeight() + 1;
    var ganttWidth      = $ganttDataArea.outerWidth() + $ganttDridData.outerWidth();

    $ganttContainer.css(
    {
        height: ganttHeight + $('#ganttHeader').outerHeight() + 80,
        width: ganttWidth + 93
    });
    $ganttView.css('height', ganttHeight);

    gantt.render();

    updateProgress(0.1);
    getRemoteScript('<?php echo $jsRoot . 'html2canvas/min.js';?>', function()
    {
        updateProgress(0.2);

        var afterFinish = function(canvas)
        {
            $ganttContainer.css(
            {
                width: '',
                height: ''
            });
            $ganttView.css('height', oldHeight);
            if(canvas) canvas.remove();
        };
        var delayTime = Math.max(1000, Math.floor(10 * (ganttHeight * ganttWidth) / 100000));
        var progressTimer;

        if(delayTime > 1500)
        {
            var startProgress = 0.2;
            var deltaProgress = 0.5 / Math.floor(delayTime/1000);
            progressTimer = setInterval(function()
            {
                startProgress += deltaProgress;
                updateProgress(Math.min(0.7, startProgress));
            }, 1000);
        }

        setTimeout(function()
        {
            if(progressTimer) clearInterval(progressTimer);

            updateProgress(0.7);
            html2canvas($ganttContainer[0], {logging: false}).then(function(canvas)
            {
                var isExportPDF = exportType === 'pdf';
                updateProgress(isExportPDF ? 0.8 : 0.9);
                canvas.onerror = function()
                {
                    afterFinish(canvas);
                    if(errorCallback) errorCallback('Cannot convert image to blob.');
                };

                if(isExportPDF)
                {
                    var width = canvas.width;
                    var height = canvas.height;

                    getRemoteScript('<?php echo $jsRoot . 'pdfjs/min.js';?>', function()
                    {
                        updateProgress(0.8);
                        var pdf = new jsPDF(
                        {
                            format: [width, height],
                            unit: 'px',
                            orientation: width > height ? 'l' : 'p'
                        });
                        pdf.addImage(canvas, 0, 0, pdf.internal.pageSize.getWidth(), pdf.internal.pageSize.getHeight());
                        pdf.save('gantt-export-<?php echo $executionID;?>.pdf');
                        if(sucessCallback) sucessCallback(pdf);
                        afterFinish();
                    }, function(error)
                    {
                        afterFinish(canvas);
                        if(errorCallback) errorCallback(error);
                    });
                }
                else
                {
                    canvas.toBlob(function(blob)
                    {
                        updateProgress(0.95);
                        var imageUrl = URL.createObjectURL(blob);
                        $('#ganttDownload').attr({href: imageUrl})[0].click();
                        if(sucessCallback) sucessCallback(imageUrl);
                        afterFinish(canvas);
                    });
                }
            }).catch(function(error)
            {
                afterFinish();
                if(errorCallback) errorCallback('Cannot draw graphic to canvas.');
            });
        }, delayTime);
    }, errorCallback);
}

/**
 * Export gantt.
 *
 * @param  string $exportType
 * @access public
 * @return void
 */
function exportGantt(exportType)
{
    var $mainContent = $('#mainContent');
    $mainContent.addClass('loading').css('height', Math.max(200, Math.floor($(window).height() - $('#footer').outerHeight() - $('#header').outerHeight() - $('#mainMenu').outerHeight() - 38)));
    $('#ganttExportDate').text(new Date().format('yyyy-MM-dd hh:mm:ss'));
    var afterFinish = function(url)
    {
        setTimeout(function()
        {
            $mainContent.css('height', '').removeClass('loading');
        }, 300);
        updateProgress(1);
    };
    drawGanttToCanvas(exportType, afterFinish, function(errorText)
    {
        afterFinish();
        $.zui.messager.danger('<?php echo $lang->execution->gantt->exportFail;?>' + (errorText || ''));
    });
}

/**
 * Get by id for gantt.
 *
 * @param  array  $list
 * @param  string $id
 * @access public
 * @return string
 */
function getByIdForGantt(list, id)
{
    for (var i = 0; i < list.length; i++)
    {
        if (list[i].key == id) return list[i].label || "";
    }
    return "";
}

/**
 * Zoom tasks.
 *
 * @param  node $node
 * @access public
 * @return void
 */
function zoomTasks(node)
{
    switch(node.value)
    {
        case "day":
            gantt.config.min_column_width = 70;
            gantt.config.scales = [{unit: "year", step: 1, format: "%Y"}, {unit: 'day', step: 1, format: '%m-%d'}];
            gantt.config.scale_height = 22 * gantt.config.scales.length;
        break;
        case "week":
            gantt.config.min_column_width = 70;
            gantt.config.scales = [{unit: "year", step: 1, format: "%Y"}, {unit: 'week', step: 1, format: "<?php echo $lang->execution->gantt->zooming['week'];?> #%W"}, {unit:"day", step:1, date:"%D"}]
            gantt.config.scale_height = 22 * gantt.config.scales.length;
        break;
        case "month":
            gantt.config.min_column_width = 70;
            gantt.config.scales = [{unit: "year", step: 1, format: "%Y"}, {unit: 'month', step: 1, format: '%M'}, {unit:"week", step:1, date:"<?php echo $lang->execution->gantt->zooming['week'];?> #%W"}];
            gantt.config.scale_height = 22 * gantt.config.scales.length;
        break;
    }

    gantt.render();
}

/**
 * Update criticalPath
 *
 * @access public
 * @return void
 */
function updateCriticalPath()
{
    gantt.config.highlight_critical_path = !gantt.config.highlight_critical_path;
    if(gantt.config.highlight_critical_path)
    {
        $('#criticalPath').html(<?php echo json_encode($lang->execution->gantt->hideCriticalPath);?>);
        gantt.config.highlight_critical_path = true;
    }
    else
    {
        $('#criticalPath').html(<?php echo json_encode($lang->execution->gantt->showCriticalPath);?>);
        gantt.config.highlight_critical_path = false;
    }

    gantt.render();
}

$(function()
{
    // Set gantt view height
    var resizeGanttView = function()
    {
        if(gantt.getState().fullscreen) return false;
        $('#ganttView').css('height', Math.max(200, Math.floor($(window).height() - $('#footer').outerHeight() - $('#header').outerHeight() - $('#mainMenu').outerHeight() - 80)));
    };

    var ganttData = $.parseJSON(<?php echo json_encode($executionData);?>);
    if(!ganttData.data) ganttData.data = [];

    gantt.serverList("userList", <?php echo json_encode($userList);?>);

    gantt.config.readonly           = true;
    gantt.config.smart_rendering    = true;
    gantt.config.smart_scales       = true;
    gantt.config.static_background  = true;
    gantt.config.show_task_cells    = false;
    gantt.config.row_height         = 25;
    gantt.config.min_column_width   = 40;
    gantt.config.details_on_create  = false;
    gantt.config.scales             = [{unit: "year", step: 1, format: "%Y"}, {unit: 'day', step: 1, format: '%m-%d'}];
    gantt.config.scale_height       = 22 * gantt.config.scales.length;
    gantt.config.duration_unit      = "day";

    gantt.config.columns = [{name: 'text', width: '*', tree: true, resize: true, width:250}, {name: 'owner_id', align: 'center', resize: true, width:90, template: function(task){return getByIdForGantt(gantt.serverList('userList'), task.owner_id)}}, {name: 'progress', align: 'left', resize: true, width:70, template: function(task) { return Math.round(task.progress * 100) + '%'}}, {name: 'start_date', align: 'center', resize: true, width: 90}, {name: 'deadline', align: 'center', resize: true, width: 90}, {name: 'duration', align: 'center', width: 70}];
    gantt.locale.labels.column_text       = <?php echo json_encode($typeHtml);?>;
    gantt.locale.labels.column_owner_id   = "<?php echo $lang->task->assignedTo;?>";
    gantt.locale.labels.column_progress   = "<?php echo $lang->task->progress;?>";
    gantt.locale.labels.column_start_date = "<?php echo $lang->execution->gantt->startDate;?>";
    gantt.locale.labels.column_deadline   = "<?php echo $lang->execution->gantt->endDate;?>";
    gantt.locale.labels.column_duration   = "<?php echo $lang->execution->gantt->duration;?>";

    gantt.templates.task_class     = function(start, end, task){return 'pri-' + (task.pri || 0);};
    gantt.templates.rightside_text = function(start, end, task)
    {
        return getByIdForGantt(gantt.serverList('userList'), task.owner_id);
    };
    gantt.templates.link_class = function(link)
    {
        var types = gantt.config.links;
        if(link.type == types.finish_to_start)  return 'finish_to_start';
        if(link.type == types.start_to_start)   return 'start_to_start';
        if(link.type == types.finish_to_finish) return 'finish_to_finish';
        if(link.type == types.start_to_finish)  return 'start_to_finish';
    };
    gantt.templates.scale_cell_class = function(date)
    {
        if(date.getDay() == 0 || date.getDay() == 6) return 'weekend';
    };
    gantt.templates.timeline_cell_class = function(item, date)
    {
        if(date.getDay() == 0 || date.getDay() == 6) return 'weekend';
    };
    gantt.attachEvent('onTemplatesReady', function()
    {
        $('#fullScreenBtn').click(function()
        {
            gantt.expand();
        });
    });

    var date2Str         = gantt.date.date_to_str(gantt.config.task_date);
    var today            = new Date();
    var isGanttExpand    = false;
    var delayTimer       = null;
    var handleFullscreen = function()
    {
        if(isGanttExpand)
        {
            $('body').addClass('gantt-fullscreen');
            $('#ganttView').css('height', $(window).height() - 40);
            isGanttExpand = false;
        }
        else
        {
            $('body').removeClass('gantt-fullscreen');
            resizeGanttView();
        }
        delayTimer = null;
    };
    var delayHandleFullscreen = function()
    {
        if(delayTimer) clearTimeout(delayTimer);
        delayTimer = setTimeout(handleFullscreen, 50);
    };

    gantt.addMarker({
        start_date: today,
        css: "today",
        text: "<?php echo $lang->action->dynamic->today;?>",
        title: "<?php echo $lang->action->dynamic->today;?>: " + date2Str(today)
    });
    gantt.attachEvent('onBeforeExpand', function()
    {
        $('body').addClass('gantt-fullscreen');
        isGanttExpand = true;
        return true;
    });

    if(document.addEventListener)
    {
        document.addEventListener('webkitfullscreenchange', delayHandleFullscreen, false);
        document.addEventListener('mozfullscreenchange', delayHandleFullscreen, false);
        document.addEventListener('fullscreenchange', delayHandleFullscreen, false);
        document.addEventListener('MSFullscreenChange', delayHandleFullscreen, false);
    }

    resizeGanttView();
    $(window).resize(resizeGanttView);

    gantt.init('ganttView');
    gantt.parse(ganttData);

    // Show task in modal on click task
    var taskModalTrigger = new $.zui.ModalTrigger({type: 'iframe', width: '80%'});
    gantt.attachEvent('onTaskClick', function(id, e)
    {
        if($(e.srcElement).hasClass('gantt_close') || $(e.srcElement).hasClass('gantt_open')) return false;

        if(typeof id === 'string') id = parseInt(id);
        if(!isNaN(id) && id > 0)
        {
            taskModalTrigger.show({url: createLink('task', 'view', 'taskID=' + id, 'html', true)});
        }
    });

    // Make folder can open or close by click
    $('#ganttView').on('click', '.gantt_close,.gantt_open', function()
    {
        var $task = $(this).closest('.gantt_row_task');
        var task  = gantt.getTask($task.attr('task_id'));
        if(task) gantt[task.$open ? 'close' : 'open'](task.id);
    });
});
</script>
<?php include '../../../common/view/footer.html.php';?>
