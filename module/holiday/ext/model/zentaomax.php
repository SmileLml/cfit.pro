<?php
public function getHolidays($begin, $end)
{
    $records = $this->dao->select('*')->from(TABLE_HOLIDAY)
        ->where('type')->eq('holiday')
        ->andWhere('begin')->le($end)
        ->andWhere('end')->ge($begin)
        ->fetchAll('id');

    $naturalDays = $this->getDaysBetween($begin, $end);

    $holidays = array();
    foreach($records as $record)
    {
        $dates    = $this->getDaysBetween($record->begin, $record->end);
        $holidays = array_merge($holidays, $dates);
    }

    return array_intersect($naturalDays, $holidays);
}

public function getWorkingDays($begin = '', $end = '')
{
    $records = $this->dao->select('*')->from(TABLE_HOLIDAY)
        ->where('type')->eq('working')
        ->andWhere('begin')->le($end)
        ->andWhere('end')->ge($begin)
        ->fetchAll('id');

    $workingDays = array();
    foreach($records as $record)
    {
        $dates = $this->getDaysBetween($record->begin, $record->end);
        $workingDays = array_merge($workingDays, $dates);
    }
    return $workingDays;
}

/**
 *获得指定工作日以后的工作时间
 *
 * @param $begin
 * @param $workingDay
 * @return mixed
 */
public function getActualWorkingDate($begin, $workingDay){
    $actualWorkingDate = $begin;
    if(!$workingDay){
        return $actualWorkingDate;
    }
    //周末类型
    $weekend = isset($this->config->project->weekend) ? $this->config->project->weekend : 2;
    while ($workingDay > 0){
        $actualWorkingDate = date('Y-m-d', strtotime("$actualWorkingDate + 1 days"));
        $holidays    = $this->getHolidays($actualWorkingDate, $actualWorkingDate);
        $workingDays = $this->getWorkingDays($actualWorkingDate, $actualWorkingDate);
        if(in_array($actualWorkingDate, $workingDays)) {
            $workingDay--;
            continue;
        }
        if(in_array($actualWorkingDate, $holidays)) {
            continue;
        }

        $w = date('w', strtotime($actualWorkingDate));
        if($weekend == 2) {
            if($w == 0 or $w == 6) continue;
        } else {
            if($w == 0) continue;
        }
        $workingDay--;
    }
    return $actualWorkingDate;
}

public function getActualWorkingDays($begin, $end)
{
    if(empty($begin) or empty($end) or $begin == '0000-00-00' or $end == '0000-00-00') return array();

    $actualDays = array();
    $currentDay = $begin;

    $holidays    = $this->getHolidays($begin, $end);
    $workingDays = $this->getWorkingDays($begin, $end);
    $weekend     = isset($this->config->project->weekend) ? $this->config->project->weekend : 2;

    /* When the start date and end date are the same. */
    if($begin == $end)
    {
        if(in_array($begin, $workingDays)) return $actualDays[] = $begin;
        if(in_array($begin, $holidays))    return $actualDays;

        $w = date('w', strtotime($begin));
        if($weekend == 2)
        {
            if($w == 0 or $w == 6) return $actualDays;
        }
        else
        {
            if($w == 0) return $actualDays;
        }

        $actualDays[] = $begin;
        return $actualDays;
    }

    for($i = 0; $currentDay < $end; $i ++)
    {
        $currentDay = date('Y-m-d', strtotime("$begin + $i days"));
        $w          = date('w', strtotime($currentDay));

        if(in_array($currentDay, $workingDays))
        {
            $actualDays[] = $currentDay;
            continue;
        }

        if(in_array($currentDay, $holidays)) continue;
        if($weekend == 2)
        {
            if($w == 0 or $w == 6) continue;
        }
        else
        {
            if($w == 0) continue;
        }
        $actualDays[] = $currentDay;
    }

    return $actualDays;
}

public function getDaysBetween($begin, $end)
{
    $beginTime = strtotime($begin);
    $endTime   = strtotime($end);
    $days      = ($endTime - $beginTime) / 86400;

    $dateList  = array();
    for($i = 0; $i <= $days; $i ++) $dateList[] = date('Y-m-d', strtotime("+$i days", $beginTime));

    return $dateList;
}

public function isHoliday($date)
{
    $record = $this->dao->select('*')->from(TABLE_HOLIDAY)
        ->where('type')->eq('holiday')
        ->andWhere('begin')->le($date)
        ->andWhere('end')->ge($date)
        ->fetch();
    return !empty($record);
}

public function isWorkingDay($date)
{
    $record = $this->dao->select('*')->from(TABLE_HOLIDAY)
        ->where('type')->eq('working')
        ->andWhere('begin')->le($date)
        ->andWhere('end')->ge($date)
        ->fetch();
    return !empty($record);
}

public function update($id)
{
   $result = parent::update($id);

   if($result)
   {
        $beginDate = $this->post->begin;
        $endDate   = $this->post->end;

        /* Update project. */
        $this->updateProgramPlanDuration($beginDate, $endDate);
        $this->updateProjectRealDuration($beginDate, $endDate);

        /* Update task. */
        $this->updateTaskPlanDuration($beginDate, $endDate);
        $this->updateTaskRealDuration($beginDate, $endDate);
   }

   return $result;
}

public function create()
{
    $lastInsertID = parent::create();

    if($lastInsertID)
    {
        $beginDate = $this->post->begin;
        $endDate   = $this->post->end;

        /* Update project. */
        $this->updateProgramPlanDuration($beginDate, $endDate);
        $this->updateProjectRealDuration($beginDate, $endDate);

        /* Update task. */
        $this->updateTaskPlanDuration($beginDate, $endDate);
        $this->updateTaskRealDuration($beginDate, $endDate);
    }

    return $lastInsertID;
}

