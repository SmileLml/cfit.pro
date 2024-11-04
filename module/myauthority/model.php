<?php

class myauthorityModel extends model
{

    public function getList($browseType, $orderBy, $pager = null ,$type)
    {
        $data = fixer::input('post')
            ->get();

        $projectOrRepository = isset($data->projectOrRepository) ?  htmlspecialchars(trim($data->projectOrRepository)) : '';
        $role                = isset($data->role) ?  htmlspecialchars(trim($data->role)) : '';
        $permsission         = isset($data->permsission) ?  htmlspecialchars(trim($data->permsission)) : '';
        $permsissionName         = isset($data->permsissionName) ?  htmlspecialchars(trim($data->permsissionName)) : '';
        if( strpos($permsissionName,'只读') !==  false || in_array($permsissionName,array('只读','只','读'))){
            $permsissionName  = array_flip($this->lang->myauthority->svnAuthority )['只读'];
        }
        if( strpos($permsissionName,'读写') !==  false || in_array($permsissionName,array('读写','写'))){
            $permsissionName  = array_flip($this->lang->myauthority->svnAuthority )['读写'];
        }
        if($browseType == 'bySearch')
        {
            $query = '';
            if($type == 'dpmp'){
                if(isset($role) && $role){
                    $query = "tgroup.name like '%$role%'";
                }
            }else{
                if(isset($projectOrRepository) && $projectOrRepository){
                    $query .= $query ? " and projectOrRepository like '%$projectOrRepository%'" : " projectOrRepository like '%$projectOrRepository%'";
                }
                if(isset($role) && $role){
                    $query .= $query ? " and role like '%$role%'" : " role like '%$role%'";
                }
                if(isset($permsission) && $permsission){
                    $query .= $query ? " and permsission like '%$permsission%'" : "  permsission like '%$permsission%'";

                }
                if(isset($permsissionName) && $permsissionName){
                    $query .= $query ? " and permsission like '%$permsissionName%'" : " permsission like '%$permsissionName%'" ;
                }
            }
            $query = !$query && $this->session->authQuery ? $this->session->authQuery : $query;
            $this->session->set('authQuery', $query);
            $data = !isset($data->projectOrRepository) && $this->session->filed ? $this->session->filed : $data;
            $this->session->set('filed', $data);
        }
        if($this->session->authQuery == false) $this->session->set('authQuery', ' 1 = 1 ');
        $authQuery = $this->session->authQuery;

        $authoritys = array();
        //研发过程 需单独查询自有权限
        if($type == 'dpmp'){
            $authoritys = $this->dao->select("tgroup.id,tgroup.name,tgroup.desc")->from(TABLE_GROUP)->alias('tgroup')
                ->leftJoin(TABLE_USERGROUP)->alias('ugroup')
                ->on('tgroup.id = ugroup.group')
                ->where('ugroup.account')->eq($this->app->user->account)
              /*  ->beginIF(isset($role) && $role)->andWhere('tgroup.name')->like("%$role%")->fi()*/
                ->beginIF($browseType == 'bySearch')->andWhere($authQuery)->fi()
                ->orderBy('id_desc')
                ->page($pager)
                ->fetchAll('id');
        }else{
            $authoritys = $this->dao->select("*")->from(TABLE_THIRDPARTY_PRIVILEGE)
                ->where('type')->eq($type)
                ->andWhere('account')->eq($this->app->user->account)
               /* ->beginIF(isset($projectOrRepository) && $projectOrRepository)->andWhere('projectOrRepository')->like("%$projectOrRepository%")->fi()
                ->beginIF(isset($role) && $role)->andWhere('role')->like("%$role%")->fi()
                ->beginIF(isset($permsission) && $permsission)->andWhere('permsission')->like("%$permsission%")->fi()
                ->beginIF(isset($permsissionName) && $permsissionName)->andWhere('permsission')->like("%$permsissionName%")->fi()*/
                ->beginIF($browseType == 'bySearch')->andWhere($authQuery)->fi()
                ->andWhere('deleted')->eq(0)
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }

        return $authoritys ;
    }

