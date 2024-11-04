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
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(common::hasPriv('duty', 'browse')) echo html::a(helper::createLink('duty', 'browse'), $lang->duty->browse . " <span class='label label-light label-badge'>{$dutyCount}</span>", '', "class='btn btn-link'");?>
  </div>
  <div class="btn-toolbar pull-right">
    <?php //if(common::hasPriv('duty', 'batchCreate')) echo html::a($this->createLink('duty', 'batchCreate'), "<i class='icon-plus'></i> {$lang->duty->batchCreate}", '', "class='btn btn-secondary'");?>
    <?php if(common::hasPriv('duty', 'create')) echo html::a($this->createLink('duty', 'create'), "<i class='icon-plus'></i> {$lang->duty->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div class="main-row">
  <div class="main-col">
    <div class="cell">
      <div id="dutyCalendar" class="calendar">
        <header class="calender-header table-row">
          <div class="btn-toolbar col-4 table-col text-middle">
            <button type="button" class="btn btn-info btn-icon btn-mini btn-prev"><i class="icon-chevron-left"></i></button>
            <button type="button" class="btn btn-info btn-mini btn-today"><?php echo $lang->today;?></button>
            <button type="button" class="btn btn-info btn-icon btn-mini btn-next"><i class="icon-chevron-right"></i></button>
            <span class="calendar-caption"></span>
          </div>
        </header>
      </div>
    </div>
  </div>
</div>
<script>
config.ajaxGetDutyUrl   = '<?php echo $this->createLink('duty', 'ajaxGetDutyList', "year={year}");?>';
config.dutyViewUrl      = '<?php echo $this->createLink('duty', 'view', 'id={id}', '', true);?>';
config.batchAddUrl      = '<?php echo $this->createLink('duty', 'create', 'date={date}', '', true);?>'
config.textNetworkError = '<?php echo $lang->textNetworkError;?>';
config.textHasMoreItems = '<?php echo $lang->textHasMoreItems;?>';
</script>
<script>
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
            var $event = $('<div title="' + event.title + '" data-id="' + (event.id || '') + '" class="event">' + '<span class="title">' + event.title + '</span>' + (event.improtantTime == '1' ? "<span><i class='icon icon-flag red' style='font-size: 14px'></i></span>" : '') + '</div>');
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
        var event = $(this).data('event');
        dutyModalTrigger.show({url: config.dutyViewUrl.replace('{id}', event.id)});
        e.stopPropagation();
    }).on('click', '.day', function(e)
    {
        var $day = $(this);
        if($day.parent().hasClass('future')) batchAddModalTrigger.show({url: config.batchAddUrl.replace('{date}', $day.data('date').format('yyyyMMdd')), showHeader:false});
        e.stopPropagation();
    });
});
</script>
<?php include '../../common/view/footer.html.php';?>
