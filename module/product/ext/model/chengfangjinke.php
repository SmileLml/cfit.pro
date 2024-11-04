<?php
/**
 * Project: chengfangjinke
 * Method: getLinePairs
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:56
 * Desc: This is the code comment. This method is called getLinePairs.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $programID
 * @return mixed
 */
public function getLinePairs($programID = 0)
{
    return $this->loadModel('productline')->getPairs();
}

/**
 * Project: chengfangjinke
 * Method: getStats
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:56
 * Desc: This is the code comment. This method is called getStats.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param string $orderBy
 * @param null $pager
 * @param string $status
 * @param int $line
 * @param string $storyType
 * @param int $programID
 * @return array
 */
public function getStats($orderBy = 'order_desc', $pager = null, $status = 'noclosed', $line = 0, $storyType = 'story', $programID = 0)
{
    $this->loadModel('report');
    $this->loadModel('story');
    $this->loadModel('bug');

    $products = $this->getList($programID, $status, $limit = 0, $line);
    if(empty($products)) return array();

    $productKeys = array_keys($products);
    if($orderBy == 'name_asc')
    {
        $products = $this->dao->query('SELECT t1.*,t2.name as lineName,t2.code lineCode FROM ' . TABLE_PRODUCT . ' t1 LEFT JOIN ' . TABLE_PRODUCTLINE . ' t2 ON t1.line = t2.id WHERE t1.id IN (' . implode(',', $productKeys) . ') ORDER BY t2.code, CONVERT(t1.name USING gbk) ASC')->fetchAll();
    }
    else if($orderBy == 'program_asc')
    {
        $products = $this->dao->select('t1.id as id, t1.*')->from(TABLE_PRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
            ->where('t1.id')->in($productKeys)
            ->orderBy('t2.order_asc, t1.line_desc, t1.order_desc')
            ->page($pager)
            ->fetchAll('id');
    }
    else
    {
        $products = $this->dao->select('*')->from(TABLE_PRODUCT)
            ->where('id')->in($productKeys)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    $linePairs = $this->getLinePairs();
    foreach($products as $product) $product->lineName = zget($linePairs, $product->line, '');

    $stories = $this->dao->select('product, status, type, count(status) AS count')
            ->from(TABLE_STORY)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('story')
            ->andWhere('product')->in($productKeys)
            ->groupBy('product, status')
            ->fetchGroup('product', 'status');

    $requirements = $this->dao->select('product, status, type, count(status) AS count')
            ->from(TABLE_STORY)
            ->where('deleted')->eq(0)
            ->andWhere('type')->eq('requirement')
            ->andWhere('product')->in($productKeys)
            ->groupBy('product, status')
            ->fetchGroup('product', 'status');

    /* Padding the stories to sure all products have records. */
    $emptyStory = array_keys($this->lang->story->statusList);
    foreach($productKeys as $productID)
    {
        if(!isset($stories[$productID]))      $stories[$productID]      = $emptyStory;
        if(!isset($requirements[$productID])) $requirements[$productID] = $emptyStory;
    }

    /* Padding the stories to sure all status have records. */
    foreach($stories as $key => $story)
    {
        foreach(array_keys($this->lang->story->statusList) as $status)
        {
            $story[$status] = isset($story[$status]) ? $story[$status]->count : 0;
        }
        $stories[$key] = $story;
    }
    foreach($requirements as $key => $requirement)
    {
        foreach(array_keys($this->lang->story->statusList) as $status)
        {
            $requirement[$status] = isset($requirement[$status]) ? $requirement[$status]->count : 0;
        }
        $requirements[$key] = $requirement;
    }

    if($storyType == 'requirement') $stories = $requirements;

    $plans = $this->dao->select('product, count(*) AS count')
            ->from(TABLE_PRODUCTPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('product')->in($productKeys)
            ->andWhere('end')->gt(helper::now())
            ->groupBy('product')
            ->fetchPairs();

    $releases = $this->dao->select('product, count(*) AS count')
            ->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('product')->in($productKeys)
            ->groupBy('product')
            ->fetchPairs();

    $bugs = $this->dao->select('product,count(*) AS conut')
            ->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andWhere('product')->in($productKeys)
            ->groupBy('product')
            ->fetchPairs();

    $unResolved = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andwhere('status')->eq('active')
            ->andWhere('product')->in($productKeys)
            ->groupBy('product')
            ->fetchPairs();

    $closedBugs = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andwhere('status')->eq('closed')
            ->andWhere('product')->in($productKeys)
            ->groupBy('product')
            ->fetchPairs();

    $assignToNull = $this->dao->select('product,count(*) AS count')
            ->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->andwhere('assignedTo')->eq('')
            ->andWhere('product')->in($productKeys)
            ->groupBy('product')
            ->fetchPairs();

    if(empty($programID))
    {
        $programKeys = array(0=>0);
        foreach($products as $product) $programKeys[] = $product->program;
        $programs = $this->dao->select('id,name')->from(TABLE_PROGRAM)
            ->where('id')->in(array_unique($programKeys))
            ->andWhere('deleted')->eq('0')
            ->fetchPairs();

        foreach($products as $product) $product->programName = isset($programs[$product->program]) ? $programs[$product->program] : '';
    }

    $stats = array();
    foreach($products as $key => $product)
    {
        $product->stories      = $stories[$product->id];
        $product->requirements = $requirements[$product->id];
        $product->plans        = isset($plans[$product->id])    ? $plans[$product->id]    : 0;
        $product->releases     = isset($releases[$product->id]) ? $releases[$product->id] : 0;

        $product->bugs         = isset($bugs[$product->id]) ? $bugs[$product->id] : 0;
        $product->unResolved   = isset($unResolved[$product->id]) ? $unResolved[$product->id] : 0;
        $product->closedBugs   = isset($closedBugs[$product->id]) ? $closedBugs[$product->id] : 0;
        $product->assignToNull = isset($assignToNull[$product->id]) ? $assignToNull[$product->id] : 0;
        $stats[] = $product;
    }

    return $stats;
}

public function getHistoryCodes($productId,$code){
    $historyCodes = $this->dao->select('`product`,`code`,`enableTime`,`desc`')
        ->from(TABLE_PRODUCTCODEINFO)
        ->where('deleted')->eq(0)
        ->andWhere('code')->ne($code)
        ->andWhere('product')->eq($productId)
        //->groupBy('product')
        ->fetchAll();
    $i = 1;
    foreach ($historyCodes as $code){
        $historyCode .=$i.'、'. $code->code.",".$code->enableTime.",".$code->desc.PHP_EOL;
        $i++;
    }

    return $historyCode;

}

/**
 * Get product stats.
 *
 * @param  int    $queryID
 * @param  string $orderBy
 * @param  object $pager
 * @param  string $status
 * @param  int    $line
 * @param  string $storyType requirement|story
 * @param  int    $programID
 * @access public
 * @return array
 */
public function getProductStats($queryID = 0, $orderBy = 'order_desc', $pager = null, $status = 'noclosed', $line = 0, $storyType = 'story', $programID = 0)
{
    $this->loadModel('report');
    $this->loadModel('story');
    $this->loadModel('bug');

    $products = $this->getProductList($queryID, $programID, $status, $limit = 0, $line);
    if(empty($products)) return array();

    $productKeys = array();
    foreach ($products as $product){
        $productKeys[] = $product->id;
    }
    if($status == 'bysearch'){
        if($orderBy == 'program_asc')
        {
            $products = $this->dao->select('t1.id as id, t1.*')->from(TABLE_PRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
                ->where('t1.id')->in($productKeys)
                ->orderBy('t2.order_asc, t1.line_desc, t1.order_desc')
                ->page($pager)
                ->fetchAll('id');
        }
        else
        {
            $products = $this->dao->select('*')->from(TABLE_PRODUCT)
                ->where('id')->in($productKeys)
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }

    }else{
        if($orderBy == 'program_asc')
        {
            $products = $this->dao->select('t1.id as id, t1.*')->from(TABLE_PRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
                ->where('t1.id')->in($productKeys)
                ->orderBy('t2.order_asc, t1.line_desc, t1.order_desc')
                ->page($pager)
                ->fetchAll('id');
        }
        else
        {
            $products = $this->dao->select('*')->from(TABLE_PRODUCT)
                ->where('id')->in($productKeys)
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }
    }
    $this->loadModel('productline');
    $linePairs = $this->productline->getPairsLineAndName();
    foreach($products as $product) $product->lineName = zget($linePairs, $product->line, '');

    $stories = $this->dao->select('product, status, type, count(status) AS count')
        ->from(TABLE_STORY)
        ->where('deleted')->eq(0)
        ->andWhere('type')->eq('story')
        ->andWhere('product')->in($productKeys)
        ->groupBy('product, status')
        ->fetchGroup('product', 'status');

    $requirements = $this->dao->select('product, status, type, count(status) AS count')
        ->from(TABLE_STORY)
        ->where('deleted')->eq(0)
        ->andWhere('type')->eq('requirement')
        ->andWhere('product')->in($productKeys)
        ->groupBy('product, status')
        ->fetchGroup('product', 'status');

    /* Padding the stories to sure all products have records. */
    $emptyStory = array_keys($this->lang->story->statusList);
    foreach($productKeys as $productID)
    {
        if(!isset($stories[$productID]))      $stories[$productID]      = $emptyStory;
        if(!isset($requirements[$productID])) $requirements[$productID] = $emptyStory;
    }

    /* Padding the stories to sure all status have records. */
    foreach($stories as $key => $story)
    {
        foreach(array_keys($this->lang->story->statusList) as $status)
        {
            $story[$status] = isset($story[$status]) ? $story[$status]->count : 0;
        }
        $stories[$key] = $story;
    }
    foreach($requirements as $key => $requirement)
    {
        foreach(array_keys($this->lang->story->statusList) as $status)
        {
            $requirement[$status] = isset($requirement[$status]) ? $requirement[$status]->count : 0;
        }
        $requirements[$key] = $requirement;
    }

    if($storyType == 'requirement') $stories = $requirements;

    $plans = $this->dao->select('product, count(*) AS count')
        ->from(TABLE_PRODUCTPLAN)
        ->where('deleted')->eq(0)
        ->andWhere('product')->in($productKeys)
        ->andWhere('end')->gt(helper::now())
        ->groupBy('product')
        ->fetchPairs();

    $releases = $this->dao->select('product, count(*) AS count')
        ->from(TABLE_RELEASE)
        ->where('deleted')->eq(0)
        ->andWhere('product')->in($productKeys)
        ->groupBy('product')
        ->fetchPairs();

    $bugs = $this->dao->select('product,count(*) AS conut')
        ->from(TABLE_BUG)
        ->where('deleted')->eq(0)
        ->andWhere('product')->in($productKeys)
        ->groupBy('product')
        ->fetchPairs();

    $unResolved = $this->dao->select('product,count(*) AS count')
        ->from(TABLE_BUG)
        ->where('deleted')->eq(0)
        ->andwhere('status')->eq('active')
        ->andWhere('product')->in($productKeys)
        ->groupBy('product')
        ->fetchPairs();

    $closedBugs = $this->dao->select('product,count(*) AS count')
        ->from(TABLE_BUG)
        ->where('deleted')->eq(0)
        ->andwhere('status')->eq('closed')
        ->andWhere('product')->in($productKeys)
        ->groupBy('product')
        ->fetchPairs();

    $assignToNull = $this->dao->select('product,count(*) AS count')
        ->from(TABLE_BUG)
        ->where('deleted')->eq(0)
        ->andwhere('assignedTo')->eq('')
        ->andWhere('product')->in($productKeys)
        ->groupBy('product')
        ->fetchPairs();

    if(empty($programID))
    {
        $programKeys = array(0=>0);
        foreach($products as $product) $programKeys[] = $product->program;
        $programs = $this->dao->select('id,name')->from(TABLE_PROGRAM)
            ->where('id')->in(array_unique($programKeys))
            ->andWhere('deleted')->eq('0')
            ->fetchPairs();

        foreach($products as $product) $product->programName = isset($programs[$product->program]) ? $programs[$product->program] : '';
    }

    $stats = array();
    foreach($products as $key => $product)
    {
        $product->stories      = $stories[$product->id];
        $product->requirements = $requirements[$product->id];
        $product->plans        = isset($plans[$product->id])    ? $plans[$product->id]    : 0;
        $product->releases     = isset($releases[$product->id]) ? $releases[$product->id] : 0;

        $product->bugs         = isset($bugs[$product->id]) ? $bugs[$product->id] : 0;
        $product->unResolved   = isset($unResolved[$product->id]) ? $unResolved[$product->id] : 0;
        $product->closedBugs   = isset($closedBugs[$product->id]) ? $closedBugs[$product->id] : 0;
        $product->assignToNull = isset($assignToNull[$product->id]) ? $assignToNull[$product->id] : 0;
        $stats[] = $product;
    }

    return $stats;
}

/**
 * Project: chengfangjinke
 * Method: getProductList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:56
 * Desc: This is the code comment. This method is called getProductList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $queryID
 * @param int $programID
 * @param string $status
 * @param int $limit
 * @param int $line
 * @return mixed
 */
public function getProductList($queryID = 0, $programID = 0, $status = 'all', $limit = 0, $line = 0)
{
    $secondlineQuery = '';
    if($status == 'bysearch')
    {
        $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
        if($query)
        {
            $this->session->set('secondlineQuery', $query->sql);
            $this->session->set('secondlineForm', $query->form);
        }

        if($this->session->secondlineQuery == false) $this->session->set('secondlineQuery', ' 1 = 1');
    }

    //拼接sql查询产品编号历史记录
    $secondlineQuery = $this->session->secondlineQuery;
    $secondlineQuery = str_replace("`name`","t1.`name`",$secondlineQuery);
    $secondlineQuery = str_replace("`code`","t2.`code`",$secondlineQuery);
    $products = $this->dao->select('t1.*')->from(TABLE_PRODUCT)->alias('t1')
        ->leftJoin(TABLE_PRODUCTCODEINFO)->alias('t2')
        ->on('t1.id = t2.product')
        ->where('t1.deleted')->eq(0)
        ->beginIF($line > 0)->andWhere('t1.line')->eq($line)->fi()
        ->beginIF(!$this->app->user->admin)->andWhere('t1.id')->in($this->app->user->view->products)->fi()
        ->beginIF($status == 'noclosed' and $status != 'bysearch')->andWhere('t1.status')->ne('closed')->fi()
        ->beginIF($status != 'all' and $status != 'noclosed' and $status != 'involved' and $status != 'bysearch' and $status != 'bysearch')->andWhere('t1.status')->in($status)->fi()
        ->beginIF($status == 'involved' and $status != 'bysearch')
        ->andWhere('t1.PO', true)->eq($this->app->user->account)
        ->orWhere('t1.QD')->eq($this->app->user->account)
        ->orWhere('t1.RD')->eq($this->app->user->account)
        ->orWhere('t1.createdBy')->eq($this->app->user->account)
        ->markRight(1)
        ->fi()
        ->beginIF($status == 'bysearch')->andWhere($secondlineQuery)->fi()
        ->orderBy('order_asc, line_desc, order_asc')
        ->beginIF($limit > 0)->limit($limit)->fi()
        ->fetchAll();


    return $products;
}

/**
 * Project: chengfangjinke
 * Method: createFromImport
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:56
 * Desc: This is the code comment. This method is called createFromImport.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @return bool|void
 */
public function createFromImport()
{
    $data  = fixer::input('post')->get();
    $apps  = $data->apps;
    $names = $data->names;
    $lines = $data->lines;
    $codes       = $data->codes;
    $enableTimes = $data->enableTime;
    $comments    = $data->comment;
    $pos   = $data->pos;
    $descs = $data->descs;
    $types = $data->types;
    $acls  = $data->acls;

    //查询已经存在的产品编号
    $existProductCodes = [];
    $productCodeList = $this->dao->select('id,code')->from(TABLE_PRODUCT)->where('code')->in($codes)->fetchPairs();
    if($productCodeList){
        $existProductCodes = array_values($productCodeList);
    }
    $condition = ['code' => $codes];
    $productCodeListTemp = $this->getProductCodeListByCondition($condition, 'code');
    if($productCodeListTemp){
        $existProductCodes = array_merge($existProductCodes, array_column($productCodeListTemp, 'code'));
    }


    foreach($codes as $key => $code)
    {
        if(in_array($code, $existProductCodes))
        {
            $error = sprintf($this->lang->product->importCodeUnique, $key);
            die(js::alert($error));
        }
    }

    $rows = array();
    foreach($names as $key => $name)
    {
        $row       = new stdClass();
        $row->app  = $apps[$key];
        $row->line = $lines[$key];
        $row->name = $name;
        $row->codes = !empty($codes[$key]) ? array($codes[$key]):[];
        $row->enableTime = !empty($enableTimes[$key])? array($enableTimes[$key]):[];
        $row->comment    = !empty($comments[$key])? array($comments[$key]):[];
        $row->PO   = $pos[$key];
        $row->desc = $descs[$key];
        $row->type = $types[$key];
        $row->acl  = $acls[$key];

        if(empty($row->app))
        {
            $error = sprintf($this->lang->product->importAppEmpty, $key);
            die(js::alert($error));
        }

        if(empty($row->name) or !trim($row->name))
        {
            $error = sprintf($this->lang->product->importNameEmpty, $key);
            die(js::alert($error));
        }

        if(empty($row->line))
        {
            $error = sprintf($this->lang->product->importLineEmpty, $key);
            die(js::alert($error));
        }

        if(empty($row->codes))
        {
            $error = sprintf($this->lang->product->importCodeEmpty, $key);
            die(js::alert($error));
        }
        if(empty($row->enableTime))
        {
            $error = sprintf($this->lang->product->importEnableTimeEmpty, $key);
            die(js::alert($error));
        }
        if(empty($row->comment))
        {
            $error = sprintf($this->lang->product->importCommentEmpty, $key);
            die(js::alert($error));
        }

        if(empty($row->type))
        {
            $error = sprintf($this->lang->product->importTypeEmpty, $key);
            die(js::alert($error));
        }

        if(empty($row->acl))
        {
            $error = sprintf($this->lang->product->importAclEmpty, $key);
            die(js::alert($error));
        }

        $rows[$key] = $row;
    }

    unset($_POST);
    $this->loadModel('action');
    foreach($rows as $product)
    {
        unset($_POST);
        $this->post->set('app', $product->app);
        $this->post->set('name', $product->name);
        $this->post->set('line', $product->line);
        $this->post->set('codes', $product->codes);
        $this->post->set('enableTime', $product->enableTime);
        $this->post->set('comment', $product->comment);
        $this->post->set('PO', $product->PO);
        $this->post->set('type', $product->type);
        $this->post->set('desc', $product->desc);
        $this->post->set('acl', $product->acl);
        $this->post->set('status', 'normal');
        $this->post->set('whitelist', array());

        $productID = $this->create();
        $this->action->create('product', $productID, 'opened');
    }
    return true;
}

/**
 * Project: chengfangjinke
 * Method: getCodePairs
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:56
 * Desc: This is the code comment. This method is called getCodePairs.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @return mixed
 */
public function getCodePairs()
{
    return $this->dao->select('id,code')->from(TABLE_PRODUCT)->fetchPairs();
}

/**
 * Project: chengfangjinke
 * Method: getCodeNamePairs
 * User: Gu Chaonan
 * Year: 2022
 * Date: 2021/06/23
 * Time: 14:56
 * Desc: This is the code comment. This method is called getCodePairs.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @return mixed
 */
public function getNamePairs()
{
    return $this->dao->select('id,name')->from(TABLE_PRODUCT)->fetchPairs();
}

/**
 * Project: chengfangjinke
 * Method: getModifyCodePairs
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:56
 * Desc: This is the code comment. This method is called getModifyCodePairs.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $productID
 * @return array
 */
public function getModifyCodePairs($productID = 0)
{
    $codeList = $this->dao->select('*')->from(TABLE_PRODUCTCODE)
        ->beginIF($productID)->where('product')->eq($productID)->fi()
        ->fetchAll();

    // Processing version number.
    $productCodeList = array();
    foreach($codeList as $details)
    {
        $code = json_decode($details->code);
        if(!isset($code->assignProduct) or empty($code->assignProduct)) continue;

        $product = $this->dao->select('id,code,line')->from(TABLE_PRODUCT)->where('id')->eq($code->assignProduct)->fetch();
        $line    = $this->dao->select('id,code')->from(TABLE_PRODUCTLINE)->where('id')->eq($product->line)->fetch();
        $codeTitle = $line->code . '-' . $product->code . '-' . $code->versionNumber . '-for-' . $code->supportPlatform;
        if(trim($code->hardwarePlatform)) $codeTitle .= '-' . $code->hardwarePlatform;
        $productCodeList[$code->assignProduct][] = $codeTitle;
    }

    return $productCodeList;
}

public function getModifyProjectLinkProduct($opinions)
{
    $projectList = array();
    foreach($opinions as $opinionID => $opinion)
    {
        if(isset($opinion->children))
        {
            foreach($opinion->children as $requirement)
            {
                if(empty($requirement->project)) continue;
                $projectList[$requirement->project] = $requirement->project;
            }
        }
    }

    $projectList = $this->dao->select('id,project')->from(TABLE_PROJECTPLAN)->where('id')->in($projectList)->fetchPairs();
    foreach($projectList as $plan => $projectID)
    {
        if(!$projectID) unset($projectList[$plan]);
    }

    $modifyList = $this->dao->select('objectID,relationID')->from(TABLE_SECONDLINE)
        ->where('objectType')->eq('project')
        ->andWhere('objectID')->in($projectList)
        ->andWhere('relationType')->eq('projectModify')
        ->fetchGroup('objectID');

    $projectModifyList = array();
    foreach($projectList as $plan => $projectID)
    {
        if(isset($modifyList[$projectID]))
        {
            foreach($modifyList[$projectID] as $modify)
            {
                $projectModifyList[$plan][] = $modify->relationID;
            }
        }
    }

    $productCodeList = array();
    foreach($projectModifyList as $plan => $modifyList)
    {
        $codeList = $this->dao->select('modify,code')->from(TABLE_PRODUCTCODE)->where('modify')->in($modifyList)->fetchAll();
        foreach($codeList as $details)
        {
            $code = json_decode($details->code);
            if(!isset($code->assignProduct) or empty($code->assignProduct)) continue;

            $product = $this->dao->select('id,code,line')->from(TABLE_PRODUCT)->where('id')->eq($code->assignProduct)->fetch();
            $line    = $this->dao->select('id,code')->from(TABLE_PRODUCTLINE)->where('id')->eq($product->line)->fetch();
            $codeTitle = $line->code . '-' . $product->code . '-' . $code->versionNumber . '-for-' . $code->supportPlatform;
            if(trim($code->hardwarePlatform)) $codeTitle .= '-' . $code->hardwarePlatform;
            $productCodeList[$plan][] = $codeTitle;
        }
    }
    return $productCodeList;
}

/**
* Get product pairs.name=code_name
*
* @param  string $mode
* @param  string $programID
* @param  string $orderBy   program_asc
* @return array
*/
public function getPairsNameLinkCode($mode = '', $programID = 0, $orderBy = '')
{
    if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getProductPairs();

    if($orderBy == 'program_asc')
    {
        $products = $this->dao->select('t1.id as id, t1.name as name, t1.*, IF(INSTR(" closed", t1.status) < 2, 0, 1) AS isClosed,concat(concat(t1.code,"_"),t1.name) as code_name')->from(TABLE_PRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
            ->where('t1.deleted')->eq(0)
            ->beginIF($programID)->andWhere('t1.program')->eq($programID)->fi()
            ->beginIF(strpos($mode, 'noclosed') !== false)->andWhere('t1.status')->ne('closed')->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t1.id')->in($this->app->user->view->products)->fi()
            ->orderBy('t2.order_asc, t1.line_desc, t1.order_desc')
            ->fetchPairs('id', 'code_name');
    }
    else
    {
        $orderBy  = !empty($this->config->product->orderBy) ? $this->config->product->orderBy : 'isClosed';
        $products = $this->dao->select('*,  IF(INSTR(" closed", status) < 2, 0, 1) AS isClosed,concat(concat(code,"_"),name) as code_name')
            ->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
            ->beginIF(strpos($mode, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->products)->fi()
            ->orderBy($orderBy)
            ->fetchPairs('id', 'code_name');
    }
    return $products;
}