    //获取svn权限
    public function getSvnAuthorityModel(){
        $config = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('svnList')->fetchPairs('key');
        $conn = ssh2_connect($config['host'], $config['port']);   //登陆远程服务器

        //用户名密码验证
        if(!ssh2_auth_password($conn, $config['username'], $config['password'])) {
            $this->saveLog('sftp auth error!', 'getSVN');
            return false;
        }
        $command = 'cat '.$config['shellfiledirectory'];
        $stream = ssh2_exec($conn, $command);
        stream_set_blocking($stream, true);
        $output = stream_get_contents($stream);
        $this->saveLog($output, 'getSVN');
        $paseResult = $this->parseSvnAuthority($output);
        $this->arrangeSvnData($paseResult[0], $paseResult[1]);
        $this->saveLog('getSVN success!', 'getSVN');
    }

    //解析svn权限
    public function parseSvnAuthority($svnString){
        $lines = explode("\n",$svnString);
        $currentSection = null;
        $groupInfo = array();
        $repoInfo = array();
        $svnUrlList = array();
        foreach ($lines as $line){
            if(!empty($line)){
                $line = trim($line);
                if(preg_match('/^\[([^\]]+)\]$/', $line, $matches)){
                    $currentSection = $matches[1];
                    continue ;
                }
                if($currentSection === 'groups' && preg_match('/^([^=]+)=([^=]+)$/', $line, $matches)){
                    $groupName = trim($matches[1]);
                    $groupMembers = explode(',', $matches[2]);
                    $groupInfo[$groupName] = $groupMembers;
                    continue;
                }
                if($currentSection !== 'groups' && preg_match('/^([^=]+)=([^=]+)$/', $line, $matches)){
                    $userNameOrGroup = trim($matches[1]);
                    $permissions = $matches[2];
                    $repoInfo[$currentSection][$userNameOrGroup] = $permissions;
                    array_push($svnUrlList, $currentSection);
                    continue;
                }
            }
        }
        $nowDate = helper::now();
        $this->dao->begin();
        foreach ($svnUrlList as $svnUrl){
            $this->insertOrUpdateUrlData($svnUrl, $nowDate, 'svn');
        }
        //将没有修改的数据都标识为删除
        $this->dao->delete()->from(TABLE_THIRDPARTY_PRIVILEGE_URL)
            ->where('type')->eq('svn')
            ->andWhere('updatetime')->lt($nowDate)
            ->exec();
        $this->dao->commit();

        return [$groupInfo,$repoInfo];
    }

    //转变svn数据
    public function arrangeSvnData($groupInfo, $repoInfo){
        $nowDate = helper::now();
        $this->dao->begin();
        foreach ($repoInfo as $key => $repo){
            foreach ($repo as $userNameOrGroup => $permissions){
                if($userNameOrGroup[0] === '@'){
                    $groupName = substr($userNameOrGroup, 1);
                    if(isset($groupInfo[$groupName])){
                        $memebers = $groupInfo[$groupName];
                        foreach ($memebers as $account){
                            $account = trim($account);
                            $user = $this->loadModel('user')->getById($account);
                            if(!empty($user)){
                                $deptId = $user->dept;
                                $this->insertOrUpdateData($account, $deptId, $key, $permissions, $groupName, $nowDate,'svn');
                            }
                        }
                    }
                }elseif($userNameOrGroup === '*'){
                    $ret = $this->dao->select('account, dept')->from(TABLE_USER)->where('deleted')->eq('0')->fetchAll();
                    foreach ($ret as $user){
                        $this->insertOrUpdateData($user->account, $user->dept, $key, $permissions, '-', $nowDate,'svn');
                    }
                }else{
                    $user = $this->loadModel('user')->getById($userNameOrGroup);
                    if(!empty($user)){
                        $deptId = $user->dept;
                        $this->insertOrUpdateData($userNameOrGroup, $deptId, $key, $permissions, '-', $nowDate,'svn');
                    }
                }
            }
        }
        //将没有修改的数据都标识为删除
        $this->dao->delete()->from(TABLE_THIRDPARTY_PRIVILEGE)
            ->where('type')->eq('svn')
            ->andWhere('updatetime')->lt($nowDate)
            ->exec();
        $this->dao->commit();
    }

