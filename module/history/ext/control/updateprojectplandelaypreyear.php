<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 年度计划是否上一年度结转状态清洗。
     * @return void
     */
    public function updateProjectplanDelayPreYear(){
        $projectplanList = $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->fetchAll();
        $account = 'guestjk';
        foreach ($projectplanList as $projectplan){
//            当【内部项目状态】为已结项、已取消、已撤销时，字段【是否上一年延续】初始化为“否”
            $updata = [];
            //有状态的直接判断  判断 年度计划年份是当年的项目， 则为 “否”
            if(in_array($projectplan->insideStatus,['done','cancel','abort']) || $projectplan->year == date("Y")){
                $updata['isDelayPreYear'] = 2;
                if($projectplan->insideStatus == 'done'){
                    $projectinfo = $this->dao->select("*")->from(TABLE_PROJECT)->where('id')->eq($projectplan->project)->fetch();
                    if($projectinfo){
                        if($projectinfo->realEnd == '0000-00-00'){
                            $tempYear = 1970;
                        }else{
                            $tempYear = date("Y",strtotime($projectinfo->realEnd));
                        }

                        //$tempYear == 1970 ||  已结项但是没有关闭时间 记为 非结转
                        if((date("Y") == $tempYear && $projectplan->year < date("Y"))){
                            $updata['isDelayPreYear'] = 1;
                        }
                    }

                }
            }else{
//            当【内部项目状态】为非上述状态时，字段【是否上一年度延续】初始化为“是”
                $updata['isDelayPreYear'] = 1;
            }

            $res = $this->dao->update(TABLE_PROJECTPLAN)->data($updata)->where("id")->eq($projectplan->id)->exec();
            if($res){
                $this->loadModel('action')->create('projectplan', $projectplan->id, 'dohistoryupisdelaylog',$projectplan->isDelayPreYear."->".$updata['isDelayPreYear'],'',$account);
            }
            $result[$projectplan->id] = $projectplan->isDelayPreYear."->".$updata['isDelayPreYear'];
        }

        a("数据处理记录条数：".count($result));
        a($result);

    }
}