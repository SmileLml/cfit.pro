<?php
include '../../control.php';
class myHistory extends history
{
    /** 更新组件zt_component_release表的发布时间
     * @return void
     */
    public function updateComponentPublishTime(){
        $componentList = $this->dao->select("*")->from(TABLE_COMPONENT_RELEASE)->fetchAll();
        $account = 'guestjk';

        if($componentList){
            $datalog = [];
            foreach ($componentList as $component){
                $actionInfo = $this->dao->select("*")->from(TABLE_ACTION)
                    ->where(' 1=1 ')
                    ->andWhere("objectID")->eq($component->id)
                    ->beginIF($component->type == 'public' && $component->componentId)->andWhere("objectType")->eq('componentpublic')->andWhere("action")->eq("publish")->fi()
                    ->beginIF($component->type == 'public' && !$component->componentId)->andWhere("objectType")->eq('componentpublic')->andWhere("action")->eq("created")->fi()
                    ->beginIF($component->type == 'third' && !$component->componentId)->andWhere("objectType")->eq('componentthird')->andWhere("action")->eq("created")->fi()
                    ->beginIF($component->type == 'third' && $component->componentId)->andWhere("objectType")->eq('componentthird')->andWhere("action")->eq("publish")->fi()
                    ->fetch()
                ;

                if($actionInfo){
                    $updata = [];
                    $updata['createTime'] = $actionInfo->date;
                    if($component->type == 'public' && $component->status == 'publish'){
                        $updata['publishTime'] = $actionInfo->date;
                    }



                    $res = $this->dao->update(TABLE_COMPONENT_RELEASE)->data($updata)->where("id")->eq($component->id)->exec();
                    if($res){
                        $changlog = $component->createTime."->".$actionInfo->date;
                        if(isset($updata['publishTime'])){
                            $changlog .= ';'.$component->publishTime."->".$actionInfo->date;
                        }


                        if($component->type == "public"){
                            $objtype = "componentpublic";
                        }elseif ($component->type == "third"){
                            $objtype = "componentthird";
                        }
                        $this->loadModel('action')->create($objtype, $component->id, 'dohistorycreatetime',$changlog,'',$account);

                        $datalog[$component->id] = $changlog;
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