    //保存权限路径数据
    public function insertOrUpdateUrlData($projectOrRepository, $nowDate, $type){
        //先查找是否存在此数据
        if($type == 'svn'){
            $svnAuth = array();
            $svnAuth['r'] = '可读';
            $svnAuth['w'] = '可写';
            $permsission= json_encode($svnAuth);
        }elseif($type == 'jenkins'){
            $permsission = json_encode($this->lang->myauthority->jenkinsAuthority);
        }elseif($type == 'gitlab'){
            $permsission = json_encode($this->lang->myauthority->gitlabAuthority);
        }
        $existDate = $this->dao->select('*')->from(TABLE_THIRDPARTY_PRIVILEGE_URL)
            ->where('`projectOrRepository`')->eq($projectOrRepository)
            ->andWhere('`type`')->eq($type)
            ->andWhere('`deleted`')->eq('0')
            ->limit(1)->fetch();
        if(!empty($existDate)){
            //更新编辑时间
            $this->dao->update(TABLE_THIRDPARTY_PRIVILEGE_URL)->set('permsission')->eq($permsission)->set('updatetime')->eq($nowDate)->where('id')->eq($existDate->id)->exec();
        }else{
            $insertDate = array();
            $insertDate['type'] = $type;
            $insertDate['projectOrRepository'] = $projectOrRepository;
            $insertDate['createtime'] = $nowDate;
            $insertDate['updatetime'] = $nowDate;
            $insertDate['permsission'] = $permsission;
            $this->dao->insert(TABLE_THIRDPARTY_PRIVILEGE_URL)->data($insertDate)->exec();
        }
    }

    //保存用户权限数据
    public function insertOrUpdateData($account, $deptId, $projectOrRepository, $permsission, $role, $nowDate, $type, $expires=''){
        //先查找是否存在此数据
        $permsission = trim($permsission);
        $existDate = $this->dao->select('*')->from(TABLE_THIRDPARTY_PRIVILEGE)
            ->where('`projectOrRepository`')->eq($projectOrRepository)
            ->andWhere('`account`')->eq($account)
            ->andWhere('`deptId`')->eq($deptId)
            ->andWhere('`type`')->eq($type)
            ->andWhere('`permsission`')->eq($permsission)
            ->andWhere('`role`')->eq($role)
            ->andWhere('`deleted`')->eq('0')
            ->beginIF(!empty($expires))->andWhere('`expires`')->eq($expires)->fi()
            ->limit(1)->fetch();
        if(!empty($existDate)){
            //更新编辑时间
            $this->dao->update(TABLE_THIRDPARTY_PRIVILEGE)->set('updatetime')->eq($nowDate)->where('id')->eq($existDate->id)->exec();
        }else{
            $insertDate = array();
            $insertDate['deptId'] = $deptId;
            $insertDate['account'] = $account;
            $insertDate['type'] = $type;
            $insertDate['role'] = !empty($role)? $role : '-';
            $insertDate['roleDesc'] = !empty($role)? $role : '-';
            $insertDate['permsission'] = $permsission;
            $insertDate['projectOrRepository'] = $projectOrRepository;
            $insertDate['createtime'] = $nowDate;
            $insertDate['updatetime'] = $nowDate;
            if(!empty($expires)){
                $insertDate['expires'] = $expires;
            }
            $this->dao->insert(TABLE_THIRDPARTY_PRIVILEGE)->data($insertDate)->exec();
        }
    }

    //获取jenkins的权限
    public function getJenkinsAuthorityModel(){
        $config = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('jenkinsList')->fetchPairs('key');
        $nowDate = helper::now();
        $this->dao->begin();
        foreach ($config as $value){
            $value = str_replace("&amp;", "&", $value);
            $url_parts = parse_url($value);
            $ip = $url_parts['host'];
            $port = $url_parts['port'];
            $queryStr = $url_parts['query'];
            parse_str($queryStr, $query);
            $username = $query['username'];
            $pwd = $query['password'];
            $headers = array('Authorization:Basic '.base64_encode("$username:$pwd") );
            $jobArray = array();
            $jobArray = $this->getAllJob($ip, $port, $headers,'',$jobArray);
            $userJobArray = $this->getAllJobUser($ip, $port, $headers,$jobArray);
            foreach ($userJobArray as $userJob){
                $user = $this->loadModel('user')->getById($userJob['account']);
                if(!empty($user)){
                    $deptId = $user->dept;
                    $userJob['authorize'] = '';
                    if(!empty($userJob['credit'])){
                        $userJob['authorize'] = '凭据【'.$userJob['credit'].'】';
                    }
                    if(!empty($userJob['jobauthorize'])){
                        $userJob['authorize'] = empty($userJob['authorize'])?'Job【'.$userJob['jobauthorize'].'】':$userJob['authorize'].','.'Job【'.$userJob['jobauthorize'].'】';
                    }
                    if(!empty($userJob['run'])){
                        $userJob['authorize'] = empty($userJob['authorize'])?'Run【'.$userJob['run'].'】':$userJob['authorize'].','.'Run【'.$userJob['run'].'】';
                    }
                    $this->insertOrUpdateData($userJob['account'], $deptId, $ip.":".$userJob['job'], $userJob['authorize'], '', $nowDate,'jenkins');
                }
            }

            foreach ($jobArray as $item) {
                $this->insertOrUpdateUrlData($ip.":".$item, $nowDate, 'jenkins');
            }
        }
        //将没有修改的数据都标识为删除
        $this->dao->delete()->from(TABLE_THIRDPARTY_PRIVILEGE)
            ->where('type')->eq('jenkins')
            ->andWhere('updatetime')->lt($nowDate)
            ->exec();
        //将没有修改的数据都标识为删除
        $this->dao->delete()->from(TABLE_THIRDPARTY_PRIVILEGE_URL)
            ->where('type')->eq('jenkins')
            ->andWhere('updatetime')->lt($nowDate)
            ->exec();
        $this->dao->commit();
        $this->saveLog('getJenkins success!', 'getJenkins');
    }

