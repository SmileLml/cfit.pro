<?php
include '../../control.php';
class myHistory extends history
{
    public function updateProjectplanInsideStatus(){
        $projectList = $this->dao->select("*")->from(TABLE_PROJECT)->where('deleted')->eq(0)->andWhere("type")->eq("project")->andWhere("status")->in(["closed","suspended"])->fetchAll();
        $account = 'guestjk';
        if($projectList){
            $datalog = [];
            foreach ($projectList as $project){
                $projectplan = $this->loadModel("projectplan")->getPlanByProjectID($project->id,"id,oldInsideStatus,insideStatus");
                if($projectplan){
                    $updata = [];
                    if($project->status == 'closed'){
                        $updata['insideStatus'] = 'done';
                    }else if($project->status == 'suspended'){
                        $updata['insideStatus'] = 'pause';
                    }

                    if($projectplan->insideStatus){
                        $updata['oldInsideStatus'] = $projectplan->insideStatus;
                    }
                    if($updata){
                        $datalog[$projectplan->id]['old'] = ['insideStatus'=>$projectplan->insideStatus,'oldInsideStatus'=>$projectplan->oldInsideStatus];
                        $datalog[$projectplan->id]['new'] = ['insideStatus'=>$updata['insideStatus'],'oldInsideStatus'=>$projectplan->insideStatus];
                        $res = $this->dao->update(TABLE_PROJECTPLAN)->data($updata)->where("id")->eq($projectplan->id)->exec();
                        if($res){

                            $changlog = 'insideStatus:'.$projectplan->insideStatus."->".$updata['insideStatus'].";oldInsideStatus:".$projectplan->oldInsideStatus.'->'.$projectplan->insideStatus;

                            $this->loadModel('action')->create('projectplan', $projectplan->id, 'dohistoryupinsidestatus',$changlog,'',$account);
                        }
                    }

                }
            }
            a("数据处理记录：");
            a($datalog);
        }else{
            a("暂无数据");
        }


    }
}