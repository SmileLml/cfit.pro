<?php
include '../../control.php';
class myHistory extends history
{
    /** 更新三方组件zt_component_release表的中英文配置
     * @return void
     */
    public function updateCnEnglishSet(){
        $componentList = $this->dao->select("*")->from(TABLE_COMPONENT_RELEASE)->where('type')->eq("third")->fetchAll();
        $account = 'guestjk';

        if($componentList){
            $objectType = 'componentthird';
            $datalog = [];

            foreach ($componentList as $component){
                //如果有值 不需再更新

                if(substr($component->chineseClassify,2) != substr($component->englishClassify,2)){
                    $tempenglish = 'yw'.substr($component->chineseClassify,2);
                    $updata = [

                        'englishClassify'=>$tempenglish,
                    ];
                    $res = $this->dao->update(TABLE_COMPONENT_RELEASE)->data($updata)->where("id")->eq($component->id)->exec();

                    $changelog = 'englishClassify:'.$component->englishClassify.'=====new englishClassify:'.$tempenglish;

                    $this->loadModel('action')->create($objectType, $component->id, 'dohistorycnenglishset',$changelog,'',$account);
                    $datalog[$component->id] = $changelog;

                }

            }
            a("数据处理记录：");
            a($datalog);
        }else{
            a("暂无数据");
        }


    }
}