    //获取jenkins所有的用户曲线
    public function getAllJobUser($ip,$port, $headers, $jobArray){
        $userJobArray = array();
        foreach ($jobArray as $job){
            $singJobArray = array();
            $prefixArray = explode('/', $job);
            $prefixJob = '';
            foreach ($prefixArray as $item){
                if(!empty($item)){
                    $prefixJob = $prefixJob.'/job/'.$item;
                }
            }
            $getJobUserUrl = 'http://'.$ip.':'.$port.'/'.$prefixJob.'/config.xml';
            $response = $this->loadModel('requestlog')->http($getJobUserUrl, '', $method = 'GET', '', '', $headers);
            $xmlString = simplexml_load_string($response);
            $jsonArray = json_decode(json_encode($xmlString), true);
            $this->saveLog($jsonArray, 'getJenkins');
            if(!empty($jsonArray)){
                $properties = $jsonArray['properties'];
                if(!empty($properties)){
                    if(!empty($properties['com.cloudbees.hudson.plugins.folder.properties.AuthorizationMatrixProperty'])){
                        $properties = $properties['com.cloudbees.hudson.plugins.folder.properties.AuthorizationMatrixProperty'];
                    }else if(!empty($properties['hudson.security.AuthorizationMatrixProperty'])){
                        $properties = $properties['hudson.security.AuthorizationMatrixProperty'];
                    }

                    if(!empty($properties)){
                        $permission = $properties['permission'];
                        if(!empty($permission)){
                            foreach ($permission as $value){
                                $valueArray = explode(':', $value);
                                $account = $valueArray[1];
                                $key = $job.'-'.$account;
                                if(empty($singJobArray[$key])){
                                    $arrayData = array();
                                    $arrayData['account'] = $account;
                                    $arrayData['job'] = $job;
                                    $arrayData['authorize'] = $this->lang->myauthority->jenkinsAuthority[$valueArray[0]];
                                    if(strpos($this->lang->myauthority->jenkinsAuthority[$valueArray[0]], '凭据') !== false){
                                        $creditArray = explode('-', $this->lang->myauthority->jenkinsAuthority[$valueArray[0]]);
                                        $arrayData['credit'] = empty($arrayData['credit'])?$creditArray[1]:$arrayData['credit'].','.$creditArray[1];
                                    }else if(strpos($this->lang->myauthority->jenkinsAuthority[$valueArray[0]], 'Job') !== false){
                                        $jobArray = explode('-', $this->lang->myauthority->jenkinsAuthority[$valueArray[0]]);
                                        $arrayData['jobauthorize'] = empty($arrayData['jobauthorize'])?$jobArray[1]:$arrayData['jobauthorize'].','.$jobArray[1];
                                    }else if(strpos($this->lang->myauthority->jenkinsAuthority[$valueArray[0]], 'Run') !== false){
                                        $runArray = explode('-', $this->lang->myauthority->jenkinsAuthority[$valueArray[0]]);
                                        $arrayData['run'] = empty($arrayData['run'])?$runArray[1]:$arrayData['run'].','.$runArray[1];
                                    }
                                    $singJobArray[$key] = $arrayData;
                                }else{
                                    $arrayData = $singJobArray[$key];
                                    $arrayData['account'] = $account;
                                    $arrayData['job'] = $job;
                                    $arrayData['authorize'] = $arrayData['authorize'].','.$this->lang->myauthority->jenkinsAuthority[$valueArray[0]];
                                    if(strpos($this->lang->myauthority->jenkinsAuthority[$valueArray[0]], '凭据') !== false){
                                        $creditArray = explode('-', $this->lang->myauthority->jenkinsAuthority[$valueArray[0]]);
                                        $arrayData['credit'] = empty($arrayData['credit'])?$creditArray[1]:$arrayData['credit'].','.$creditArray[1];
                                    }else if(strpos($this->lang->myauthority->jenkinsAuthority[$valueArray[0]], 'Job') !== false){
                                        $jobArray = explode('-', $this->lang->myauthority->jenkinsAuthority[$valueArray[0]]);
                                        $arrayData['jobauthorize'] = empty($arrayData['jobauthorize'])?$jobArray[1]:$arrayData['jobauthorize'].','.$jobArray[1];
                                    }else if(strpos($this->lang->myauthority->jenkinsAuthority[$valueArray[0]], 'Run') !== false){
                                        $runArray = explode('-', $this->lang->myauthority->jenkinsAuthority[$valueArray[0]]);
                                        $arrayData['run'] = empty($arrayData['run'])?$runArray[1]:$arrayData['run'].','.$runArray[1];
                                    }
                                    $singJobArray[$key] = $arrayData;
                                }
                            }
                        }
                    }
                }
            }
            $userJobArray = array_merge($userJobArray, $singJobArray);
        }
        return $userJobArray;
    }