public function delete($id, $null = null)
{
    $holidayInformation = $this->dao->select('begin,end')->from(TABLE_HOLIDAY)->where('id')->eq($id)->fetch();

    $result = parent::delete($id, $null = null);
    if($result)
    {
        /* Update project. */
        $this->updateProgramPlanDuration($holidayInformation->begin, $holidayInformation->end);
        $this->updateProjectRealDuration($holidayInformation->begin, $holidayInformation->end);

        /* Update task. */
        $this->updateTaskPlanDuration($holidayInformation->begin, $holidayInformation->end);
        $this->updateTaskRealDuration($holidayInformation->begin, $holidayInformation->end);
    }

    return $result;
}

public function updateProgramPlanDuration($beginDate, $endDate)
{
    $updateProjectList = $this->dao->select('id, begin, end')
        ->from(TABLE_PROJECT)
        ->where('begin')->between($beginDate, $endDate)
        ->orWhere('end')->between($beginDate, $endDate)
        ->orWhere("(begin < '$beginDate' AND end > '$endDate')")
        ->andWhere('status')->ne('done')
        ->fetchAll();

    foreach($updateProjectList as $project)
    {
        $realDuration = $this->getActualWorkingDays($project->begin, $project->end);
        $realDuration = count($realDuration);

        $this->dao->update(TABLE_PROJECT)
          ->set('planDuration')->eq($realDuration)
          ->where('id')->eq($project->id)
          ->exec();
    }
}

public function updateProjectRealDuration($beginDate, $endDate)
{
    $updateProjectList = $this->dao->select('id, realBegan, realEnd')
        ->from(TABLE_PROJECT)
        ->where('realBegan')->between($beginDate, $endDate)
        ->orWhere('realEnd')->between($beginDate, $endDate)
        ->orWhere("(realBegan < '$beginDate' AND realEnd > '$endDate')")
        ->andWhere('status')->ne('done')
        ->fetchAll();

    foreach($updateProjectList as $project)
    {
        $realDuration = $this->getActualWorkingDays($project->realBegan, $project->realEnd);
        $realDuration = count($realDuration);

        $this->dao->update(TABLE_PROJECT)
          ->set('realDuration')->eq($realDuration)
          ->where('id')->eq($project->id)
          ->exec();
    }
}

public function updateTaskPlanDuration($beginDate, $endDate)
{
    $updateTaskList = $this->dao->select('id, estStarted, deadline')
        ->from(TABLE_TASK)
        ->where('estStarted')->between($beginDate, $endDate)
        ->orWhere('deadline')->between($beginDate, $endDate)
        ->orWhere("(estStarted < '$beginDate' AND deadline > '$endDate')")
        ->andWhere('status') ->ne('done')
        ->fetchAll();

    foreach($updateTaskList as $task)
    {
        $planduration = $this->getActualWorkingDays($task->estStarted, $task->deadline);
        $planduration = count($planduration);

        $this->dao->update(TABLE_TASK)
        ->set('planduration')->eq($planduration)
        ->where('id')->eq($task->id)
        ->exec();
    }

}

public function updateTaskRealDuration($beginDate, $endDate)
{
    $updateTaskList = $this->dao->select('id, realStarted, finishedDate')
        ->from(TABLE_TASK)
        ->where('realStarted')->between($beginDate, $endDate)
        ->orWhere("date_format(finishedDate,'%Y-%m-%d')")->between($beginDate, $endDate)
        ->orWhere("(realStarted < '$beginDate' AND date_format(finishedDate,'%Y-%m-%d') > '$endDate')")
        ->andWhere('status')->ne('done')
        ->fetchAll();

    foreach($updateTaskList as $task)
    {
        $realDuration = $this->getActualWorkingDays($task->realBegan, date('Y-m-d',strtotime($task->finishedDate)));
        $realDuration = count($realDuration);

        $this->dao->update(TABLE_TASK)
        ->set('realDuration')->eq($realDuration)
        ->where('id')->eq($task->id)
        ->exec();
    }
}

// 计算时间间隔天、时、分、秒(有第三个参数时只计算秒数)
public function getTimeBetween($begin, $end, $sec = ''){
    $r = '';
    if(empty($begin) || empty($end)){
        return $r;
    }
    // 过滤节假日
    $holiday = $this->getHolidays($begin, $end);
    $beginTime = strtotime($begin);
    $endTime   = strtotime($end);
    if (empty($holiday)){
        $secs = $endTime - $beginTime;
    }else{
        $days = count($holiday);
        $secs = $endTime - $beginTime - $days * 86400;
    }

    if(empty($sec)){
        $r = $this->secToStr($secs);
    }else{
        $r = $secs;
    }

    return $r;
}

//秒数转时分秒
public function secToStr($secs){
    $r = '';
    if(!empty($secs)){
        if($secs >= 3600){
            $hours = floor($secs/3600);
            $secs = $secs%3600;
            if($hours < 10){
                $r = '0'.$hours.':';
            }else{
                $r = $hours.':';
            }
        }else{
            $r = '00:';
        }
        if($secs >= 60){
            $min = floor($secs/60);
            $secs = $secs%60;
            if($min < 10){
                $r .= '0'.$min.':';
            }else{
                $r .= $min.':';
            }
        }else{
            $r .= '00:';
        }
        if($secs < 10){
            $r .= '0'.$secs;
        }else{
            $r .= $secs;
        }
    }

    return $r;
}