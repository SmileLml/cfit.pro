<?php
class chengfangjinkeExecution extends executionModel
{
    /**
     * Project: chengfangjinke
     * Method: printStage
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:28
     * Desc: This is the code comment. This method is called printStage.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $executions
     * @param $tasks
     * @param int $grade
     * @return array|void
     */
    public function printStage($executions, $tasks, $grade = 1)
    {
        if(empty($executions)) return array();

        $users = $this->loadModel('user')->getPairs('noletter|noclosed');

        foreach($executions as $execution)
        {
            echo "<tr data-id='$execution->id' data-order='$execution->order'>";

            echo "<td class='text-left item' " . (!empty($execution->children) ? 'has-child' : '') .  " data-path='" . $execution->path . "' title='$execution->name' style='padding-left:" . ($grade-1)*40 . "px'>";
            if(!empty($execution->children) || !empty($tasks[$execution->id]))
            {
                echo '<span class="table-nest-icon icon table-nest-toggle plan-toggle ' . ($execution->type !== 'stage' ? 'collapsed' : ''). '" data-path="' . $execution->path . '" data-id="' . $execution->id . '"></span>';
            }
            echo !empty($execution->children) ? $execution->name : html::a(helper::createLink('execution', 'view', 'execution=' . $execution->id), $execution->name);
            if(isset($execution->delay)) echo "<span class='label label-danger label-badge'>{$this->lang->execution->delayed}</span> ";
            echo "</td>";

            echo "<td>" . $execution->begin . "</td>";
            echo "<td>" . $execution->end . "</td>";

            $executionStatus = $this->processStatus('execution', $execution);
            echo "<td class='c-status text-center' title='$executionStatus'>";
            echo "<span class='status-execution status-{$execution->status}'>$executionStatus</span>";
            echo "</td>";

            echo "<td class='hours' title='{$execution->hours->totalEstimate} {$this->lang->execution->workHour}'>{$execution->hours->totalEstimate}{$this->lang->execution->workHourUnit}</td>";
            echo "<td class='hours' title='{$execution->hours->totalConsumed} {$this->lang->execution->workHour}'>{$execution->hours->totalConsumed}{$this->lang->execution->workHourUnit}</td>";
            echo "<td class='hours' title='{$execution->hours->totalLeft} {$this->lang->execution->workHour}'>{$execution->hours->totalLeft}{$this->lang->execution->workHourUnit}</td>";
            echo "<td title='$execution->resource'>" . $execution->resource . "</td>";

            echo "<td class='c-progress'>";
            echo "<div class='progress-pie' data-doughnut-size='90' data-color='#00da88' data-value='{$execution->hours->progress}' data-width='24' data-height='24' data-back-color='#e8edf3'>";
            echo "<div class='progress-info'>{$execution->hours->progress}</div>";
            echo "</div>";
            echo "</td>";

            echo "<td class='c-actions'>";
            common::printIcon('execution', 'edit', "executionID=$execution->id", $execution, 'list');
            echo "</td>";
            echo "</tr>";

            $this->printStage($execution->children, $tasks, $grade + 1);
            if(isset($tasks[$execution->id])) $this->printTask($tasks[$execution->id], $grade + 1, $execution->path);
        }
    }

