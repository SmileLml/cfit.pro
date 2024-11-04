<?php
public function setNew()
{
        return 'new100';
}

public function buildSearchForm($queryID, $actionURL)
{
  $this->config->testingrequest->search['actionURL'] = $actionURL;
  $this->config->testingrequest->search['queryID']   = $queryID;
  $this->config->testingrequest->search['params']['createdDept']['values'] = array('' => '') + $this->loadModel('dept')->getOptionMenu();

  $this->config->testingrequest->search['params']['app']['values'] = array(''=>'') + $this->loadModel('application')->getPairs();
  $productList      = $this->loadModel('product')->getList();
  $this->config->testingrequest->search['params']['productId']['values'] = array(''=>'') + array_column($productList, 'name' , 'id');
//  $this->config->testingrequest->search['params']['productId']['values'] = $this->loadModel('outwarddeliver')->getCodePairs();


//  $isPaymentList = array();
//  foreach($this->lang->application->isPaymentList as $paymentID => $paymentValue)
//  {
//    if(!$paymentID) continue;
//    $isPaymentList[$paymentID] = $paymentValue;
//  }
//  $this->config->testingrequest->search['params']['isPayment']['values'] += $isPaymentList;

//  $this->config->testingrequest->search['params']['projectPlanId']['values']  = array(''=>'') + $this->loadModel('projectplan')->getCodeProjects(false);
  $this->config->testingrequest->search['params']['projectPlanId']['values'] = array('' => '') + $this->loadModel('projectplan')->getAllProjects();
  $this->config->testingrequest->search['params']['CBPprojectId']['values']   =  array(''=>'') + $this->dao->select('code,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_CBPPROJECT)->fetchPairs();
  $this->config->testingrequest->search['params']['problemId']['values']   = array(''=>'') + $this->loadModel('problem')->getPairsAbstract();
  $this->config->testingrequest->search['params']['demandId']['values']   = array(''=>'') + $this->loadModel('demand')->getPairsTitle('noclosed');
  $this->config->testingrequest->search['params']['requirementId']['values']   = array(''=>'') + $this->dao->select('id,concat(concat(concat(code,"（"),name),"）")')->from(TABLE_REQUIREMENT)->where('status')->ne('deleted')->andwhere('entriesCode')->like('requirements%')->fetchPairs();
  $this->config->testingrequest->search['params']['isPayment']['values'] = array(''=>'') + $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('isPaymentList')->fetchPairs();
  $this->config->testingrequest->search['params']['team']['values'] = array(''=>'') + $this->dao->select('`key`,value')->from(TABLE_LANG)->where('module')->eq('application')->andWhere('section')->eq('teamList')->fetchPairs();
  $this->config->testingrequest->search['params']['secondorderId']['values'] = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
 
  $this->loadModel('search')->setSearchParams($this->config->testingrequest->search);
}