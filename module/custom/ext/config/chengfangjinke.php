<?php

$config->custom->canAdd['change']         = 'levelList,typeList,categoryList,subCategoryList,isInteriorProList,isMasterProList,isSlaveProList';
$config->custom->canAdd['productplan']    = 'osTypeList,archTypeList,';
$config->custom->canAdd['project']        = 'outsideReportStatusList,insideReportStatusList,roleList,setOrganization,pushWeeklyreportQingZong,setSystemAdmin';
$config->custom->canAdd['opinion']        = 'sourceTypeList,sourceModeList,categoryList,unionList,synUnionList,levelList';
$config->custom->canAdd['testcase']       = 'priList,typeList,stageList,resultList,statusList,categoryList';
////tongyanqi 2022-04-20
$config->custom->canAdd['projectplan']    = 'sourceList,typeList,basisList,storyStatusList,structureList,localizeList,architrcturalTransformList,systemAssembleList,cloudComputingList,passwordChangeList,isImportantList,dataEnterLakeList,basicUpgradeList,platformownerList,isDelayPreYearList,changeNoticeUser'; //tongyanqi 2022-04-19
$config->custom->canAdd['outsideplan']    = 'apptypeList,projectisdelayList,projectischangeList,sourceList,typeList,basisList,storyStatusList,structureList,localizeList,subProjectDemandPartyList,subProjectBearDeptList,subProjectUnitList';
$config->custom->canAdd['review']         = 'objectList,typeList,gradeList,reviewerList';
$config->custom->canAdd['reviewmeeting']  = 'initMeetingCodeList';
$config->custom->canAdd['reviewqz']       = '';
$config->custom->canAdd['processimprove'] = 'processList,involvedList,sourceList,priorityList,isAcceptList';
$config->custom->canAdd['duty']            = 'typeList';
$config->custom->canAdd['residentsupport'] = 'typeList,subTypeList,durationTypeList,postType,modifyScheduling,dateTypeList,areaList';
$config->custom->canAdd['info']           = 'nodeList,fixTypeList,techList,typeList,deliveryTypeList,revertReasonList,cancelLinkageUserList';
$config->custom->canAdd['infoqz']         = 'revertReasonList,cancelLinkageUserList,demandUnitTypeList,demandUnitList1,demandUnitList2,demandUnitList3';
$config->custom->canAdd['modify']         = 'feasibilityAnalysisList,judgeDepList,cooperateDepNameListList,nodeList,operationTypeList,classifyList,typeList,modeList,implementationFormList,changeSourceList,changeStageList,implementModalityList,isBusinessCooperateList,isBusinessJudgeList,isBusinessAffectList,resultList,levelList,revertReasonList,jxLevelList,secondLineReviewList,cancelLinkageUserList';
//implementModalityList
$config->custom->canAdd['modifycncc']     = 'benchmarkVerificationTypeList,feasibilityAnalysisList,judgeDepList,cooperateDepNameListList,nodeList,operationTypeList,classifyList,typeList,modeList,fixTypeList,changeSourceList,changeStageList,isBusinessCooperateList,isBusinessJudgeList,isBusinessAffectList,resultList,levelList,urgentSourceList,changeFormList,automationToolsList,implementModalityNewList';
$config->custom->canAdd['requirement']   = '';
$config->custom->canAdd['demand']         = 'stateList,expireDaysList,suspendList,requirementSuspendList,opinionSuspendList,demandCloseList,outTimeList,demandOutTime,deptReviewList,unLockList,changeSwitchList,feedbackOverErList,productManagerList,secondLineDepStatusList,ifApprovedList';
$config->custom->canAdd['secondorder']    = 'typeList,childTypeList,sourceList,apiDealUserList,secondUserList,taskIdentificationList,externalTypeList,externalSubTypeList,delTypeList,JXApiDealUserList,secondLineDepStatusList,secondLineDepApprovedList,requestCategoryList,urgencyDegreeList';
$config->custom->canAdd['problem']        = 'stateList,severityList,priList,sourceList,IssueStatusList,typeList,problemGradeList,standardVerifyList,problemCauseList,isExtendedUserList,problemOutTime,delayCCUserList,redealUserList,secondLineDepStatusList,secondLineDepApprovedList';
$config->custom->canAdd['application']    = 'attributeList,networkList,runStatusList,fromUnitList,teamList,isPaymentList,continueLevelList,resourceLocatList,belongOrganizationList,facilitiesStatusList,architectureList,userScopeList';
$config->custom->canAdd['risk']           = 'probabilityList,impactList,categoryList,sourceList,strategyList,strategyList,timeFrameList,cancelReasonList';
$config->custom->canAdd['build']          = 'purposeList,roundsList,leaderList';
$config->custom->canAdd['cm']             = 'typeList';
$config->custom->canAdd['demandcollection']      = 'statusList,typeList,writerList,viewerList,copyForList,belongModel,belongPlatform,correctionReasonList';
$config->custom->canAdd['productionchange']      = 'onlineTypeList,ifEffectSystemList,ifReportList';
$config->custom->canAdd['copyrightqz']    = 'devLanguageList,systemList,firstPublicCountryList,techFeatureTypeList,secondLineReviewList';