    /**
     * Project: chengfangjinke
     * Method: printTask
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:28
     * Desc: This is the code comment. This method is called printTask.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $tasks
     * @param int $grade
     * @param string $path
     * @return array|void
     */
    public function printTask($tasks, $grade = 1, $path = '')
    {
        if(empty($tasks)) return array();

        $users = $this->loadModel('user')->getPairs('noletter|noclosed');

        foreach($tasks as $task)
        {
            echo "<tr data-id='$task->id'>";

            $taskPath = $path . '-' . $task->id; // 分隔符不能和阶段一致，防止混淆
            echo "<td class='text-left item' " . (!empty($task->children) ? 'has-child' : '') . " data-path='" . $taskPath . "' title='$task->name' style='padding-left:" . ($grade-1)*40 . "px'>";
            if(!empty($task->children))
            {
                echo '<span class="table-nest-icon icon table-nest-toggle plan-toggle" data-path="' . $taskPath . '" data-id="' . $task->id . '"></span>';
            }
            echo !empty($task->children) ? $task->name : html::a(helper::createLink('task', 'view', 'task=' . $task->id), $task->name);
            $delay = helper::diffDate(helper::today(), $task->deadline);
            if($task->status != 'done' and $task->status != 'closed' and $task->status != 'suspended')
            {
                if($delay > 0) echo "<span class='label label-danger label-badge'>{$this->lang->task->delayed}</span> ";
            }
            echo "</td>";

            echo "<td>" . $task->estStarted . "</td>";
            echo "<td>" . $task->deadline . "</td>";

            $taskStatus = zget($this->lang->task->statusList, $task->status, '');
            echo "<td class='c-status text-center' title='$taskStatus'>";
            echo "<span class='status-task status-{$task->status}'>$taskStatus</span>";
            echo "</td>";

            echo "<td class='hours' title='{$task->estimate} {$this->lang->execution->workHour}'>{$task->estimate}{$this->lang->execution->workHourUnit}</td>";
            echo "<td class='hours' title='{$task->consumed} {$this->lang->execution->workHour}'>{$task->consumed}{$this->lang->execution->workHourUnit}</td>";
            echo "<td class='hours' title='{$task->left} {$this->lang->execution->workHour}'>{$task->left}{$this->lang->execution->workHourUnit}</td>";
            echo "<td title='$task->resource'>" . $task->resource . "</td>";

            echo "<td class='c-progress'>";
            $progress = $task->consumed == 0 ? 0 : round($task->consumed/($task->consumed+$task->left)*100);
            echo "<div class='progress-pie' data-doughnut-size='90' data-color='#00da88' data-value='{$progress}' data-width='24' data-height='24' data-back-color='#e8edf3'>";
            echo "<div class='progress-info'>{$progress}</div>";
            echo "</div>";
            echo "</td>";

            echo "<td class='c-actions'>";
            common::printIcon('task', 'edit',   "taskID=$task->id", $task, 'list');
            if(empty($task->children) and $task->status != 'pause') common::printIcon('task', 'start', "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
            if(empty($task->children) and $task->status == 'pause') common::printIcon('task', 'restart', "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
            if(empty($task->children)) common::printIcon('task', 'close',  "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
            if(empty($task->children)) common::printIcon('task', 'finish', "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
            if(empty($task->children)) common::printIcon('task', 'recordEstimate', "taskID=$task->id", $task, 'list', 'time', '', 'iframe', true);
            echo "</td>";
            echo "</tr>";

            if(!empty($task->children)) $this->printTask($task->children, $grade + 1, $taskPath);
        }
    }

     /**
     ** Get by id with version.
     **
     ** @param  int $executionID
     ** @param  int $version
     ** @access public
     ** @return array
     **/
    public function getByIDAndVersion($executionID, $setImgSize = false,  $version = 0)
    {
        $execution = $this->getByID($executionID, $setImgSize);

        // 从阶段历史版本记录中获取相应数据。
        if($version == 0) return $execution;
        $versionInfo = $this->dao->select('*')->from(TABLE_EXECUTIONSPEC)->where('execution')->eq($executionID)->andWhere('version')->eq($version)->fetch();
        if(empty($versionInfo)) return $execution;
        $execution->name         = $versionInfo->name;
        $execution->milestone    = $versionInfo->milestone;
        $execution->begin        = $versionInfo->begin;
        $execution->end          = $versionInfo->end;
        $execution->realBegan    = $versionInfo->realBegan;
        $execution->realEnd      = $versionInfo->realEnd;
        $execution->planDuration = $versionInfo->planDuration;
        $execution->desc         = $versionInfo->desc;
        return $execution;
    }
}
