<?php
include '../../control.php';
class myHistory extends history
{
    /** 迭代32 更新组件引入申请终态时间
     * @return void
     */
    public function updateComponentFinalstatetime(){
        $componentList = $this->dao->select("*")->from(TABLE_COMPONENT)->where('status')->in("published,reject,incorporate")->fetchAll();
        $account = 'guestjk';

        if($componentList){
            $objectType = 'component';
            $datalog = [];

            foreach ($componentList as $component){
                //如果有值 不需再更新
                if($component->finalstatetime){
                   continue;
                }
                $actionInfoList = $this->dao->select("*")->from(TABLE_ACTION)
                    ->where(' 1=1 ')
                    ->andWhere("objectID")->eq($component->id)
                    ->andWhere("objectType")->eq($objectType)->andWhere("action")->in("publish,incorporate,reviewed")
                    ->orderBy("id_desc")
                    ->fetchAll()
                ;

                if($actionInfoList){
                    $finalstatetime='';
                    foreach ($actionInfoList as $key=>$value){
                        if($value->action == 'incorporate'){
                            $finalstatetime = $value->date;
                            break;
                        }
                        if($value->action == 'publish'){
                            $finalstatetime = $value->date;
                            break;
                        }
                        if($value->action == 'reviewed'){
                            if($value->comment && strpos($value->comment,'不通过') !== false){
                                $finalstatetime = $value->date;
                                break;
                            }

                        }

                    }
                    if($finalstatetime){
                        $updata = [

                            'finalstatetime'=>$finalstatetime,
                        ];
                        $res = $this->dao->update(TABLE_COMPONENT)->data($updata)->where("id")->eq($component->id)->exec();

                        $changelog = 'finalstatetime:'.$component->finalstatetime.'=====new finalstatetime:'.$finalstatetime;
                        $objtype = "component";
                        $this->loadModel('action')->create($objtype, $component->id, 'dohistoryfinalstatetime',$changelog,'',$account);
                        $datalog[$component->id] = $changelog;
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