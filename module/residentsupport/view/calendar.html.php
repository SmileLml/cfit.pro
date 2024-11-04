<?php
/**
 * The view file of my module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Yangyang Shi <shiyangyang@cnezsoft.com>
 * @package     calendar
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/ext/view/calendar.html.php';?>
<style>
    .showDetails{padding: 20px;background-color: #fff;position: fixed;z-index: 1041;top:120px;display: flex;box-shadow:3px 3px 3px 3px #3333;border-radius: 8px;flex-flow:wrap;justify-content: start;}
    .cell-day:hover .showDetails{display:flex!important; }
    .detailsList{margin-bottom: 15px;}
    .detailsTitle{font-size:16px;}
    .detailsList>div{margin-bottom: 6px;}
    .detailsDay{text-indent: 1em;}
    .colorWhite{background-color: #fff!important;}
</style>

<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-right">
        <?php
        if(common::hasPriv('residentsupport', 'calendarexport')){?>
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('residentsupport', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('residentsupport', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('residentsupport', 'export') ? $this->createLink('residentsupport', 'export', "") : '#';
                echo "<li $class>" . html::a($link, $lang->residentsupport->export, '', $misc) . "</li>";

                $class = common::hasPriv('residentsupport', 'exportRostering') ? '' : "class=disabled";
                $misc  = common::hasPriv('residentsupport', 'exportRostering') ? "data-toggle='modal' data-type='iframe' class='exportRostering'" : "class=disabled";
                $link  = common::hasPriv('residentsupport', 'exportRostering') ? $this->createLink('residentsupport', 'exportRostering', "") : '#';
                echo "<li $class>" . html::a($link, $lang->residentsupport->exportRostering, '', $misc) . "</li>";

                $class = common::hasPriv('residentsupport', 'exportRosteringData') ? '' : "class=disabled";
                $misc  = common::hasPriv('residentsupport', 'exportRosteringData') ? "data-toggle='modal' data-type='iframe' class='exportRosteringData'" : "class=disabled";
                $link  = common::hasPriv('residentsupport', 'exportRosteringData') ? $this->createLink('residentsupport', 'exportRosteringData', "") : '#';
                echo "<li $class>" . html::a($link, $lang->residentsupport->exportRosteringData, '', $misc) . "</li>";
                ?>
            </ul>
        </div>
        <?php    }
        ?>
        <?php
        if(common::hasPriv('residentsupport', 'calendarimport')){?>
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-import muted"></i> <span class="text"><?php echo $lang->import ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='importActionMenu'>
                <?php

                $class = common::hasPriv('residentsupport', 'import') ? '' : "class=disabled";
                $misc  = common::hasPriv('residentsupport', 'import') ? "data-toggle='modal' data-type='iframe' class='exportTemplate'" : "class=disabled";
                $link  = common::hasPriv('residentsupport', 'import') ? $this->createLink('residentsupport', 'import', "importType=rostering") : '#';
                echo "<li $class>" . html::a($link, $lang->residentsupport->import, '', $misc) . "</li>";

                ?>
            </ul>
        </div>
        <?php }?>
        <?php
            $logRole = common::hasPriv("residentwork",'recordDutyLog') ? '1' : '0';//判断是否有填写值班日志的权限
            $attr = common::hasPriv('residentsupport','rostering') ? "" : 'disabled';
            $link  = common::hasPriv('residentsupport', 'rostering') ? $this->createLink('residentsupport', 'rostering') : '#';

        ?>
        <a href="<?php echo $link;?>" class="btn btn-primary" <?php echo $attr;?>><i class="icon icon-plus"></i><?php echo $lang->residentsupport->rostering;?></a>
    </div>
</div>
<div class="main-row">
  <div class="main-col">
    <div class="cell">
      <div id="dutyCalendar" class="calendar">
        <header class="calender-header table-row">
          <div class="btn-toolbar col-4 table-col text-middle">
            <button type="button" class="btn btn-info btn-icon btn-mini btn-prev" onclick="editColor()"><i class="icon-chevron-left"></i></button>
            <button type="button" class="btn btn-info btn-mini btn-today"><?php echo $lang->today;?></button>
            <button type="button" class="btn btn-info btn-icon btn-mini btn-next" onclick="editColor()"><i class="icon-chevron-right"></i></button>
            <span class="calendar-caption"></span>
          </div>
        </header>
      </div>
    </div>
  </div>
</div>

<script>
config.ajaxGetDutyUrl   = '<?php echo $this->createLink('residentsupport', 'ajaxGetCalendarList', "year={year}");?>';
config.dutyViewUrl      = '<?php echo $this->createLink('duty', 'view', 'id={id}', '', true);?>';
config.batchAddUrl      = '<?php echo $this->createLink('residentwork', 'recordDutyLog', 'dayId={date}', '', true);?>'
config.textNetworkError = '<?php echo $lang->textNetworkError;?>';
config.textHasMoreItems = '<?php echo $lang->textHasMoreItems;?>';
</script>
<script>
    function editColor() {
        setInterval(function (args) {
            $(".cell-day .events").each(function () {
                if ($(this).html() == '') {
                    $(this).parent().parent().parent().addClass("colorWhite");
                    $(this).parent().parent().siblings().remove()
                }
            })
        },20);
    }
var dutyModalTrigger = new $.zui.ModalTrigger(
{
    width: '70%',
    type: 'iframe',
    rememberPos: 'dutyViewModal',
    waittime: 5000
});

var batchAddModalTrigger = new $.zui.ModalTrigger(
{
    width: '80%',
    type: 'iframe',
    waittime: 5000
});

var displayDate = 0;
var calendar    = false;
$(function()
{
    var list = {};
    var _innerWidth = $(window).innerWidth();
    var expandedDays   = {};
    var minExpandCount = 6;
    var $calendar      = $('#dutyCalendar');
    var toggleLoading  = function(loading)
    {
        $calendar.toggleClass('loading', !!loading);
    };
    calendar = $calendar.calendar(
    {
        dragThenDrop: false,
        hideEmptyWeekends: true,
        data:
        {
            events: [],
            calendars:
            {
                defaultCal: {color: '#fff'}
            }
        },
        beforeDisplay: function(display, doDisplay) {
            var date = display.date;
            var thisDisplayDate = date.getFullYear();
            if(displayDate === thisDisplayDate)
            {
                return doDisplay();
            }
            else
            {
                displayDate = thisDisplayDate;
            }

            var calendar = this;
            toggleLoading(true);

            $.ajax(
            {
                url: config.ajaxGetDutyUrl.replace('{year}', date.getFullYear()),
                dataType: 'json',
                success: function(data)
                {
                    $("#data").val(JSON.stringify(data))
                    list = data
                    calendar.resetData({events: data});
                    doDisplay();
                },
                error: function()
                {
                    $.zui.messager.danger(config.textNetworkError);
                },
                complete: function() {toggleLoading(false);}
            });
            return false;
        },
        eventCreator: function(event, $cell, calendar)
        {
            if ($cell.children().hasClass("showDetails")){
                $cell.children(".showDetails").remove()
            }
            var $event = {};
            if (event.title != ''){
                $cell.css("background-color",event.color);
                if ($cell.hasClass("colorWhite")){
                    $cell.removeClass("colorWhite")
                }
                if ($cell.attr("data-status") != 0){
                    $cell.attr("data-status",event.status)
                }

                $cell.attr("data-dayId",event.id);
                $event = $('<div title="' + event.title + '" data-id="' + (event.id || '') + '" class="event">' + '<span class="title">' + event.title + '</span>' + (event.improtantTime == '1' ? "<span><i class='icon icon-flag red' style='font-size: 14px'></i></span>" : '') + '</div>');
                if ($cell.children().hasClass("showDetails") == false){
                    var _length = list.length;
                    var arr = [];
                    for (var i = 0; i < _length; i++){
                        var eventtime = getTime(event.start);
                        var startTime = getTime(list[i]['start']);
                        if (eventtime == startTime){
                            arr.push(list[i]);
                        }
                    }
                    var _eventLength = arr.length;
                    var htmlTagStar = '<div class="showDetails">';
                    for (var j = 0; j < _eventLength; j++){
                        htmlTagStar += '<div class="detailsList">\n'+
                            '                    <div class="detailsTitle">'+arr[j]['title']+'</div>';
                        for (var m = 0; m < arr[j]['details'].length; m++){
                            htmlTagStar += '<div class="detailsDay">'+arr[j]['details'][m]['dutyName']+'</div>'
                        }
                        htmlTagStar += '</div>'
                    }
                    htmlTagStar += '</div>'
                    $cell.append(htmlTagStar);
                    var showWidth = $cell.children(".showDetails").css("width");
                    $cell.children(".showDetails").css("display","none")
                    var _left = (_innerWidth - 120 - parseInt(showWidth)) / 2
                    $cell.children(".showDetails").css("left",_left+'px')
                }
            }
            return $event;
        },
        dayFormater: function($cell, date, dayEvents, calendar)
        {
            if(dayEvents && dayEvents.maxPos >= minExpandCount)
            {
                var hideManyEvents = !expandedDays[date.toDateString()];
                $cell.toggleClass('hide-many-events', hideManyEvents);
                if(hideManyEvents)
                {
                    var $cellContent = $cell.find('.day > .content');
                    var $showMore = $cellContent.find('.show-more-events');
                    if(!$showMore.length)
                    {
                        $showMore = $('<div class="show-more-events" />').appendTo($cellContent);
                    }
                    else
                    {
                        $showMore.show();
                    }
                    $showMore.text(config.textHasMoreItems.format(dayEvents.maxPos - minExpandCount + 1));
                }
            }
            else
            {
                $cell.removeClass('hide-many-events');
            }
            if($cell.is('.future')) $cell.addClass('with-plus-sign');
        },
    }).data('zui.calendar');
    $calendar.on('click', '.show-more-events', function(e)
    {
        var $cell = $(this).hide().closest('.cell-day');
        $cell.removeClass('hide-many-events');
        expandedDays[$cell.find('.day').attr('data-date')] = true;
        e.stopPropagation();
    }).on('click', '.event', function(e)
    {
        // var event = $(this).data('event');
        // dutyModalTrigger.show({url: config.dutyViewUrl.replace('{id}', event.id)});
        // e.stopPropagation();
    }).on('click', '.day', function(e)
    {
        var $logRole = '<?php echo $logRole?>';
        var $day = $(this);
        var ClickDate = $day.attr("data-date")
        ClickDate = getTime(ClickDate)
        var isClick = $day.parent().attr("data-status");//是否可以点击
        var nowTime = getTime()
        var status = 0;
        if (ClickDate <= nowTime){
            status = 1;
        }
        var _number = $(".number").parent().parent().attr("data-date");
        if($day.siblings().hasClass('showDetails') && status == 1 && $logRole == 1 && isClick == 1){
            var day_date = getTime($day.attr("data-date"),',');
            batchAddModalTrigger.show({url: config.batchAddUrl.replace('{date}', day_date), showHeader:false})
            $(".modal-header").attr("style","");
        }

        e.stopPropagation();
    });
});
//获取时间
function getTime(ClickDate = '',separator = '-') {
    if (ClickDate != ''){
        var date = new Date(ClickDate);
    }else{
        var date = new Date();
    }
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    m = m < 10 ? ('0' + m) : m;
    var d = date.getDate();
    d = d < 10 ? ('0' + d) : d;
    var time = y + separator + m + separator + d
    return time;
}
</script>
<?php include '../../common/view/footer.html.php';?>