    //获取jenkins所有的job
    public function getAllJob($ip,$port, $headers, $prefix, $jobArray){
        if(empty($prefix)){
            $getJobUrl = 'http://'.$ip.':'.$port.'/api/json?pretty=true';
        }else{
            $prefixArray = explode('/', $prefix);
            $prefixJob = '';
            foreach ($prefixArray as $item){
                if(!empty($item)){
                    $prefixJob = $prefixJob.'job/'.$item;
                }
            }
            $getJobUrl = 'http://'.$ip.':'.$port.'/'.$prefixJob.'/api/json?pretty=true';
        }
        $response = $this->loadModel('requestlog')->http($getJobUrl, '', $method = 'GET', '', '', $headers);
        $response = json_decode($response,true);
        $this->saveLog($response, 'getJenkins');
        if(!empty($response['jobs'])){
            foreach ($response['jobs'] as $job){
                if($job['_class'] == 'com.cloudbees.hudson.plugins.folder.Folder' || $job['_class'] == 'org.jenkinsci.plugins.workflow.multibranch.WorkflowMultiBranchProject'){
                    array_push($jobArray, $prefix.'/'.$job['name']);
                    $jobArray = $this->getAllJob($ip,$port, $headers, $prefix.'/'.$job['name'], $jobArray);
                }else{
                    array_push($jobArray, $prefix.'/'.$job['name']);
                }
            }
        }
        return $jobArray;
    }

