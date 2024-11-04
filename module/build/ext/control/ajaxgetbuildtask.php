<?php
include '../../control.php';
class myBuild extends build
{

    public function ajaxGetBuildTask($app,$projectID, $productID,$version,$orderBy = 'openedDate_asc,id_asc')
    {
        //查询关联表
     
     /* if($orderBy != 'openedDate_asc,id_asc'){
          $executAndTask = array_filter(explode(',',$orderBy));
          $execution = isset($executAndTask[0]) ? $executAndTask[0] : 0;
          $taskid  = isset($executAndTask[1]) ? $executAndTask[1] : 0;
          $tasks = $this->dao->select('concat(t1.name,"/",t2.name) name,t2.id,t1.execution ' )->from(TABLE_TASK)->alias('t1')
              ->leftJoin(TABLE_TASK)->alias('t2')
              ->on('t1.id = t2.parent')
              ->where('t1.deleted')->eq('0')
              ->andWhere('t2.id')->in($taskid)
              ->fetch();

          $executions = $this->dao->select('concat(t1.name,"/",t2.name) name,t1.id')->from(TABLE_EXECUTION)->alias('t1')
              ->leftJoin(TABLE_EXECUTION)->alias('t2')
              ->on('t1.id = t2.parent')
              ->where('t1.deleted')->eq('0')
              ->andWhere('t2.id')->in($execution)
              ->fetch();
          $executionList = array($tasks->execution.','.$tasks->id=> $executions->name .'/'.$tasks->name);
      }else {*/
          $build_task = $this->dao->select('*')->from(TABLE_TASK_DEMAND_PROBLEM)
              ->where('deleted')->eq('0')
              ->andWhere('project')->eq((int)$projectID)
              ->andWhere('application')->eq((int)$app)
              ->andWhere('product')->eq((int)$productID)
              ->andWhere('version')->eq((int)$version)
              ->fetchAll();

          //存在，直接显示
          if($build_task && count(array_unique(array_column($build_task,'taskid') )) == 1){

              $taskid = array_column($build_task,'taskid'); //$build_task->taskid;
              $tasks = $this->dao->select('concat(t1.name,"/",t2.name) name,t2.id,t1.execution ' )->from(TABLE_TASK)->alias('t1')
                  ->leftJoin(TABLE_TASK)->alias('t2')
                  ->on('t1.id = t2.parent')
                  ->where('t1.deleted')->eq('0')
                  ->andWhere('t2.id')->in($taskid)
                  ->fetch();

              $executions = $this->dao->select('concat(t1.name,"/",t2.name) name,t1.id')->from(TABLE_EXECUTION)->alias('t1')
                  ->leftJoin(TABLE_EXECUTION)->alias('t2')
                  ->on('t1.id = t2.parent')
                  ->where('t1.deleted')->eq('0')
                  ->andWhere('t2.id')->in($tasks->execution)
                  ->fetch();
              $executionList = array($tasks->execution.','.$tasks->id=> $executions->name .'/'.$tasks->name);
          }else{
              //不存在，查询所有
             
              $build_task = $this->dao->select('*')->from(TABLE_TASK_DEMAND_PROBLEM)
                  ->where('deleted')->eq('0')
                  ->andWhere('project')->eq((int)$projectID)
                  ->andWhere('execution')->ne(0)
                  ->fetchAll();
              $tasks = $this->dao->select('concat(t1.name,"/",t2.name) name,t2.id,t1.execution ' )->from(TABLE_TASK)->alias('t1')
                  ->leftJoin(TABLE_TASK)->alias('t2')
                  ->on('t1.id = t2.parent')
                  ->where('t1.deleted')->eq('0')
                  ->andWhere('t2.id')->in(array_column($build_task,'taskid'))
                  ->fetchAll();
              $executions = $this->dao->select('concat(t1.name,"/",t2.name) name,t2.id,t2.parent')->from(TABLE_EXECUTION)->alias('t1')
                  ->leftJoin(TABLE_EXECUTION)->alias('t2')
                  ->on('t1.id = t2.parent')
                  ->where('t1.deleted')->eq('0')
                  ->andWhere('t2.id')->in(array_column($tasks,'execution'))
                  ->fetchAll('id');
              /* foreach ($executions as $key=>$item) {

                   $executionList[$item->id.','.$tasks[$item->id]->id] = $item->name.'/'.$tasks[$item->id]->name;
               }*/
              foreach ($tasks as $item) {
                  //阶段,任务 拼接
                  if(isset($executions[$item->execution])){
                      $executionList[$item->execution.','.$item->id] =  $executions[$item->execution]->name.'/'.$item->name;
                  }
              }
              $executionList = isset($executionList) ? array('' => '') + $executionList : ''  ;
          }
     // }

       if($orderBy != 'openedDate_asc,id_asc' && isset($executionList))
       {
           echo  html::select('taskName', $executionList, $orderBy,"class='form-control' required onChange='taskNameChange()'");
       }
       else if(isset($executionList)) echo  html::select('taskName', $executionList, $executionList,"class='form-control'  onChange='taskNameChange()'");
       else echo '<select id="taskName" name="taskName" class="form-control"></select>';
    }

}
