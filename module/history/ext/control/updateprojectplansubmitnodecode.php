<?php
include '../../control.php';
class myHistory extends history
{
    public function updateProjectplanSubmitNodeCode(){
        $reviewnodeList = $this->dao->select("*")->from(TABLE_REVIEWNODE)->where('objectType')->eq('projectplan')->orderBy("id asc")->fetchAll();
        $submitnodeCode[0]   = 'deptLeader';//申请部门负责人意见
        $submitnodeCode[1]  = 'chargeLeader';//申请部门分管领导意见
        $submitnodeCode[2]    = 'deptsSign';//各部门会签意见
        $submitnodeCode[3]  = 'gm';//总经理意见
        $account = 'guestjk';
        if($reviewnodeList){
            $actionNodeList = [];
            foreach ($reviewnodeList as $reviewnode){
                $actionNodeList[$reviewnode->objectID][$reviewnode->version][] = $reviewnode;
            }

            //校验 立项的每个审批版本节点是否正常
            /*foreach ($actionNodeList as $planID=>$actionNode){
                $str = $planID.' versionNum:'.count($actionNode);
                foreach ($actionNode as $node){
                    $str .= "   everyVNum: ".count($node);
                }
                a($str);

            }*/

            foreach ($actionNodeList as $planID=>$actionNode){

                foreach ($actionNode as $nodeList){
                    foreach ($nodeList as $key=>$node){
//
//                        $res = $this->dao->update(TABLE_REVIEWNODE)->set('nodeCode')->eq($submitnodeCode[$key])->where('id')->eq($node->id)->get();
                        $res = $this->dao->update(TABLE_REVIEWNODE)->set('nodeCode')->eq($submitnodeCode[$key])->where('id')->eq($node->id)->exec();
                        a($res);
                    }
                }


            }
//            a($actionNodeList);
            $datalog = [];
            /*foreach ($reviewnodeList as $project){
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
            }*/
            a("数据处理记录：");
            a($datalog);
        }else{
            a("暂无数据");
        }


    }
}