    //获取gitlab权限
    public function getGitlabAuthorityModel(){
        $this->app->loadLang('authorityapply');
        $gitlabUserAuthorityArray = array();
        $nowDate = helper::now();
        $config = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('gitlabList')->fetchPairs('key');
        $pwd = $config['password'];
        $ip = $config['host'];
        $port =$config['port'];
        $headers = array('PRIVATE-TOKEN:'.$pwd);
        $jobArray = array();

        $jobUrl = 'http://'.$ip.':'.$port.'/api/v4/projects';

        $jobUrlArray = array();
        $jobMap = array();
        $pageNum = 1;
        while($pageNum<200){
            $newUrl = $jobUrl."?per_page=100&page=".$pageNum;
            $jobArrayObj = $this->loadModel('requestlog')->http($newUrl, '', $method = 'GET', '', '', $headers);
            $jobArrayArray = json_decode($jobArrayObj, true);
            if(count($jobArrayArray) == 0){
                a("执行终止".$pageNum);
                break;
            }
            foreach ($jobArrayArray as $item) {
                $jobdata = array();
                $jobdata['name_with_namespace'] = $item['name_with_namespace'];
                $jobdata['id'] = $item['id'];
                $id = $item['id'];
                array_push($jobUrlArray, $jobdata);
                $jobMap[$id] = str_replace(" ", "", $item['name_with_namespace']);
            }
            a("执行".$pageNum);
            $pageNum = $pageNum+1;
        }
        $this->dao->begin();
        foreach ($jobUrlArray as $item) {
            $this->insertOrUpdateUrlData(str_replace(" ", "", $item['name_with_namespace']), $nowDate, 'gitlab');
        }

        //将没有修改的数据都标识为删除
        $this->dao->delete()->from(TABLE_THIRDPARTY_PRIVILEGE_URL)
            ->where('type')->eq('gitlab')
            ->andWhere('updatetime')->lt($nowDate)
            ->exec();
        $this->dao->commit();

        if(!empty($jobMap)){

            foreach ($jobMap as $projectId=>$job){
                $pageNum = 1;
                while($pageNum<200){
                    $authorityUrl = 'http://'.$ip.':'.$port."/api/v4/projects/".$projectId."/members?per_page=100&page=".$pageNum;
                    $responseAuthority = $this->loadModel('requestlog')->http($authorityUrl, '', $method = 'GET', '', '', $headers);
                    $userAuth = json_decode($responseAuthority, true);
                    if(count($userAuth) == 0){
                        a("用户执行终止".$pageNum);
                        break;
                    }
                    if(!empty($userAuth)){
                        foreach ($userAuth as $value){
                            $authData = array();
                            $account = $value['username'];
                            $authData['account'] = $account;
                            $authData['job'] = $job;
                            $authData['authorize'] = $this->lang->authorityapply->gitLabPermission[$value['access_level']];
                            $authData['expires'] = empty($value['expires_at'])?'':$value['expires_at'];
                            array_push($gitlabUserAuthorityArray, $authData);
                        }
                    }
                    a("用户执行".$pageNum);
                    $pageNum = $pageNum+1;
                }
            }
        }

        $this->dao->begin();
        foreach ($gitlabUserAuthorityArray as $gitlabUserAuthority){
            $user = $this->loadModel('user')->getById($gitlabUserAuthority['account']);
            if(!empty($user)){
                $deptId = $user->dept;
                $this->insertOrUpdateData($gitlabUserAuthority['account'], $deptId, $gitlabUserAuthority['job'], $gitlabUserAuthority['authorize'], '', $nowDate,'gitlab',$gitlabUserAuthority['expires']);
            }
        }
        //将没有修改的数据都标识为删除
        $this->dao->delete()->from(TABLE_THIRDPARTY_PRIVILEGE)
            ->where('type')->eq('gitlab')
            ->andWhere('updatetime')->lt($nowDate)
            ->exec();
        $this->dao->commit();
        $this->saveLog('getGitlab success!', 'getGitlab');


    }

    //查询权限路径
    public function getAuthorizeUrl($type){
        $dataList = $this->dao->select('*')->from(TABLE_THIRDPARTY_PRIVILEGE_URL)
            ->where('type')->eq($type)
            ->andWhere('deleted')->eq('0')->orderBy('projectOrRepository asc')->fetchAll();
        foreach ($dataList as &$data){
            $data->name = $data->projectOrRepository;
            $data->value = $data->id;
        }
        return $dataList;
        /*if($type == 'gitlab'){
            foreach ($dataList as &$data){
                $data->name = $data->projectOrRepository;
                $data->value = $data->id;
            }
            return $dataList;
        }else if($type == 'svn'){
            $result =  $this->buileTree($dataList);
            return $result[0]->children;
        }else if($type == 'jenkins'){
            $config = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('jenkinsList')->fetchPairs('key');
            $ipArray = array();
            foreach ($config as $value){
                if(!empty($value)){
                    $value = str_replace("&amp;", "&", $value);
                    $url_parts = parse_url($value);
                    $ip = $url_parts['host'];
                    array_push($ipArray, $ip);
                }
            }
            $dataIpList = array();
            foreach ($ipArray as $ip) {
                $dataArray = array();
                foreach ($dataList as $data){
                    if(strpos($data->projectOrRepository, $ip) !== false){
                        array_push($dataArray, $data);
                    }
                }
                $dataIpList[$ip] = $dataArray;
            }
            $returnResult = array();
            foreach ($dataIpList as $ip=>$dataArray) {
                $returnResult = array_merge($returnResult,$this->buileJenkinsTree($dataArray, $ip));
            }
            return $returnResult;
        }*/
    }

