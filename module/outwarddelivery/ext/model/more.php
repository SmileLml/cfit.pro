<?php
public function setNew()
    {
        return 'new100';
    }

public function buildSearchForm($queryID, $actionURL)
{
  $this->app->loadLang('modifycncc');
  $this->config->outwarddelivery->search['actionURL'] = $actionURL;
  $this->config->outwarddelivery->search['queryID']   = $queryID;
  $apps = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
  $this->config->outwarddelivery->search['params']['app']['values'] = array(''=>'') + array_column($apps, 'name', 'id');
  $this->config->outwarddelivery->search['params']['createdDept']['values'] = array('' => '') + $this->loadModel('dept')->getOptionMenu();
  $products = $this->loadModel('product')->getList();
  $this->config->outwarddelivery->search['params']['productId']['values'] = array('' => '') +  array_column($products, 'name' , 'id');
  $this->config->outwarddelivery->search['params']['productLine']['values'] = array('' => '') +  $this->loadModel('productline')->getPairs();
  $this->config->outwarddelivery->search['params']['productCode']['values'] = array('' => '') +  array_column($products, 'code' , 'id');
  $this->config->outwarddelivery->search['params']['projectPlanId']['values'] = array('' => '') + $this->loadModel('projectplan')->getAllProjects();
  $this->config->outwarddelivery->search['params']['CBPprojectId']['values'] = array('' => '') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->where('deleted')->eq('0')->fetchPairs();
  $this->config->outwarddelivery->search['params']['problemId']['values'] = array('' => '') + $this->loadModel('problem')->getPairsAbstract();
  $this->config->outwarddelivery->search['params']['demandId']['values'] = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');
  $this->config->outwarddelivery->search['params']['requirementId']['values'] = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('entriesCode')->like('requirements%')->andWhere('status')->ne('deleted')->fetchPairs();
  $this->config->outwarddelivery->search['params']['testingRequestId']['values'] = array('' => '') + $this->loadModel('testingrequest')->getCodeGiteePairs();
  $this->config->outwarddelivery->search['params']['productEnrollId']['values'] = array('' => '') + $this->loadModel('productenroll')->getCodeGiteePairs();
  $this->config->outwarddelivery->search['params']['modifycnccId']['values'] = array('' => '') + $this->loadModel('modifycncc')->getCodeGiteePairs();
  $this->config->outwarddelivery->search['params']['release']['values'] = array('' => '') + $this->loadModel('release')->getNamePairs();
  $this->config->outwarddelivery->search['params']['isPayment']['values'] = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('isPaymentList')->fetchPairs();
  $this->config->outwarddelivery->search['params']['team']['values'] = $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('teamList')->fetchPairs();
  $this->config->outwarddelivery->search['params']['secondorderId']['values'] = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
  $this->config->outwarddelivery->search['params']['urgentSource']['values'] = array('' => '') + $this->lang->modifycncc->urgentSourceList;

    unset($this->lang->modifycncc->implementModalityNewList[0]);
    $this->config->outwarddelivery->search['params']['implementModality']['values'] = $this->lang->modifycncc->implementModalityList + $this->lang->modifycncc->implementModalityNewList;

  $this->config->outwarddelivery->search['params']['revertReason']['values']= array('' => '');
  $revertReasonList = $this->lang->outwarddelivery->revertReasonList;
  $childTypeList = json_decode($this->lang->outwarddelivery->childTypeList['all'],true);
  foreach($revertReasonList as $key=>$value){
      $this->config->outwarddelivery->search['params']['revertReason']['values'] += array(base64_encode('"RevertReason":"'.$key.'"')=>$value);   //退回原因为json格式，不能只匹配key值
  }
    foreach ($childTypeList as $k=>$v) {
        foreach ($v as $vk=>$vv){
            $this->config->outwarddelivery->search['params']['revertReason']['values'] += array(base64_encode('"RevertReasonChild":"'.$vk.'"')=>$revertReasonList[$k].'-'.$vv);   //退回原因为json格式，不能只匹配key值
        }
    }
  $this->loadModel('search')->setSearchParams($this->config->outwarddelivery->search);
}