$config->custom->canAdd['component'] = 'developLanguageList,productManagerReviewer,categoryList,thirdcategoryList,publishStatusList,thirdStatusList,chineseClassifyList,englishClassifyList,carbonCopyList';
$config->custom->canAdd['datamanagement'] = 'testDepartReviewer';
$config->custom->canAdd['copyright']    = 'devLanguageList,firstPublicCountryList,techFeatureTypeList,innovateReviewerList';
$config->custom->canAdd['deptorder']    = 'typeList,childTypeList,sourceList,unionList,secondLineDepStatusList,secondLineDepApprovedList';
$config->custom->canAdd['sectransfer']    = 'transitionPhase';
$config->custom->canAdd['closingitem']    = 'demandAdviseList,constructionAdviseList,toolsType,versionCodeOSSP,feedbackResult,assemblyPerson,toolsPerson,knowledgePerson,preResearchPerson';
$config->custom->canAdd['osspchange']    = 'interfacePerson,systemProcessList,systemVersionList,resultList,changeNoticeList,systemManagerList,QMDmanagerList,maxLeaderList,interfaceClosedList';
$config->custom->canAdd['implementionplan']    = 'levelList';
$config->custom->canAdd['workreport']          = 'leaderList,deptList';
$config->custom->canAdd['requestlog']          = 'userList';
$config->custom->canAdd['productenroll'] = 'appList';
//monthReportPandMStaticDept,monthReportPandMShowStaticDept,monthReportWorkloadDept,monthReportWorkloadShowDept,
$config->custom->canAdd['secondmonthreport'] = 'monthReportCustomUser,monthReportNeedDept,monthReportNeedShowDept,monthReportOrderDept,monthReportSecondLineProject,quarterReportNeedDept';

$config->custom->canAdd['putproduction'] = 'levelList,propertyList,stageList,dataCenterList,cancelList,mailCcList';
$config->custom->canAdd['cmdbsync'] = 'apiDealUserList,reSendUserList';
$config->custom->canAdd['issue'] = 'leaderList,assignToList,frameworkToList';
$config->custom->canAdd['credit'] = 'levelList,changeNodeList,changeSourceList,modeList,typeList,executeModeList';
$config->custom->canAdd['localesupport'] = 'projectList,areaList,stypeList';
$config->custom->canAdd['environmentorder'] = 'originList,priorityList,createByList,reviewerList,executorList';
$config->custom->canAdd['authorityapply'] = 'noticeList,projectAlert,subSystemList,gitLabPermission,svnPermission,jenkinsPermission';
$config->custom->canAdd['api'] = 'jenkinsList';