    //构建树结构
    public function buileTree($list){
        usort($list, function ($a, $b){
            return strcasecmp($a->projectOrRepository , $b->projectOrRepository);
        });
        $data = [];
        $id = 0;
        foreach ($list as $item){
            $tmps = explode('/', $item->projectOrRepository);
            $parent = '';
            $tmps_count = 0;
            foreach($tmps as $v){
                if($parent == ''){
                    if(empty($data)){
                        $parentID = 0;
                    }else{
                        $parentID = 1;
                    }
                }else if(!empty($data[$parent])){
                    $parentID = $data[$parent]->code;
                }
                if(!empty($v)){
                    $parent .= $v.'/';
                }
                $tmps_count++;
                if(!isset($data[$parent]) && $tmps_count == count($tmps)){
                    $id++;
                    $item->code = $id;
                    $item->parentCode = $parentID;
                    $item->name = $item->projectOrRepository;
                    $item->value = $item->id;
                    $data[$parent] = $item;
                }
            }
        }
        $newData = array();
        foreach ($data as $item){
            $newData[$item->projectOrRepository] = $item;
        }
        $tree = $this->arraysToTree($newData);
        while(!empty($newData)){
            $i = 0;
            foreach ($newData as $key => $value){
                if($i == 0){
                    $parentCode = $value->parentCode;
                }
                $tree1 = $this->arraysToTree($newData, $parentCode);
                $tree = array_merge($tree,$tree1);
                $i++;
            }
        }
        return $tree;
    }

    public function buileJenkinsTree($list, $ip){
        usort($list, function ($a, $b){
            return strcasecmp($a->projectOrRepository , $b->projectOrRepository);
        });
        $data = [];
        $id = 1;
        $ipItem = new stdClass();
        $ipItem->code = 1;
        $ipItem->parentCode = 0;
        $ipItem->name = $ip;
        $ipItem->projectOrRepository = $ip;
        $ipItem->value = 0;
        $data[$ip] = $ipItem;

        foreach ($list as $item){
            $tmps = explode('/', $item->projectOrRepository);
            $parent = $ip;
            $tmps_count = 0;
            foreach($tmps as $v){
                if($parent == ''){
                    if(empty($data)){
                        $parentID = 0;
                    }else{
                        $parentID = 1;
                    }
                }else if(!empty($data[$parent])){
                    $parentID = $data[$parent]->code;
                }
                if(!empty($v)){
                    $parent .= $v.'/';
                }
                $tmps_count++;
                if(!isset($data[$parent]) && $tmps_count == count($tmps)){
                    $id++;
                    $item->code = $id;
                    $item->parentCode = $parentID;
                    $item->name = $item->projectOrRepository;
                    $item->value = $item->id;
                    $data[$parent] = $item;
                }
            }
        }
        $newData = array();
        foreach ($data as $item){
            $newData[$item->projectOrRepository] = $item;
        }
        $tree = $this->arraysToTree($newData);
        while(!empty($newData)){
            $i = 0;
            foreach ($newData as $key => $value){
                if($i == 0){
                    $parentCode = $value->parentCode;
                }
                $tree1 = $this->arraysToTree($newData, $parentCode);
                $tree = array_merge($tree,$tree1);
                $i++;
            }
        }
        return $tree;
    }

    public function arraysToTree(&$data, $parentCode = 0){
        $tree = array();
        foreach ($data as $key => $value){
            if($value->parentCode == $parentCode){
                unset($data[$key]);
                $children = $this->arraysToTree($data, $value->code);
                if(!empty($children)){
                    $value->children = $children;
                }
                $tree[] = $value;
            }
        }
        return $tree;
    }

    /**
     * 记录日志
     * @param $line
     */
    function saveLog($line, $model = 'getSVN', $func = 'run'){
        if(is_array($line) || is_object($line))
        {
            $line = json_encode($line, JSON_UNESCAPED_UNICODE);
        }
        $line = '['.date('H:i:s').']-'. $model .'-'. $func . ':: '.$line .PHP_EOL;
        $logPath = $_SERVER['DOCUMENT_ROOT'].'/data/authoritylog/'.date('Ym').'/';
        if(!is_dir($logPath)) mkdir($logPath, 0777, true);
        $logFile = $logPath.'authorityapi-'.date('Y-m-d').'-'.$model.'-'.$func.'.log';
        file_put_contents($logFile, $line, FILE_APPEND);
    }

}

