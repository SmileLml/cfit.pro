<?php
include '../../control.php';
class myProblem extends problem
{

    public function ajaxGetExecutionSelect($projectID, $executionID = 0,$fixtype = null,$app = null)
    {
        $this->loadModel('project');
        $defaults =  array('0' => '');
        if(!empty($projectID))
        {
            $executions = $this->project->getExecutionByAvailable($projectID);

            if(!empty($executions)) $defaults += $executions;
        }
        $where = '';
        $this->app->loadLang('task');
        $gd = $this->lang->task->stageList['sendgd'] ;
        if($fixtype == 'second'){
            if($app) {
                $appname = '';
                $class= '';
                $apps = explode(',', $app);
                $new = array();
                foreach ($apps as $app) {
                    if($app == '') continue;
                    $appname = $this->dao->select('concat(code,"_",name) as name')->from(TABLE_APPLICATION)->where('id')->eq($app)->fetch('name');
                    $defaults = array_filter($defaults);
                    $where = "readonly = 'readonly'";
                    if(empty($defaults) && $projectID){
                        die(html::input('execution', "", "class= form-control executionClass' notype=1 $where " ));
                    }else if($defaults && $projectID){
                        foreach ($defaults as $key=>$default) {
                          //过滤二线工单
                            if(strstr($default,$gd) !== false){
                               continue;
                            }
                            $defa = trim(strrchr($default,'/'),'/');
                            if($defa == $appname){
                                $executionID = $key;
                                unset($defaults);
                                $new = array($key=>$default);
                                $defaults = $new;
                                $class = $app;
                                break;
                            }
                        }
                    }
                    if($new){
                        break;
                    }
                }
                if($executionID == 'undefined' && !$new){
                    die(html::input('execution', "", "class='form-control executionClass' notype=1 $where " ));
                }
                if($projectID && $defaults ){
                    die(html::select('execution', $defaults, $executionID, "class='form-control chosen executionClass' app =$class notype=2" ));
                }
            }
           /* if($app){
                $appname = $this->dao->select('concat("_",code,name) as name')->from(TABLE_APPLICATION)->where('id')->eq($app)->fetch('name');
            }
            $defaults = array_filter($defaults);
            $where = "readonly = 'readonly'";
            if(empty($defaults) && $projectID){
                die(html::input('execution', "", "class= form-control ' notype=1 $where " ));
            }else if($defaults && $projectID){
                foreach ($defaults as $key=>$default) {
                    $defa = trim(strrchr($default,'/'),'/');
                    if($defa == $appname){
                        $executionID = $key;
                           unset($defaults);
                           $defaults = array();
                           $defaults = array($key=>$default);
                    }
               }
                if($executionID == 'undefined'){
                    die(html::input('execution', "", "class='form-control ' notype=1 $where " ));
                }
            }
            if(empty($projectID)&&empty($defaults)){
                die(html::select('execution', $defaults, $executionID, "class='form-control'  notype=2" ));
            }*/
        }
        $notype = '';
        if( $defaults ){
           $notype = 'notype = 2';
        }

        die(html::select('execution', $defaults, $executionID, "class='form-control chosen executionClass'  $notype" ));
    }
}
