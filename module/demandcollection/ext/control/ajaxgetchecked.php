<?php

include '../../control.php';
class myDemandcollection extends demandcollection
{
    public function ajaxGetChecked($collectionId, $demandId = '')
    {

        $collectionInfo = $this->demandcollection->getByID($collectionId);

        $info = new stdClass();
        $info->title    = $collectionInfo->title;
        $info->newTitle = $collectionInfo->title;
        $info->demandID = $demandId;
        $info->desc     = $collectionInfo->desc; //富文本；默认值：需求收集-需求描述
        $info->reason   = !empty($collectionInfo->analysis) ? '【需求收集ID ' . $collectionId . '】' . $collectionInfo->analysis : ''; //富文本；默认值：需求收集-需求分析；形式：【需求收集IDXXX】需求分析内容
        $info->files    = $collectionInfo->files; //文件上传；默认值：需求收集-附件
        $info->PO       = $collectionInfo->productmanager; //下拉框；默认值：带出需求收集-产品经理，支持可修改
        $info->mailto   = $collectionInfo->copyFor; //下拉框；默认值：需求收集-抄送人

        if(!empty($demandId)){
            /**
             * @var demandinsideModel $demandMode
             * @var requirementinsideModel $requirementModel
             */
            $demandMode = $this->loadModel('demandinside');
            $requirementModel = $this->loadModel('requirementinside');

            $info = $demandMode->getByID($demandId);
            $requirementObj = $requirementModel->getByID($info->requirementID);
            $info->PO = $requirementObj->productManager;
            $info->reason .= !empty($collectionInfo->analysis) ? '<br />【需求收集ID '.$collectionId.'】' . $collectionInfo->analysis : '';
        }

        $info->mailto = explode(',', trim($info->mailto,','));

        die(json_encode($info));
    }

}
