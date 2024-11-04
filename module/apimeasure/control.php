<?php

/**
 * The control file of api of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     api
 * @version     $Id: control.php 5143 2013-07-15 06:11:59Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
class apimeasure extends control
{
    static $_statusList = [
        '701' => 'filename error',  //下载文件名错误
        '702' => 'sign error',      //签名错误
        '703' => 'download exists', //其他进程正在下载此文件
    ];

    /**
     * Return session to the client.
     *
     * @access public
     * @return void
     */
    public function getSessionID()
    {
        $this->session->set('rand', mt_rand(0, 10000));
        $this->view->sessionName = session_name();
        $this->view->sessionID = session_id();
        $this->view->rand = $this->session->rand;
        $this->display();
    }

    /**
     * Execute a module's model's method, return the result.
     *
     * @param string $moduleName
     * @param string $methodName
     * @param string $params param1=value1,param2=value2, don't use & to join them.
     * @access public
     * @return string
     */
    public function getModel($moduleName, $methodName, $params = '')
    {
        if (!$this->config->features->apiGetModel) die(sprintf($this->lang->api->error->disabled, '$config->features->apiGetModel'));

        $params = explode(',', $params);
        $newParams = array_shift($params);
        foreach ($params as $param) {
            $sign = strpos($param, '=') !== false ? '&' : ',';
            $newParams .= $sign . $param;
        }

        parse_str($newParams, $params);
        $module = $this->loadModel($moduleName);
        $result = call_user_func_array(array(&$module, $methodName), $params);
        if (dao::isError()) die(json_encode(dao::getError()));
        $output['status'] = $result ? 'success' : 'fail';
        $output['data'] = json_encode($result);
        $output['md5'] = md5($output['data']);
        $this->output = json_encode($output);
        die($this->output);
    }

    /**
     * The interface of api.
     *
     * @param int $filePath
     * @param int $action
     * @access public
     * @return void
     */
    public function debug($filePath, $action)
    {
        $filePath = helper::safe64Decode($filePath);
        if ($action == 'extendModel') {
            $method = $this->api->getMethod($filePath, 'Model');
        } elseif ($action == 'extendControl') {
            $method = $this->api->getMethod($filePath);
        }

        if (!empty($_POST)) {
            $result = $this->api->request($method->className, $method->methodName, $action);
            $content = json_decode($result['content']);
            $status = $content->status;
            $data = json_decode($content->data);
            $data = '<xmp>' . print_r($data, true) . '</xmp>';

            $response['result'] = 'success';
            $response['status'] = $status;
            $response['url'] = $result['url'];
            $response['data'] = $data;
            $this->send($response);
        }

        $this->view->method = $method;
        $this->view->filePath = $filePath;
        $this->display();
    }

    /**
     * Query sql.
     *
     * @param string $keyField
     * @access public
     * @return void
     */
    public function sql($keyField = '')
    {
        if (!$this->config->features->apiSQL) die(sprintf($this->lang->api->error->disabled, '$config->features->apiSQL'));

        $sql = isset($_POST['sql']) ? $this->post->sql : '';
        $output = $this->api->sql($sql, $keyField);

        $output['sql'] = $sql;
        $this->output = json_encode($output);
        die($this->output);
    }

    /**
     * 接口验证token
     * @param $queryString
     */
    public function checkApiToken()
    {
        parse_str($this->server->query_String, $queryString);
        if (empty($queryString['time']) || empty($queryString['token']) || empty($queryString['code'])) {
            $this->response('PARAMS_ERROR');
        }
        $this->loadModel('entry');
        $entry = $this->entry->getByCode($queryString['code']);
        $timestamp = $queryString['time'];
        if (abs(time() - $timestamp) > 3600) $this->response('TIMESTAMP_EXPIRED'); //todo 一小时过期
        $result = $queryString['token'] == md5($entry->code . $entry->key . $queryString['time']);
        if ($result == false) $this->response('TOKEN_ERROR');
    }

    /**
     * 获取测试token
     */
    public function getTestToken()
    {
        parse_str($this->server->query_String, $queryString);
        $this->loadModel('entry');
        $entry = $this->entry->getByCode($queryString['code']);
        die("code:" . $entry->code . "<br>key:" . $entry->key . "<br>time:" . time() . '<br>token:' . md5($entry->code . $entry->key . time())); //获取测试token
    }

    /**
     * Response.
     *
     * @param string $code
     * @access public
     * @return void
     */
    public function response($code, $status = '')
    {
        $response = new stdclass();
        $response->errcode = $code;
        if ($status && is_numeric($status)) {
            header('HTTP/1.1 ' . $status . ' ' . self::$_statusList[$status]);
        }
        die(helper::jsonEncode($response));
    }

    public function success($code, $data)
    {
        $response = new stdclass();
        $response->code = $code;
        $response->data = $data;
        die(helper::jsonEncode($response));
    }

    const PARAMS_MISSING = 1001; //缺少参数
    const PARAMS_ERROR = 1002; //缺少参数
    const FAIL_CODE = 999;    //请求失败

    /**
     * @param $SqlStr
     * @param $varArr
     * @return array
     * 执行防SQL注入
     */
    private function SqlPrepare($SqlStr, $varArr, $varArrType=[])
    {
        $stmt = $this->slaveDBH->prepare($SqlStr);
        foreach ($varArr as $item => $value) {
            if (isset($varArrType) && array_key_exists($item, $varArrType)){
                $stmt->bindValue(':' . $item, $value, $varArrType[$item]);
            }else{
                $stmt->bindValue(':' . $item, $value);
            }
        }
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     *
     * 获取部门维度报工数据
     */
    public function getdeptconsumed()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('effort', '度量-工作量');
        // 接口token校验
        $this->checkApiToken();
        $varArr = [];
        $varArr['start'] = $_POST['start'];
        $varArr['end'] = $_POST['end'];

        // 查询工作量部门维度数据
        $sql = "select 
(CASE WHEN zd.parent = 0 THEN zd.name ELSE (SELECT name from zt_dept where id = zd.parent) END) AS departmentUpName,
            (CASE WHEN zd.parent = 0 THEN zd.id ELSE (SELECT id from zt_dept where id = zd.parent) END) AS departmentId,
            (CASE WHEN zd.parent = 0 THEN zd.`order` ELSE (SELECT `order` from zt_dept where id = zd.parent) END) AS 'order',
        ROUND(SUM(a.projectInside), 2)  projectInside,ROUND(SUM(a.projectOut), 2)  projectOut,ROUND(SUM(a.projectTotal), 2)  projectTotal,ROUND(SUM(a.secondInside), 2)  secondInside,
        ROUND(SUM(a.secondOut), 2)  secondOut,ROUND(SUM(a.secondTotal), 2)  secondTotal,ROUND(SUM(a.deptInside), 2)  deptInside,ROUND(SUM(a.deptOut), 2)  deptOut,
        ROUND(SUM(a.deptTotal), 2)  deptTotal,ROUND(SUM(a.sumInside), 2)  sumInside,ROUND(SUM(a.sumOut), 2)  sumOut,ROUND(SUM(a.sumTotal), 2)  sumTotal
FROM 
(SELECT 
zp.code code,zu.account account,ze.deptId deptId,
        ROUND(SUM(IF(RIGHT(zp.code, 4) != '_DEP' AND RIGHT(zp.code, 3) != '_二线' AND RIGHT(zp.code, 3) != '_EX' AND LEFT(zu.realname,2) != 't_', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS projectInside,
        ROUND(SUM(IF(RIGHT(zp.code, 4) != '_DEP' AND RIGHT(zp.code, 3) != '_二线' AND RIGHT(zp.code, 3) != '_EX' AND LEFT(zu.realname,2) = 't_', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS projectOut,
        ROUND(SUM(IF(RIGHT(zp.code, 4) != '_DEP' AND RIGHT(zp.code, 3) != '_二线' AND RIGHT(zp.code, 3) != '_EX', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS projectTotal,
        ROUND(SUM(IF((RIGHT(zp.code, 3) = '_二线' OR RIGHT(zp.code, 3) = '_EX') AND LEFT(zu.realname,2) != 't_', CAST(ze.consumed AS DECIMAL(11,1)) , 0))/(zp.workHours * 8), 4) AS secondInside,
        ROUND(SUM(IF((RIGHT(zp.code, 3) = '_二线' OR RIGHT(zp.code, 3) = '_EX')  AND LEFT(zu.realname,2) = 't_', CAST(ze.consumed AS DECIMAL(11,1)) , 0))/(zp.workHours * 8), 4) AS secondOut,
        ROUND(SUM(IF((RIGHT(zp.code, 3) = '_二线' OR RIGHT(zp.code, 3) = '_EX') , CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS secondTotal,
        ROUND(SUM(IF(RIGHT(zp.code, 4) = '_DEP' AND LEFT(zu.realname,2) != 't_', CAST(ze.consumed AS DECIMAL(11,1)) , 0))/(zp.workHours * 8), 4) AS deptInside,
        ROUND(SUM(IF(RIGHT(zp.code, 4) = '_DEP' AND LEFT(zu.realname,2) = 't_', CAST(ze.consumed AS DECIMAL(11,1)) , 0))/(zp.workHours * 8), 4) AS deptOut,
        ROUND(SUM(IF(RIGHT(zp.code, 4) = '_DEP' , CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS deptTotal,
        ROUND(SUM(IF(LEFT(zu.realname,2) != 't_', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS sumInside,
        ROUND(SUM(IF(LEFT(zu.realname,2) = 't_', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS sumOut,
        ROUND(SUM(CAST(ze.consumed AS DECIMAL(11,1)))/(zp.workHours * 8), 4) AS sumTotal 
        FROM `zt_effort` AS ze  
        INNER JOIN `zt_project` AS zp  ON zp.id=ze.project
        INNER JOIN `zt_user` AS zu  ON zu.account=ze.account  wHeRe ze.deleted  = '0' AND  ze.`date`  BETWEEN :start  AND  :end  gRoUp bY zp.id,zu.id,ze.deptId ) a
        INNER JOIN zt_dept zd ON zd.id = a.deptId group by departmentUpName ;
        ";

        $dataList = $this->SqlPrepare($sql, $varArr);

        $this->saveLogs1("getdeptconsumed-detail----Sql".$sql);


        $sumSql ="select 
        ROUND(SUM(a.projectInside), 2)  projectInside,ROUND(SUM(a.projectOut), 2)  projectOut,ROUND(SUM(a.projectTotal), 2)  projectTotal, ROUND(SUM(a.secondInside), 2)  secondInside,ROUND(SUM(a.secondOut), 2)  secondOut,
        ROUND(SUM(a.secondTotal), 2)  secondTotal,ROUND(SUM(a.deptInside), 2)  deptInside,ROUND(SUM(a.deptOut), 2)  deptOut,ROUND(SUM(a.deptTotal), 2)  deptTotal,ROUND(SUM(a.sumInside), 2)  sumInside,
        ROUND(SUM(a.sumOut), 2)  sumOut,ROUND(SUM(a.sumTotal), 2)  sumTotal
        FROM 
        (SELECT zp.code code,zu.account account,ze.deptId deptId,
        ROUND(SUM(IF(RIGHT(zp.code, 4) != '_DEP' AND RIGHT(zp.code, 3) != '_二线' AND RIGHT(zp.code, 3) != '_EX' AND LEFT(zu.realname,2) != 't_', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS projectInside,
        ROUND(SUM(IF(RIGHT(zp.code, 4) != '_DEP' AND RIGHT(zp.code, 3) != '_二线' AND RIGHT(zp.code, 3) != '_EX' AND LEFT(zu.realname,2) = 't_', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS projectOut,
        ROUND(SUM(IF(RIGHT(zp.code, 4) != '_DEP' AND RIGHT(zp.code, 3) != '_二线' AND RIGHT(zp.code, 3) != '_EX', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS projectTotal,
        ROUND(SUM(IF((RIGHT(zp.code, 3) = '_二线' OR RIGHT(zp.code, 3) = '_EX') AND LEFT(zu.realname,2) != 't_', CAST(ze.consumed AS DECIMAL(11,1)) , 0))/(zp.workHours * 8), 4) AS secondInside,
        ROUND(SUM(IF((RIGHT(zp.code, 3) = '_二线' OR RIGHT(zp.code, 3) = '_EX')  AND LEFT(zu.realname,2) = 't_', CAST(ze.consumed AS DECIMAL(11,1)) , 0))/(zp.workHours * 8), 4) AS secondOut,
        ROUND(SUM(IF((RIGHT(zp.code, 3) = '_二线' OR RIGHT(zp.code, 3) = '_EX') , CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS secondTotal,
        ROUND(SUM(IF(RIGHT(zp.code, 4) = '_DEP' AND LEFT(zu.realname,2) != 't_', CAST(ze.consumed AS DECIMAL(11,1)) , 0))/(zp.workHours * 8), 4) AS deptInside,
        ROUND(SUM(IF(RIGHT(zp.code, 4) = '_DEP' AND LEFT(zu.realname,2) = 't_', CAST(ze.consumed AS DECIMAL(11,1)) , 0))/(zp.workHours * 8), 4) AS deptOut,
        ROUND(SUM(IF(RIGHT(zp.code, 4) = '_DEP' , CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS deptTotal,
        ROUND(SUM(IF(LEFT(zu.realname,2) != 't_', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS sumInside,
        ROUND(SUM(IF(LEFT(zu.realname,2) = 't_', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8), 4) AS sumOut,
        ROUND(SUM(CAST(ze.consumed AS DECIMAL(11,1)))/(zp.workHours * 8), 4) AS sumTotal 
        FROM `zt_effort` AS ze  
        INNER JOIN `zt_project` AS zp  ON zp.id=ze.project
        INNER JOIN `zt_user` AS zu  ON zu.account=ze.account  wHeRe ze.deleted  = '0' AND  ze.`date`  BETWEEN :start  AND  :end   gRoUp bY zp.id,zu.id,ze.deptId ) a
        INNER JOIN zt_dept zd ON zd.id = a.deptId ;";

        $this->saveLogs1("getdeptconsumed-sum----Sql".$sumSql);
        $sum = $this->SqlPrepare($sumSql, $varArr);

        $res = new stdClass();
        $res->data = $dataList;
        $res->sum = $sum[0];

        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);
    }

    /**
     *
     * 工作量投入（整体）-月度趋势
     */
    public function getconsumedbyyear()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('effort' , '工作量投入（整体）-月度趋势');
        // 接口token校验
        $this->checkApiToken();
        $year = $_POST['year'];

        // 查询工作量部门维度数据
        $dataList = $this->dao->select("
        IF(RIGHT(zp.code, 4) != '_DEP', IF(RIGHT(zp.code, 3) != '_二线','1','2'), '3') effortType,
        IF(LEFT(zu.realname,2) = 't_', '2', '1') personCategory,
        ROUND(SUM(CAST(ze.consumed AS DECIMAL(11,1)))/174, 2) consumed,
        month(ze.`date`) stage")->from(TABLE_EFFORT)->alias('ze')
            ->leftJoin(TABLE_PROJECT)->alias('zp')->on('zp.id=ze.project')
            ->leftJoin(TABLE_USER)->alias('zu')->on('zu.account=ze.account')
            ->where('ze.deleted')->eq('0')
            ->andWhere('year(ze.`date`)')->eq($year)
            ->groupBy('effortType,personCategory,stage')
            ->orderBy('stage')
            ->fetchAll();

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     *
     * 获取工作量统计整体明细数据
     */
    public function getconsumedtotaldetail()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('effort' , '度量-工作量整体明细');
        // 接口token校验
        $this->checkApiToken();
        $begin = $_POST['start'];
        $end = $_POST['end'];
        $personCategory = $_POST['personCategory'];
        $departmentIdPerson = $_POST['departmentIdPerson'];
        $name = $_POST['name'];
        $pageSize = $_POST['pageSize'];
        $pageCurr = ($_POST['page'] - 1)*$pageSize;
        $isAll = $_POST['isAll'];
        $accountType = $_POST['accountType'];
        $account = $_POST['account'];
        $orderColStr = $_POST['orderColStr'];
        $orderTypeStr = $_POST['orderTypeStr'];
        if(!$orderColStr) $orderSql = 'a.`parent`,a.`order`,a.account';
        else $orderSql = $orderColStr.' '.$orderTypeStr;
        $res = new stdClass();

        // 组合入参
        $varArr = [];
        // 过滤数据
        if($accountType == '2') {

            $authArr = $this->dao->select('zu.account account')->from(TABLE_USER)->alias('zu')
                ->innerJoin(TABLE_DEPT)->alias('zd')->on('zd.id = zu.dept')
                ->leftJoin(TABLE_DEPT)->alias('zd1')->on('zd1.parent = zd.id')
                ->where('zd.manager1')->eq($account)
                ->fetchAll();
            $authArr = implode(',',array_column($authArr,'account'));
        }
        if($accountType == '3' or !$authArr) {
            $authArr = $account;
        }

        // 分页总数获取
        $nameSql = '';
        $realnameSql = '';
        $deptSql = '';
        $accountSql = '';
        $stageSql = '';

        if($name) {
            $nameSql = ' and zu.realname like(:name) ';
            $varArr['name'] = '%' . $name . '%';
        }
        if($personCategory == '1') {
            $realnameSql = " and LEFT(zu.realname,2) != 't_' ";
        }else if($personCategory == '2') {
            $realnameSql = " and LEFT(zu.realname,2) = 't_' ";
        }
        if($departmentIdPerson) {
            $deptSql = " and (ze.deptId = :departmentIdPerson  or zd.parent = :departmentIdPerson ) ";
            $varArr['departmentIdPerson'] = $departmentIdPerson;
        }
        if($accountType != '1' and $authArr) $accountSql = " and zu.account in ('" . str_replace(',', "', '",$authArr) . "') ";
        if($begin and $end) {
            $stageSql = "AND  ze.`date`  BETWEEN :begin and :end  ";
            $varArr['begin'] = $begin;
            $varArr['end'] = $end;
        }

        $sql = "select a.account, a.accountName, a.accountDept, a.departmentPerson, a.parent, a.`order`, a.personCategory, ROUND(sum(a.projectTotal), 2) projectTotal, ROUND(sum(a.secondTotal), 2) secondTotal, ROUND(sum(a.deptTotal), 2) deptTotal, ROUND(sum(a.sumTotal), 2) sumTotal from 
  (select zp.id id, zu.account account, zu.realname accountName, ze.deptID accountDept, zd.parent parent, zd.`order` `order`,
    (case when zd.parent = 0 then zd.name else CONCAT(CONCAT((select zd1.name from zt_dept zd1 where zd1.id = zd.parent ), '-'), zd.name) end) departmentPerson,
    if(left(zu.realname, 2) = 't_', '外协', '正式') personCategory,
    ROUND(SUM(if(right(zp.code, 4) != '_DEP' and right(zp.code, 3) != '_二线' AND RIGHT(zp.code, 3) != '_EX' , cast(ze.consumed as DECIMAL(11, 1)), 0))/ (zp.workHours * 8), 4) as projectTotal,
    ROUND(SUM(if(RIGHT(zp.code, 3) = '_二线' OR RIGHT(zp.code, 3) = '_EX', cast(ze.consumed as DECIMAL(11, 1)), 0))/ (zp.workHours * 8), 4) as secondTotal,
    ROUND(SUM(if(right(zp.code, 4) = '_DEP' , cast(ze.consumed as DECIMAL(11, 1)), 0))/ (zp.workHours * 8), 4) as deptTotal,
    ROUND(SUM(if(ze.consumed is not null, cast(ze.consumed as DECIMAL(11, 1)), 0))/ (zp.workHours * 8), 4) as sumTotal
                        FROM `zt_user` AS zu   
                        INNER JOIN `zt_effort` AS ze  ON ze.account = zu.account and ze.deleted = '0' " . $stageSql .
                        " INNER JOIN `zt_dept` AS zd  ON zd.id=ze.deptID  
                        LEFT JOIN `zt_project` AS zp  ON zp.id = ze.project 
                        WHERE zu.id is not null". $nameSql . $realnameSql . $deptSql . $accountSql . "
                        group by account, accountDept, zp.id ) a
group by a.account, a.accountDept order by $orderSql";

        $this->saveLogs1("getconsumedtotaldetail-Sql--".$sql);
        $limit =" limit " . $pageCurr . "," . $pageSize;
        if(!$isAll) $pageSql = $sql . $limit;
        else $pageSql = $sql;

        $dataList = $this->SqlPrepare($pageSql, $varArr);


        $allSql = "
SELECT COUNT(1) num, ROUND(SUM(projectTotal), 2) projectTotal, ROUND(SUM(secondTotal), 2) secondTotal, ROUND(SUM(deptTotal), 2) deptTotal, ROUND(SUM(sumTotal), 2) sumTotal FROM (
    SELECT COUNT(1) num, ROUND(SUM(projectTotal), 4) projectTotal, ROUND(SUM(secondTotal), 4) secondTotal, ROUND(SUM(deptTotal), 4) deptTotal, ROUND(SUM(sumTotal), 4) sumTotal FROM (
        SELECT zp.id, zu.account account,
                                ze.deptID accountDept,
                                SUM(IF(RIGHT(zp.code, 4) != '_DEP' AND RIGHT(zp.code, 3) != '_二线' AND RIGHT(zp.code, 3) != '_EX', CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8) AS projectTotal,
                                SUM(IF(RIGHT(zp.code, 3) = '_二线' OR RIGHT(zp.code, 3) = '_EX' , CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8) AS secondTotal,
                                SUM(IF(RIGHT(zp.code, 4) = '_DEP' , CAST(ze.consumed AS DECIMAL(11,1)), 0))/(zp.workHours * 8) AS deptTotal,
                                SUM(IF(ze.consumed is not null,CAST(ze.consumed AS DECIMAL(11,1)),0))/(zp.workHours * 8) AS sumTotal 
                        FROM `zt_user` AS zu   
                        INNER JOIN `zt_effort` AS ze  ON ze.account = zu.account and ze.deleted = '0' " . $stageSql .
                        " INNER JOIN `zt_dept` AS zd  ON zd.id=ze.deptID  
                        LEFT JOIN `zt_project` AS zp  ON zp.id = ze.project  
                        WHERE zu.id is not null". $nameSql . $realnameSql . $deptSql . $accountSql . " gRoUp bY account,accountDept,zp.id  
    ) a group by account,accountDept 
) b";

        $this->saveLogs1("getconsumedtotaldetail-countSql--".$allSql);
        $all = $this->SqlPrepare($allSql, $varArr);


        // 合计工时获取
        $sum = new stdClass();
        $sum->secondTotal = $all[0]->secondTotal;
        $sum->projectTotal = $all[0]->projectTotal;
        $sum->deptTotal = $all[0]->deptTotal;
        $sum->sumTotal = $all[0]->sumTotal;

        $res->data = $dataList;
        $pageInfo = new stdClass();
        $pageInfo->num = $all[0]->num;
        $res->pageInfo = $pageInfo;
        $res->sum = $sum;

        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);
    }

    /**
     *
     * 工作量投入（项目）-查询
     */
    public function getconsumedproject()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('effort' , '工作量投入（项目）');
        // 接口token校验
        $this->checkApiToken();
        $begin = $_POST['start'];
        $end = $_POST['end'];
        $projectCode = $_POST['projectCode'];
        $departmentIdProject = $_POST['departmentIdProject'];
        $projectName = $_POST['projectName'];
        $pageSize = $_POST['pageSize'];
        $pageCurr = ($_POST['page'] - 1)*$pageSize;
        $isAll = $_POST['isAll'] ;
        $accountType = $_POST['accountType'];
        $account = $_POST['account'];
        $orderColStr = $_POST['orderColStr'];
        $orderTypeStr = $_POST['orderTypeStr'];
        $ids = $_POST['ids'];
        if(!$orderColStr) $orderSql = 'parent,`order`';
        else $orderSql = $orderColStr.' '.$orderTypeStr;
        $year = substr($end, 0, 4);
        $res = new stdClass();
        $stageSql = '';

        // 组合入参
        $varArr = [];
        if($begin and $end) {
            $stageSql = "AND  zt_effort.`date`  BETWEEN :begin  and :end ";
            $stageSqlYear = "AND  zt_effort.`date` <= :end ";
            $varArr['begin'] = $begin;
            $varArr['end'] = $end;
        }
        $authSql = " and zt_project.id in ('". str_replace('，', "', '",$ids) . "') ";

        // 获取工作量投入（项目）
        $sql = "select departmentIdProject, departmentProject,ROUND(SUM(outStage), 2) outStage,ROUND(SUM(insideStage), 2) insideStage,ROUND(SUM(totalStage), 2) totalStage,count(*) projectNum,ROUND(SUM(outAll), 2) outAll,ROUND(SUM(insideAll), 2) insideAll,ROUND(SUM(totalAll), 2) totalAll
                from (
                select a.*, b.outStage,b.insideStage, b.totalStage from
            (select
                zt_dept.id departmentIdProject,
                zt_dept.parent parent,
                zt_dept.`order` `order`,
                (CASE WHEN zt_dept.parent = 0 THEN zt_dept.name ELSE (SELECT zd.name from zt_dept zd where zd.id = zt_dept.parent ) END) departmentProject,
                plan.id projectPlanId,
                plan.bearDept,
                plan.workloadBase,
                plan.workloadChengdu,
                CAST(plan.workloadBase AS DECIMAL(11,1)) + CAST(plan.workloadChengdu AS DECIMAL(11,1)) AS planYearConsumed,
		        plan.workload planConsumed,
                plan.`begin` planBegin,
                plan.`end` planEnd,
                plan.insideStatus insideStatus,
                plan.depts,
                zt_project.id projectId,
                plan.mark projectCode,
                ROUND(SUM(if(effort.realname like 't\_%', effort.consumed, 0))/(zt_project.workHours * 8), 4) outAll,
                ROUND(SUM(if(effort.realname not like 't\_%', effort.consumed, 0))/(zt_project.workHours * 8), 4) insideAll,   
                ROUND(IFNULL(SUM(effort.consumed),0)/(zt_project.workHours * 8), 4) totalAll
            from zt_project
            inner join (select case when zt_projectplan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_projectplan.id)  ) else zt_projectplan.bearDept end onlyDept, zt_projectplan.* from zt_projectplan ) plan on plan.project = zt_project.id
            inner join zt_dept on plan.onlyDept = zt_dept.id
            left join ( select zt_effort.project project,zt_effort.account account,SUM(CAST(zt_effort.consumed AS DECIMAL(11,1))) consumed,zt_user.realname realname from zt_effort left join zt_user on zt_effort.account = zt_user.account where zt_effort.objectType= 'task' and zt_effort.deleted = '0' " . $stageSqlYear . " group by zt_effort.project,zt_effort.account,zt_user.realname) effort on effort.project = zt_project.id 
            where  zt_project.id is not null " . $authSql ."
            group by plan.id, zt_project.id, zt_dept.id
             ) a
            left join (
            SELECT
                zt_dept.id departmentIdProject,
                plan.id projectPlanId,
                zt_project.id projectId,
                ROUND(SUM(if(effort.realname like 't\_%', effort.consumed, 0))/(zt_project.workHours * 8), 4) outStage,
                ROUND(SUM(if(effort.realname not like 't\_%', effort.consumed, 0))/(zt_project.workHours * 8), 4) insideStage,
                ROUND(IFNULL(SUM(effort.consumed),0)/(zt_project.workHours * 8), 4) totalStage
            FROM zt_project
            inner join (select case when zt_projectplan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_projectplan.id)  ) else zt_projectplan.bearDept end onlyDept, zt_projectplan.* from zt_projectplan ) plan on plan.project = zt_project.id
            inner join zt_dept on plan.onlyDept = zt_dept.id
            LEFT JOIN ( select zt_effort.project project,zt_effort.account account,SUM(CAST(zt_effort.consumed AS DECIMAL(11,1)))  consumed,zt_user.realname realname from zt_effort left join zt_user on zt_effort.account = zt_user.account where zt_effort.objectType= 'task' and zt_effort.deleted = '0' " . $stageSql . " group by zt_effort.project,zt_effort.account,zt_user.realname) effort on effort.project = zt_project.id
            where zt_dept.id is not null and zt_project.id is not null " . $authSql ."
            group by  plan.id, zt_project.id, zt_dept.id 
            ) b on a.departmentIdProject = b.departmentIdProject and a.projectPlanId = b.projectPlanId and a.projectId = b.projectId
                ) c group by departmentProject
            order by " . $orderSql;
        $this->saveLogs1("getconsumedproject-Sql".$sql);

        $sumSql = "select count(1) num,ROUND(SUM(c.outStage), 2) outStage,ROUND(SUM(c.insideStage), 2) insideStage, ROUND(SUM(c.totalStage), 2) totalStage,ROUND(SUM(c.outAll), 2) outAll,ROUND(SUM(c.insideAll), 2) insideAll,ROUND(SUM(c.totalAll), 2) totalAll,SUM(projectNum) projectNum from (";

        $dataList = $this->SqlPrepare($sql, $varArr);

        // 合计获取
        $sum = $this->SqlPrepare($sumSql . $sql .') c', $varArr);

        $this->saveLogs1("getconsumedproject-sumSql".$sumSql . $sql .') c');


        $res->data = $dataList;
        $pageInfo = new stdClass();
        $pageInfo->num = $sum[0]->num;
        $res->pageInfo = $pageInfo;
        $res->sum = $sum;

        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);
    }

    /**
     *
     * 工作量投入明细数据（项目）-查询
     */
    public function getconsumedprojectdetail()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('effort' , '工作量投入明细数据（项目）');
        // 接口token校验
        $this->checkApiToken();
        $begin = $_POST['start'];
        $end = $_POST['end'];
        $projectCode = $_POST['projectCode'];
        $departmentIdProject = $_POST['departmentIdProject'];
        $projectName = $_POST['projectName'];
        $pageSize = $_POST['pageSize'];
        $pageCurr = ($_POST['page'] - 1)*$pageSize;
        $isAll = $_POST['isAll'] ;
        $accountType = $_POST['accountType'];
        $account = $_POST['account'];
        $orderColStr = $_POST['orderColStr'];
        $orderTypeStr = $_POST['orderTypeStr'];
        $ids = $_POST['ids'];
        $year = substr($end, 0, 4);
        $res = new stdClass();
        $departmentIdProjectSql = '';
        $projectNameSql = '';
        $projectCodeSql = '';
        $authSql = '';

        // 组合入参
        $varArr = [];
        $varArr['begin'] = $begin;
        $varArr['end'] = $end;

        if($departmentIdProject) {
            $departmentIdProjectSql = ' and (a.departmentIdProject = :departmentIdProject  or a.parent = :departmentIdProject ) ';
            $varArr['departmentIdProject'] = $departmentIdProject;
        }
        if($projectName) {
            $projectNameSql = ' and projectName like(:projectName) ';
            $varArr['projectName'] = '%' . $projectName . '%';
        }
        if($projectCode) {
            $projectCodeSql = ' and projectCode like(:projectCode) ';
            $varArr['projectCode'] = '%' . $projectCode . '%';
        }

        // 过滤数据
        if($accountType == '2') {
            $authArr = $this->dao->select('id')->from(TABLE_DEPT)->where('manager1')->eq($account)
                ->fetch('id');
            if($authArr) $authSql = ' and  (a.departmentIdProject = ' . $authArr  .' or a.parent = ' . $authArr . ') ';
            else {
                // 在度量是部门领导，禅道不是默认查所在部门
                $authArr = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($account)
                    ->fetch('dept');
                $authSql = ' and (a.departmentIdProject = ' . $authArr  .' or a.parent = ' . $authArr . ') ';
            }
        }
        $authSql .= " and a.projectId in ('". str_replace('，', "', '",$ids) . "') ";


        // 获取工作量投入明细数据（项目）
        $varArr['degree'] = '2';
        $sql = "select a.*, b.outStage,b.insideStage, b.totalStage from
            (select
                zt_dept.id departmentIdProject,
                zt_dept.parent parent,
                zt_dept.`order` `order`,
                (CASE WHEN zt_dept.parent = 0 THEN zt_dept.name ELSE CONCAT(CONCAT((SELECT zd.name from zt_dept zd where zd.id = zt_dept.parent ), '-'), zt_dept.name)END) departmentProject,
                plan.id projectPlanId,
                plan.bearDept,               
		        zpc.workload approvalConsumed,
		        plan.workload planYearConsumed,
                zt_project.planWorkload planConsumed,
                plan.`begin` planBegin,
                plan.`end` planEnd,
                plan.insideStatus insideStatus,
                plan.depts,
                zt_project.id projectId,
                zt_project.name  projectName,
                plan.mark projectCode,
                COUNT(if(effort.realname like 't\_%', 1, null)) outNum,   
                COUNT(if(effort.realname not like 't\_%', 1, null)) insideNum,
                COUNT(effort.realname) totalNum,
                ROUND(SUM(if(effort.realname like 't\_%', effort.consumed, 0))/(zt_project.workHours * 8), :degree ) outAll,
                ROUND(SUM(if(effort.realname not like 't\_%', effort.consumed, 0))/(zt_project.workHours * 8), :degree ) insideAll,
                ROUND(IFNULL(SUM(effort.consumed),0)/(zt_project.workHours * 8), :degree) totalAll
            from zt_project
            inner join (select case when zt_projectplan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_projectplan.id)  ) else zt_projectplan.bearDept end onlyDept, zt_projectplan.* from zt_projectplan ) plan on plan.project = zt_project.id
            inner join zt_projectcreation zpc on zpc.plan = plan.id and zpc.deleted = '0'
            inner join zt_dept on plan.onlyDept = zt_dept.id
            left join ( select zt_effort.project project,zt_effort.account account,SUM(CAST(zt_effort.consumed AS DECIMAL(11,1))) consumed,zt_user.realname realname from zt_effort left join zt_user on zt_effort.account = zt_user.account where zt_effort.objectType= 'task' and zt_effort.deleted = '0' and zt_effort.consumed != 0 and zt_effort.`date` <= :end group by zt_effort.project,zt_effort.account,zt_user.realname) effort on effort.project = zt_project.id 
            where  zt_project.id is not null 
            group by plan.id, zt_project.id, zt_dept.id
             ) a
            left join (
            SELECT
                zt_dept.id departmentIdProject,
                plan.id projectPlanId,
                zt_project.id projectId,
                ROUND(SUM(if(effort.realname like 't\_%', effort.consumed, 0))/(zt_project.workHours * 8), :degree ) outStage,
                ROUND(SUM(if(effort.realname not like 't\_%', effort.consumed, 0))/(zt_project.workHours * 8), :degree ) insideStage,
                ROUND(IFNULL(SUM(effort.consumed),0)/(zt_project.workHours * 8), :degree ) totalStage
            FROM zt_project
            inner join (select case when zt_projectplan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_projectplan.id)  ) else zt_projectplan.bearDept end onlyDept, zt_projectplan.* from zt_projectplan ) plan on plan.project = zt_project.id
            inner join zt_dept on plan.onlyDept = zt_dept.id
            LEFT JOIN ( select zt_effort.project project,zt_effort.account account,SUM(CAST(zt_effort.consumed AS DECIMAL(11,1)))  consumed,zt_user.realname realname from zt_effort left join zt_user on zt_effort.account = zt_user.account where zt_effort.objectType= 'task' and zt_effort.deleted = '0' and zt_effort.consumed != 0 and zt_effort.`date` between :begin  and :end  group by zt_effort.project,zt_effort.account,zt_user.realname) effort on effort.project = zt_project.id
            where zt_dept.id is not null and zt_project.id is not null 
            group by  plan.id, zt_project.id, zt_dept.id 
            ) b on a.departmentIdProject = b.departmentIdProject and a.projectPlanId = b.projectPlanId and a.projectId = b.projectId
            where a.departmentIdProject is not null " . $departmentIdProjectSql . $projectCodeSql . $projectNameSql . $authSql .
            " order by " . $orderColStr . " " . $orderTypeStr . ",`order`,projectCode ";

        $this->saveLogs1("getconsumedprojectdetail-Sql-----". $sql );
        $limit =" limit " . $pageCurr . "," . $pageSize;

        if(!$isAll) $pageSql = $sql . $limit;
        else $pageSql = $sql;

        $dataList = $this->SqlPrepare($pageSql, $varArr);

        $varArr['degree'] = '4';
        $sumSql = "select count(1) num,SUM(c.outNum) outNum,SUM(c.insideNum) insideNum,SUM(c.totalNum) totalNum,ROUND(SUM(c.outStage), 2) outStage,ROUND(SUM(c.insideStage), 2) insideStage, ROUND(SUM(c.totalStage), 2) totalStage,ROUND(SUM(c.outAll), 2) outAll,ROUND(SUM(c.insideAll), 2) insideAll,ROUND(SUM(c.totalAll), 2) totalAll from (";

        $this->saveLogs1("getconsumedprojectdetail-countSql-----". $sumSql );

        $this->app->loadLang('projectplan');
        foreach($dataList as $data) {
            $data->insideStatus = zget($this->lang->projectplan->insideStatusList, $data->insideStatus, '');
        }
        // 合计获取
        $sum = $this->SqlPrepare($sumSql . $sql .') c', $varArr);


        $res->data = $dataList;
        $pageInfo = new stdClass();
        $pageInfo->num = $sum[0]->num;
        $res->pageInfo = $pageInfo;
        $res->sum = $sum;

        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);
    }

    /**
     *
     * 同步查询禅道工作量基础明细数据-查询
     */
    public function getconsumedbasedetail()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('effort' , '工作量投入基础明细数据');
        // 接口token校验
        $this->checkApiToken();
        $begin = $_POST['start'];
        $end = $_POST['end'];
        $projectCode = $_POST['projectCode'];
        $departmentIdProject = $_POST['departmentIdProject'];
        $personCategory = $_POST['personCategory'];
        $projectName = $_POST['projectName'];
        $pageSize = $_POST['pageSize'];
        $pageCurr = ($_POST['page'] - 1)*$pageSize;
        $isAll = $_POST['isAll'] ;
        $accountType = $_POST['accountType'];
        $account = $_POST['account'];
        $orderColStr = $_POST['orderColStr'];
        $orderTypeStr = $_POST['orderTypeStr'];
        $departmentIdPerson = $_POST['departmentIdPerson'];
        $effortType = $_POST['effortType'];
        $taskSource = $_POST['taskSource'];
        $ids = $_POST['ids'];
        $name = $_POST['name'];

        $departmentIdProjectSql = '';
        $effortTypeSql = '';
        $projectCodeSql = '';
        $departmentIdPersonSql = '';
        $personCategorySql = '';
        $nameSql = '';
        $taskSourceSql = '';
        $accountSql = '';
        $authSql = '';

        // 过滤数据
        if($accountType == '2') {
            $authArr = $this->dao->select('GROUP_CONCAT(zu.account)')->from(TABLE_USER)->alias('zu')
                ->innerJoin(TABLE_DEPT)->alias('zd')->on('zd.id = zu.dept')->andWhere('zd.manager1')->eq($account)
                ->fetch('GROUP_CONCAT(zu.account)');
            if($authArr) $accountSql = " and account in ('" . str_replace(',', "', '",$authArr)  . "') ";
            $auth = $this->dao->select('id')->from(TABLE_DEPT)->where('manager1')->eq($account)
                ->fetch('id');
            if($auth) $authSql = ' and (zt_dept.id = ' . $auth  .' or zt_dept.parent = ' . $auth . ') ';
        }else if($accountType == '3') {
            if($ids) $authSql = " and zt_effort.project in ('". str_replace('，', "', '",$ids) . "') ";
            $accountSql = " and account = '" . $account . "'";
        }

        // 组合入参
        $varArr = [];
        $varArr['begin'] = $begin;
        $varArr['end'] = $end;
        if($departmentIdProject) {
            $departmentIdProjectSql = ' and (zt_dept.id = :departmentIdProject  or zt_dept.parent = :departmentIdProject ) ';
            $varArr['departmentIdProject'] = $departmentIdProject;
        }
        if($departmentIdPerson) {
            $departmentIdPersonSql = ' and (zt_dept.id = :departmentIdPerson  or zt_dept.parent = :departmentIdPerson ) ';
            $varArr['departmentIdPerson'] = $departmentIdPerson;
        }
        if($name) {
            $nameSql = ' and accountName like(:name) ';
            $varArr['name'] = '%' . $name . '%';
        }
        if($projectCode) {
            $projectCodeSql = ' and projectCode like(:projectCode) ';
            $varArr['projectCode'] = '%' . $projectCode . '%';
        }
        if($projectName) {
            $projectNameSql = " and projectName like(:projectName) ";
            $varArr['projectName'] = '%' . $projectName . '%';
        }
        if($personCategory == '1') {
            $personCategorySql = ' and personCategory = "正式"';
        }else if($personCategory == '2'){
            $personCategorySql = ' and personCategory = "外协"';
        }
        if($effortType) {
            $effortTypeSql = ' and effortType = :effortType ';
            $varArr['effortType'] = $effortType;
        }
        if(!$orderColStr) $orderColStr = '`order`';

        // 同步查询禅道工作量基础明细数据
        $sql = "select effortProject,ROUND(sum(consumed)/(other.workHours * 8),2) consumed,personCategory,accountName,departmentPerson,departmentProject,`order`,effortType,projectCode,projectName,outsideTask,
                    case when LEFT(effort.taskName,4) = '部门工单' then '部门工单'
                    when LEFT(effort.taskName,4) = '二线工单' then '二线工单'
                    when LEFT(effort.taskName,3) = '问题池' then '问题单'
                    when LEFT(effort.taskName,4) = '外部需求' then '外部需求'
                    when LEFT(effort.taskName,4) = '内部需求' then '内部需求'
                    when other.effortType = '部门报工' then '部门工单'
                    when other.effortType = '二线报工' then '二线工单'
                    else '需求单' end taskSource
                    from 
                        ( select zt_effort.id,zt_effort.project effortProject,zt_effort.account,zt_task.name taskName,ROUND(SUM(zt_effort.consumed), 4) consumed, zt_dept.id userDeptId, IF(LEFT(zt_user.realname,2) = 't_', '外协', '正式') personCategory, zt_user.realname accountName, (CASE WHEN zt_dept.parent = 0 THEN zt_dept.name ELSE CONCAT(CONCAT((SELECT zd.name from zt_dept zd where zd.id = zt_dept.parent ), '-'), zt_dept.name)END) departmentPerson  from zt_effort 
                        LEFT JOIN zt_task ON zt_task.id = zt_effort.objectId
                        INNER JOIN zt_dept ON zt_dept.id = zt_effort.deptID
                        INNER JOIN zt_user ON zt_user.account = zt_effort.account
                        where zt_effort.objectType= 'task' and zt_effort.deleted = '0' and zt_effort.consumed != 0 and zt_effort.`date` between :begin and :end " .$departmentIdPersonSql  . $authSql . " 
                        group by zt_effort.id, zt_effort.account, zt_effort.objectID, zt_dept.id, zt_user.id ) effort
                        inner join (
                        select    
                            zt_dept.id deptId,
                            zt_dept.`order` `order`,
                            (CASE WHEN zt_dept.parent = 0 THEN zt_dept.name ELSE CONCAT(CONCAT((SELECT zd.name from zt_dept zd where zd.id = zt_dept.parent ), '-'), zt_dept.name)END) departmentProject,
                            plan.id projectPlanId,
                            plan.outsideTask outsideTask,
                            plan.bearDept,
                            plan.`begin`,
                            plan.`end`,
                            plan.insideStatus,
                            zt_project.id projectId,
                            plan.mark projectCode,
                            plan.name projectName,
                            zt_project.workHours workHours,
                            case when zt_project.code like '%_DEP' then '部门报工'
                            when (zt_project.code like '%_二线' OR zt_project.code like '%_EX') then '二线报工'
                            else '项目报工' end effortType
                        from zt_project
                        inner join (select case when zt_projectplan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_projectplan.id)  ) else zt_projectplan.bearDept end onlyDept, zt_projectplan.* from zt_projectplan ) plan on plan.project = zt_project.id
                        inner join zt_dept on plan.onlyDept = zt_dept.id where 1=1 ". $departmentIdProjectSql ." ) other
                        on effort.effortProject = other.projectId
                    where id is not null " . $projectCodeSql . $projectNameSql . $nameSql . $personCategorySql . $effortTypeSql . $accountSql .
                    " group by effortProject,personCategory,accountName,departmentPerson,departmentProject,`order`,effortType,projectCode,taskSource,outsideTask order by $orderColStr $orderTypeStr ,projectCode" . " " . $orderTypeStr;
        $this->saveLogs1("getconsumedbasedetail-Sql". $sql );
        $limit =" limit " . $pageCurr . "," . $pageSize;
        $sumSql = "select count(1) num from (";

        if($taskSource) $sql = "select * from (" . $sql . ") a where taskSource = '" . $taskSource . "'";
        if(!$isAll) $pageSql = $sql . $limit;
        else $pageSql = $sql;

        $dataList = $this->SqlPrepare($pageSql, $varArr);
        $this->saveLogs1("getconsumedbasedetail-Sql". $pageSql );

        // 处理承建单位
        $this->app->loadLang('application');
        foreach ($dataList as $key => $data) {

            $contractors =   $this->dao->select("subTaskBearDept")->from(TABLE_OUTSIDEPLANTASKS)->where ('id')->in($data->outsideTask)->fi()->fetchAll();
            $contractorss = implode(',',array_column($contractors,'subTaskBearDept'));
            $vlist = explode(',', $contractorss);
            $arr = [];
            foreach (array_unique($vlist) as $itemv) {
                if (empty($itemv)) continue;
                $arr[] = zget($this->lang->application->teamList, $itemv, '');
            }
            $data->contractor = implode(',', $arr);
        }

        // 合计获取
        $sum = $this->SqlPrepare($sumSql . $sql .') c', $varArr);

        $res = new stdClass();
        $res->data = $dataList;
        $pageInfo = new stdClass();
        $pageInfo->num = $sum[0]->num;
        $res->pageInfo = $pageInfo;

        if(dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);
    }


    /**
     *
     * 同步按照年度查询禅道全部年度项目
     */
    public function getprojectsbyyear()
    {
        $this->app->loadLang('application');
        $this->app->loadLang('projectplan');
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('project', '项目数据');
        // 接口token校验
        $this->checkApiToken();
        $date = $_POST['statisticalDate'];
        $account = $_POST['userId'];
        $projectCode = $_POST['projectCode'];
        $deptCn = $_POST['departmentCn'];
        $projectName = $_POST['projectName'];
        $projectStatus = $_POST['projectStatus'];
        $isGetOut = $_POST['isGetOut'];
        $isSecondLine = $_POST['isSecondLine'];
        $res = new stdClass();

        $lastYear = $date -1;

        $userView = $this->dao->select('*')->from(TABLE_USERVIEW)->where('account')->eq($account)->fetch();
        if (empty($userView)) $userView = $this->loadModel('user')->computeUserView($account);
        $secondLine = "zpp.secondLine !=1 and zpp.code not like '%DEP%' and zpp.code not like '%EX%'";

        $openedProjects = $this->dao->select('id')->from(TABLE_PROJECT)->where('acl')->eq('open')->andWhere('type')->eq('project')->fetchAll('id');
        $openedProjects = join(',', array_keys($openedProjects));
        $projects = rtrim($userView->projects, ',') . ',' . $openedProjects;
        $sqlDate = "zpp.year = '$date' or (zpp.year = $lastYear and zpp.isDelayPreYear = 1 ) or (zpp.year < '$lastYear' and zpp.isDelayPreYear = 1 and (year(zp.realEnd) >= '$date' or zp.realEnd = '0000-00-00' ))";
        $statusList = " ('" . str_replace(',', "', '",$projectStatus) . "') ";
        $sqlStatus = "zpp.insideStatus not in $statusList or zpp.insideStatus is null ";
        $this->saveLogs1("公开项目用户列表项目列表projectName为------".$openedProjects);
        $this->saveLogs1("总项目列表为------".$projects);
        // 获取内部项目
        $inDataList = $this->dao->select("zpp.insidestatus, zpp.type projectType,zpp.onlyDept bearDept,zpp.mark codeName,zpp.name projectName,zd.name projectDept,zpc.pm projectManager,zd.ldapName,zp.id projectId,zpp.outsideTask outsideTask,zd.`order` deptOrder")
            ->from(" (select case when zt_projectplan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_projectplan.id)  ) else zt_projectplan.bearDept end onlyDept, zt_projectplan.* from zt_projectplan ) ")
            ->alias('zpp')
            ->leftJoin(TABLE_PROJECT)->alias('zp')->on('zpp.project = zp.id')
            ->leftJoin(TABLE_DEPT)->alias('zd')->on("zd.id = zpp.onlyDept")
            ->leftJoin(TABLE_PROJECTCREATION)->alias('zpc')->on("zpp.id = zpc.plan")
            ->where('(zp.deleted = "0" or zp.deleted is null)')
            ->andwhere('(zp.type = "project" or zp.type is null)')
            ->andwhere('zpp.deleted')->eq('0')
            ->andwhere('(zpp.insidestatus != "no" or zpp.insidestatus is null)')
            ->beginIF($isSecondLine !=1)->andWhere($secondLine)->fi()
            ->beginIF($deptCn)->andWhere('zd.ldapName')->like("$deptCn%")->fi()
            ->beginIF($projectCode)->andWhere('zpp.mark')->like("%$projectCode%")->fi()
            ->beginIF($projectStatus)->andWhere("(".$sqlStatus.")")->fi()
            ->beginIF($account)->andWhere('zp.id')->in($projects)->fi()
            ->beginIF($projectName)->andWhere('zpp.name')->like("%$projectName%")->fi()
            ->beginIF($date)->andWhere("(".$sqlDate.")")->fi()
            ->groupBy('zpp.id,zd.id,zp.id,zpc.PM')
            ->fetchAll();

        foreach ($inDataList as $key => $data) {

            $data->projectType = zget($this->lang->projectplan->typeList, $data->projectType, '');
            $contractors =   $this->dao->select("subTaskBearDept")->from(TABLE_OUTSIDEPLANTASKS)->where ('id')->in($data->outsideTask)->fi()->fetchAll();
            $contractorss = implode(',',array_column($contractors,'subTaskBearDept'));
            $vlist = explode(',', $contractorss);
            $arr = [];
            foreach (array_unique($vlist) as $itemv) {
                if (empty($itemv)) continue;
                $arr[] = zget($this->lang->application->teamList, $itemv, '');
            }
            $data->contractor = implode(',', $arr);
        }
        $res->data = $inDataList;
        $ids = "";

       foreach ($inDataList as $key => $data){
           $ids = $ids.$data->codeName."------";
       }
        $this->saveLogs1("查询项目入参------"."---account----".$account."---projectCode----".$projectCode."---deptCn----".$deptCn."---projectName----".$projectName."---date----".$date);
        $this->saveLogs1("内部项目列表为------".$ids);

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $inDataList);
    }

    /**
     *
     * 查询部门评审汇总
     */
    public function getDepartmentReviewStatistic()
    {
        $res = new stdClass();
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('risk', '度量-部门风险汇总');
        // 接口token校验
        $this->checkApiToken();
        $beginDay = $_POST['startDay'];
        $endDay = $_POST['endDay'];
        $projectIds = $_POST['projectIds'];
        //若projectIds为空，则查询出结果为空
        if (!isset($projectIds) || empty($projectIds)){
            $this->loadModel('requestlog')->response('success', $this->lang->api->successful, []);
        }
        $projectInSql = " and pl.project in (" . $projectIds . ")";
        if (empty($beginDay)) {
            $beginDay = '1900-01-01 00:00:00';
        }
        if (empty($endDay)) {
            $endDay = date('Y-m-d').' 23:59:59';
        }
        $sql = "SELECT
        (CASE WHEN zd.parent = 0 THEN zd.name ELSE (SELECT name from zt_dept where id = zd.parent) END) AS deptName,
    	(CASE WHEN zd.parent = 0 THEN zd.id ELSE (SELECT id from zt_dept where id = zd.parent) END) AS departmentId,
    	(CASE WHEN zd.parent = 0 THEN zd.`order` ELSE (SELECT `order` from zt_dept where id = zd.parent) END) AS deptOrder,
        (CASE WHEN zd.parent = 0 THEN zd.ldapName ELSE (SELECT ldapName from zt_dept where id = zd.parent) END) AS departmentCn,
        SUM(ifnull(p.num,0)) as projectNum,
        SUM(ifnull(r1.num,0))as newProRevNum,
        SUM(ifnull(r2.num,0) )as newMgtRevNum,
        SUM(ifnull(r3.num ,0)) as newCbpRevNum,
        SUM((ifnull(r1.num,0)+ifnull(r2.num,0)+ifnull(r3.num ,0))) as newNum,
        SUM(ifnull(r4.num ,0) )as totalProRevNum,
        SUM(ifnull(r5.num ,0) )as totalMgtRevNum,
        SUM(ifnull(r6.num ,0) )as totalCbpRevNum,
        SUM((ifnull(r4.num,0) +  ifnull(r5.num ,0) +ifnull(r6.num ,0)))  as totalNum
        from
        (SELECT bearDept,count(*) as num from zt_projectplan pl where pl.deleted ='0' " . $projectInSql . " group by
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) p
        left join
        zt_dept zd on zd.id = p.bearDept
        left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_review` r ,`zt_projectplan` pl
        WHERE r.project = pl.project and r.`type`= 'pro' and r.deleted ='0' and r.status not in ('drop','recall') and r.closeTime BETWEEN :beginDay AND :endDay ". $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r1 on r1.bearDept = p.bearDept
        left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_review` r ,`zt_projectplan` pl
        WHERE r.project = pl.project and r.`type`= 'manage' and r.deleted ='0' and r.status not in ('drop','recall') and r.closeTime BETWEEN :beginDay AND :endDay  ". $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r2 on r2.bearDept = p.bearDept
        left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_review` r ,`zt_projectplan` pl
        WHERE r.project = pl.project and r.`type`= 'cbp' and r.deleted ='0' and r.status not in ('drop','recall') and r.closeTime BETWEEN  :beginDay AND :endDay  ". $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r3 on r3.bearDept = p.bearDept
        left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_review` r ,`zt_projectplan` pl 
        WHERE r.project = pl.project and r.`type`= 'pro' and r.deleted ='0' and r.status not in ('drop','recall')  and r.closeTime >= ('1900-01-01 00:00:00')  and r.closeTime <=('" . $endDay . "') " . $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r4 on r4.bearDept = p.bearDept
         left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_review` r ,`zt_projectplan` pl 
        WHERE r.project = pl.project and r.`type`= 'manage' and r.deleted ='0' and r.status not in ('drop','recall') and r.closeTime >= ('1900-01-01 00:00:00')  and r.closeTime <=('" . $endDay . "') " . $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r5 on r5.bearDept = p.bearDept
         left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_review` r ,`zt_projectplan` pl 
        WHERE r.project = pl.project and r.`type`= 'cbp' and r.deleted ='0' and r.status not in ('drop','recall') and r.closeTime >= ('1900-01-01 00:00:00')  and r.closeTime <=('" . $endDay . "') " . $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r6 on r6.bearDept = p.bearDept
        group by deptName, departmentId, deptOrder, departmentCn
        order by deptOrder";

        // 组合入参
        $varArr = [];
        $varArr['beginDay'] = $beginDay;
        $varArr['endDay'] = $endDay;
        $dataList = $this->SqlPrepare($sql, $varArr);
//        $dataList = $this->dao->query($sql)
//            ->fetchAll();
        $res->data = $dataList;

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);

    }

    /**
     *
     * 分页查询当前起止日期的项目评审汇总情况
     */
    public function getprojectreviewstatistic()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('review', '度量-项目评审汇总');
        // 接口token校验
        $this->checkApiToken();
        $begin = $_POST['startDay'];
        $end = $_POST['endDay'];
        $projects = $_POST['projectIds'];
        $orderCol = $_POST['orderCol'];
        $orderType = $_POST['orderType'];
        //若projectIds为空，则查询出结果为空
        if (!isset($projects) || empty($projects)){
            $this->loadModel('requestlog')->response('success', $this->lang->api->successful, []);
        }
        $pageSize = $_POST['pageSize'];
        $pageCurr = ($_POST['currentPage'] - 1) * $pageSize;
        $res = new stdClass();

        if (empty($begin)){
            $begin = '1900-01-01 00:00:00';
        }
        if (empty($end)){
            $end = date('Y-m-d').' 23:59:59';
        }
//        $Projects = " zr.id in (" . $projects . ")";
        $zrProjects = " zr.project in (" . $projects . ")";
        $Projects = " project in (" . $projects . ")";


//        $sql = "SELECT *
//FROM ( select
//
//        zd.ldapName AS departmentCn,
//        zd.name AS deptName,
//        zd.order AS deptOrder,
//        zp.name projectName,
//        zpp.mark AS projectCode,
//        IFNULL(zpc.PM, zpp.owner) AS manager,
//        zp.id AS aprojectId,
//        zd.id AS adeptId,
//    	COUNT(DISTINCT CASE WHEN zr.`type`= 'pro' THEN zr.id END) AS totalProRevNum,
//    	COUNT(DISTINCT CASE WHEN zr.`type`= 'manage' THEN zr.id END) AS totalMgtRevNum,
//    	COUNT(DISTINCT CASE WHEN zr.`type`= 'cbp' THEN zr.id END) AS totalCbpRevNum,
//    	COUNT(DISTINCT zr.id ) AS totalNum,
//    	COUNT(DISTINCT CASE WHEN zri.status = 'closed'  THEN zri.id END) AS totalQusAdoptedNum,
//    	COUNT(DISTINCT CASE WHEN zri.status = 'nadopt'  THEN zri.id  END) AS totalQusNotAdoptedNum,
//    	COUNT(DISTINCT CASE WHEN zri.status in ('nvalidation','repeat') THEN zri.id  END) AS totalQusNoModNum,
//    	COUNT(DISTINCT zri.id) AS totalQusNum
//from
//zt_project zp
//LEFT JOIN zt_review zr on zp.id = zr.project AND zr.closeTime is not null AND SUBSTRING(zr.closeTime,1,10) != SUBSTRING('0000-00-00',1,10) and zr.type in ('pro', 'manage', 'cbp')
//       AND zr.closeTime >= ('1900-01-01 00:00:00')  and zr.closeTime <=('" . $end . "')
//LEFT JOIN zt_reviewissue zri on zr.id = zri.review and zri.status in('nvalidation','nadopt','closed','repeat')
//LEFT JOIN zt_projectplan zpp on zp.id = zpp.project
//LEFT JOIN zt_projectcreation zpc on zpc.plan = zpp.id
//inner join (select case when zt_projectplan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.PM from zt_projectcreation zp where zp.plan = zt_projectplan.id)) else zt_projectplan.bearDept end onlyDept, zt_projectplan.* from zt_projectplan ) plan1 on plan1.project = zp.id
//LEFT JOIN zt_dept zd on zd.id = plan1.onlyDept
//WHERE  zp.deleted = '0'  and zp.`type`= 'project' AND $projects
//GROUP BY zp.id,zd.id,zpp.id) a
//
//LEFT join (select
//
//        zp.id AS projectId,
//        zd.id AS deptId,
//    	COUNT(DISTINCT CASE WHEN zr.`type`= 'pro' THEN zr.id END) AS newProRevNum,
//    	COUNT(DISTINCT CASE WHEN zr.`type`= 'manage' THEN zr.id END) AS newMgtRevNum,
//    	COUNT(DISTINCT CASE WHEN zr.`type`= 'cbp' THEN zr.id END) AS newCbpRevNum,
//    	COUNT(DISTINCT zr.id ) AS newNum,
//    	COUNT(DISTINCT CASE WHEN zri.status = 'closed'  THEN zri.id END) AS newQusAdoptedNum,
//    	COUNT(DISTINCT CASE WHEN zri.status = 'nadopt'  THEN zri.id END) AS newQusNotAdoptedNum,
//    	COUNT(DISTINCT CASE WHEN zri.status in ('nvalidation','repeat') THEN zri.id END) AS newQusNoModNum,
//    	COUNT(DISTINCT zri.id ) AS newQusNum
//from
//zt_project zp
//LEFT JOIN zt_review zr on zp.id = zr.project AND zr.closeTime is not null and SUBSTRING(zr.closeTime,1,10) != SUBSTRING('0000-00-00',1,10) and zr.closeTime between '" . $begin . "' AND '" . $end . "'
//and zr.type in ('pro', 'manage', 'cbp')
//LEFT JOIN zt_reviewissue zri on zr.id = zri.review and zri.status in('nvalidation','nadopt','closed','repeat')
//LEFT JOIN zt_projectplan zpp on zp.id = zpp.project
//LEFT JOIN zt_projectcreation zpc on zpc.plan = zpp.id
//inner join (select case when zt_projectplan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.PM from zt_projectcreation zp where zp.plan = zt_projectplan.id)  ) else zt_projectplan.bearDept end onlyDept, zt_projectplan.* from zt_projectplan ) plan on plan.project = zp.id
//LEFT JOIN zt_dept zd on zd.id = plan.onlyDept
//WHERE  zp.`type`= 'project' AND $projects
//GROUP BY zp.id,zd.id,zpp.id) b on a.aprojectId = b.projectId AND  a.adeptId = b.deptId GROUP BY deptOrder,projectName
//";
//
        $sql = "select  zd.ldapName AS departmentCn,
        zd.name AS deptName,
        zd.order AS deptOrder,
        zpp.name projectName,
        zpp.mark AS projectCode,
        IFNULL(zpc.PM, zpp.owner) AS manager,
        IFNULL(total.totalProRevNum,0) AS totalProRevNum,
    	IFNULL(total.totalMgtRevNum,0) AS totalMgtRevNum,
    	IFNULL(total.totalCbpRevNum,0) AS totalCbpRevNum,
    	IFNULL(total.totalNum,0) AS totalNum,
    	IFNULL(total.totalQusAdoptedNum,0) AS totalQusAdoptedNum,
    	IFNULL(total.totalQusNotAdoptedNum,0) AS totalQusNotAdoptedNum,
    	IFNULL(total.totalQusNoModNum,0) AS totalQusNoModNum,
    	IFNULL(total.totalQusNum,0) AS totalQusNum,
        IFNULL(new.newProRevNum,0) AS newProRevNum,
    	IFNULL(new.newMgtRevNum,0) AS newMgtRevNum,
    	IFNULL(new.newCbpRevNum,0) AS newCbpRevNum,
    	IFNULL(new.newNum,0) AS newNum,
    	IFNULL(new.newQusAdoptedNum,0) AS newQusAdoptedNum,
    	IFNULL(new.newQusNotAdoptedNum,0) AS newQusNotAdoptedNum,
    	IFNULL(new.newQusNoModNum,0)  AS newQusNoModNum,
    	IFNULL(new.newQusNum,0) AS newQusNum
from (select * from zt_projectplan
     where deleted ='0' and " .$Projects ." ) zpp
LEFT JOIN zt_projectcreation zpc on zpc.plan = zpp.id
LEFT JOIN zt_dept zd on zd.id = (case when zpp.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = zpc.pm) ELSE zpp.bearDept end)
LEFT JOIN (select
        zr.project As project,
    	COUNT(DISTINCT CASE WHEN zr.type= 'pro' THEN zr.id END) AS totalProRevNum,
    	COUNT(DISTINCT CASE WHEN zr.type= 'manage' THEN zr.id END) AS totalMgtRevNum,
    	COUNT(DISTINCT CASE WHEN zr.type= 'cbp' THEN zr.id END) AS totalCbpRevNum,
    	COUNT(DISTINCT zr.id ) AS totalNum,
    	COUNT(DISTINCT CASE WHEN zritotal.status = 'closed'  THEN zritotal.id END) AS totalQusAdoptedNum,
    	COUNT(DISTINCT CASE WHEN zritotal.status = 'nadopt'  THEN zritotal.id  END) AS totalQusNotAdoptedNum,
    	COUNT(DISTINCT CASE WHEN zritotal.status in ('nvalidation','repeat') THEN zritotal.id  END) AS totalQusNoModNum,
    	COUNT(DISTINCT zritotal.id) AS totalQusNum
from
zt_review zr
LEFT JOIN zt_reviewissue zritotal on zritotal.review = zr.id and zritotal.deleted='0' and zritotal.status in('nvalidation','nadopt','closed','repeat')
where zr.closeTime between '1900-01-01 00:00:00' AND :end and zr.type in ('pro', 'manage', 'cbp') and zr.status not in ('drop','recall') and zr.deleted ='0' 
    and " .$zrProjects ."
group by zr.project
)total on total.project = zpp.project
LEFT JOIN (select
        zr.project As project,
    	COUNT(DISTINCT CASE WHEN zr.type= 'pro' THEN zr.id END) AS newProRevNum,
    	COUNT(DISTINCT CASE WHEN zr.type= 'manage' THEN zr.id END) AS newMgtRevNum,
    	COUNT(DISTINCT CASE WHEN zr.type= 'cbp' THEN zr.id END) AS newCbpRevNum,
    	COUNT(DISTINCT zr.id ) AS newNum,
    	COUNT(DISTINCT CASE WHEN zrinew.status = 'closed'  THEN zrinew.id END) AS newQusAdoptedNum,
    	COUNT(DISTINCT CASE WHEN zrinew.status = 'nadopt'  THEN zrinew.id  END) AS newQusNotAdoptedNum,
    	COUNT(DISTINCT CASE WHEN zrinew.status in ('nvalidation','repeat') THEN zrinew.id  END) AS newQusNoModNum,
    	COUNT(DISTINCT zrinew.id) AS newQusNum
from
zt_review zr
LEFT JOIN zt_reviewissue zrinew on zrinew.review = zr.id and zrinew.deleted='0' and zrinew.status in('nvalidation','nadopt','closed','repeat')
where zr.closeTime between :begin AND :end and zr.type in ('pro', 'manage', 'cbp') and zr.status not in ('drop','recall') and zr.deleted ='0' 
    and " .$zrProjects."
group by zr.project
)new on new.project = zpp.project ORDER BY " . $orderCol . " " . $orderType;

        $limit = " limit :pageCurr , :pageSize";
        $sumSql = "select count(1) num,
sum(c.newMgtRevNum) allNewMgtRevNum,
sum(c.newProRevNum) allNewProRevNum,
sum(c.newCbpRevNum) allNewCbpRevNum,
sum(c.newMgtRevNum)+sum(c.newProRevNum)+sum(c.newCbpRevNum)  allNewNum,
sum(c.newQusAdoptedNum) allNewQusAdoptedNum,
sum(c.newQusNotAdoptedNum) allNewQusNotAdoptedNum,
sum(c.newQusNoModNum) allNewQusNoModNum,
sum(c.newQusAdoptedNum)+sum(c.newQusNotAdoptedNum)+sum(c.newQusNoModNum) allNewQusNum,
sum(c.totalMgtRevNum) allMgtRevNum,
sum(c.totalProRevNum) allProRevNum,
sum(c.totalCbpRevNum) allCbpRevNum,
sum(c.totalMgtRevNum)+sum(c.totalProRevNum)+sum(c.totalCbpRevNum)  allNum,
sum(c.totalQusAdoptedNum) allQusAdoptedNum,
sum(c.totalQusNotAdoptedNum) allQusNotAdoptedNum,
sum(c.totalQusNoModNum) allQusNoModNum,
sum(c.totalQusAdoptedNum)+sum(c.totalQusNotAdoptedNum)+sum(c.totalQusNoModNum)  allQusNum from (";



        $pageSql = $sql . $limit;

        // 组合入参
        $varArr = [];
        $varArr['begin'] = $begin;
        $varArr['end'] = $end;
        $varArr['pageSize'] = $pageSize;
        $varArr['pageCurr'] = $pageCurr;
        $varArrType['pageCurr'] = PDO::PARAM_INT;
        $varArrType['pageSize'] = PDO::PARAM_INT;
        $dataList = $this->SqlPrepare($pageSql, $varArr,$varArrType);
//        $dataList = $this->dao->query($pageSql)
//            ->fetchAll();
        unset($varArr['pageSize']);
        unset($varArr['pageCurr']);
        // 合计获取
        $sum = $this->SqlPrepare($sumSql . $sql .') c', $varArr);
//        // 合计获取
//        $sum = $this->dao->query($sumSql . $sql . ') c')
//            ->fetchAll();
        $this->saveLogs1($sql);
        $res->allNewMgtRevNum = $sum[0]->allNewMgtRevNum;
        $res->allNewProRevNum = $sum[0]->allNewProRevNum;
        $res->allNewCbpRevNum = $sum[0]->allNewCbpRevNum;
        $res->allNewNum = $sum[0]->allNewNum;
        $res->allNewQusAdoptedNum = $sum[0]->allNewQusAdoptedNum;
        $res->allNewQusNotAdoptedNum = $sum[0]->allNewQusNotAdoptedNum;
        $res->allNewQusNoModNum = $sum[0]->allNewQusNoModNum;
        $res->allNewQusNum = $sum[0]->allNewQusNum;
        $res->allMgtRevNum = $sum[0]->allMgtRevNum;
        $res->allProRevNum = $sum[0]->allProRevNum;
        $res->allCbpRevNum = $sum[0]->allCbpRevNum;
        $res->allNum = $sum[0]->allNum;
        $res->allQusAdoptedNum = $sum[0]->allQusAdoptedNum;
        $res->allQusNotAdoptedNum = $sum[0]->allQusNotAdoptedNum;
        $res->allQusNoModNum = $sum[0]->allQusNoModNum;
        $res->allQusNum = $sum[0]->allQusNum;
        $pageInfo = new stdClass();
        $pageInfo->currentPage = $_POST['currentPage'];
        $pageInfo->pageSize = $pageSize;
        $pageInfo->num = $sum[0]->num;
        $pageInfo->dataList = $dataList;
        $res->projectReviewStatistic = $pageInfo;

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);

    }

    public function saveLogs1($log)
    {
        $logFile = $this->app->getTmpRoot() . 'log/upgrade.' . date('Ymd') . '.log.php';
        $log     = date('Y-m-d H:i:s') . ' ' . trim($log) . "\n";
        if(!file_exists($logFile)) $log = "<?php\ndie();\n?" . ">\n" . $log;

        static $fh;
        if(empty($fh)) $fh = fopen($logFile, 'a');
        fwrite($fh, $log);
    }


    /**
     *
     * 分页查询当前统计起止时间范围内的评审明細情况
     */
    public function getprojectreviewdetails()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('review', '度量-评审明細');
        // 接口token校验
        $this->checkApiToken();
        $begin = $_POST['startDay'];
        $end = $_POST['endDay'];
        $projects = $_POST['projectIds'];
        //若projectIds为空，则查询出结果为空
        if (!isset($projects) || empty($projects)){
            $this->loadModel('requestlog')->response('success', $this->lang->api->successful, []);
        }
        $type = $_POST['reviewType'];
        $name = $_POST['reviewName'];
        $orderCol = $_POST['orderCol'];
        $orderType = $_POST['orderType'];
        $pageSize = $_POST['pageSize'];
        $pageCurr = ($_POST['currentPage'] - 1) * $pageSize;
        // 组合入参
        $varArr = [];
        $res = new stdClass();
        if (empty($begin)){
            $begin = '1900-01-01 00:00:00';
        }
        if (empty($end)){
            $end = date('Y-m-d').' 23:59:59';
        }
        $projects = $array = explode(',', $projects);
        $newProjects = "(";
        foreach ($projects as $key => $data) {
            $newProjects = $newProjects . "'" . $data . "'" . ",";
        }
        $newProjects = substr($newProjects, 0, -1) . ")";

        $projects = "zr.project in " . $newProjects;

        if (!isset($type) || empty($type)) {
            $type = 1;
        } else {
            $type = "zr.type = :type ";
            $varArr['type']=$_POST['reviewType'];
        }
        if (!isset($name) || empty($name)) {
            $name = 1;
        } else {
            $name = "zr.title like (:name) ";
            $varArr['name'] = '%' . $_POST['reviewName'] . '%';
        }
        if (empty($orderCol)) $orderCol = "reviewCloseTime";
        if (empty($orderType)) $orderType = "DESC";
        $orderSql = "";
        if ($orderCol=="reviewType" && $orderType=="ASC") $orderSql=" (case when zr.type='cbp' then 1 when zr.type='manage' then 2 when zr.type='pro' then 3 else 0 end), ";
        if ($orderCol=="reviewType" && $orderType=="DESC") $orderSql=" (case when zr.type='cbp' then 2 when zr.type='manage' then 1 when zr.type='pro' then 0 else 3 end), ";

        $sql = "select 
 zd.ldapName AS departmentCn,
 zd.name AS deptName,
 zpp.name AS projectName,
 zpp.mark AS projectCode,
 IFNULL(zpc.PM, zpp.owner)  AS manager,
 zr.title AS reviewName,
case  zr.type
    when 'manage' then '管理评审'
    when 'pro' then '专业评审'
    when 'cbp' then 'CBP评审'
    else zr.type
 end as reviewType,
 zr.createdDate AS reviewCreatTime,
 IF((submit1.date is null || (SUBSTRING(submit1.date,1,10) = SUBSTRING('0000-00-00',1,10)) || (submit1.date > submit2.date)),submit2.date, submit1.date) as reviewSubmitTime,
 zr.closeTime AS reviewCloseTime,
 rtime.suspendTimes AS suspendTimes,
 rtime.renewTimes AS renewTimes, 
 zr.closeTime,
 ifnull(qus1.num,0) AS qusAdoptedNum,
 ifnull(qus2.num,0)  AS qusNotAdoptedNum,
 ifnull(qus3.num,0)  AS qusNoModNum,
 (ifnull(qus1.num,0) +ifnull(qus2.num,0) +ifnull(qus3.num,0)) AS allQusNum,
 CONCAT('/review-view-',zr.id,'.html') AS dpmpDetailUrl
from 
    (select * from zt_review zr where zr.deleted ='0' AND zr.type in ('manage','pro','cbp') and zr.status not in ('drop','recall') 
        AND zr.closeTime between :begin AND :end AND $name AND $type AND $projects) zr
left join (select review, COUNT(1) num from zt_reviewissue zri where zri.status = 'closed' and zri.deleted='0' group by review) qus1 on zr.id = qus1.review
left join (select review, COUNT(1) num from zt_reviewissue zri where zri.status  = 'nadopt' and zri.deleted='0' group by review) qus2 on zr.id = qus2.review
left join (select review, COUNT(1)  num from zt_reviewissue zri where zri.status in('nvalidation','repeat') and zri.deleted='0' group by review) qus3 on zr.id = qus3.review
LEFT JOIN zt_projectplan zpp on zr.project = zpp.project  and zpp.deleted ='0'
LEFT JOIN zt_projectcreation zpc on zpc.plan = zpp.id
LEFT JOIN (select objectID, max(createdDate) as date from zt_consumed where objectType='review' and `before`='waitApply' and `after`='waitPreReview' and deleted='0' group by objectID)submit1
    on zr.id = submit1.objectID
LEFT JOIN (select objectID, min(createdDate) as date from zt_consumed where objectType='review' and `before`='waitFirstAssignDept' and `after` in ('waitFirstAssignReviewer','waitFormalAssignReviewer') and deleted='0' group by objectID)submit2
    on zr.id = submit2.objectID
LEFT JOIN zt_dept zd on zd.id = (case when zpp.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = zpc.pm)
	                    ELSE zpp.bearDept end)
left join (select objectID, GROUP_CONCAT(CASE WHEN action= 'suspend' THEN date END ORDER BY date ASC) AS suspendTimes,
            GROUP_CONCAT(CASE WHEN action= 'renew' THEN date END ORDER BY date ASC) AS renewTimes from zt_action where objectType = 'review' and  action in ('renew','suspend') group by objectID) rtime on rtime.objectID = zr.id
group by zr.id,zpp.id,reviewCloseTime,zd.id,zpc.id
order by " . $orderSql . $orderCol . " " . $orderType;
        $this->saveLogs1($sql);

        $limit = " limit :pageCurr , :pageSize";

        $pageSql = $sql . $limit;

        $varArr['begin'] = $begin;
        $varArr['end'] = $end;
        $varArr['pageSize'] = $pageSize;
        $varArr['pageCurr'] = $pageCurr;
        $varArrType['pageCurr'] = PDO::PARAM_INT;
        $varArrType['pageSize'] = PDO::PARAM_INT;

        $dataList = $this->SqlPrepare($pageSql, $varArr,$varArrType);

       $sumSql = "select count(1) num from (";

        foreach ($dataList as $key => $data) {
            if(!empty($data->reviewCloseTime) && $data->reviewCloseTime != "0000-00-00 00:00:00" && !empty($data->reviewSubmitTime) && $data->reviewSubmitTime!= "0000-00-00 00:00:00"){

                    if (!$this->isWorkDay(substr($data->reviewSubmitTime,0,10))){
                        $reviewSubmitTime = substr($data->reviewSubmitTime,0,10) . ' 00:00:00';
                    }else{
                        $reviewSubmitTime = $data->reviewSubmitTime;
                    }
                    if (!$this->isWorkDay(substr($data->reviewCloseTime,0,10))){
                        $reviewCloseTime = substr($data->reviewCloseTime,0,10) . ' 23:59:59';
                    }else{
                        $reviewCloseTime = $data->reviewCloseTime;
                    }
                    $realSec = $this->getWorkingTimeBetween($reviewSubmitTime, $reviewCloseTime,'sec');

                //                    $realDuration = $this->loadModel('holiday')->getActualWorkingDays(substr($data->reviewSubmitTime,0,10), substr($data->reviewCloseTime,0,10));
                // 当两个时间都在节假日时会查1s，计算结果为-1
                if($realSec<0) $realSec=0;
//                $realDuration = count($realDuration);
//                //如8.21提交 8.23关闭应该是2天
//                if($realDuration >0){
//                    $realDuration = $realDuration-1;
//                }
                //计算sum（'恢复项目评审'时间-‘挂起项目评审’时间-节假日-倒休）
                $renewTimeArray = $array = explode(',', $data->renewTimes);
                $suspendTimeArray = $array = explode(',', $data->suspendTimes);
                $renewTimeDuration = 0.00;
                foreach ($renewTimeArray as $key=>$renewTime){
                    if($renewTime != null && count($suspendTimeArray)>$key && $suspendTimeArray[$key] != null && $renewTime!="0000-00-00 00:00:00" && $suspendTimeArray[$key]!="0000-00-00 00:00:00" ) {
                        if (!$this->isWorkDay(substr($suspendTimeArray[$key],0,10))){
                            $suspendTimeArray[$key] = substr($suspendTimeArray[$key],0,10) . ' 00:00:00';
                        }
                        if (!$this->isWorkDay(substr($renewTime,0,10))){
                            $renewTime = substr($renewTime,0,10) . ' 23:59:59';
                        }
                        $renewTimeSec = $this->getWorkingTimeBetween($suspendTimeArray[$key], $renewTime,'sec');
//                        $diffTime = $this->loadModel('holiday')->getActualWorkingDays(substr($suspendTimeArray[$key], 0, 10), substr($renewTime, 0, 10));
//                        $diffTime = count($diffTime);
//                        if ($diffTime > 0) $diffTime = $diffTime - 1;
                    }else{
                        $renewTimeSec = 0;
                    }
                    // 当两个时间都在节假日时会查1s，计算结果为-1
                    if($renewTimeSec<0) $renewTimeSec=0;
                    $renewTimeDuration = $renewTimeDuration + $renewTimeSec;
                }
                $data->reviewDuration = ($realSec - $renewTimeDuration)/(24*60*60);
                $data->reviewDuration = round($data->reviewDuration, 2) ;
            }
            if ($data->renewTimes == null || empty($data->renewTimes)) $data->renewTimes='-';
            if ($data->suspendTimes == null || empty($data->suspendTimes)) $data->suspendTimes='-';
        }
        // 合计获取
        unset($varArr['pageSize']);
        unset($varArr['pageCurr']);
        // 合计获取
        $sum = $this->SqlPrepare($sumSql . $sql .') c', $varArr);

        $projectReviewDetailPageInfo = new stdClass();
        $projectReviewDetailPageInfo->currentPage = $_POST['currentPage'];
        $projectReviewDetailPageInfo->pageSize = $pageSize;
        $projectReviewDetailPageInfo->num = $sum[0]->num;
        $projectReviewDetailPageInfo->dataList = $dataList;
        $res->projectReviewDetailPageInfo = $projectReviewDetailPageInfo;

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);


    }

    /**
     *
     * 实时查询项目评审明细信息情况中的合计列
     */
    public function getprojectreviewdetailstotal()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('review', '度量-评审明细-合计列');
        // 接口token校验
        $this->checkApiToken();
        $begin = $_POST['startDay'];
        $end = $_POST['endDay'];
        $projects = $_POST['projectIds'];
        //若projectIds为空，则查询出结果为空
        if (!isset($projects) || empty($projects)){
            $this->loadModel('requestlog')->response('success', $this->lang->api->successful, []);
        }
        $type = $_POST['reviewType'];
        $name = $_POST['reviewName'];
        // 组合入参
        $varArr = [];
        $res = new stdClass();
        if (empty($begin)){
            $begin = '1900-01-01 00:00:00';
        }
        if (empty($end)){
            $end = date('Y-m-d').' 23:59:59';
        }
        $projects = $array = explode(',', $projects);
        $newProjects = "(";
        foreach ($projects as $key => $data) {
            $newProjects = $newProjects . "'" . $data . "'" . ",";
        }
        $newProjects = substr($newProjects, 0, -1) . ")";

        $projects = "zr.project in " . $newProjects;

        if (!isset($type) || empty($type)) {
            $type = 1;
        } else {
            $type = "zr.type = :type ";
            $varArr['type']=$_POST['reviewType'];
        }
        if (!isset($name) || empty($name)) {
            $name = 1;
        } else {
            $name = "zr.title like (:name) ";
            $varArr['name'] = '%' . $_POST['reviewName'] . '%';
        }

        $sql = "select 
 zr.id AS reviewId,
 zr.createdDate AS reviewCreatTime,
 IF((submit1.date is null || (SUBSTRING(submit1.date,1,10) = SUBSTRING('0000-00-00',1,10)) || (submit1.date > submit2.date)),submit2.date, submit1.date) as reviewSubmitTime,       
 zr.closeTime AS reviewCloseTime,
 zr.suspendTime,
 rtime.suspendTimes AS suspendTimes,
 rtime.renewTimes AS renewTimes, 
 ifnull(qus1.num,0) AS qusAdoptedNum,
 ifnull(qus2.num,0)  AS qusNotAdoptedNum,
 ifnull(qus3.num,0)  AS qusNoModNum,
 (ifnull(qus1.num,0) +ifnull(qus2.num,0) +ifnull(qus3.num,0)) AS allQusNum
from 
    (select * from zt_review zr where zr.deleted ='0' AND zr.type in ('manage','pro','cbp') and zr.status not in ('drop','recall') 
        AND zr.closeTime between :begin AND :end AND $name AND $type AND $projects) zr
left join (select review, COUNT(1) num from zt_reviewissue zri where zri.status = 'closed' and zri.deleted='0' group by review) qus1 on zr.id = qus1.review
left join (select review, COUNT(1) num from zt_reviewissue zri where zri.status  = 'nadopt' and zri.deleted='0' group by review) qus2 on zr.id = qus2.review
left join (select review, COUNT(1)  num from zt_reviewissue zri where zri.status in('nvalidation','repeat') and zri.deleted='0' group by review) qus3 on zr.id = qus3.review
LEFT JOIN zt_projectplan zpp on zr.project = zpp.project  and zpp.deleted ='0'
LEFT JOIN (select objectID, max(createdDate) as date from zt_consumed where objectType='review' and `before`='waitApply' and `after`='waitPreReview' and deleted='0' group by objectID)submit1
    on zr.id = submit1.objectID
LEFT JOIN (select objectID, min(createdDate) as date from zt_consumed where objectType='review' and `before`='waitFirstAssignDept' and `after` in ('waitFirstAssignReviewer','waitFormalAssignReviewer')  and deleted='0' group by objectID)submit2
    on zr.id = submit2.objectID
LEFT JOIN zt_dept zd on zd.id = (case when zpp.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zpc.pm from zt_projectcreation zpc where zpc.plan =zpp.id))
	                    ELSE zpp.bearDept end)
left join (select objectID, GROUP_CONCAT(CASE WHEN action= 'suspend' THEN date END ORDER BY date ASC) AS suspendTimes,
            GROUP_CONCAT(CASE WHEN action= 'renew' THEN date END ORDER BY date ASC) AS renewTimes from zt_action where objectType = 'review' and  action in ('renew','suspend') group by objectID) rtime on rtime.objectID = zr.id
group by zr.id,zpp.id,zd.id";
        $this->saveLogs1($sql);

        $sumSql = "select count(1) num, sum(c.qusAdoptedNum) allQusAdoptedNum, sum(c.qusNotAdoptedNum) allQusNotAdoptedNum, sum(c.qusNoModNum) allQusNoModNum,  sum(c.allQusNum) allAllQusNum from (";

        //计算合计栏的评审周期
        $varArr['begin'] = $begin;
        $varArr['end'] = $end;
        $all = $this->SqlPrepare($sql, $varArr);
        $allReviewDuration = 0.00;
        $CountNotEmptyDuration = 0;
        foreach ($all as $data) {
            if(!empty($data->reviewCloseTime) && $data->reviewCloseTime != "0000-00-00 00:00:00" && !empty($data->reviewSubmitTime) && $data->reviewSubmitTime!= "0000-00-00 00:00:00"){

                    if (!$this->isWorkDay(substr($data->reviewSubmitTime,0,10))){
                        $data->reviewSubmitTime = substr($data->reviewSubmitTime,0,10) . ' 00:00:00';
                    }
                    if (!$this->isWorkDay(substr($data->reviewCloseTime,0,10))){
                        $data->reviewCloseTime = substr($data->reviewCloseTime,0,10) . ' 23:59:59';
                    }
                    $realSec = $this->getWorkingTimeBetween($data->reviewSubmitTime, $data->reviewCloseTime,'sec');

                // 当两个时间都在节假日时会查1s，计算结果为-1
                if($realSec<0) $realSec=0;

                //计算sum（'恢复项目评审'时间-‘挂起项目评审’时间-节假日-倒休）
                $renewTimeArray = $array = explode(',', $data->renewTimes);
                $suspendTimeArray = $array = explode(',', $data->suspendTimes);
                $renewTimeDuration = 0;
                foreach ($renewTimeArray as $key=>$renewTime){
                    if($renewTime != null && count($suspendTimeArray)>$key && $suspendTimeArray[$key] != null && $renewTime!="0000-00-00 00:00:00" && $suspendTimeArray[$key]!="0000-00-00 00:00:00" ) {
                        if (!$this->isWorkDay(substr($suspendTimeArray[$key],0,10))){
                            $suspendTimeArray[$key] = substr($suspendTimeArray[$key],0,10) . ' 00:00:00';
                        }
                        if (!$this->isWorkDay(substr($renewTime,0,10))){
                            $renewTime = substr($renewTime,0,10) . ' 23:59:59';
                        }
                        $renewTimeSec = $this->getWorkingTimeBetween($suspendTimeArray[$key], $renewTime,'sec');
                    }else{
                        $renewTimeSec = 0;
                    }
                    // 当两个时间都在节假日时会查1s，计算结果为-1
                    if($renewTimeSec<0) $renewTimeSec=0;
                    $renewTimeDuration = $renewTimeDuration + $renewTimeSec;
                }
                $data->reviewDuration = ($realSec - $renewTimeDuration)/(24*60*60);
                $data->reviewDuration = round($data->reviewDuration, 2) ;
                $CountNotEmptyDuration = $CountNotEmptyDuration+1;
            }
            $allReviewDuration = $allReviewDuration +$data->reviewDuration;
        }
        // 合计获取
//        $sum = $this->dao->query($sumSql . $sql . ') c')
//            ->fetchAll();
        $sum = $this->SqlPrepare($sumSql . $sql . ') c', $varArr);
        $res->allQusAdoptedNum = $sum[0]->allQusAdoptedNum;
        $res->allQusNotAdoptedNum = $sum[0]->allQusNotAdoptedNum;
        $res->allQusNoModNum = $sum[0]->allQusNoModNum;
        $res->allAllQusNum = $sum[0]->allAllQusNum;
        $res->averageReviewDuration = round($allReviewDuration/$CountNotEmptyDuration, 2);
        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);


    }

    /**
     * 查询全部部门列表
     */
    public function getalldepts()
    {

//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('dept', '人员所属部门/所有部门');
        // 接口token校验
        $this->checkApiToken();
        $account = $_POST['userId'];
        $leafNode = $_POST['leafNode'];
        if($leafNode) $leafArr = $this->dao->select('parent')->from(TABLE_DEPT)->fetchPairs();

        $dataList = $this->dao->select("zd.id,zd.ldapName,(CASE WHEN zd.parent = 0 THEN zd.name ELSE CONCAT(CONCAT((SELECT zt_dept.name from zt_dept zt_dept where zt_dept.id = zd.parent ), '-'), zd.name)END) name")
            ->from(TABLE_DEPT)->alias('zd')
            ->beginIF($account)->innerJoin(TABLE_USER)->alias('zu')->on('zu.dept=zd.id')->andWhere('zu.account')->eq($account)->fi()
            ->beginIF($leafNode)->where('zd.id')->notin(array_values($leafArr))->fi()
            ->orderBy('zd.parent,zd.`order`')->fetchAll();

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     * Desc: 实时查询当前统计起止时间范围内的部门风险信息
     *
     */
    public function getDepartmentRiskStatistic()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('risk', '度量-部门风险汇总');
        // 接口token校验
        $this->checkApiToken();
        $beginDay = $_POST['startDay'];
        $endDay = $_POST['endDay'];
        $projectIds = $_POST['projectIds'];
        //若projectIds为空，则查询出结果为空
        if (!isset($projectIds) || empty($projectIds)){
            $this->loadModel('requestlog')->response('success', $this->lang->api->successful, []);
        }
        $projectInSql = " and pl.project in (" . $projectIds . ")";
        if (empty($beginDay)) {
            $beginDay = '1900-01-01 00:00:00';
        }
        if (empty($endDay)) {
            $endDay = date('Y-m-d').' 23:59:59';
        }
        $sql = "SELECT
        (CASE WHEN zd.parent = 0 THEN zd.name ELSE (SELECT name from zt_dept where id = zd.parent) END) AS deptName,
    	(CASE WHEN zd.parent = 0 THEN zd.id ELSE (SELECT id from zt_dept where id = zd.parent) END) AS departmentId,
    	(CASE WHEN zd.parent = 0 THEN zd.`order` ELSE (SELECT `order` from zt_dept where id = zd.parent) END) AS deptOrder,
        (CASE WHEN zd.parent = 0 THEN zd.ldapName ELSE (SELECT ldapName from zt_dept where id = zd.parent) END) AS departmentCn,
        SUM(ifnull(p.num,0)) as projectNum,
        SUM(ifnull(r1.num,0))as newIncreaseNum,
        SUM(ifnull(r2.num,0) )as totalNum,
        SUM(ifnull(r3.num ,0)) as newCloseNum,
        SUM(ifnull(r4.num ,0) )as totalClosedNum,
        SUM((ifnull(r2.num,0) -  ifnull(r4.num ,0) )) as totalUnclosedNum
        from
        (SELECT bearDept,count(*) as num from zt_projectplan pl where pl.deleted ='0' " . $projectInSql . " group by
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) p
        left join
        zt_dept zd on zd.id = p.bearDept
        left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_risk` r ,`zt_projectplan` pl
        WHERE r.project = pl.project and r.deleted='0' and r.identifiedDate BETWEEN :beginDay  AND :endDay ". $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r1 on r1.bearDept = p.bearDept
        left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_risk` r ,`zt_projectplan` pl WHERE r.project = pl.project and r.deleted='0' and r.identifiedDate >= ('1900-01-01 00:00:00')  and r.identifiedDate <=(:endDay) " . $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r2 on r2.bearDept = p.bearDept
        left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_risk` r ,`zt_projectplan` pl WHERE r.project = pl.project and r.deleted='0' and SUBSTRING(r.actualClosedDate ,1,7)= SUBSTRING(:endDay ,1,7) " . $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r3 on r3.bearDept = p.bearDept
        left join
        (SELECT pl.bearDept,COUNT(*)  as num FROM `zt_risk` r ,`zt_projectplan` pl WHERE r.project = pl.project and r.deleted='0' and r.actualClosedDate  BETWEEN '1900-01-01 00:00:00' AND :endDay ". $projectInSql . " GROUP BY 
         case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)) ELSE pl.bearDept end) r4 on r4.bearDept = p.bearDept
        group by deptName, departmentId, deptOrder, departmentCn
        order by deptOrder";
        // 组合入参
        $varArr = [];
        $varArr['beginDay'] = $beginDay;
        $varArr['endDay'] = $endDay;
        $dataList = $this->SqlPrepare($sql, $varArr);
//        $dataList = $this->dao->query($sql)
//            ->fetchAll();

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     * Desc: 分页查询当前统计起止时间范围内的项目风险汇总情况
     *
     */
    public function getProjectRiskStatistic()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('risk', '项目风险汇总');
        // 接口token校验
        $this->checkApiToken();
        $beginDay = $_POST['startDay'];
        $endDay = $_POST['endDay'];
        $projectIds = $_POST['projectIds'];
        $orderCol = $_POST['orderCol'];
        $orderType = $_POST['orderType'];
        //若projectIds为空，则查询出结果为空
        if (!isset($projectIds) || empty($projectIds)){
            $this->loadModel('requestlog')->response('success', $this->lang->api->successful, []);
        }
        $pageSize = $_POST['pageSize'];
        $pageCurr = ($_POST['currentPage'] - 1) * $pageSize;
        $projectInSql = "project in (" . $projectIds . ")";
        if (empty($beginDay)) {
            $beginDay = '1900-01-01 00:00:00';
        }
        if (empty($endDay)) {
            $endDay = date('Y-m-d').' 23:59:59';
        }
        $res = new stdClass();
        $sql = "SELECT
                zd.name AS deptName,
                zd.ldapName AS departmentCn,
                zd.order AS departOrder,
                p.name AS projectName,
                pl.mark AS projectCode,
                u.account as manager,
                sum(ifnull(r1.num,0))as newIncreaseNum,
                group_concat(r1.ids) AS newIncreaseIds,
                sum(ifnull(r3.num ,0)) as newCloseNum,
                group_concat(r3.ids) AS newCloseIds,
                sum(ifnull(r4.num ,0) )as totalClosedNum,
                group_concat(r4.ids) AS totalClosedIds,
                sum(ifnull(r2.num,0) )as totalNum,
                group_concat(r2.ids) AS totalNumIds,
                sum(( ifnull(r2.num,0) -  ifnull(r4.num ,0) )) as totalUnclosedNum,
                (IF(sum((ifnull(r2.num, 0) - ifnull(r4.num, 0))) < 0, group_concat(r6.ids), group_concat(r5.ids))) AS totalUnclosedIds,
                (IF(sum(ifnull(r2.num ,0) )=0 ,1,ROUND(sum(ifnull(r4.num,0) )/ sum(ifnull(r2.num ,0) ) , 2))) AS totalRiskCloseRate
                from (select * from zt_projectplan where deleted ='0' and " . $projectInSql . ") pl
                left join
                zt_dept zd on zd.id = (case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id))
	                    ELSE pl.bearDept end)
                left join
                zt_user u on u.account = IFNULL((select zp.pm from zt_projectcreation zp where zp.plan = pl.id), pl.owner)
                left join 
                zt_project p on p.id=pl.project
                left join
                (SELECT pl.project,COUNT(*)  as num,group_concat(r.id) AS ids  FROM `zt_risk` r ,`zt_projectplan` pl
                WHERE r.project = pl.project and r.deleted='0' and r.identifiedDate BETWEEN :beginDay AND :endDay group by pl.project) r1 on r1.project = pl.project
                left join
                (SELECT pl.project,COUNT(*)  as num ,group_concat(r.id) AS ids FROM `zt_risk` r ,`zt_projectplan` pl WHERE r.project = pl.project and r.deleted='0' and r.identifiedDate >= ('1900-01-01 00:00:00')  and r.identifiedDate <=(:endDay) group by pl.project) r2 on r2.project = pl.project
                left join
                (SELECT pl.project,COUNT(*)  as num ,group_concat(r.id) AS ids FROM `zt_risk` r ,`zt_projectplan` pl WHERE r.project = pl.project and r.deleted='0' and SUBSTRING(r.actualClosedDate,1,7)= SUBSTRING(:endDay,1,7) group by pl.project) r3 on r3.project = pl.project
                left join
                (SELECT pl.project,COUNT(*)  as num ,group_concat(r.id) AS ids FROM `zt_risk` r ,`zt_projectplan` pl WHERE r.project = pl.project and r.deleted='0' and r.actualClosedDate BETWEEN '1900-01-01 00:00:00' AND :endDay group by pl.project) r4 on r4.project = pl.project
                left join
                (SELECT pl.project,COUNT(*)  as num , group_concat(r.id) AS ids FROM `zt_risk` r ,`zt_projectplan` pl WHERE r.project = pl.project and r.deleted='0' and r.identifiedDate >= ('1900-01-01 00:00:00')  and r.identifiedDate <=(:endDay)
                and r.id not in (SELECT id  FROM `zt_risk` r  WHERE " . $projectInSql . " and r.actualClosedDate BETWEEN '1900-01-01 00:00:00' AND :endDay)
                group by pl.project) r5 on r5.project = pl.project
                left join
                (SELECT pl.project,COUNT(*) as num , group_concat(r.id) AS ids FROM `zt_risk` r ,`zt_projectplan` pl WHERE r.project = pl.project and r.deleted='0' and r.actualClosedDate BETWEEN '1900-01-01 00:00:00' AND :endDay
                 and r.id not in (SELECT id FROM `zt_risk` r WHERE " . $projectInSql . " and r.identifiedDate >= ('1900-01-01 00:00:00')  and r.identifiedDate <=(:endDay))
                group by pl.project) r6 on r6.project = pl.project
                group by deptName, departmentCn, departOrder, projectName,projectCode, u.account
                order by " . $orderCol . " " . $orderType;
        $limit = " limit :pageCurr , :pageSize";
        $pageSql = $sql . $limit;
//        $setSql = "SET GLOBAL group_concat_max_len =10240;SET SESSION group_concat_max_len =10240;";
//        $this->dao->exec($setSql);
        $sumSql = "select count(1) num, sum(s.newIncreaseNum) allNewIncreaseNum, group_concat(s.newIncreaseIds) AS allNewIncreaseIds,
                    sum(s.newCloseNum) allNewCloseNum, group_concat(s.newCloseIds) AS allNewCloseIds,
       sum(s.totalClosedNum) allClosedNum, group_concat(s.totalClosedIds) AS allClosedIds,
       sum(s.totalUnclosedNum) allUnclosedNum, group_concat(s.totalUnclosedIds) AS allUnclosedIds,
       sum(s.totalNum) allNum, group_concat(s.totalNumIds) AS allNumIds from (";

        // 组合入参
        $varArr = [];
        $varArr['beginDay'] = $beginDay;
        $varArr['endDay'] = $endDay;
        $varArr['pageSize'] = $pageSize;
        $varArr['pageCurr'] = $pageCurr;
        $varArrType['pageCurr'] = PDO::PARAM_INT;
        $varArrType['pageSize'] = PDO::PARAM_INT;

        $dataList = $this->SqlPrepare($pageSql, $varArr,$varArrType);
//        $dataList = $this->dao->query($pageSql)
//            ->fetchAll();
        // 合计获取
        unset($varArr['pageSize']);
        unset($varArr['pageCurr']);
        $sum = $this->SqlPrepare($sumSql . $sql .') s', $varArr);
//        $sum = $this->dao->query($sumSql . $sql . ') s')
//            ->fetchAll();
        $res->allNewIncreaseNum = $sum[0]->allNewIncreaseNum;
        $res->allNewIncreaseIds = $sum[0]->allNewIncreaseIds;
        $res->allNewCloseNum = $sum[0]->allNewCloseNum;
        $res->allNewCloseIds = $sum[0]->allNewCloseIds;
        $res->allClosedNum = $sum[0]->allClosedNum;
        $res->allClosedIds = $sum[0]->allClosedIds;
        $res->allUnclosedNum = $sum[0]->allUnclosedNum;
        $res->allUnclosedIds = $sum[0]->allUnclosedIds;
        $res->allNum = $sum[0]->allNum;
        $res->allNumIds = $sum[0]->allNumIds;
        $pageInfo = new stdClass();
        $pageInfo->currentPage = $_POST['currentPage'];
        $pageInfo->pageSize = $pageSize;
        $pageInfo->num = $sum[0]->num;
        $pageInfo->dataList = $dataList;
        $res->projectRiskPageInfo = $pageInfo;

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);
    }

    /**
     * Desc: 分页查询风险明细情况
     *
     */
    public function getProjectRiskDetails()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('risk', '风险明细汇总');
        // 接口token校验
        $this->checkApiToken();
        $beginDay = $_POST['startDay'];
        $endDay = $_POST['endDay'];
        $projectIds = $_POST['projectIds'];
        //风险id列表（英文逗号分割），项目汇总页面跳转至详情页面时有效，根据ids搜索，其他搜索条件无效
        $riskIds = $_POST['riskIds'];
        //若projectIds为空，则查询出结果为空
        if (!isset($projectIds) || empty($projectIds)){
            $this->loadModel('requestlog')->response('success', $this->lang->api->successful, []);
        }
        $riskStatus = $_POST['riskStatus'];
        $riskLevel = $_POST['riskLevel'];
        $isSearchByRiskIds = $_POST['isSearchByRiskIds'];
        $orderCol = $_POST['orderCol'];
        $orderType = $_POST['orderType'];
        $pageSize = $_POST['pageSize'];
        $pageCurr = ($_POST['currentPage'] - 1) * $pageSize;
        // 组合入参
        $varArr = [];
        //入参校验，配置初始值和初始sql
        $projectInSql = "and r.project in (" . $projectIds . ")";
        if (!empty($riskIds) && preg_match('/^[0-9,]+$/', $riskIds)) {
            //正则校验，只支持数字和逗号
            $riskIdInSql = "id in (" . $riskIds . ")";
        }
        if (empty($beginDay)) {
            $beginDay = '1900-01-01 00:00:00';
        }
        if (empty($endDay)) {
            $endDay = date('Y-m-d').' 23:59:59';
        }
        $riskStatusSql = "";
        if (!empty($riskStatus)) {
            if ($riskStatus == "closed") {
                $riskStatusSql = " status!='active' AND";
            } else {
                $riskStatusSql = " status ='active' AND";
            }
        }
        $riskLevelSql = "";
        if (!empty($riskLevel)) {
            $riskLevelSql = " pri= :riskLevel AND";
            $varArr['riskLevel']=$riskLevel;
        }
        if (empty($orderCol)) $orderCol = "riskIdentityDate";
        if (empty($orderType)) $orderType = "DESC";
        $orderSql = "";
        if ($orderCol == "riskStatus" && $orderType == "ASC") $orderSql = " (case when r.status='active' then 1 else 0 end), ";
        if ($orderCol == "riskStatus" && $orderType == "DESC") $orderSql = " (case when r.status='active' then 0 else 1 end), ";
        if ($orderCol == "riskLevel" && $orderType == "ASC") $orderSql = " (case when r.pri='low' then 1 when r.pri='middle' then 2 when r.pri='high' then 3 else 0 end), ";
        if ($orderCol == "riskLevel" && $orderType == "DESC") $orderSql = " (case when r.pri='low' then 2 when r.pri='middle' then 1 when r.pri='high' then 0 else 3 end), ";
        $res = new stdClass();
        $sql = "";
        //风险id列表（英文逗号分割），项目汇总页面跳转至详情页面时有效，根据ids搜索，其他搜索条件无效
        if ($isSearchByRiskIds=='1'){
            $sql = "SELECT d.ldapName AS departmentCn,d.name AS deptName,p.name AS projectName,pl.mark AS projectCode,u.account as manager,
                    CONCAT('/risk-view-',r.id,'.html') AS dpmpDetailUrl,
                    r.name AS riskDescription ,pl.project ,
                    case  r.pri
                    when 'low' then '低'
                    when 'middle' then '中'
                    when 'high' then '高'
                    else r.pri
                    end as riskLevel,
                    IF(trim(IF(prevention is NULL or trim(prevention)='', '', CONCAT('预防措施:',prevention,'<br/>')))=''
                    and trim(IF(remedy is NULL or trim(remedy)='', '',CONCAT('应急措施:',remedy,'<br/>')))=''
                    and trim(IF(resolution is NULL or trim(resolution)='', '',CONCAT('解决措施:',resolution,'<br/>')))='' , '-',
                    CONCAT(IF(prevention is NULL or trim(prevention)='', '', CONCAT('预防措施:',prevention,'<br/>')),
                    IF(remedy is NULL or trim(remedy)='', '',CONCAT('应急措施:',remedy,'<br/>')),
                    IF(resolution is NULL or trim(resolution)='', '',CONCAT('解决措施:',resolution,'<br/>'))))  AS riskResponseMeasure,
                    case r.status
                    when 'hangup' then '关闭'
                    when 'active' then '未关闭'
                    when 'closed' then '关闭'
                    when 'canceled' then '关闭'
                    else r.status
                    end
                    AS riskStatus,
                    r.identifiedDate AS riskIdentityDate,
                    r.actualClosedDate AS riskCloseDate
                    FROM
                    (SELECT * FROM zt_risk WHERE " . $riskIdInSql . " and deleted='0') r
                    INNER JOIN
                    zt_projectplan pl ON pl.deleted='0' and pl.project = r.project " . $projectInSql . "
                    left join 
                    zt_project p on p.id = r.project " . $projectInSql . "
                    LEFT JOIN
                    zt_dept d ON d.id = (case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id))
	                    ELSE pl.bearDept end)
                    left join
                    zt_user u on u.account = IFNULL((select zp.pm from zt_projectcreation zp where zp.plan = pl.id), pl.owner)
                    order by " . $orderSql . $orderCol . " " . $orderType;
        } else {
            $sql = "SELECT d.ldapName AS departmentCn,d.name AS deptName,p.name AS projectName,pl.mark AS projectCode,u.account as manager,
                    CONCAT('/risk-view-',r.id,'.html') AS dpmpDetailUrl,
                    r.name AS riskDescription ,pl.project ,
                    case  r.pri
                    when 'low' then '低'
                    when 'middle' then '中'
                    when 'high' then '高'
                    else r.pri
                    end as riskLevel,
                    IF(trim(IF(prevention is NULL or trim(prevention)='', '', CONCAT('预防措施:',prevention,'<br/>')))=''
                    and trim(IF(remedy is NULL or trim(remedy)='', '',CONCAT('应急措施:',remedy,'<br/>')))=''
                    and trim(IF(resolution is NULL or trim(resolution)='', '',CONCAT('解决措施:',resolution,'<br/>')))='' , '-',
                    CONCAT(IF(prevention is NULL or trim(prevention)='', '', CONCAT('预防措施:',prevention,'<br/>')),
                    IF(remedy is NULL or trim(remedy)='', '',CONCAT('应急措施:',remedy,'<br/>')),
                    IF(resolution is NULL or trim(resolution)='', '',CONCAT('解决措施:',resolution,'<br/>'))))  AS riskResponseMeasure,
                    case r.status
                    when 'hangup' then '关闭'
                    when 'active' then '未关闭'
                    when 'closed' then '关闭'
                    when 'canceled' then '关闭'
                    else r.status
                    end
                    AS riskStatus,
                    r.identifiedDate AS riskIdentityDate,
                    r.actualClosedDate AS riskCloseDate
                    FROM
                    (SELECT * FROM zt_risk WHERE " . $riskLevelSql . $riskStatusSql . " deleted='0' ) r
                    INNER JOIN
                    zt_projectplan pl ON pl.deleted='0' and pl.project = r.project " . $projectInSql . "
                    left join 
                    zt_project p on p.id = r.project " . $projectInSql . "
                    LEFT JOIN
                    zt_dept d ON d.id = (case when pl.bearDept like '%,%' then (select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id))
	                    ELSE pl.bearDept end)
                    left join
                    zt_user u on u.account = (select zp.pm from zt_projectcreation zp where zp.plan = pl.id)
                    order by " . $orderSql . $orderCol . " " . $orderType;
        }

        $limit = " limit :pageCurr , :pageSize";
        $pageSql = $sql . $limit;
        $sumSql = "select count(1) num from (";

        $varArr['pageSize'] = $pageSize;
        $varArr['pageCurr'] = $pageCurr;
        $varArrType['pageCurr'] = PDO::PARAM_INT;
        $varArrType['pageSize'] = PDO::PARAM_INT;

        $dataList = $this->SqlPrepare($pageSql, $varArr,$varArrType);
//        $dataList = $this->dao->query($pageSql)
//            ->fetchAll();
        // 合计获取
        unset($varArr['pageSize']);
        unset($varArr['pageCurr']);
        $sum = $this->SqlPrepare($sumSql . $sql .') c', $varArr);
//        $sum = $this->dao->query($sumSql . $sql . ') s')
//            ->fetchAll();
        $res->currentPage = $_POST['currentPage'];
        $res->pageSize = $pageSize;
        $res->num = $sum[0]->num;
        $res->dataList = $dataList;

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $res);

    }

    /**
     * Desc: 部门风险-月变化趋势接口
     *
     */
    public function getRiskMonthlyTrendData()
    {
//        $logID = $this->loadModel('requestlog')->insideSaveRequestLog('risk', '月变化趋势');
        // 接口token校验
        $this->checkApiToken();
        $statisticalDateStrs = $_POST['statisticalDateList'];
        $projectIds = $_POST['projectIds'];
        //若projectIds为空，则查询出结果为空
        if (!isset($projectIds) || empty($projectIds)){
            $this->loadModel('requestlog')->response('success', $this->lang->api->successful, []);
        }
        //入参校验，配置初始值和初始sql
        $projectInSql = "and project in (" . $projectIds . ")";
        $statisticalDateArr = explode(',', $statisticalDateStrs);
        $dataList = array();
        foreach ($statisticalDateArr as $statisticalDate) {
            $sql = "SELECT SUBSTRING('" . $statisticalDate . "',1,7) as month,
                       SUM(ifnull(r1.num,0))as newIncreaseNum,SUM(ifnull(r2.num,0) )as totalNum,
                       SUM(ifnull(r3.num ,0)) as newCloseNum,SUM(ifnull(r4.num ,0) )as totalClosedNum,
                       SUM((ifnull(r2.num,0) - ifnull(r4.num ,0) )) as totalUnclosedNum
	            from (SELECT project from zt_projectplan where deleted ='0' " . $projectInSql . ") p
	            left join (SELECT project, COUNT(*) as num FROM zt_risk r  WHERE SUBSTRING(r.identifiedDate,1,7)= SUBSTRING('" . $statisticalDate . "',1,7) " . $projectInSql . " and r.deleted='0' group by project) r1 on r1.project = p.project
	            left join (SELECT project, COUNT(*) as num FROM zt_risk r  WHERE r.identifiedDate >= ('1900-01-01')  and r.identifiedDate <=LAST_DAY('" . $statisticalDate . "-01') " . $projectInSql . " and r.deleted='0' group by project) r2 on r2.project = p.project 
	            left join (SELECT project, COUNT(*) as num FROM zt_risk r  WHERE SUBSTRING(r.actualClosedDate,1,7)= SUBSTRING('" . $statisticalDate . "',1,7) " . $projectInSql . " and r.deleted='0' group by project) r3 on r3.project = p.project
	            left join (SELECT project, COUNT(*) as num FROM zt_risk r  WHERE r.actualClosedDate>= ('1900-01-01')  and r.actualClosedDate<=LAST_DAY('" . $statisticalDate . "-01') " . $projectInSql . " and r.deleted='0' group by project) r4 on r4.project = p.project";
            $data = $this->dao->query($sql)
                ->fetchAll();
            $dataList[] = $data[0];
        }
        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     *
     * 查询需求意向
     */
    function getOpinionByDate() {
        //$logID = $this->loadModel('requestlog')->insideSaveRequestLog('measure-opinion', '度量同步需求意向');
        // 接口参数和token校验
        $this->checkApiToken();
        $errMsg = $this->checkInput('startDate', 'endDate');
        if(!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], 0, self::PARAMS_ERROR);
        }

        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];

        $dataList = $this->dao->select("id,receiveDate,createdDate,category,status,if(status = 'online', onlineTimeByDemand, null) onlineTime,deadline,opinionChangeTimes,sourceMode,`union`,name,overview,
        solvedTime deliveryTime,
        if(sourceMode = '8', (select max(maxdate) from (select if(zr.feedbackStatus = 'feedbacksuccess', zr.`end`, zr.deadLine) maxdate from zt_requirement zr where zr.opinion = zo.id) d), deadline) deadlineKind,
        (select GROUP_CONCAT(za1.date order by za1.date desc) from zt_action za1 where za1.objectType = 'opinion' and za1.action = 'suspenditem' and za1.objectID = zo.id) suspendedTimes,
        (select GROUP_CONCAT(za2.date order by za2.date desc) from zt_action za2 where za2.objectType = 'opinion' and za2.action = 'reseted' and za2.objectID = zo.id) activatedTimes,
        if(status = 'online',(select GROUP_CONCAT(CONCAT(zd.product,CONCAT('+',zd.productPlan))) from zt_demand zd where zd.opinionID = zo.id and zd.product != 0 and zd.product != 99999 and zd.productPlan != 0 and zd.productPlan != 1), null) products,
        code as opinionNumber,
        zo.updateTime as updateTime")
            ->from(TABLE_OPINION)->alias('zo')
            ->where('sourceOpinion')->eq('1')
            ->andWhere("zo.updateTime >= '$startDate'")
            ->beginIF($endDate)->andWhere("zo.updateTime <= '$endDate'")->fi()
            ->fetchAll();

        foreach ($dataList as $data ) {
            // 计算挂起时间
            $suspendTimeDuration = $this->getSuspendTimeDuration($data->suspendedTimes,$data->activatedTimes);
            $suspendedTimeError = substr_count($data->suspendedTimes, ',') == substr_count($data->activatedTimes, ',') ? false : true;

            // 计算交付周期
            if($data->status == 'online' || $data->status == 'delivery') {
                $actualDeliveryDays = $this->loadModel('holiday')->getActualWorkingDays(substr($data->receiveDate,0,10), substr($data->deliveryTime,0,10));
                if(!empty($actualDeliveryDays)) {
                    $data->deliveryCycle = count($actualDeliveryDays) - $suspendTimeDuration;
                }
                if(empty($data->receiveDate) || $data->receiveDate == '0000-00-00 00:00:00' || empty($data->deliveryTime) || $data->deliveryTime == '0000-00-00 00:00:00' || $suspendedTimeError) $data->deliveryCycleError = 'N/A';
            }

            // 计算实现周期、延迟时间
            if($data->status == 'online') {
                $actualAchieveDays = $this->loadModel('holiday')->getActualWorkingDays(substr($data->receiveDate,0,10), substr($data->onlineTime,0,10));
                if(!empty($actualAchieveDays)) {
                    $data->achieveCycle = count($actualAchieveDays) - $suspendTimeDuration;
                }
                if(empty($data->onlineTime) || $data->onlineTime == '0000-00-00 00:00:00' || empty($data->receiveDate) || $data->receiveDate == '0000-00-00 00:00:00' || $suspendedTimeError) $data->achieveCycleError = 'N/A';
                if(!empty($data->onlineTime) && !empty($data->deadlineKind)) {
                    $delayTimeDays = $this->loadModel('holiday')->getDaysBetween($data->deadlineKind, $data->onlineTime);
                    $data->delayTime = count($delayTimeDays) > 0 ? count($delayTimeDays) -1 : 0;
                }
                if(empty($data->onlineTime) || $data->onlineTime == '0000-00-00 00:00:00' || empty($data->deadlineKind) || $data->deadlineKind == '0000-00-00') $data->delayTimeError = 'N/A';
            }

            $data->syncTime = date(DT_DATETIME1, strtotime("-5 minute"));
            $data->union = trim($data->union, ',');
            if($data->onlineTime == '0000-00-00 00:00:00') $data->onlineTime = null;
            if($data->deliveryTime == '0000-00-00 00:00:00') $data->deliveryTime = null;
            if($data->deadline == '0000-00-00') $data->deadline = null;
            if($data->deadlineKind == '0000-00-00' || $data->deadlineKind == null) $data->deadlineKind = $data->deadline;
            if($data->sourceMode == '8') {
                $data->sourceMode = '1';
            }else {
                $data->sourceMode = '2';
            }

        }

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     *
     * 获取需求任务
     */
    function getRequirements() {
       // $logID = $this->loadModel('requestlog')->insideSaveRequestLog('measure-opinion', '度量同步需求任务');
        // 接口token校验 和 参数校验
        $this->checkApiToken();
        $errMsg = $this->checkInput('startDate', 'endDate');
        if(!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], 0, self::PARAMS_ERROR);
        }

        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];

        $dataList = $this->dao->select(' zr.id id,zo.`union`,zo.sourceMode,zr.id requirementId,zo.id opinionId,zr.name requirementTitle,zr.`desc` requirementDesc,zr.acceptTime acceptTime,zr.createdDate,zr.status requirementStatus,
                             zr.deadLine deadLine,zr.onlineTimeByDemand launchTime,zr.requirementChangeTimes changeTimes,zr.createdBy,zr.end,zr.feedbackStatus,zr.activatedDate,zr.solvedTime deliveryTime,
                             dtime.suspendedTimes as suspendedTimes,
                             dtime.activatedTimes as activatedTimes,
                             zr.code as requirementNumber,
                             zr.planEnd as planDate,
                             zr.createdBy as creator,
                             zu.realname as creatorName,
                             greatest(IFNULL(zr.updateTime, 0), IFNULL(zo.updateTime, 0)) as updateTime')
            ->from(TABLE_REQUIREMENT)->alias('zr')
            ->leftJoin('zt_user')->alias('zu')->on('zr.createdBy = zu.account')
            ->leftJoin('zt_opinion')->alias('zo')->on('zr.opinion = zo.id')
            ->leftJoin("(select objectID, GROUP_CONCAT(CASE WHEN action in ('suspenditem') THEN date END ORDER BY date ASC) AS suspendedTimes,
                          GROUP_CONCAT(CASE WHEN action in ('activated','activate') THEN date END ORDER BY date ASC) AS  activatedTimes 
                          from zt_action where objectType = 'requirement' and  action in ('activated','activate','suspenditem') group by objectID) dtime")->on('dtime.objectID=zr.id')
            ->where("zr.sourceRequirement")->eq('1')
            ->andWhere()
            ->markLeft(2)
            ->where("zr.updateTime >= '$startDate'")
            ->beginIF($endDate)->andWhere("zr.updateTime <= '$endDate'")->fi()
            ->markRight(1)
            ->orWhere()
            ->markLeft(1)
            ->where("zo.updateTime >= '$startDate'")
            ->beginIF($endDate)->andWhere("zo.updateTime <= '$endDate'")->fi()
            ->markRight(2)
            ->fetchAll();

        foreach ($dataList as $data ) {

            $suspendTimeDuration = $this->getSuspendTimeDuration($data->suspendedTimes, $data->activatedTimes);
            $suspendedTimeError = substr_count($data->suspendedTimes, ',') == substr_count($data->activatedTimes, ',') ? false : true;

            if($suspendTimeDuration != 0){
                $this->saveLogs1("挂起时间------".$suspendTimeDuration."---任务id----".$data->id);
            }
            if($data->sourceMode == '8') {
                $data->sourceMode = '1';
            }else {
                $data->sourceMode = '2';
            }

            $data->union = trim($data->union, ',');
            if(empty($data->union) ){
                $data->union = '100';
            }
            $data->syncTime = date(DT_DATETIME1, strtotime("-5 minute"));
            /*if($data->deadLine == '0000-00-00' || empty($data->deadLine)){
                $data->deadLine = '';
            }*/

            if($data->feedbackStatus =='feedbacksuccess'){
                $data->deadLineTotal = $data->end;
            }else{
                $data->deadLineTotal = $data->deadLine;
            }
            if(($data->requirementStatus == 'delivered' || $data->requirementStatus == 'onlined') ){

                $actualDeliveryDays =  $this->loadModel('holiday')->getActualWorkingDays(substr($data->acceptTime,0,10), substr($data->deliveryTime,0,10));
                if(!empty($actualDeliveryDays) ) {
                    $data->deliveryCycle = count($actualDeliveryDays)-$suspendTimeDuration;
                }
                if(empty($data->acceptTime) || $data->acceptTime == '0000-00-00 00:00:00' || empty($data->deliveryTime) || $data->deliveryTime == '0000-00-00 00:00:00' || $suspendedTimeError) $data->deliveryCycleError = 'N/A';

            }
            if ($data->requirementStatus == 'onlined') {
                $actualRealizationCycle = $this->loadModel('holiday')->getActualWorkingDays(substr($data->acceptTime, 0, 10), substr($data->launchTime, 0, 10));
                if (!empty($actualRealizationCycle)) {
                    $data->realizationCycle = count($actualRealizationCycle) - $suspendTimeDuration;
                }
                if (empty($data->acceptTime) || $data->acceptTime == '0000-00-00 00:00:00' || empty($data->launchTime) || $data->launchTime == '0000-00-00 00:00:00' || $suspendedTimeError) $data->realizationCycleError = 'N/A';

                if (!$this->replaceIllegalTimeByNULL($data->deadLine) || !$this->replaceIllegalTimeByNULL($data->launchTime)) {
                    $data->delayTimeError = 'N/A';
                } else {
                    $actualdelayTime = $this->loadModel('holiday')->getDaysBetween(substr($data->deadLine, 0, 10), substr($data->launchTime, 0, 10));
                    if (!empty($actualdelayTime)) {
                        $data->delayTime = count($actualdelayTime);
                    }
                }

            }

            $data->acceptTime = $this->replaceIllegalTimeByNULL($data->acceptTime);
            $data->createdDate = $this->replaceIllegalTimeByNULL($data->createdDate);
            $data->launchTime = $this->replaceIllegalTimeByNULL($data->launchTime);
            $data->deliveryTime = $this->replaceIllegalTimeByNULL($data->deliveryTime);
            $data->suspendedTimes = $this->replaceIllegalTimeByNULL($data->suspendedTimes);
            $data->activatedTimes = $this->replaceIllegalTimeByNULL($data->activatedTimes);
            $data->deadLine = $this->replaceIllegalTimeByNULL($data->deadLine);
            $data->deadLineTotal = $this->replaceIllegalTimeByNULL($data->deadLineTotal);
            $data->planDate = $this->replaceIllegalTimeByNULL($data->planDate);
            $data->updateTime = $this->replaceIllegalTimeByNULL($data->updateTime);
        }
        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     * 获取挂起时间段
     */
    public function getSuspendTimeDuration($suspendTimes,$activatedTimes)
    {
        $activatedTimeArray = $array = explode(',', $activatedTimes);
        $suspendTimeArray = $array = explode(',', $suspendTimes);
        $suspendTimeDuration = 0;
        foreach ($activatedTimeArray as $key=>$activatedTime){
            if(!empty($activatedTime) && count($suspendTimeArray)>$key && !empty($suspendTimeArray[$key]) && $activatedTime!="0000-00-00 00:00:00" && $suspendTimeArray[$key]!="0000-00-00 00:00:00" ) {
                $diffTime = $this->loadModel('holiday')->getActualWorkingDays(substr($suspendTimeArray[$key], 0, 10), substr($activatedTime, 0, 10));
                $diffTime = count($diffTime);
            }else{
                $diffTime = 0;
            }
            $suspendTimeDuration = $suspendTimeDuration + $diffTime;
        }
        return $suspendTimeDuration;
    }

    /**
     * 获取需求单位
     */
    function getUnionList(){
       // $logID = $this->loadModel('requestlog')->insideSaveRequestLog('measure-union', '度量同步需求意向');
        $unions = $_POST['unions'];
        $this->loadModel('opinion');
        $unionList = $this->lang->opinion->unionList;
        $dataList   = array();
        $index= 0;
        foreach ($unionList as $key => $value) {
            if($key != null){
                $index += 1;
                $data = new stdclass();
                $data->unionkey = $key;
                $data->unionvalue = $value;
                $data->unionIndex = $index;
                $dataList[] = $data;
            }
        }
        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     * 获取需求条目明细
     */
    function getDemandItemList(){
        //$logID = $this->loadModel('requestlog')->insideSaveRequestLog('measure-demandItem', '度量同步需求明细');
        // 接口参数和token校验
        $this->checkApiToken();
        $errMsg = $this->checkInput('startDate', 'endDate');
        if(!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], 0, self::PARAMS_ERROR);
        }

        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];

        $dataList = $this->dao->select("zd.id as demandId,
                                        zd.opinionID as opinionID,
                                        zd.requirementID as requirementID,
                                        (CASE WHEN dept.parent = 0 THEN dept.ldapName ELSE (SELECT ldapName from zt_dept where id = dept.parent) END) as departmentCn,
                                        IF(zo.sourceMode = '8', '1', '2') as sourceMethod,
                                        IF(REPLACE(RTRIM(LTRIM(REPLACE(zo.`union`,',',' '))),' ',',') is null || REPLACE(RTRIM(LTRIM(REPLACE(zo.`union`,',',' '))),' ',',') =' ',
                                            '100', REPLACE(RTRIM(LTRIM(REPLACE(zo.`union`,',',' '))),' ',',')) as bizDemandDept,
                                        zd.title as title,
                                        zd.`desc` as `desc`,
                                        zd.fixType as implMethod,
                                        zd.status as status,
                                        zd.product as productId,
                                        zp.name as productName,
                                        zd.productPlan as productVersionId,
                                        zpp.title as productVersion,
                                        IF((SUBSTRING(zd.actualOnlineDate,1,10) = SUBSTRING('0000-00-00',1,10) || zd.status != 'onlinesuccess'), null, zd.actualOnlineDate) as onlineTime,
                                        IF(SUBSTRING(zd.solvedTime,1,10) = SUBSTRING('0000-00-00',1,10), null, zd.solvedTime) as deliveryTime,
                                        IF(SUBSTRING(zd.createdDate,1,10) = SUBSTRING('0000-00-00',1,10), null, zd.createdDate) as itemCreateTime,
                                        IF(SUBSTRING(consumed.publishedTime,1,10) = SUBSTRING('0000-00-00',1,10), null, consumed.publishedTime) as publishedTime,
                                        dtime.suspendedTimes as suspendedTimes,
                                        dtime.activatedTimes as activatedTimes,
                                        zd.acceptUser as developerId,
                                        zu.realname as developerName,
                                        zd.code as demandNumber,
                                        zru.productManager as productManagerId,
                                        zru.realname as productManagerName,
                                        greatest(IFNULL(zd.updateTime, 0), IFNULL(zru.updateTime, 0), IFNULL(zo.updateTime, 0)) as updateTime")
            ->from(TABLE_DEMAND)->alias('zd')
            ->leftJoin('zt_user')->alias('zu')->on('zd.acceptUser = zu.account')
            ->leftJoin('(select zr.id, zr.productManager, zu.realname, zr.createdDate, zr.updateTime from zt_requirement zr left join zt_user zu on zu.account = zr.productManager)')->alias('zru')->on('zd.requirementID = zru.id')
            ->leftJoin('zt_opinion')->alias('zo')->on('zd.opinionID = zo.id')
            ->leftJoin('zt_dept')->alias('dept')->on('zd.acceptDept = dept.id')
            ->leftJoin('zt_product')->alias('zp')->on('zd.product= zp.id and zp.deleted="0"')
            ->leftJoin('zt_productplan')->alias('zpp')->on('zd.productPlan= zpp.id and zpp.deleted="0"')
            ->leftJoin('(select objectID, max(createdDate) as publishedTime from zt_consumed where objectType ="requirement" and `before` not in ("published") and `after`="published" and deleted = "0"  group by objectID) consumed')->on('consumed.objectID = zd.requirementID')
            ->leftJoin("(select objectID, GROUP_CONCAT(CASE WHEN action in ('suspended','suspend') THEN date END ORDER BY date ASC) AS suspendedTimes,
                          GROUP_CONCAT(CASE WHEN action= 'activated' THEN date END ORDER BY date ASC) AS activatedTimes 
                          from zt_action where objectType = 'demand' and  action in ('activated','suspended','suspend') group by objectID) dtime")->on('dtime.objectID=zd.id')
            ->where('zd.sourceDemand')->eq('1')
            ->andWhere()
            ->markLeft(2)
            ->where("zd.updateTime >= '$startDate'")
            ->beginIF($endDate)->andWhere("zd.updateTime <= '$endDate'")->fi()
            ->markRight(1)
            ->orWhere()
            ->markLeft(1)
            ->where("zru.updateTime >= '$startDate'")
            ->beginIF($endDate)->andWhere("zru.updateTime <= '$endDate'")->fi()
            ->markRight(1)
            ->orWhere()
            ->markLeft(1)
            ->where("zo.updateTime >= '$startDate'")
            ->beginIF($endDate)->andWhere("zo.updateTime <= '$endDate'")->fi()
            ->markRight(2)
            ->fetchAll();
        //状态为【上线成功】时计算实现周期：条目上线时间-条目所属任务的发布时间-sum(激活时间-挂起时间-周末法定节假日)-周末法定节假日
        foreach ($dataList as $data) {
            $data->syncTime = date(DT_DATETIME1, strtotime("-5 minute"));
            if($data->status == 'onlinesuccess' ){
                $activatedTimeArray = $array = explode(',', $data->activatedTimes);
                $suspendTimeArray = $array = explode(',', $data->suspendTimes);
                if(!empty($data->onlineTime) && $data->onlineTime != "0000-00-00 00:00:00" && !empty($data->publishedTime)
                    && $data->publishedTime != "0000-00-00 00:00:00" && count($activatedTimeArray)==count($suspendTimeArray)) {
                    //从条目上线时间到条目创建时间的周末法定节假日天数
                    $realDuration = $this->loadModel('holiday')->getActualWorkingDays(substr($data->publishedTime, 0, 10), substr($data->onlineTime, 0, 10));
                    $realDuration = count($realDuration);
                    //计算sum（'激活时间'时间-‘挂起时间’时间-周末法定节假日）
                    $suspendTimeDuration = 0;
                    foreach ($activatedTimeArray as $key => $activatedTime) {
                        if (!empty($activatedTime) && count($suspendTimeArray) > $key && !empty($suspendTimeArray[$key]) && $activatedTime != "0000-00-00 00:00:00" && $suspendTimeArray[$key] != "0000-00-00 00:00:00") {
                            $diffTime = $this->loadModel('holiday')->getActualWorkingDays(substr($suspendTimeArray[$key], 0, 10), substr($activatedTime, 0, 10));
                            $diffTime = count($diffTime);
                        } else {
                            $diffTime = 0;
                        }
                        $suspendTimeDuration = $suspendTimeDuration + $diffTime;
                    }
                    $data->implDuration = $realDuration - $suspendTimeDuration;
                    //$realDuration=0代表 上线时间早于发布时间，为异常数据；$data->implDuration<=0则挂起的时长大于等于（上线-发布时间），实现周期为负数或零，为异常数据，异常数据显示’N/A‘
                    if ($realDuration == 0 || $data->implDuration <= 0) {
                        $data->implDuration = 'N/A';
                    }
                }else{
                    //部分时间缺失(上线或发布时间为空、挂起激活时间不配对)，不能计算实现周期，异常数据显示’N/A‘
                    $data->implDuration = 'N/A';
                }
            }else{
                //还不是上线成功状态，无需计算实现周期
                $data->implDuration = '-';
            }
        }
        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     * 获取禅道部门简称不为空的部门信息
     */
    function getDpmpDeptInfoList(){
        //$logID = $this->loadModel('requestlog')->insideSaveRequestLog('measure-demandItem', '度量同步需求明细');
        // 接口token校验
        $this->checkApiToken();
        $dataList = $this->dao->select("id as dpmpDeptId,name,ldapName as departmentCn,parent as parentDpmpDeptId,grade,`order` as departmentOrder")
            ->from(TABLE_DEPT)
            ->where('ldapName')->isNotNull()
            ->andWhere('ldapName')->ne('')
            ->fetchAll();

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    public function getDeliveryDate($type,$objectID,$status)
    {
        $dealDate = $this->dao->select('id,createdDate')->from(TABLE_CONSUMED)
            ->where('objectType')->eq($type)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('after')->eq($status)
            ->orderBy('id desc')
            ->fetch();
        return $dealDate;
    }

    /**
     * 获取节假日所有信息
     */
    function getHolidayList(){
        // 接口token校验
        $this->checkApiToken();
        $dataList = $this->dao->select("id,name,`type`,`desc`,`year`,`begin`,`end`")
            ->from(TABLE_HOLIDAY)
            ->fetchAll();

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     * 计算时间间隔天、时、分、秒(有第三个参数时只计算秒数),去掉周末和节假日
     */
    public function getWorkingTimeBetween($begin, $end, $sec = ''){
        $r = '';
        if(empty($begin) || empty($end)){
            return $r;
        }
        // 过滤假期（总节假日+周末-调休工作日）
        $holidayDays = $this->getAllHolidays($begin, $end);
        $beginTime = strtotime($begin);
        $endTime   = strtotime($end);
        if (empty($holidayDays)){
            $secs = $endTime - $beginTime;
        }else{
            $secs = $endTime - $beginTime - $holidayDays * 86400;
        }
        if(empty($sec)){
            $r = $this->loadModel('holiday')->secToStr($secs);
        }else{
            $r = $secs;
        }
        return $r;
    }

    /**
     * 判断是不是工作日（除去周末，节假日，包含调休的工作日）
     */
    public function isWorkDay($date)
    {
        $isWorkDay = true;
        //先判断是不是节假日
        $isHoliday = $this->loadModel('holiday')->isHoliday(substr($date,0,10));
        if($isHoliday){
            return false;
        }
        //再判断是不是调休的工作日
        $isWorkingDay = $this->loadModel('holiday')->isWorkingDay(substr($date,0,10));
        if ($isWorkingDay){
            return true;
        }
        //最后判断是不是周末
        $day =  date('w', strtotime($date));
        if($day == 6 || $day == 0){
            return false;
        }
        return true;
    }

    /**
     * 获取两个日期之间假期天数（总节假日+周末-调休工作日）
     *
     */
    public function getAllHolidays($begin, $end){
        $allHolidays = 0;
        //节假日总天数
        $holiday = $this->loadModel('holiday')->getHolidays(substr($begin,0,10), substr($end,0,10));
        $holidays = count($holiday);
        //计算除去节假日和调休工作日的周末天数
        $begin = date('Y-m-d',strtotime($begin));
        $end   = date('Y-m-d',strtotime($end));
        $currentDay = $begin;
        $weekendDays = 0;
        for($i = 0; $currentDay < $end; $i ++) {
            $currentDay = date('Y-m-d', strtotime("$begin + $i days"));
            //先判断是不是节假日
            $isHoliday = $this->loadModel('holiday')->isHoliday(substr($currentDay,0,10));
            //再判断是不是调休的工作日
            $isWorkingDay = $this->loadModel('holiday')->isWorkingDay(substr($currentDay,0,10));
            $w          = date('w', strtotime($currentDay));
            if(($w == 0 or $w == 6)and(!$isHoliday)and (!$isWorkingDay)) {
                $weekendDays = $weekendDays+1;
            }
        }
        $allHolidays = $holidays + $weekendDays;
        return $allHolidays;
    }


    /**
     * 获取用户项目权限信息
     */
    public function syncGetUserView(){
        // 接口token校验
        $this->checkApiToken();
        $dataList = $this->dao->select("account,trim(','from projects) as projects")
            ->from(TABLE_USERVIEW)
            ->fetchAll();

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList,0,200);
    }
    /**
     * 获取项目表信息
     */
    public function syncGetProjects(){
        $this->app->loadLang('application');
        $this->app->loadLang('projectplan');
        $this->app->loadLang('project');
        // 接口token校验
        $this->checkApiToken();
        $dataList = $this->dao->select("zp.id projectId, zpp.type projectType,zpp.onlyDept projectDept,zp.name projectName, zp.code codeName,zpc.pm projectManager,zp.status projectStatus,
        CAST(zpp.workloadBase AS DECIMAL(11,1)) workloadBase , CAST(zpp.workloadChengdu AS DECIMAL(11,1)) workloadChengdu,zpc.workload approvalConsumed,zpp.workload planConsumed,
         zpp.`begin` planBegin,zpp.`end` planEnd,zp.workHours workHours,zpp.year year, zpp.isDelayPreYear isDelayPreYear, zp.realEnd realEnd,zp.createtime createTime,
         zp.acl acl,zpp.outsideTask outsideTask")
            ->from(TABLE_PROJECT)->alias('zp')
            ->leftJoin(" (select (case when zt_projectplan.bearDept like '%,%' then ( select zt_user.dept from zt_user  where zt_user.account = (select pc.pm from zt_projectcreation pc where pc.plan = zt_projectplan.id  and pc.deleted = '0')  ) else zt_projectplan.bearDept end) onlyDept, zt_projectplan.* from zt_projectplan ) ")->alias('zpp')->on('zpp.project = zp.id')
            ->leftJoin(TABLE_DEPT)->alias('zd')->on("zd.id = zpp.onlyDept")
            ->leftJoin(TABLE_PROJECTCREATION)->alias('zpc')->on("zpp.id = zpc.plan")
            ->where('zp.deleted')->eq(0)
            ->andwhere('zp.type')->eq('project')
            ->andwhere('(zpc.deleted = 0 or zpp.secondLine in(0,1))')
            ->fetchAll();
        //查询子任务部门
        $subTaskBearDept = $this->dao->select("id,subTaskBearDept")->from(TABLE_OUTSIDEPLANTASKS)->where ('deleted')->eq(0)->fetchPairs('id','subTaskBearDept');
        foreach ($dataList as $key => $data) {

            $data->projectType = zget($this->lang->projectplan->typeList, $data->projectType, '');
            $data->projectStatus = zget($this->lang->project->featureBar, $data->projectStatus, '');
            $contractors =   zmget($subTaskBearDept,$data->outsideTask,'') ? zmget($subTaskBearDept,$data->outsideTask,',') : '';//$this->dao->select("subTaskBearDept")->from(TABLE_OUTSIDEPLANTASKS)->where ('id')->in($data->outsideTask)->fi()->fetchAll();
            $data->contractor = zmget($this->lang->application->teamList,$contractors,'');
            unset($data->outsideTask);
        }
       /* foreach ($dataList as $key => $data) {

            $data->projectType = zget($this->lang->projectplan->typeList, $data->projectType, '');
            $data->projectStatus = zget($this->lang->project->featureBar, $data->projectStatus, '');
            $contractors =   $this->dao->select("subTaskBearDept")->from(TABLE_OUTSIDEPLANTASKS)->where ('id')->in($data->outsideTask)->fi()->fetchAll();
            $contractorss = implode(',',array_column($contractors,'subTaskBearDept'));
            $vlist = explode(',', $contractorss);
            $arr = [];
            foreach (array_unique($vlist) as $itemv) {
                if (empty($itemv)) continue;
                $arr[] = zget($this->lang->application->teamList, $itemv, '');
            }
            $data->contractor = implode(',', $arr);
        }*/
        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList,0,200);
    }

    /**
     * 获取项目风险明细
     */
    public function syncGetRisk(){
        // 接口token校验
        $this->checkApiToken();
        $dataList = $this->dao->select("id as dpmpRiskId,project as projectId,name as description,status ,pri as level,
                         identifiedDate,actualClosedDate as closeDate,preventionAndremedy as reply,resolution,
                         createdDate as createTime")
            ->from(TABLE_RISK)
            ->where('deleted')->eq(0)
            ->fetchAll();
        foreach ($dataList as $key=> $item) {
            if(!$item) continue;
            $dataList[$key]->reply            = strip_tags($item->reply);
            $dataList[$key]->resolution       = strip_tags($item->resolution);
            $dataList[$key]->identifiedDate   = strpos($item->identifiedDate,'0000') === false ? date('Y-m-d',strtotime($item->identifiedDate)) : '';
            $dataList[$key]->closeDate        = strpos($item->closeDate,'0000') === false ? date('Y-m-d',strtotime($item->closeDate)) : '';
            $dataList[$key]->createTime       = date('Y-m-d H:i:s',strtotime($item->createTime));
            $dataList[$key]->dpmpDetailUrl    = '/risk-view-'.$item->dpmpRiskId.'.html';
        }

        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList,0,200);
    }

    /**
     * 校验
     * @return array
     */
    public function checkConsumedInput()
    {
        $errMsg = [];
        if(!isset($_POST['year'])){
            $errMsg[] = "缺少『year』参数";
        }
        if( isset($_POST['year']) && !$_POST['year']){
            $errMsg[] = '『年份』不能为空';
        }
        if(isset($_POST['year']) && $_POST['year']){
            $is_date = date('Y',strtotime($_POST['year'].'-01-01')) == $_POST['year'] ? $_POST['year'] :false;
            if( $is_date === false){
                $errMsg[] = '日期格式非法!';
            }
        }
        return $errMsg;
    }

    /**
     * 获取工作量基础明细
     */
    public function syncGetConsumedBaseDetail(){
        // 接口token校验
        $this->checkApiToken();
        $errMsg = $this->checkConsumedInput();
        if (!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], 0, self::FAIL_CODE);
        }
        $year = $_POST['year'];
      //  $dataList = $this->dao->select("effort.account,  (case when left(user.realname,2) = 't_' then 2 else 1 end) personCategory ,user.realname as accountName, (case when zt_project.code like '%_DEP' then '部门报工'
         //$dataList = $this->dao->select("effort.account,  (case when zt_project.code like '%_DEP' then '部门报工'
         $dataList = $this->dao->select("effort.account,  (case when left(user.realname,2) = 't_' then 2 else 1 end) personCategory ,user.realname as accountName, (case when zt_project.code like '%_DEP' then '3'
             when (zt_project.code like '%_二线' OR zt_project.code like '%_EX') then '2'
             else '1' end) effortType ,effort.deptID as departmentIdPerSon,if(zt_dept.parent !=0,zt_dept.parent,effort.deptID) as departmentUpIdPerson,
             (case when LEFT(zt_task.name,4) = '部门工单' then '4'
             when LEFT(zt_task.name,4) = '二线工单' then '5'
             when LEFT(zt_task.name,3) = '问题池' then '6'
             when LEFT(zt_task.name,4) = '外部需求' then '7'
             when LEFT(zt_task.name,4) = '内部需求' then '8'
             when zt_project.code like '%_DEP' then '4'
             when (zt_project.code like '%_二线' OR zt_project.code like '%_EX') then '5'
             else '9' end) taskSource,effort.consumed,effort.date as effortDate,effort.project,
             (case when zt_plan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_plan.id)  ) else zt_plan.bearDept end )departmentIdProject,
             if((select zt_dept.parent from zt_dept where zt_dept.id =(case when zt_plan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_plan.id)  ) else zt_plan.bearDept end )) != 0,
                (select zt_dept.parent from zt_dept where zt_dept.id =(case when zt_plan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_plan.id)  ) else zt_plan.bearDept end )
             ),
                (case when zt_plan.bearDept like '%,%' then ( select zt_user.dept from zt_user where zt_user.account = (select zp.pm from zt_projectcreation zp where zp.plan = zt_plan.id)  ) else zt_plan.bearDept end )
             ) as departmentUpIdProject"
        )
            ->from(TABLE_EFFORT)->alias('effort')
            ->leftJoin(TABLE_USER)->alias('user')->on('effort.account = user.account')
            ->leftJoin(TABLE_PROJECT)->alias('zt_project')->on('effort.project = zt_project.id')
            ->leftJoin(TABLE_TASK)->alias('zt_task')->on('effort.objectID = zt_task.id')
            ->leftJoin(TABLE_DEPT)->alias('zt_dept')->on('effort.deptID = zt_dept.id')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('zt_plan')->on('effort.project = zt_plan.project')
            ->where('effort.objectType')->eq('task')
            ->andWhere('effort.deleted')->eq(0)
            ->andWhere('year(effort.date)')->eq("$year")
            ->andWhere('month(effort.date)')->ne("0")
            ->andWhere('day(effort.date)')->ne("0")
            ->fetchAll();

        /*$dept = $this->dao->select('id,parent')->from(TABLE_DEPT)->fetchPairs('id','parent');
        $user = $this->dao->select('account,realname')->from(TABLE_USER)->fetchPairs('account','realname');
        foreach ($dataList as $key=> $item) {
            if(!$item) continue;

            $dataList[$key]->departmentUpIdProject   = !empty($dept[$item->departmentIdProject]) ? $dept[$item->departmentIdProject] :$item->departmentIdProject;
            $dataList[$key]->personCategory          =  substr($user[$item->account],0,2) == 't_' ? 2 :1;
            $dataList[$key]->accountName             =  $user[$item->account];

        }*/
        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList,0,200);
    }

    /**
     * 校验
     * @return array
     */
    public function checkReviewInput()
    {
        $errMsg = [];
        if(!isset($_POST['closeBeginTime'])){
            $errMsg[] = "缺少『closeBeginTime』参数";
        }
        if(!isset($_POST['closeEndTime'])){
            $errMsg[] = "缺少『closeEndTime』参数";
        }
        foreach ($_POST as $key => $v)
        {
            if(!isset($_POST['closeBeginTime']) && !isset($_POST['closeEndTime'])){
                $errMsg[] = $key."不是协议字段";
            }
        }

        if( isset($_POST['closeBeginTime']) && !$_POST['closeBeginTime']){
            $errMsg[] = '『关闭时间的开始时间』不能为空';
        }
        if( isset($_POST['closeEndTime']) && !$_POST['closeEndTime']){
            $errMsg[] = '『关闭时间的结束时间』不能为空';
        }
        if(isset($_POST['closeBeginTime']) && $_POST['closeBeginTime']){
            $is_date = date('Y-m-d H:i:s',strtotime($_POST['closeBeginTime'])) == $_POST['closeBeginTime'] ? $_POST['closeBeginTime'] :false;
            if( $is_date === false){
                $errMsg[] = '关闭时间的开始时间格式非法!';
            }
        }
        if(isset($_POST['closeEndTime']) && $_POST['closeEndTime']){
            $is_date = date('Y-m-d H:i:s',strtotime($_POST['closeEndTime'])) == $_POST['closeEndTime'] ? $_POST['closeEndTime'] :false;
            if( $is_date === false){
                $errMsg[] = '关闭时间的结束时间格式非法!';
            }
        }
        if(isset($_POST['closeEndTime']) && $_POST['closeEndTime'] && isset($_POST['closeBeginTime']) && $_POST['closeBeginTime']){

            if( strtotime($_POST['closeEndTime']) < strtotime($_POST['closeBeginTime'])){
                $errMsg[] = '关闭时间的结束时间不能早于关闭时间的开始时间!';
            }
        }
        return $errMsg;
    }
    /**
     * 项目评审明细
     */
    public function syncGetReviewDetail(){
        // 接口token校验
        $this->checkApiToken();
        $errMsg = $this->checkReviewInput();
        if (!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', $errMsg, [], 0, self::FAIL_CODE);
        }
        $closeBeginTime = $_POST['closeBeginTime'];
        $closeEndTime = $_POST['closeEndTime'];
        $dataList = $this->dao->select("review.id as id,review.id as dpmpReviewId,review.project as projectId,review.title as name,review.status,review.type as type,review.createdDate as createTime,review.closeTime as closeTime, CONCAT('/review-view-',review.id,'.html') AS dpmpDetailUrl,
             ifnull((select sum(if(zr.status = 'closed',1,0)) from zt_reviewissue zr  where zr.review  = review.id and zr.deleted = '0'),0) as qusAdoptedNum,
             ifnull((select sum(if(zr.status = 'nadopt',1,0)) from zt_reviewissue zr  where zr.review  = review.id and zr.deleted = '0'),0) as qusNotAdoptedNum,
             ifnull((select sum(if(zr.status in ('nvalidation','repeat'),1,0)) from zt_reviewissue zr  where zr.review  = review.id and zr.deleted = '0'),0) as qusNoModNum
             ")
            ->from(TABLE_REVIEW)->alias('review')
            ->where('review.deleted')->eq(0)
            ->andWhere('review.closeTime')->ge($closeBeginTime)
            ->andWhere('review.closeTime')->le($closeEndTime)
           // ->andWhere('id')->eq(435)
            ->fetchAll();

        //获取操作记录中 挂起时间和恢复时间的集合
        $reviewAction = $this->dao->select("objectID, GROUP_CONCAT(CASE WHEN action= 'suspend' THEN date END ORDER BY date ASC) AS suspendTimes,GROUP_CONCAT(CASE WHEN action= 'renew' THEN date END ORDER BY date ASC) AS renewTimes")
            ->from(TABLE_ACTION)->where('objectType')->eq('review')->andWhere('action')->in('renew,suspend')
            ->groupBy('objectID')
            ->fetchAll('objectID');

        //获取流程中 待提交-待预审 最晚的时间
        $reviewConsumedMax = $this->dao->select('objectID, max(createdDate) as date')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('review')
            ->andWhere('`before`')->eq('waitApply')
            ->andWhere('`after`')->eq('waitPreReview')
            ->andWhere('deleted')->eq('0')
            ->groupBy('objectID')
            ->fetchAll('objectID');
        //获取流程中 待指派初审部门- 已指派 最早的时间
        $reviewConsumedMin = $this->dao->select('objectID, min(createdDate) as date')->from(TABLE_CONSUMED)
             ->where('objectType')->eq('review')
             ->andWhere('`before`')->eq('waitFirstAssignDept')
             ->andWhere('`after`')->in ('waitFirstAssignReviewer,waitFormalAssignReviewer,waitOutReview')
             ->andWhere('deleted')->eq('0')
             ->groupBy('objectID')
             ->fetchAll('objectID');

       // a($this->dao->printSQL());
        foreach ($dataList as $key => $item){
            $dataList[$key]->allQusNum = $item->qusAdoptedNum + $item->qusNotAdoptedNum + $item->qusNoModNum; //问题总数
            $dataList[$key]->suspendTimes = isset($reviewAction[$item->id]) ? $reviewAction[$item->id]->suspendTimes :'';//挂起时间
            $dataList[$key]->renewTimes =  isset($reviewAction[$item->id]) ? $reviewAction[$item->id]->renewTimes : ''; //恢复时间
            //提交时间：a、取最后一次待提交到待预审时间，若取不到则取最早一次的待指派初审部门到 已指派的时间；b、如果最后一次待提交到待预审晚于最早一次待指派初审部门到 已指派，则取最早一次待指派初审部门到 已指派的时间
            $reviewConsumedMindate = isset($reviewConsumedMin[$item->id]->date) ? $reviewConsumedMin[$item->id]->date : '0000-00-00 00:00:00';
            if(empty($reviewConsumedMax[$item->id]->date) || strpos($reviewConsumedMax[$item->id]->date,'0000') !== false || strtotime($reviewConsumedMax[$item->id]->date) > strtotime($reviewConsumedMindate)){
                $subitTime = $reviewConsumedMindate;
            }else{
                $subitTime = $reviewConsumedMax[$item->id]->date;
            }
            $dataList[$key]->submitTime = $subitTime; //提交时间

            if(!empty($item->closeTime) && strpos($item->closeTime,"0000-00-00") === false && !empty($dataList[$key]->submitTime) && strpos($dataList[$key]->submitTime,"0000-00-00") === false){
                if (!$this->isWorkDay(substr($dataList[$key]->submitTime,0,10))){
                    $submitTime = substr($dataList[$key]->submitTime,0,10) . ' 00:00:00';

                }else{
                    $submitTime = $item->submitTime;
                }

                if (!$this->isWorkDay(substr($item->closeTime,0,10))){
                    $closeTime = substr($item->closeTime,0,10) . ' 23:59:59';
                }else{
                    $closeTime = $item->closeTime;
                }
                $realSec = $this->getWorkingTimeBetween($submitTime, $closeTime,'sec');
                // 当两个时间都在节假日时会查1s，计算结果为-1
                if($realSec < 0) $realSec = 0;

                //计算sum（'恢复项目评审'时间-‘挂起项目评审’时间-节假日-倒休）
                $renewTimeArray = $array = explode(',', $dataList[$key]->renewTimes);
                $suspendTimeArray = $array = explode(',', $dataList[$key]->suspendTimes);
                $renewTimeDuration = 0;
                foreach ($renewTimeArray as $k => $renewTime){
                    if($renewTime != null && count($suspendTimeArray) > $k && $suspendTimeArray[$k] != null && $renewTime != "0000-00-00 00:00:00" && $suspendTimeArray[$k]!= "0000-00-00 00:00:00" ) {

                        if (!$this->isWorkDay($suspendTimeArray[$k])){
                            $suspendTimeArray[$k] = substr($suspendTimeArray[$k],0,10) . ' 00:00:00';
                        }
                        if (!$this->isWorkDay($renewTime)){
                            $renewTime = substr($renewTime,0,10) . ' 23:59:59';
                        }
                        $renewTimeSec = $this->getWorkingTimeBetween($suspendTimeArray[$k], $renewTime,'sec');

                    }else{
                        $renewTimeSec = 0;
                    }
                    // 当两个时间都在节假日时会查1s，计算结果为-1
                    if($renewTimeSec < 0) $renewTimeSec = 0;
                    $renewTimeDuration = $renewTimeDuration + $renewTimeSec;
                }
                $dataList[$key]->durationSec = round($realSec - $renewTimeDuration,2);
               // $dataList[$key]->durationSec = round((($realSec - $renewTimeDuration)/(24*60*60)),2);

            }else{
                $dataList[$key]->durationSec = '';
            }
            $dataList[$key]->suspendTimes = !empty($dataList[$key]->suspendTimes) ? $dataList[$key]->suspendTimes :'-';//挂起时间
            $dataList[$key]->renewTimes   =  !empty($dataList[$key]->renewTimes) ? $dataList[$key]->renewTimes : '-';
            $dataList[$key]->closeTime    = strpos($dataList[$key]->closeTime ,'0000') === false ? $dataList[$key]->closeTime  : '';
            $dataList[$key]->submitTime   = strpos($dataList[$key]->submitTime,'0000') === false ? $dataList[$key]->submitTime : '';
            unset($item->id);
        }
        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList,0,200);
    }

    /**
     * Project: chengfangjinke
     * Method: getProblem   获取问题接口: 查询获取问题按计划完成率相关信息
     */
    public function getProblem() {
        // token校验
        $this->checkApiToken();
        // 参数校验
        $errMsg = $this->checkInput('start', 'end');
        if(!empty($errMsg)) {
            $this->loadModel('requestlog')->response('fail', implode(',',$errMsg), [], 0, self::PARAMS_ERROR);
        }

        $updateTime = $_POST['start'];
        $createEndDay = $_POST['end'];

        $dataList = $this->dao->select('zp.id as problemId,
                           zp.code as problemNumber,
                           zp.abstract as summary,
                           zp.status as problemStatus,
                           zp.app as sysId,
                           zp.acceptDept as deptId,
                           zp.examinationResult as examineResult,
                           zp.completedPlan as ifFinishOnTime,
                           zp.solvedTime as deliveryDate,
                           zp.PlannedTimeOfChange as plannedSolutionTime,
                           zd.delayResolutionDate as delaySolutionTime,
                           zd.delayStatus as delayStatus,
                           zp.createdBy as creator,
                           zu.realname as creatorName,
                           zp.createTime,
                           zp.updateTime')
            ->from(TABLE_PROBLEM)->alias('zp')
            ->leftJoin(TABLE_DELAY)->alias('zd')->on('zp.id = zd.objectId and zd.objectType = "problem"
	                                    and zd.id = (select MAX(id) from zt_delay where objectId = zp.id group by objectId)')
            ->leftJoin(TABLE_USER)->alias('zu')->on('zp.createdBy = zu.account')
            ->where("zp.updateTime >= '$updateTime'")
            ->beginIF($createEndDay)->andWhere("zp.updateTime <= '$createEndDay'")
            ->fetchAll();

        foreach ($dataList as $data) {
            $data->problemStatus = $this->lang->apimeasure->problemStatusList[$data->problemStatus];
            $data->delayStatus = $this->lang->apimeasure->delayStatusList[$data->delayStatus];
            $data->deliveryDate = $this->replaceIllegalTimeByNULL($data->deliveryDate);
            $data->plannedSolutionTime = $this->replaceIllegalTimeByNULL($data->plannedSolutionTime);
            $data->delaySolutionTime = $this->replaceIllegalTimeByNULL($data->delaySolutionTime);
            $data->createTime = $this->replaceIllegalTimeByNULL($data->createTime);
        }
        if (dao::isError()) {
            $this->loadModel('requestlog')->response('fail', dao::getError(), [], 0, self::FAIL_CODE);
        }
        header('Content-Type: application/json;Language=UTF-8;charset=UTF-8');
        $this->loadModel('requestlog')->response('success', $this->lang->api->successful, $dataList);
    }

    /**
     * Project: chengfangjinke
     * Method: checkInput 度量接口参数校验
     * @param $startDate   REQUIRED
     * @param $endDate     optional
     * @return array
     */
    private function checkInput($startDate, $endDate) {
        $errMsg = [];
        $params = array_keys($_POST);

        if (!empty($this->check($startDate, $params))) $errMsg[] = $this->check($startDate, $params);
        if (in_array($endDate, $params)) {
            if (!empty($this->check($endDate, $params))) $errMsg[] = $this->check($endDate, $params);
        }
        foreach ($params as $param) {
            if($param != $startDate && $param != $endDate){
                $errMsg[] = "『{$param}』不是协议字段";
            }
        }

        if(isset($_POST[$startDate]) && $_POST[$startDate] && isset($_POST[$endDate]) && $_POST[$endDate]){
            if(strtotime($_POST[$endDate]) && strtotime($_POST[$startDate]) && (strtotime($_POST[$endDate]) < strtotime($_POST[$startDate]))){
                $errMsg[] = "『{$endDate}』不能早于『{$startDate}』!";
            }
        }

        return $errMsg;
    }

    private function check($date, $list) {
        if (!in_array($date, $list) || empty($_POST[$date])) {
            $errMsg = "缺少『{$date}』参数";
        }else if (!strtotime($_POST[$date])) {
            $errMsg = "『{$date}』为非法日期格式";
        }
        return $errMsg;
    }

    /**
     * Project: chengfangjinke
     * Method: replaceIllegalTimeByNULL
     * @param $dateTime
     * @return If the parameter is empty or zero, assign a value of null,Otherwise, return the original value
     */
    private function replaceIllegalTimeByNULL($dateTime) {
        if (empty($dateTime) || $dateTime == '0000-00-00 00:00:00'|| $dateTime == '0000-00-00' ) {
            return null;
        }
        return $dateTime;
    }
}
