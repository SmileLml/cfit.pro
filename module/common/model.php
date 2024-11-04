<?php
/**
 * The model file of common module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     common
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class commonModel extends model
{
    static public $requestErrors = array();
    /*
     * 不需要权限的方法model-method
     */
    public static $openURIs =
        [
            'projectplan-checktaskdate',
            'release-repush',
            'outsideplan-ajaxSubProjects',
            'outsideplan-ajaxTask',
            'problem-ajaxGetIfUltimateSolutionTd',
            'demand-ajaxGetDemand',
            'demand-ajaxGetSelectRequirement',
            'demand-getRequirementByOpinionID',
            'outwarddelivery-catlog',
            'review-editfiles',
            'file-ajaxwopifiles',
            'file-getfile',
            'doclib-ajaxwopifiles',
            'entry-visit',
            'outwarddelivery-ajaxGetProductenroll',
            'outwarddelivery-ajaxGetTestRequest',
            'outwarddelivery-ajaxGetProduct',
            'outwarddelivery-ajaxGetSecondLine',
            'outwarddelivery-ajaxGetPartitionByCode',
            'user-regularlyimportldap',
            'user-updateldapdaily',
            'user-getldapdeptuser',
            'review-autosendmail',
            'review-autodealreview',
            'review-autodealreviewmain',
            'modify-syncreleaseinfo',
            'modifycncc-syncreleaseinfo',
            'problem-isextended',
            'problem-isbackextended',
            'info-cancellinkage',
            'infoqz-cancellinkage',
            'modify-cancellinkage',
            'outwarddelivery-cancellinkage',
            'componentpublic-demandadvice',
            'reviewmanage-editusersbyfield',
            'message-ajaxgetmessage',
            'infoqz-push',
            'qareport-bugtrendtimer',
            'project-editprojectinfo',
            'report-qualitygatecheckresult',
            'report-qualitygatebugdetail',
            'reviewmanage-setverifyresult',
            'reviewmanage-sendundealissueusersmail',
        ];
    /**
     * The construc method, to do some auto things.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if(!defined('FIRST_RUN'))
        {
            define('FIRST_RUN', true);
            $this->sendHeader();
            $this->setCompany();
            $this->setUser();
            $this->loadConfigFromDB();
            $this->app->setTimezone();
            $this->loadCustomFromDB();
            if(!$this->checkIP()) die($this->lang->ipLimited);
            $this->app->loadLang('company');
        }
    }

    /**
     * Set the header info.
     *
     * @access public
     * @return void
     */
    public function sendHeader()
    {
        header("Content-Type: text/html; Language={$this->config->charset}");
        header("Cache-control: private");

        /* Send HTTP header. */
        if($this->config->framework->sendXCTO)  header("X-Content-Type-Options: nosniff");
        if($this->config->framework->sendXXP)   header("X-XSS-Protection: 1; mode=block");
        if($this->config->framework->sendHSTS)  header("Strict-Transport-Security: max-age=3600; includeSubDomains");
        if($this->config->framework->sendRP)    header("Referrer-Policy: no-referrer-when-downgrade");
        if($this->config->framework->sendXPCDP) header("X-Permitted-Cross-Domain-Policies: master-only");
        if($this->config->framework->sendXDO)   header("X-Download-Options: noopen");

        /* Set Content-Security-Policy header. */
        if($this->config->CSPs)
        {
            foreach($this->config->CSPs as $CSP) header("Content-Security-Policy: $CSP;");
        }

        if($this->loadModel('setting')->getItem('owner=system&module=sso&key=turnon'))
        {
            if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on')
            {
                $session = $this->config->sessionVar . '=' . session_id();
                header("Set-Cookie: $session; SameSite=None; Secure=true", false);
            }
        }
        else
        {
            if(!empty($this->config->xFrameOptions)) header("X-Frame-Options: {$this->config->xFrameOptions}");
        }
    }

    /**
     * Set the commpany.
     *
     * First, search company by the http host. If not found, search by the default domain. Last, use the first as the default.
     * After get the company, save it to session.
     * @access public
     * @return void
     */
    public function setCompany()
    {
        $httpHost = $this->server->http_host;

        if($this->session->company)
        {
            $this->app->company = $this->session->company;
        }
        else
        {
            $company = $this->loadModel('company')->getFirst();
            if(!$company) $this->app->triggerError(sprintf($this->lang->error->companyNotFound, $httpHost), __FILE__, __LINE__, $exit = true);
            $this->session->set('company', $company);
            $this->app->company  = $company;
        }
    }

    /**
     * Set the user info.
     *
     * @access public
     * @return void
     */
    public function setUser()
    {
        if($this->session->user)
        {
            if(!defined('IN_UPGRADE')) $this->session->user->view = $this->loadModel('user')->grantUserView();
            $this->app->user = $this->session->user;
        }
        elseif($this->app->company->guest or PHP_SAPI == 'cli')
        {
            $user             = new stdClass();
            $user->id         = 0;
            $user->account    = 'guest';
            $user->realname   = 'guest';
            $user->role       = 'guest';
            $user->admin      = false;
            $user->rights     = $this->loadModel('user')->authorize('guest');
            $user->groups     = array('group');
            if(!defined('IN_UPGRADE')) $user->view = $this->user->grantUserView($user->account, $user->rights['acls']);
            $this->session->set('user', $user);
            $this->app->user = $this->session->user;
        }
    }

    /**
     * Load configs from database and save it to config->system and config->personal.
     *
     * @access public
     * @return void
     */
    public function loadConfigFromDB()
    {
        $this->loadModel('setting');
        $xxItems  = $this->setting->getItems('owner=system&module=common&section=xuanxuan');
        $xxConfig = array();
        foreach($xxItems as $xxItem) $xxConfig[$xxItem->key] = $xxItem->value;
        if(empty($xxConfig['key']))
        {
            $this->setting->setItem('system.common.xuanxuan.turnon', 1);
            $this->setting->setItem('system.common.xuanxuan.key', $this->setting->computeSN());
        }
        if(!isset($xxConfig['chatPort']))       $this->setting->setItem('system.common.xuanxuan.chatPort', 11444);
        if(!isset($xxConfig['commonPort']))     $this->setting->setItem('system.common.xuanxuan.commonPort', 11443);
        if(!isset($xxConfig['ip']))             $this->setting->setItem('system.common.xuanxuan.ip', '0.0.0.0');
        if(!isset($xxConfig['uploadFileSize'])) $this->setting->setItem('system.common.xuanxuan.uploadFileSize', 20);
        if(!isset($xxConfig['https']) and !isset($xxConfig['isHttps'])) $this->setting->setItem('system.common.xuanxuan.https', 'off');
        /* Get configs of system and current user. */
        $account = isset($this->app->user->account) ? $this->app->user->account : '';
        if($this->config->db->name) $config  = $this->loadModel('setting')->getSysAndPersonalConfig($account);
        $this->config->system   = isset($config['system']) ? $config['system'] : array();
        $this->config->personal = isset($config[$account]) ? $config[$account] : array();

        /* Overide the items defined in config/config.php and config/my.php. */
        if(isset($this->config->system->common)) $this->app->mergeConfig($this->config->system->common, 'common');
        if(isset($this->config->personal->common)) $this->app->mergeConfig($this->config->personal->common, 'common');
    }

    /**
     * Load custom lang from db.
     *
     * @access public
     * @return void
     */
    public function loadCustomFromDB()
    {
        $this->loadModel('custom');

        if(defined('IN_UPGRADE')) return;
        if(!$this->config->db->name) return;

        $records = $this->custom->getAllLang();
        if(!$records) return;

        $this->lang->db = new stdclass();
        $this->lang->db->custom = $records;
    }

    /**
     * Juage a method of one module is open or not?
     *
     * @param  string $module
     * @param  string $method
     * @access public
     * @return bool
     */
    public function isOpenMethod($module, $method)
    {
        if(in_array(strtolower($module .'-'. $method), self::$openURIs)) return true;

        if($this->loadModel('user')->isLogon() or ($this->app->company->guest and $this->app->user->account == 'guest'))
        {
            if($module == 'flow' and $method == 'browse')         return true;
            if($module == 'flow' and $method == 'create')         return true;
            if($module == 'flow' and $method == 'batchcreate')    return true;
            if($module == 'flow' and $method == 'edit')           return true;
            if($module == 'flow' and $method == 'operate')        return true;
            if($module == 'flow' and $method == 'batchoperate')   return true;
            if($module == 'flow' and $method == 'view')           return true;
            if($module == 'flow' and $method == 'delete')         return true;
            if($module == 'flow' and $method == 'link')           return true;
            if($module == 'flow' and $method == 'unlink')         return true;
            if($module == 'flow' and $method == 'export')         return true;
            if($module == 'flow' and $method == 'exporttemplate') return true;
            if($module == 'flow' and $method == 'import')         return true;
            if($module == 'flow' and $method == 'showimport')     return true;

            if($module == 'workflowfield' and $method == 'checkfieldlength') return true;
            if($module == 'workflowfield' and $method == 'addSqlVar')        return true;
            if($module == 'workflowfield' and $method == 'delSqlVar')        return true;
            if($module == 'workflowfield' and $method == 'buildVarControl')  return true;
        }
        if($module == 'api')
        {
            if($method == 'mobilegetlist'    ||
                $method == 'mobilegetinfo'    ||
                $method == 'mobilegetuser'    ||
                $method == 'mobilegetusers'   ||
                $method == 'mobilegethistory' ||
                $method == 'mobilecomment'    ||
                $method == 'mobilegetcustom') return true;
        }
        if($module == 'mobileapi') {
            return true;
        }

        if($module == 'api' and $method == 'getlicenses') return true;
        if($module == 'upgrade' and $method == 'ajaxupdatefile') return true;
        if($module == 'user' and strpos('login|logout|deny|reset|refreshrandom', $method) !== false) return true;
        if($module == 'cas'  and strpos('login|logout|curl|tokenlogin', $method) !== false) return true;
        if($module == 'api'  and $method == 'getsessionid') return true;
        if($module == 'misc' and $method == 'checktable') return true;
        if($module == 'misc' and $method == 'qrcode') return true;
        if($module == 'misc' and $method == 'about') return true;
        if($module == 'misc' and $method == 'checkupdate') return true;
        if($module == 'misc' and $method == 'ping')  return true;
        if($module == 'misc' and $method == 'captcha')  return true;
        if($module == 'sso' and $method == 'login')  return true;
        if($module == 'sso' and $method == 'logout') return true;
        if($module == 'sso' and $method == 'bind') return true;
        if($module == 'sso' and $method == 'gettodolist') return true;
        if($module == 'block' and $method == 'main' and isset($_GET['hash'])) return true;
        if($module == 'file' and $method == 'read') return true;
        if($module == 'index' and $method == 'changelog') return true;
        if($module == 'my' and $method == 'preference') return true;

        if($this->loadModel('user')->isLogon() or ($this->app->company->guest and $this->app->user->account == 'guest'))
        {
            if(stripos($method, 'ajax') !== false) return true;
            if($module == 'misc' and $method == 'downloadclient') return true;
            if($module == 'misc' and $method == 'changelog')  return true;
            if($module == 'tutorial' and $method == 'start')  return true;
            if($module == 'tutorial' and $method == 'index')  return true;
            if($module == 'tutorial' and $method == 'quit')   return true;
            if($module == 'tutorial' and $method == 'wizard') return true;
            if($module == 'block' and $method == 'admin') return true;
            if($module == 'block' and $method == 'set') return true;
            if($module == 'block' and $method == 'sort') return true;
            if($module == 'block' and $method == 'resize') return true;
            if($module == 'block' and $method == 'dashboard') return true;
            if($module == 'block' and $method == 'printblock') return true;
            if($module == 'block' and $method == 'main') return true;
            if($module == 'block' and $method == 'delete') return true;
            if($module == 'product' and $method == 'showerrornone') return true;
            if($module == 'report' and $method == 'annualdata') return true;
        }
        return false;
    }

    /**
     * Deny access.
     *
     * @param  varchar  $module
     * @param  varchar  $method
     * @param  bool     $reload
     * @access public
     * @return mixed
     */
    public function deny($module, $method, $reload = true)
    {
        if($reload)
        {
            /* Get authorize again. */
            $user = $this->app->user;
            $user->rights = $this->loadModel('user')->authorize($user->account);
            $user->groups = $this->user->getGroups($user->account);
            $this->session->set('user', $user);
            $this->app->user = $this->session->user;
            if(commonModel::hasPriv($module, $method)) return true;
        }

        $vars = "module=$module&method=$method";
        if(isset($this->server->http_referer))
        {
            $referer = helper::safe64Encode($this->server->http_referer);
            $vars   .= "&referer=$referer";
        }
        $denyLink = helper::createLink('user', 'deny', $vars);

        /* Fix the bug of IE: use js locate, can't get the referer. */
        if(strpos($this->server->http_user_agent, 'Trident') !== false)
        {
            echo "<a href='$denyLink' id='denylink' style='display:none'>deny</a>";
            echo "<script>document.getElementById('denylink').click();</script>";
        }
        else
        {
            echo js::locate($denyLink);
        }
        exit;
    }

    /**
     * Print the run info.
     *
     * @param mixed $startTime  the start time.
     * @access public
     * @return array    the run info array.
     */
    public function printRunInfo($startTime)
    {
        $info['timeUsed'] = round(getTime() - $startTime, 4) * 1000;
        $info['memory']   = round(memory_get_peak_usage() / 1024, 1);
        $info['querys']   = count(dao::$querys);
        vprintf($this->lang->runInfo, $info);
        return $info;
    }

    /**
     * Print top bar.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printUserBar()
    {
        global $lang, $app;

        if(isset($app->user))
        {
            $isGuest = $app->user->account == 'guest';

            echo "<a class='dropdown-toggle' data-toggle='dropdown'>";
            echo "<div id='main-avatar' class='avatar avatar bg-secondary avatar-circle'>";
            echo !empty($app->user->avatar) ? html::image($app->user->avatar) : strtoupper($app->user->account[0]);
            echo "</div>\n";
            echo '</a>';
            echo "<ul class='dropdown-menu pull-right'>";
            if(!$isGuest)
            {
                $noRole = (!empty($app->user->role) && isset($lang->user->roleList[$app->user->role])) ? '' : ' no-role';
                echo '<li class="user-profile-item">';
                echo "<a href='" . helper::createLink('my', 'profile', '', '', true) . "' data-width='600' class='iframe $noRole'" . '>';
                echo "<div id='menu-avatar' class='avatar avatar bg-secondary avatar-circle'>";
                echo !empty($app->user->avatar) ? html::image($app->user->avatar) : strtoupper($app->user->account[0]);
                echo "</div>\n";
                echo '<div class="user-profile-name">' . (empty($app->user->realname) ? $app->user->account : $app->user->realname) . '</div>';
                if(isset($lang->user->roleList[$app->user->role])) echo '<div class="user-profile-role">' . $lang->user->roleList[$app->user->role] . '</div>';
                echo '</a></li><li class="divider"></li>';
                echo '<li>' . html::a(helper::createLink('my', 'profile', '', '', true), "<i class='icon icon-account'></i> " . $lang->profile, '', "class='iframe' data-width='600'") . '</li>';
                echo '<li>' . html::a(helper::createLink('my', 'preference', '', '', true), "<i class='icon icon-controls'></i> " . $lang->preference, '', "class='iframe' data-width='650'") . '</li>';
                //echo '<li>' . html::a(helper::createLink('my', 'changepassword', '', '', true), "<i class='icon icon-cog-outline'></i> " . $lang->changePassword, '', "class='iframe' data-width='600'") . '</li>';
                echo '<li><a href="'. $app->config->global->resetPasswordIp.'" target="_blank"><i class="icon icon-cog-outline"></i> ' . $lang->changePassword . '</a></li>';

                echo "<li class='divider'></li>";
            }

            echo "<li class='dropdown-submenu top'>";
            echo "<a href='javascript:;'>" . "<i class='icon icon-theme'></i> " . $lang->theme . "</a><ul class='dropdown-menu pull-left'>";
            foreach($app->lang->themes as $key => $value)
            {
                echo "<li " . ($app->cookie->theme == $key ? "class='selected'" : '') . "><a href='javascript:selectTheme(\"$key\");' data-value='" . $key . "'>" . $value . "</a></li>";
            }
            echo '</ul></li>';

            echo "<li class='dropdown-submenu top'>";
            echo "<a href='javascript:;'>" . "<i class='icon icon-lang'></i> " . $lang->lang . "</a><ul class='dropdown-menu pull-left'>";
            foreach ($app->config->langs as $key => $value)
            {
                echo "<li " . ($app->cookie->lang == $key ? "class='selected'" : '') . "><a href='javascript:selectLang(\"$key\");'>" . $value . "</a></li>";
            }
            echo '</ul></li>';

            //if(!$isGuest and !commonModel::isTutorialMode() and $app->viewType != 'mhtml')
            //{
            //    $customLink = helper::createLink('custom', 'ajaxMenu', "module={$app->getModuleName()}&method={$app->getMethodName()}", '', true);
            //    echo "<li class='custom-item'><a href='$customLink' data-toggle='modal' data-type='iframe' data-icon='cog' data-width='80%'>$lang->customMenu</a></li>";
            //}

            commonModel::printAboutBar();
            echo '<li class="divider"></li>';
            echo '<li>';
            if($isGuest)
            {
                echo html::a(helper::createLink('user', 'login'), $lang->login, '_top');
            }
            else
            {
                $app->loadConfig('cas');
                $logoutMod = empty($app->config->cas->turnon) ? 'user' : 'cas';
                echo html::a(helper::createLink($logoutMod, 'logout'), "<i class='icon icon-exit'></i> " . $lang->logout, '_top');
            }
            echo '</li></ul>';
        }
    }

    /**
     * Print about bar.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printAboutBar()
    {
        global $app, $config, $lang;
        echo "<li class='dropdown-submenu'>";
        echo "<a data-toggle='dropdown'>" . "<i class='icon icon-help'></i> " . $lang->help . "</a>";
        echo "<ul class='dropdown-menu pull-left'>";
        //if($config->global->flow == 'full' && !commonModel::isTutorialMode() and $app->user->account != 'guest') echo '<li>' . html::a(helper::createLink('tutorial', 'start'), $lang->noviceTutorial, '', "class='iframe' data-class-name='modal-inverse' data-width='800' data-headerless='true' data-backdrop='true' data-keyboard='true'") . "</li>";

        $manualUrl = (!empty($config->isINT)) ? $config->manualUrl['int'] : $config->manualUrl['home'];
        echo '<li>' . html::a($manualUrl, $lang->manual, '', "class='open-in-app' id='helpLink' data-app='help'") . '</li>';

        echo '<li>' . html::a(helper::createLink('misc', 'changeLog'), $lang->changeLog, '', "class='iframe' data-width='800' data-headerless='true' data-backdrop='true' data-keyboard='true'") . '</li>';
        echo "</ul></li>\n";

        self::printClientLink();

        echo '<li>' . html::a(helper::createLink('misc', 'about'), "<i class='icon icon-about'></i> " . $lang->aboutZenTao, '', "class='about iframe' data-width='1050' data-headerless='true' data-backdrop='true' data-keyboard='true' data-class='modal-about'") . '</li>';
        echo '<li>' . $lang->designedByAIUX . '</li>';
    }

    /**
     * Create menu item link
     *
     * @param object $menuItem
     *
     * @static
     * @access public
     * @return string
     */
    public static function createMenuLink($menuItem, $group)
    {
        global $app;
        $link = $menuItem->link;
        if(is_array($menuItem->link))
        {
            $vars = isset($menuItem->link['vars']) ? $menuItem->link['vars'] : '';
            if(isset($menuItem->tutorial) && $menuItem->tutorial)
            {
                if(!empty($vars)) $vars = helper::safe64Encode($vars);
                $link = helper::createLink('tutorial', 'wizard', "module={$menuItem->link['module']}&method={$menuItem->link['method']}&params=$vars");
            }
            else
            {
                //项目管理-设置，后台配置权限后，此处再判断是否是项目的项目经理(or  admin用户全部显示 or 后台自定义配置的人员)，是且有权限，设置显示。否则不显示
               if($menuItem->link['module'] == 'project' && $menuItem->link['method'] == 'view'  ){
                   $commonModel = new self();
                   $projectIds = $commonModel->isLoginUserProjectPm();
                   $ids = $projectIds ? array_column($projectIds['project'],'id') : array();
                   $param = explode('=',$vars);
                  // if(!is_array($param)) parse_str($param, $param);
                   if(!in_array($param[1],$ids) && !in_array($app->user->account,$projectIds['member']) ){
                       $menuItem->text = '';
                       $link = '';
                   }else{
                       $link = helper::createLink($menuItem->link['module'], $menuItem->link['method'], $vars);
                   }
               }else{
                   $link = helper::createLink($menuItem->link['module'], $menuItem->link['method'], $vars);
               }
            }
        }
        return $link;
    }

    /**
     * 查询当前登录用户是否是项目的项目经理 、admin 、或后台项目-可查看项目-设置人员
     * @return mixed
     */
    public function isLoginUserProjectPm(){
        $project = $this->dao->select('id')->from(TABLE_PROJECT)
            ->where('deleted')->eq('0')
            ->andWhere('type')->eq('project')
            ->andWhere('PM')->eq($this->app->user->account)
            ->fetchAll();
        $setMember = $this->lang->project->projectSetList;
        $setMember = $setMember['setMember'] ? $setMember['setMember'].','.'admin' : 'admin';
        return array('project' =>$project,'member' => array_filter(array_unique(explode(',',$setMember))));
    }
    /**
     * Create sub menu by settings in lang files.
     *
     * @param  array    $items
     * @param  mixed    $replace
     * @static
     * @access public
     * @return array
     */
    public static function createDropMenu($items, $replace)
    {
        $dropMenu = array();
        foreach($items as $dropMenuKey => $dropMenuLink)
        {
            if(is_array($dropMenuLink) and isset($dropMenuLink['link'])) $dropMenuLink = $dropMenuLink['link'];
            if(is_array($replace))
            {
                $dropMenuLink = vsprintf($dropMenuLink, $replace);
            }
            else
            {
                $dropMenuLink = sprintf($dropMenuLink, $replace);
            }
            list($dropMenuName, $dropMenuModule, $dropMenuMethod, $dropMenuParams) = explode('|', $dropMenuLink);

            $link = array();
            $link['module'] = $dropMenuModule;
            $link['method'] = $dropMenuMethod;
            $link['vars']   = $dropMenuParams;

            $dropMenuItem     = isset($items->$dropMenuKey) ? $items->$dropMenuKey : array();
            $menu            = new stdclass();
            $menu->name      = $dropMenuKey;
            $menu->link      = $link;
            $menu->text      = $dropMenuName;
            $menu->subModule = isset($dropMenuItem['subModule']) ? $dropMenuItem['subModule'] : '';
            $menu->alias     = isset($dropMenuItem['alias'])     ? $dropMenuItem['alias'] : '';
            $menu->hidden    = false;
            $dropMenu[$dropMenuKey] = $menu;
        }

        return $dropMenu;
    }

    /**
     * Print admin dropMenu.
     *
     * @param  string    $dropMenu
     * @static
     * @access public
     * @return void
     */
    public static function printAdminDropMenu($dropMenu)
    {
        global $app, $lang;
        $currentModule = $app->getModuleName();
        $currentMethod = $app->getMethodName();
        if(isset($lang->admin->dropMenuOrder->$dropMenu))
        {
            ksort($lang->admin->dropMenuOrder->$dropMenu);
            foreach($lang->admin->dropMenuOrder->$dropMenu as $type)
            {
                if(isset($lang->admin->dropMenu->$dropMenu->$type))
                {
                    $subModule = '';
                    $alias     = '';
                    $link      = $lang->admin->dropMenu->$dropMenu->$type;
                    if(is_array($lang->admin->dropMenu->$dropMenu->$type))
                    {
                        $dropMenuType = $lang->admin->dropMenu->$dropMenu->$type;
                        if(isset($dropMenuType['subModule'])) $subModule = $dropMenuType['subModule'];
                        if(isset($dropMenuType['alias']))     $alias     = $dropMenuType['alias'];
                        if(isset($dropMenuType['link']))      $link      = $dropMenuType['link'];
                    }

                    list($text, $moduleName, $methodName)= explode('|', $link);
                    if(!common::hasPriv($moduleName, $methodName)) continue;

                    $active = ($currentModule == $moduleName and $currentMethod == $methodName) ? 'btn-active-text' : '';
                    if($subModule and strpos(",{$subModule}," , ",{$currentModule},") !== false) $active = 'btn-active-text';
                    if($alias and $currentModule == $moduleName and strpos(",$alias,", ",$currentMethod,") !== false) $active = 'btn-active-text';
                    echo html::a(helper::createLink($moduleName, $methodName), "<span class='text'>$text</span>", '', "class='btn btn-link {$active}' id='{$type}Tab'");
                }
            }
        }
    }

    /**
     * Print the main nav.
     *
     * @param  string $moduleName
     *
     * @static
     * @access public
     * @return void
     */
    public static function printMainNav($moduleName)
    {
        $items = common::getMainNavList($moduleName);
        foreach($items as $item)
        {
            if($item == 'divider')
            {
                echo "<li class='divider'></li>";
            }
            else
            {
                $active = $item->active ? ' class="active"' : '';
                echo "<li$active>" . html::a($item->url, $item->title) . '</li>';
            }
        }
    }

    /**
     * Print upper left corner home button.
     *
     * @param  string $openApp
     * @static
     * @access public
     * @return void
     */
    public static function printHomeButton($openApp)
    {
        global $lang;
        global $config;

        if(!$openApp) return;
        $icon = zget($lang->navIcons, $openApp, '');

        if(!in_array($openApp, array('program', 'product', 'project')))
        {
            $nav = $lang->mainNav->$openApp;
            list($title, $currentModule, $currentMethod, $vars) = explode('|', $nav);
            if($openApp == 'execution') $currentMethod = 'all';
        }
        else
        {
            $currentModule = $openApp;
            if($openApp == 'program' or $openApp == 'project') $currentMethod = 'browse';
            if($openApp == 'product') $currentMethod = 'all';
        }

        if($config->systemMode == 'classic' and $openApp == 'execution') $icon = zget($lang->navIcons, 'project', '');
        $link = ($openApp != 'execution' or ($config->systemMode == 'classic')) ? helper::createLink($currentModule, $currentMethod) : '';
        $html = $link ? html::a($link, "$icon {$lang->$openApp->common}", '', "class='btn'") : "$icon {$lang->$openApp->common}";

        echo "<div class='btn-group header-btn'>" . $html . '</div>';
    }

    /**
     * Get main nav items list
     *
     * @param  string $moduleName
     *
     * @static
     * @access public
     * @return array
     */
    public static function getMainNavList($moduleName)
    {
        global $lang;

        $menuOrder = $lang->mainNav->menuOrder;
        ksort($menuOrder);

        $items = array();
        $lastItem = end($menuOrder);
        $divider = false;
        foreach($menuOrder as $key => $group)
        {
            $nav = $lang->mainNav->$group;
            list($title, $currentModule, $currentMethod, $vars) = explode('|', $nav);

            /* When last divider is not used in mainNav, use it next menu. */
            $divider = ($divider || ($lastItem != $key) && strpos($lang->dividerMenu, ",{$group},") !== false) ? true : false;

            if(!common::hasPriv($currentModule, $currentMethod)) continue;

            if($divider)
            {
                $items[] = 'divider';
                $divider = false;
            }

            $item = new stdClass();
            $item->group      = $group;
            $item->code       = $group;
            $item->active     = zget($lang->navGroup, $moduleName, '') == $group || $moduleName != 'program' && $moduleName == $group;
            $item->title      = $title;
            $item->moduleName = $currentModule;
            $item->methodName = $currentMethod;
            $item->vars       = $vars;
            $item->url        = helper::createLink($currentModule, $currentMethod, $vars, '', 0, 0, 1);

            $items[] = $item;
        }
        return $items;
    }

    /**
     * Print the main menu.
     *
     * @static
     * @access public
     * @return string
     */
    public static function printMainMenu()
    {
        global $app, $lang, $config;

        /* Set main menu by openApp and module. */
        self::setMainMenu();

        $activeMenu = '';
        $openApp = $app->openApp;

        $currentModule = $app->rawModule;
        $currentMethod = $app->rawMethod;

        /* Print all main menus. */
        $menu     = customModel::getMainMenu();
        $lastMenu = end($menu);

        echo "<ul class='nav nav-default'>\n";
        foreach($menu as $menuItem)
        {
            if(isset($menuItem->hidden) && $menuItem->hidden) continue;
            if(empty($menuItem->link)) continue;
            if($menuItem->divider) echo "<li class='divider'></li>";

            /* Init the these vars. */
            $alias     = isset($menuItem->alias) ? $menuItem->alias : '';
            $subModule = isset($menuItem->subModule) ? explode(',', $menuItem->subModule) : array();
            $class     = isset($menuItem->class) ? $menuItem->class : '';
            $exclude   = isset($menuItem->exclude) ? $menuItem->exclude : '';
//            print_r([$menuItem->name, $currentModule]);die();
            $active = '';
            if($menuItem->name == $currentModule and strpos(",$exclude,", ",$currentModule-$currentMethod,") === false)
            {
                $activeMenu = $menuItem->name;
                $active = 'active';
            }
            if($subModule and in_array($currentModule, $subModule) and strpos(",$exclude,", ",$currentModule-$currentMethod,") === false)
            {
                $activeMenu = $menuItem->name;
                $active = 'active';
            }

            if($menuItem->link)
            {
                $target = '';
                $module = '';
                $method = '';
                $link   = commonModel::createMenuLink($menuItem, $openApp);

                if(is_array($menuItem->link))
                {
                    if(isset($menuItem->link['target'])) $target = $menuItem->link['target'];
                    if(isset($menuItem->link['module'])) $module = $menuItem->link['module'];
                    if(isset($menuItem->link['method'])) $method = $menuItem->link['method'];
                }
                if($module == $currentModule and ($method == $currentMethod or strpos(",$alias,", ",$currentMethod,") !== false) and strpos(",$exclude,", ",$currentMethod,") === false)
                {
                    $activeMenu = $menuItem->name;
                    $active = 'active';
                }
                $label    = $menuItem->text;
                $dropMenu = '';
                /* Print drop menus. */
                if(isset($menuItem->dropMenu))
                {
                    foreach($menuItem->dropMenu as $dropMenuName => $dropMenuItem)
                    {
                        if(isset($dropMenuItem->hidden) and $dropMenuItem->hidden) continue;


                        /* Parse drop menu link. */
                        $dropMenuLink = $dropMenuItem;
                        if(is_array($dropMenuItem) and isset($dropMenuItem['link'])) $dropMenuLink = $dropMenuLink['link'];

                        list($subLabel, $subModule, $subMethod, $subParams) = explode('|', $dropMenuLink);
                        $subLink = helper::createLink($subModule, $subMethod, $subParams);

                        $subActive = '';
                        $activeMainMenu = false;
                        if($currentModule == strtolower($subModule) && $currentMethod == strtolower($subMethod))
                        {
                            $activeMainMenu = true;
                        }
                        else
                        {
                            $subModule = isset($dropMenuItem['subModule']) ? explode(',', $dropMenuItem['subModule']) : array();
                            if($subModule and in_array($currentModule, $subModule) and strpos(",$exclude,", ",$currentModule-$currentMethod,") === false) $activeMainMenu = true;
                        }

                        if($activeMainMenu)
                        {
                            $activeMenu = $dropMenuName;
                            $active     = 'active';
                            $subActive  = 'active';
                            $label      = $subLabel;
                        }
                        $dropMenu .= "<li class='$subActive' data-id='$subLabel'>" . html::a($subLink, $subLabel, '', "data-app='$openApp'") . '</li>';
                    }

                    if(empty($dropMenu)) continue;

                    $label   .= "<span class='caret'></span>";
                    $dropMenu = "<ul class='dropdown-menu'>{$dropMenu}</ul>";
                }

                $misc = (isset($lang->navGroup->$module) and $openApp != $lang->navGroup->$module) ? "data-app='$openApp'" : '';

                if($module == 'jenkinslogin' and $method == 'login')
                {
                    $link =  $app->config->global->jenkinsServerIP;
                    $misc = 'target="_blank"';
                }
                if($module == 'sonarqube' and $method == 'login')
                {
                    $link = $app->config->global->sonarcubeServerIP;
                    $misc = 'target="_blank"';
                }
                if($module == 'nextcloud' and $method == 'login')
                {
                    $link = $app->config->global->networkDiskServerIP;;
                    $misc = 'target="_blank"';
                }
                if($module == 'my' and $method == 'work')
                {
                    // 判断是否在待处理的菜单上显示小红点
                    $hasHandle = $app->control->loadModel('my')->getHandle();
                    if(!empty($hasHandle)) $label .= '<span class="red-point"></span>';
                }

                //内外部年度信息化项目计划一览表菜单 特殊处理
                if(in_array($currentMethod,['outlook','chart','inlook']))
                {
                    if($module == 'outsideplan' && !in_array($menuItem->name,['outlook','inlook','chart'])) { $active = '';  }
                    if($currentMethod == 'chart' && $menuItem->name == 'outlook') { $active = 'active';  }
                    if($currentMethod == 'inlook' && $menuItem->name == 'outlook') { $active = 'active';  }
                }
                $menuItemHtml = "<li class='$class $active' data-id='$menuItem->name'>" . html::a($link, $label, $target, $misc) . $dropMenu . "</li>\n";
                echo $menuItemHtml;
            }
            else
            {
                echo "<li class='$class $active' data-id='$menuItem->name'>$menuItem->text</li>\n";
            }
        }
        echo "</ul>\n";

        return $activeMenu;
    }

    /**
     * Print the search box.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printSearchBox()
    {
        global $lang;
        global $config;

        $searchObject = 'bug';
        echo "<div class='input-group-btn'>";
        echo html::hidden('searchType', $searchObject);
        echo "<ul id='searchTypeMenu' class='dropdown-menu'>";

        $searchObjects = $lang->searchObjects;
        if($config->systemMode != 'new') unset($searchObjects['program'], $searchObjects['project']);

        foreach ($searchObjects as $key => $value)
        {
            $class = $key == $searchObject ? "class='selected'" : '';
            if($key == 'program')    $key = 'program-product';
            if($key == 'deploystep') $key = 'deploy-viewstep';

            echo "<li $class><a href='javascript:$.setSearchType(\"$key\");' data-value='{$key}'>{$value}</a></li>";
        }
        echo '</ul></div>';
    }

    /**
     * Print the module menu.
     *
     * @param  string $actveMenu
     * @param  string $methodName
     * @static
     * @access public
     * @return void
     */
    public static function printModuleMenu($activeMenu)
    {
        global $app, $lang;
        $moduleName = $app->rawModule;
        $methodName = $app->rawMethod;

        $openApp = $app->openApp;

        if(!isset($lang->$openApp->menu))
        {
            echo "<ul></ul>";
            return;
        }

        /* get current module and method. */
        $isTutorialMode = commonModel::isTutorialMode();
        $currentModule  = $app->getModuleName();
        $currentMethod  = $app->getMethodName();
        $isMobile       = $app->viewType === 'mhtml';
//        print_r([$currentModule,$currentMethod]);die();
        /* When use workflow then set rawModule to moduleName. */
        if($moduleName == 'flow') $activeMenu = $app->rawModule;
        $menu = customModel::getModuleMenu($activeMenu);

        /* If this is not workflow then use rawModule and rawMethod to judge highlight. */
        if($app->isFlow)
        {
            $currentModule = $app->rawModule;
            $currentMethod = $app->rawMethod;
        }

        if($isTutorialMode and defined('WIZARD_MODULE')) $currentModule = WIZARD_MODULE;
        if($isTutorialMode and defined('WIZARD_METHOD')) $currentMethod = WIZARD_METHOD;

        /* The beginning of the menu. */
        echo $isMobile ? '' : "<ul class='nav nav-default'>\n";

        /* Cycling to print every sub menu. */
        foreach($menu as $menuItem)
        {
            if(isset($menuItem->hidden) && $menuItem->hidden) continue;
            if($isMobile and empty($menuItem->link)) continue;
            if($menuItem->divider) echo "<li class='divider'></li>";

            /* Init the these vars. */
            $alias     = isset($menuItem->alias) ? $menuItem->alias : '';
            $subModule = isset($menuItem->subModule) ? explode(',', $menuItem->subModule) : array();
            $class     = isset($menuItem->class) ? $menuItem->class : '';
            $active    = '';
            if($subModule and in_array($currentModule, $subModule)) $active = 'active';
            // if($alias and $moduleName == $currentModule and strpos(",$alias,", ",$currentMethod,") !== false) $active = 'active';
            if($menuItem->link)
            {
                $target = '';
                $module = '';
                $method = '';
                $link   = commonModel::createMenuLink($menuItem, $openApp);
                if(is_array($menuItem->link))
                {
                    if(isset($menuItem->link['target'])) $target = $menuItem->link['target'];
                    if(isset($menuItem->link['module'])) $module = $menuItem->link['module'];
                    if(isset($menuItem->link['method'])) $method = $menuItem->link['method'];
                }
                if($module == $currentModule and ($method == $currentMethod or strpos(",$alias,", ",$currentMethod,") !== false)) $active = 'active';

                $label    = $menuItem->text;
                $dropMenu = '';
                /* Print sub menus. */
                if(isset($menuItem->dropMenu))
                {
                    foreach($menuItem->dropMenu as $dropMenuItem)
                    {
                        if($dropMenuItem->hidden) continue;

                        $subActive = '';
                        $subModule = '';
                        $subMethod = '';
                        $subParams = '';
                        $subLabel  = $dropMenuItem->text;
                        if(isset($dropMenuItem->link['module'])) $subModule = $dropMenuItem->link['module'];
                        if(isset($dropMenuItem->link['method'])) $subMethod = $dropMenuItem->link['method'];
                        if(isset($dropMenuItem->link['vars']))   $subParams = $dropMenuItem->link['vars'];

                        $subLink = helper::createLink($subModule, $subMethod, $subParams);

                        if($currentModule == strtolower($subModule) && $currentMethod == strtolower($subMethod)) $subActive = 'active';

                        $misc = (isset($lang->navGroup->$subModule) and $openApp != $lang->navGroup->$subModule) ? "data-app='$openApp'" : '';
                        $dropMenu .= "<li class='$subActive' data-id='$dropMenuItem->name'>" . html::a($subLink, $subLabel, '', $misc) . '</li>';
                    }

                    if(empty($dropMenu)) continue;

                    $label   .= "<span class='caret'></span>";
                    $dropMenu  = "<ul class='dropdown-menu'>{$dropMenu}</ul>";
                }

                $misc = (isset($lang->navGroup->$module) and $openApp != $lang->navGroup->$module) ? "data-app='$openApp'" : '';

                if($module =='weeklyreport' && $currentModule =='weeklyreport') $active = 'active'; //周报三级菜单特殊处理
                if($label =='产品' && $currentMethod == 'manageproductplans') $active = 'active'; //产品三级菜单特殊处理

                $menuItemHtml = "<li class='$class $active' data-id='$menuItem->name'>" . html::a($link, $label, $target, $misc) . $dropMenu . "</li>\n";
                if($isMobile) $menuItemHtml = html::a($link, $menuItem->text, $target, $misc . " class='$class $active'") . "\n";
                echo $menuItemHtml;
            }
            else
            {
                echo $isMobile ? $menuItem->text : "<li class='$class $active' data-id='$menuItem->name'>$menuItem->text</li>\n";
            }
        }
        echo $isMobile ? '' : "</ul>\n";
    }

    /**
     * Print the bread menu.
     *
     * @param  string $moduleName
     * @param  string $position
     * @static
     * @access public
     * @return void
     */
    public static function printBreadMenu($moduleName, $position)
    {
        global $lang;
        $mainMenu = $moduleName;
        echo "<ul class='breadcrumb'>";
        echo '<li>' . html::a(helper::createLink('my', 'index'), $lang->zentaoPMS) . '</li>';
        if($moduleName != 'index')
        {
            if(isset($lang->menu->$mainMenu))
            {
                $menuLink = $lang->menu->$mainMenu;
                if(is_array($menuLink)) $menuLink = $menuLink['link'];
                list($menuLabel, $module, $method) = explode('|', $menuLink);
                echo '<li>' . html::a(helper::createLink($module, $method), $menuLabel) . '</li>';
            }
        }
        else
        {
            echo '<li>' . $lang->index->common . '</li>';
        }

        if(empty($position))
        {
            echo '</ul>';
            return;
        }

        if(is_array($position))
        {
            foreach($position as $key => $link) echo "<li class='active'>" . $link . '</li>';
        }
        echo '</ul>';
    }

    /**
     * Print the link for notify file.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printNotifyLink()
    {
        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        {
            global $lang;
            echo html::a(helper::createLink('misc', 'downNotify'), "<i class='icon-bell'></i>", '', "title='$lang->downNotify' class='text-primary'") . ' &nbsp; ';
        }
    }

    /**
     * Print the link for zentao client.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printClientLink()
    {
        global $config, $lang;
        if(isset($config->xxserver->installed) and $config->xuanxuan->turnon)
        {
            echo "<li class='dropdown-submenu'>";
            echo "<a href='javascript:;'>" . "<i class='icon icon-download'></i> " . $lang->clientName . "</a><ul class='dropdown-menu pull-left'>";
            echo '<li>' . html::a(helper::createLink('misc', 'downloadClient', '', '', true), $lang->downloadClient, '', "title='$lang->downloadClient' class='iframe text-ellipsis' data-width='600'") . '</li>';
            echo '<li>' . html::a($lang->clientHelpLink, $lang->clientHelp, '', "title='$lang->clientHelp' target='_blank'") . '</li>';
            echo '</ul></li>';
        }
    }

    /**
     * Print QR code Link.
     *
     * @param string $color
     *
     * @static
     * @access public
     * @return void
     */
    public static function printQRCodeLink($color = '')
    {
        global $lang;
        echo html::a('javascript:;', "<i class='icon-qrcode'></i>", '', "class='qrCode $color' id='qrcodeBtn' title='{$lang->user->mobileLogin}'");
        echo "<div class='popover top' id='qrcodePopover'><div class='arrow'></div><h3 class='popover-title'>{$lang->user->mobileLogin}</h3><div class='popover-content'><img src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'></div></div>";
        echo '<script>$(function(){$("#qrcodeBtn").click(function(){$("#qrcodePopover").toggleClass("show");}); $("#wrap").click(function(){$("#qrcodePopover").removeClass("show");});});</script>';
        echo '<script>$(function(){$("#qrcodeBtn").hover(function(){$(".popover-content img").attr("src", "' . helper::createLink('misc', 'qrCode') . '");});});</script>';
    }

    /**
     * Print the link contains orderBy field.
     *
     * This method will auto set the orderby param according the params. Fox example, if the order by is desc,
     * will be changed to asc.
     *
     * @param  string $fieldName    the field name to sort by
     * @param  string $orderBy      the order by string
     * @param  string $vars         the vars to be passed
     * @param  string $label        the label of the link
     * @param  string $module       the module name
     * @param  string $method       the method name
     *
     * @access public
     * @return void
     */
    public static function printOrderLink($fieldName, $orderBy, $vars, $label, $module = '', $method = '')
    {
        global $lang, $app;
        if(empty($module)) $module = isset($app->rawModule) ? $app->rawModule : $app->getModuleName();
        if(empty($method)) $method = isset($app->rawMethod) ? $app->rawMethod : $app->getMethodName();
        $className = 'header';
        $isMobile  = $app->viewType === 'mhtml';

        $order = explode('_', $orderBy);
        $order[0] = trim($order[0], '`');
        if($order[0] == $fieldName)
        {
            if(isset($order[1]) and $order[1] == 'asc')
            {
                $orderBy   = "{$order[0]}_desc";
                $className = $isMobile ? 'SortUp' : 'sort-up';
            }
            else
            {
                $orderBy = "{$order[0]}_asc";
                $className = $isMobile ? 'SortDown' : 'sort-down';
            }
        }
        else
        {
            $orderBy   = "" . trim($fieldName, '`') . "" . '_' . 'asc';
            $className = 'header';
        }
        $link = helper::createLink($module, $method, sprintf($vars, $orderBy));
        echo $isMobile ? html::a($link, $label, '', "class='$className' data-app={$app->openApp}") : html::a($link, $label, '', "class='$className' data-app={$app->openApp}");
    }

    /**
     *
     * Print link to an modules' methd.
     *
     * Before printing, check the privilege first. If no privilege, return fasle. Else, print the link, return true.
     *
     * @param string $module    the module name
     * @param string $method    the method
     * @param string $vars      vars to be passed
     * @param string $label     the label of the link
     * @param string $target    the target of the link
     * @param string $misc      others
     * @param bool   $newline
     * @param bool   $onlyBody
     * @param        $object
     *
     * @static
     * @access public
     * @return bool
     */
    public static function printLink($module, $method, $vars = '', $label, $target = '', $misc = '', $newline = true, $onlyBody = false, $object = null)
    {
        /* Add data-app attribute. */
        global $app;
        if(strpos($misc, 'data-app') === false) $misc .= ' data-app="' . $app->openApp . '"';

        if(!commonModel::hasPriv($module, $method, $object)) return false;
        echo html::a(helper::createLink($module, $method, $vars, '', $onlyBody), $label, $target, $misc, $newline);
        return true;
    }

    /**
     *
     * Print link to an modules' methd.
     *
     * 该方法可用于权限不存在的时候，还能输出链接中的文本。
     *
     * @param string $module    the module name
     * @param string $method    the method
     * @param string $vars      vars to be passed
     * @param string $label     the label of the link
     * @param string $target    the target of the link
     * @param string $misc      others
     * @param bool   $newline
     * @param bool   $onlyBody
     * @param        $object
     *
     * @static
     * @access public
     * @return bool
     */
    public static function printLinks($module, $method, $vars = '', $label, $target = '', $misc = '', $newline = true, $onlyBody = false, $object = null)
    {
        global $lang;
        if(empty($label))
        {
            echo $lang->noData;
            return true;
        }

        if($label == $lang->naProduct)
        {
            echo $lang->naProduct;
            return true;
        }

        /* Add data-app attribute. */
        global $app;
        if(strpos($misc, 'data-app') === false) $misc .= ' data-app="' . $app->openApp . '"';

        if(!commonModel::hasPriv($module, $method, $object))
        {
            echo $label;
            return false;
        }

        echo html::a(helper::createLink($module, $method, $vars, '', $onlyBody), $label, $target, $misc, $newline);
        return true;
    }

    /**
     * Print icon of split line.
     *
     * @static
     * @access public
     * @return void
     */
    public static function printDivider()
    {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    /**
     * Print icon of comment.
     *
     * @param string $commentFormLink
     * @param object $object
     *
     * @static
     * @access public
     * @return mixed
     */
    public static function printCommentIcon($commentFormLink, $object = null)
    {
        global $lang;

        if(!commonModel::hasPriv('action', 'comment', $object)) return false;
        echo html::commonButton('<i class="icon icon-chat-line"></i> ' . $lang->action->create, '', 'btn btn-link pull-right btn-comment');
        echo <<<EOD
<div class="modal fade modal-comment">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><i class="icon icon-close"></i></button>
        <h4 class="modal-title">{$lang->action->create}</h4>
      </div>
      <div class="modal-body">
        <form class="load-indicator" action="{$commentFormLink}" target='hiddenwin' method='post'>
          <div class="form-group">
            <textarea id='comment' name='comment' class="form-control" rows="8" autofocus="autofocus"></textarea>
          </div>
          <div class="form-group form-actions text-center">
            <button type="submit" class="btn btn-primary btn-wide">{$lang->save}</button>
            <button type="button" class="btn btn-wide" data-dismiss="modal">{$lang->close}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
$(function()
{
    \$body = $('body', window.parent.document);
    if(\$body.hasClass('hide-modal-close')) \$body.removeClass('hide-modal-close');
});
</script>
EOD;
    }

    /**
     * Build icon button.
     *
     * @param  string $module
     * @param  string $method
     * @param  string $vars
     * @param  object $object
     * @param  string $type button|list
     * @param  string $icon
     * @param  string $target
     * @param  string $extraClass
     * @param  bool   $onlyBody
     * @param  string $misc
     * @static
     * @access public
     * @return void
     */
    public static function buildIconButton($module, $method, $vars = '', $object = '', $type = 'button', $icon = '', $target = '', $extraClass = '', $onlyBody = false, $misc = '', $title = '', $programID = 0, $viewType = '')
    {
        if(isonlybody() and strpos($extraClass, 'showinonlybody') === false) return false;

        /* Remove iframe for operation button in modal. Prevent pop up in modal. */
        if(isonlybody() and strpos($extraClass, 'showinonlybody') !== false) $extraClass = str_replace('iframe', '', $extraClass);

        global $app, $lang, $config;

        /* Add data-app attribute. */
        if(strpos($misc, 'data-app') === false) $misc .= ' data-app="' . $app->openApp . '"';

        /* Judge the $method of $module clickable or not, default is clickable. */
        $clickable = true;
        if(is_object($object))
        {
            if($app->getModuleName() != $module) $app->control->loadModel($module);
            $modelClass = class_exists("ext{$module}Model") ? "ext{$module}Model" : $module . "Model";
            if(class_exists($modelClass) and is_callable(array($modelClass, 'isClickable')))
            {
                $clickable = call_user_func_array(array($modelClass, 'isClickable'), array('object' => $object, 'method' => $method));
            }
        }

        /* Set module and method, then create link to it. */
        if(strtolower($module) == 'story'   and strtolower($method) == 'createcase') ($module = 'testcase') and ($method = 'create');
        if(strtolower($module) == 'bug'     and strtolower($method) == 'tostory')    ($module = 'story') and ($method = 'create');
        if(strtolower($module) == 'bug'     and strtolower($method) == 'createcase') ($module = 'testcase') and ($method = 'create');
        if($config->systemMode == 'classic' and strtolower($module) == 'project') $method = substr(strtolower($method), 3);
        if(!commonModel::hasPriv($module, $method, $object)) return false;
        $link = helper::createLink($module, $method, $vars, $viewType, $onlyBody, $programID);

        /* Set the icon title, try search the $method defination in $module's lang or $common's lang. */
        if(empty($title))
        {
            $title = $method;
            if($method == 'create' and $icon == 'copy') $method = 'copy';
            if(isset($lang->$method) and is_string($lang->$method)) $title = $lang->$method;
            if((isset($lang->$module->$method) or $app->loadLang($module)) and isset($lang->$module->$method))
            {
                $title = $method == 'report' ? $lang->$module->$method->common : $lang->$module->$method;
            }
            if($icon == 'toStory')   $title  = $lang->bug->toStory;
            if($icon == 'createBug') $title  = $lang->testtask->createBug;
        }

        /* set the class. */
        if(!$icon)
        {
            $icon = isset($lang->icons[$method]) ? $lang->icons[$method] : $method;
        }
        if(strpos(',edit,copy,report,export,delete,', ",$method,") !== false) $module = 'common';
        $class = "icon-$module-$method";

        if(!$clickable) $class .= ' disabled';
        if($icon)       $class .= ' icon-' . $icon;


        /* Create the icon link. */
        if($clickable)
        {
            if($app->getViewType() == 'mhtml')
            {
                return "<a data-remote='$link' class='$extraClass' $misc>$title</a>";
            }
            if($type == 'button')
            {
                if($method != 'edit' and $method != 'copy' and $method != 'delete')
                {
                    return html::a($link, "<i class='$class'></i> " . "<span class='text'>{$title}</span>", $target, "class='btn btn-link $extraClass' $misc", true);
                }
                else
                {
                    return html::a($link, "<i class='$class'></i>", $target, "class='btn btn-link $extraClass' title='$title' $misc", false);
                }
            }else if($type == 'view-button'){
                //增加详情页按钮样式
                return html::a($link, "<i class='$class'></i> " . "<span class='text'>{$title}</span>", $target, "class='btn $extraClass' $misc", true);
            }
            else
            {
                return html::a($link, "<i class='$class'></i>", $target, "class='btn $extraClass' title='$title' $misc", false) . "\n";
            }
        }
        else
        {
            if($type == 'list')
            {
                return "<button type='button' class='disabled btn $extraClass'><i class='$class' title='$title' $misc></i></button>\n";
            }
        }
    }

    /**
     * Print link icon.
     *
     * @param  string $module
     * @param  string $method
     * @param  string $vars
     * @param  object $object
     * @param  string $type button|list
     * @param  string $icon
     * @param  string $target
     * @param  string $extraClass
     * @param  bool   $onlyBody
     * @param  string $misc
     * @static
     * @access public
     * @return void
     */
    public static function printIcon($module, $method, $vars = '', $object = '', $type = 'button', $icon = '', $target = '', $extraClass = '', $onlyBody = false, $misc = '', $title = '', $programID = 0, $viewType = '')
    {
        echo common::buildIconButton($module, $method, $vars, $object, $type, $icon, $target, $extraClass, $onlyBody, $misc, $title, $programID, $viewType);
    }

    /**
     * Print backLink and preLink and nextLink.
     *
     * @param string $backLink
     * @param object $preAndNext
     * @param string $linkTemplate
     *
     * @static
     * @access public
     * @return void
     */
    static public function printRPN($backLink, $preAndNext = '', $linkTemplate = '')
    {
        global $lang, $app;
        if(isonlybody()) return false;

        $title = $lang->goback . $lang->backShortcutKey;
        echo html::a($backLink, '<i class="icon-goback icon-back icon-large"></i>', '', "id='back' class='btn' title={$title}");

        if(isset($preAndNext->pre) and $preAndNext->pre)
        {
            $id = (isset($_SESSION['testcaseOnlyCondition']) and !$_SESSION['testcaseOnlyCondition'] and $app->getModuleName() == 'testcase' and isset($preAndNext->pre->case)) ? 'case' : 'id';
            $title = isset($preAndNext->pre->title) ? $preAndNext->pre->title : $preAndNext->pre->name;
            $title = '#' . $preAndNext->pre->$id . ' ' . $title . ' ' . $lang->preShortcutKey;
            $link  = $linkTemplate ? sprintf($linkTemplate, $preAndNext->pre->$id) : inLink('view', "ID={$preAndNext->pre->$id}");
            echo html::a($link, '<i class="icon-pre icon-chevron-left"></i>', '', "id='pre' class='btn' title='{$title}'");
        }
        if(isset($preAndNext->next) and $preAndNext->next)
        {
            $id = (isset($_SESSION['testcaseOnlyCondition']) and !$_SESSION['testcaseOnlyCondition'] and $app->getModuleName() == 'testcase' and isset($preAndNext->next->case)) ? 'case' : 'id';
            $title = isset($preAndNext->next->title) ? $preAndNext->next->title : $preAndNext->next->name;
            $title = '#' . $preAndNext->next->$id . ' ' . $title . ' ' . $lang->nextShortcutKey;
            $link  = $linkTemplate ? sprintf($linkTemplate, $preAndNext->next->$id) : inLink('view', "ID={$preAndNext->next->$id}");
            echo html::a($link, '<i class="icon-pre icon-chevron-right"></i>', '', "id='next' class='btn' title='$title'");
        }
    }

    /**
     * Print back link
     *
     * @param  string $backLink
     * @static
     * @access public
     * @return void
     */
    static public function printBack($backLink, $class = '')
    {
        global $lang, $app;
        if(isonlybody()) return false;

        if(empty($class)) $class = 'btn';
        $title = $lang->goback . $lang->backShortcutKey;
        echo html::a($backLink, '<i class="icon-goback icon-back"></i> ' . $lang->goback, '', "id='back' class='{$class}' title={$title}");
    }

    /**
     * Print pre and next link
     *
     * @param  string $preAndNext
     * @param  string $linkTemplate
     * @static
     * @access public
     * @return void
     */
    public static function printPreAndNext($preAndNext = '', $linkTemplate = '')
    {
        global $lang, $app;
        if(isonlybody()) return false;

        $moduleName = ($app->getModuleName() == 'story' and $app->openApp == 'project') ? 'projectstory' : $app->getModuleName();
        echo "<nav class='container'>";
        if(isset($preAndNext->pre) and $preAndNext->pre)
        {
            $id = (isset($_SESSION['testcaseOnlyCondition']) and !$_SESSION['testcaseOnlyCondition'] and $app->getModuleName() == 'testcase' and isset($preAndNext->pre->case)) ? 'case' : 'id';
            $title = isset($preAndNext->pre->title) ? $preAndNext->pre->title : $preAndNext->pre->name;
            $title = '#' . $preAndNext->pre->$id . ' ' . $title . ' ' . $lang->preShortcutKey;

            $link  = $linkTemplate ? sprintf($linkTemplate, $preAndNext->pre->$id) : helper::createLink($moduleName, 'view', "ID={$preAndNext->pre->$id}");
            $link .= '#app=' . $app->openApp;
            echo html::a($link, '<i class="icon-pre icon-chevron-left"></i>', '', "id='prevPage' class='btn' title='{$title}'");
        }
        if(isset($preAndNext->next) and $preAndNext->next)
        {
            $id = (isset($_SESSION['testcaseOnlyCondition']) and !$_SESSION['testcaseOnlyCondition'] and $app->getModuleName() == 'testcase' and isset($preAndNext->next->case)) ? 'case' : 'id';
            $title = isset($preAndNext->next->title) ? $preAndNext->next->title : $preAndNext->next->name;
            $title = '#' . $preAndNext->next->$id . ' ' . $title . ' ' . $lang->nextShortcutKey;
            $link  = $linkTemplate ? sprintf($linkTemplate, $preAndNext->next->$id) : helper::createLink($moduleName, 'view', "ID={$preAndNext->next->$id}");
            $link .= '#app=' . $app->openApp;
            echo html::a($link, '<i class="icon-pre icon-chevron-right"></i>', '', "id='nextPage' class='btn' title='$title'");
        }
        echo '</nav>';
    }

    /**
     * Create changes of one object.
     *
     * @param mixed $old    the old object
     * @param mixed $new    the new object
     * @param array $extChangeInfo
     * @static
     * @access public
     * @return array
     */
    public static function createChanges($old, $new, $extChangeInfo = [])
    {
        global $app, $config;

        /**
         * 当主状态改变并且未设置子状态的值时把子状态的值设置为默认值并记录日志。
         * Change sub status when status is changed and sub status is not set, and record the changes.
         */
        if(isset($config->bizVersion))
        {
            $oldID        = zget($old, 'id', '');
            $oldStatus    = zget($old, 'status', '');
            $newStatus    = zget($new, 'status', '');
            $newSubStatus = zget($new, 'subStatus', '');

            if($oldID && $oldStatus && $newStatus && !$newSubStatus && $oldStatus != $newStatus)
            {
                $moduleName = $app->getModuleName();

                $field = $app->dbh->query('SELECT options FROM ' . TABLE_WORKFLOWFIELD . " WHERE `module` = '$moduleName' AND `field` = 'subStatus'")->fetch();
                if(!empty($field->options)) $field->options = json_decode($field->options, true);

                if(!empty($field->options[$newStatus]['default']))
                {
                    $flow    = $app->dbh->query('SELECT `table` FROM ' . TABLE_WORKFLOW . " WHERE `module`='$moduleName'")->fetch();
                    $default = $field->options[$newStatus]['default'];

                    $app->dbh->exec("UPDATE `$flow->table` SET `subStatus` = '$default' WHERE `id` = '$oldID'");

                    $new->subStatus = $default;
                }
            }
        }

        $changes    = array();
        foreach($new as $key => $value)
        {
            if(is_object($value) or is_array($value)) continue;
            if(strtolower($key) == 'lastediteddate')  continue;
            if(strtolower($key) == 'lasteditedby')    continue;
            if(strtolower($key) == 'assigneddate')    continue;
            if(strtolower($key) == 'editedby')        continue;
            if(strtolower($key) == 'editeddate')      continue;
            if(strtolower($key) == 'uid')             continue;
            if(strtolower($key) == 'finisheddate' && $value == '')  continue;
            if(strtolower($key) == 'canceleddate' && $value == '')  continue;
            if(strtolower($key) == 'closeddate'   && $value == '')  continue;

            if(isset($old->$key) and is_string($old->$key) and $value != stripslashes($old->$key))
            {
                $diff = '';
                if(substr_count($value, "\n") > 1     or
                    substr_count($old->$key, "\n") > 1 or
                    strpos('name,title,desc,spec,steps,content,digest,verify,report', strtolower($key)) !== false)
                {
                    $diff = commonModel::diff($old->$key, $value);
                }
                $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);
            }
        }

        //新增扩展信息
        if(!empty($extChangeInfo)){
            $diff = '';
            foreach ($extChangeInfo as $key => $val){
                if (is_array($val)){
                    foreach ($val as $subKey => $subVal){
                        $changes[] = array('field' => $key.'_'.$subKey, 'old' => $subVal->old, 'new' =>  $subVal->new, 'diff' => $diff);
                    }
                }else{
                    $changes[] = array('field' => $key, 'old' => $val->old, 'new' =>  $val->new, 'diff' => $diff);
                }
            }
        }
        return $changes;
    }

    /**
     * Create changes of one object.
     *
     * @param mixed $old    the old object
     * @param mixed $new    the new object
     * @param array $extChangeInfo
     * @static
     * @access public
     * @return array
     */
    public static function createNewChanges($old, $new, $extChangeInfo = [])
    {
        global $app, $config;

        /**
         * 当主状态改变并且未设置子状态的值时把子状态的值设置为默认值并记录日志。
         * Change sub status when status is changed and sub status is not set, and record the changes.
         */
        if(isset($config->bizVersion))
        {
            $oldID        = zget($old, 'id', '');
            $oldStatus    = zget($old, 'status', '');
            $newStatus    = zget($new, 'status', '');
            $newSubStatus = zget($new, 'subStatus', '');

            if($oldID && $oldStatus && $newStatus && !$newSubStatus && $oldStatus != $newStatus)
            {
                $moduleName = $app->getModuleName();

                $field = $app->dbh->query('SELECT options FROM ' . TABLE_WORKFLOWFIELD . " WHERE `module` = '$moduleName' AND `field` = 'subStatus'")->fetch();
                if(!empty($field->options)) $field->options = json_decode($field->options, true);

                if(!empty($field->options[$newStatus]['default']))
                {
                    $flow    = $app->dbh->query('SELECT `table` FROM ' . TABLE_WORKFLOW . " WHERE `module`='$moduleName'")->fetch();
                    $default = $field->options[$newStatus]['default'];

                    $app->dbh->exec("UPDATE `$flow->table` SET `subStatus` = '$default' WHERE `id` = '$oldID'");

                    $new->subStatus = $default;
                }
            }
        }

        $changes    = array();
        foreach($new as $key => $value)
        {
            if(is_object($value) or is_array($value)) continue;
            if(strtolower($key) == 'lastediteddate')  continue;
            if(strtolower($key) == 'lasteditedby')    continue;
            if(strtolower($key) == 'assigneddate')    continue;
            if(strtolower($key) == 'editedby')        continue;
            if(strtolower($key) == 'editeddate')      continue;
            if(strtolower($key) == 'uid')             continue;
            if(strtolower($key) == 'finisheddate' && $value == '')  continue;
            if(strtolower($key) == 'canceleddate' && $value == '')  continue;
            if(strtolower($key) == 'closeddate'   && $value == '')  continue;

            if(isset($old->$key) and is_string($old->$key) and $value != stripslashes($old->$key))
            {
                $diff = '';
                if(substr_count($value, "\n") > 1     or
                    substr_count($old->$key, "\n") > 1 or
                    strpos('name,title,desc,spec,steps,content,digest,verify,report', strtolower($key)) !== false)
                {
                    $diff = commonModel::diff($old->$key, $value);
                }
                $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);
            }else if(property_exists($old,$key) and !is_array($old->$key) and $value != stripslashes($old->$key))
            {
                $diff = '';
                if(substr_count($value, "\n") > 1     or
                    substr_count($old->$key, "\n") > 1 or
                    strpos('name,title,desc,spec,steps,content,digest,verify,report', strtolower($key)) !== false)
                {
                    $diff = commonModel::diff($old->$key, $value);
                }
                $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);
            }
        }

        //新增扩展信息
        if(!empty($extChangeInfo)){
            $diff = '';
            foreach ($extChangeInfo as $key => $val){
                if (is_array($val)){
                    foreach ($val as $subKey => $subVal){
                        $changes[] = array('field' => $key.'_'.$subKey, 'old' => $subVal->old, 'new' =>  $subVal->new, 'diff' => $diff);
                    }
                }else{
                    $changes[] = array('field' => $key, 'old' => $val->old, 'new' =>  $val->new, 'diff' => $diff);
                }
            }
        }
        return $changes;
    }

    /**
     * 增加字段校验规则判断逻辑
     * @param $old
     * @param $new
     * @param $extChangeInfo
     * @param $rules 字段校验规则
     * @return array
     */
    public static function createRuleChanges($old, $new, $extChangeInfo = [],$rules=[])
    {
        global $app, $config;

        /**
         * 当主状态改变并且未设置子状态的值时把子状态的值设置为默认值并记录日志。
         * Change sub status when status is changed and sub status is not set, and record the changes.
         */
        if(isset($config->bizVersion))
        {
            $oldID        = zget($old, 'id', '');
            $oldStatus    = zget($old, 'status', '');
            $newStatus    = zget($new, 'status', '');
            $newSubStatus = zget($new, 'subStatus', '');

            if($oldID && $oldStatus && $newStatus && !$newSubStatus && $oldStatus != $newStatus)
            {
                $moduleName = $app->getModuleName();

                $field = $app->dbh->query('SELECT options FROM ' . TABLE_WORKFLOWFIELD . " WHERE `module` = '$moduleName' AND `field` = 'subStatus'")->fetch();
                if(!empty($field->options)) $field->options = json_decode($field->options, true);

                if(!empty($field->options[$newStatus]['default']))
                {
                    $flow    = $app->dbh->query('SELECT `table` FROM ' . TABLE_WORKFLOW . " WHERE `module`='$moduleName'")->fetch();
                    $default = $field->options[$newStatus]['default'];

                    $app->dbh->exec("UPDATE `$flow->table` SET `subStatus` = '$default' WHERE `id` = '$oldID'");

                    $new->subStatus = $default;
                }
            }
        }

        $changes    = array();
        foreach($new as $key => $value)
        {
            if(is_object($value) or is_array($value)) continue;
            if(strtolower($key) == 'lastediteddate')  continue;
            if(strtolower($key) == 'lasteditedby')    continue;
            if(strtolower($key) == 'assigneddate')    continue;
            if(strtolower($key) == 'editedby')        continue;
            if(strtolower($key) == 'editeddate')      continue;
            if(strtolower($key) == 'uid')             continue;
            if(strtolower($key) == 'finisheddate' && $value == '')  continue;
            if(strtolower($key) == 'canceleddate' && $value == '')  continue;
            if(strtolower($key) == 'closeddate'   && $value == '')  continue;

            if(isset($old->$key) and is_string($old->$key) and $value != stripslashes($old->$key) && isset($rules[$key]))
            {
                if($rules[$key] == 'multipleChosen'){
                    $tempValue = trim($value,',');
                    $tempOldValue = trim($old->$key,',');
                    $tempValueArr = explode(',',$tempValue);
                    $tempOldValueArr = explode(',',$tempOldValue);
                    $valueDiff = array_diff($tempValueArr,$tempOldValueArr);
                    $oldValueDiff = array_diff($tempOldValueArr,$tempValueArr);
                    if($valueDiff || $oldValueDiff){
                        $diff = '';
                        if(substr_count($value, "\n") > 1     or
                            substr_count($old->$key, "\n") > 1 or
                            strpos('name,title,desc,spec,steps,content,digest,verify,report', strtolower($key)) !== false)
                        {
                            $diff = commonModel::diff($old->$key, $value);
                        }
                        $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);

                    }else{

                        continue;
                    }

                }

            }else if(isset($old->$key) and is_string($old->$key) and $value != stripslashes($old->$key))
            {
                $diff = '';
                if(substr_count($value, "\n") > 1     or
                    substr_count($old->$key, "\n") > 1 or
                    strpos('name,title,desc,spec,steps,content,digest,verify,report', strtolower($key)) !== false)
                {
                    $diff = commonModel::diff($old->$key, $value);
                }
                $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);
            }else if(property_exists($old,$key) and !is_array($old->$key) and $value != stripslashes($old->$key))
            {
                $diff = '';
                if(substr_count($value, "\n") > 1     or
                    substr_count($old->$key, "\n") > 1 or
                    strpos('name,title,desc,spec,steps,content,digest,verify,report', strtolower($key)) !== false)
                {
                    $diff = commonModel::diff($old->$key, $value);
                }
                $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);
            }
        }

        //新增扩展信息
        if(!empty($extChangeInfo)){
            $diff = '';
            foreach ($extChangeInfo as $key => $val){
                if (is_array($val)){
                    foreach ($val as $subKey => $subVal){
                        $changes[] = array('field' => $key.'_'.$subKey, 'old' => $subVal->old, 'new' =>  $subVal->new, 'diff' => $diff);
                    }
                }else{
                    $changes[] = array('field' => $key, 'old' => $val->old, 'new' =>  $val->new, 'diff' => $diff);
                }
            }
        }
        return $changes;
    }

    /**
     * 从日志中获得自定义信息
     *
     * @param $logChanges
     * @param $specialFiled
     * @return string
     */
    public static function getLogChangesSpecialInfo($logChanges, $specialFiled){
        $specialInfo = '';
        $specialInfoList = [];
        if(!$logChanges){
            return $specialInfo;
        }
        foreach ($logChanges as $val){
            if($val['field'] == $specialFiled){
                $specialInfo = $val['new'];
                return $specialInfo;
                break;
            }else{
                $field = $val['field'];
                $fieldArray = explode('_', $field);
                if(isset($fieldArray[1]) && $fieldArray[0] == $specialFiled){
                    $subFiled = $fieldArray[1];
                    $specialInfoList[$subFiled] = $val['new'];
                }
            }
        }
        if(!empty($specialInfoList)){
            return $specialInfoList;
        }else{
            return  $specialInfo;
        }
    }

    /**
     * Diff two string. (see phpt)
     *
     * @param string $text1
     * @param string $text2
     * @static
     * @access public
     * @return string
     */
    public static function diff($text1, $text2)
    {
        $text1 = str_replace('&nbsp;', '', trim($text1));
        $text2 = str_replace('&nbsp;', '', trim($text2));
        $w  = explode("\n", $text1);
        $o  = explode("\n", $text2);
        $w1 = array_diff_assoc($w,$o);
        $o1 = array_diff_assoc($o,$w);
        $w2 = array();
        $o2 = array();
        foreach($w1 as $idx => $val) $w2[sprintf("%03d<",$idx)] = sprintf("%03d- ", $idx+1) . "<del>" . trim($val) . "</del>";
        foreach($o1 as $idx => $val) $o2[sprintf("%03d>",$idx)] = sprintf("%03d+ ", $idx+1) . "<ins>" . trim($val) . "</ins>";
        $diff = array_merge($w2, $o2);
        ksort($diff);
        return implode("\n", $diff);
    }

    /**
     * Judge Suhosin Setting whether the actual size of post data is large than the setting size.
     *
     * @param  int    $countInputVars
     * @static
     * @access public
     * @return bool
     */
    public static function judgeSuhosinSetting($countInputVars)
    {
        if(extension_loaded('suhosin'))
        {
            $maxPostVars    = ini_get('suhosin.post.max_vars');
            $maxRequestVars = ini_get('suhosin.request.max_vars');
            if($countInputVars > $maxPostVars or $countInputVars > $maxRequestVars) return true;
        }
        else
        {
            $maxInputVars = ini_get('max_input_vars');
            if($maxInputVars and $countInputVars > (int)$maxInputVars) return true;
        }

        return false;
    }

    /**
     * Get the previous and next object.
     *
     * @param  string $type story|task|bug|case
     * @param  string $objectID
     * @access public
     * @return void
     */
    public function getPreAndNextObject($type, $objectID)
    {
        /* Get SQL. */
        $queryCondition    = $type . 'QueryCondition';
        $typeOnlyCondition = $type . 'OnlyCondition';
        $queryCondition    = $this->session->$queryCondition;

        $table   = $this->config->objectTables[$type];
        $orderBy = $type . 'OrderBy';
        $orderBy = $this->session->$orderBy;

        //解决地盘点击任务详情不显示问题
        if($type == 'task' && strpos($orderBy,'mark')){
            $orderBy = explode(',',$orderBy);
            foreach ($orderBy as $key=>$item) {
                if(strpos($item,'mark')){
                    unset($orderBy[$key]);
                }
            }
            $orderBy = implode(',',$orderBy);
        }

        if(empty($queryCondition) or $this->session->$typeOnlyCondition)
        {
            $sql = $this->dao->select('*')->from($table)
                ->beginIF($queryCondition != false)->where($queryCondition)->fi()
                ->beginIF($orderBy != false)->orderBy($orderBy)->fi()
                ->get();
        }
        else
        {
            $sql = $queryCondition . (empty($orderBy) ? '' : " ORDER BY $orderBy");
        }

        /* 没有查询条件时直接返回。 */
        if(stripos($sql, 'where') === false) return false;

        /* Get objectIDList. */
        $objectIdListKey  = $type . 'BrowseList';
        $existsObjectList = $this->session->$objectIdListKey;
        if(empty($existsObjectList) or $existsObjectList['sql'] != $sql)
        {
            $queryObjects = $this->dao->query($sql);
            $objectList   = array();
            while($object = $queryObjects->fetch())
            {
                $key = (!$this->session->$typeOnlyCondition and $type == 'testcase' and isset($object->case)) ? 'case' : 'id';
                $id  = $object->$key;
                $objectList[$id] = $object;
            }

            $this->session->set($objectIdListKey, array('sql' => $sql, 'objectList' => $objectList), $this->app->openApp);
            $existsObjectList = $this->session->$objectIdListKey;
        }

        $preAndNextObject       = new stdClass();
        $preAndNextObject->pre  = '';
        $preAndNextObject->next = '';

        $preObj = false;
        foreach($existsObjectList['objectList'] as $id => $object)
        {
            /* Get next object. */
            if($preObj === true)
            {
                $preAndNextObject->next = $object;
                break;
            }

            /* Get pre object. */
            if($id == $objectID)
            {
                if($preObj) $preAndNextObject->pre = $preObj;
                $preObj = true;
            }
            if($preObj !== true) $preObj = $object;
        }

        return $preAndNextObject;
    }

    /**
     * Save one executed query.
     *
     * @param  string    $sql
     * @param  string    $objectType story|task|bug|testcase
     * @access public
     * @return void
     */
    public function saveQueryCondition($sql, $objectType, $onlyCondition = true)
    {
        /* Set the query condition session. */
        if($onlyCondition)
        {
            $queryCondition = explode(' WHERE ', $sql);
            $queryCondition = isset($queryCondition[1]) ? $queryCondition[1] : '';
            if($queryCondition)
            {
                $queryCondition = explode(' ORDER BY ', $queryCondition);
                $queryCondition = str_replace('t1.', '', $queryCondition[0]);
            }
        }
        else
        {
            $queryCondition = explode(' ORDER BY ', $sql);
            $queryCondition = $queryCondition[0];
        }
        $queryCondition = trim($queryCondition);
        if(empty($queryCondition)) $queryCondition = "1=1";
        $this->session->set($objectType . 'QueryCondition', $queryCondition, $this->app->openApp);
        $this->session->set($objectType . 'OnlyCondition', $onlyCondition, $this->app->openApp);

        /* Set the query condition session. */
        $orderBy = explode(' ORDER BY ', $sql);
        $orderBy = isset($orderBy[1]) ? $orderBy[1] : '';
        if($orderBy)
        {
            $orderBy = explode(' LIMIT ', $orderBy);
            $orderBy = $orderBy[0];
            if($onlyCondition) $orderBy = str_replace('t1.', '', $orderBy);
        }
        $this->session->set($objectType . 'OrderBy', $orderBy, $this->app->openApp);
        $this->session->set($objectType . 'BrowseList', array(), $this->app->openApp);
    }

    /**
     * Remove duplicate for story, task, bug, case, doc.
     *
     * @param  string       $type  e.g. story task bug case doc.
     * @param  array|object $data
     * @param  string       $condition
     * @access public
     * @return array
     */
    public function removeDuplicate($type, $data = '', $condition = '')
    {
        $table      = $this->config->objectTables[$type];
        $titleField = $type == 'task' ? 'name' : 'title';
        $date       = date(DT_DATETIME1, time() - $this->config->duplicateTime);
        $dateField  = $type == 'doc' ? 'addedDate' : 'openedDate';
        $titles     = $data->$titleField;

        if(empty($titles)) return false;
        $duplicate = $this->dao->select("id,$titleField")->from($table)
            ->where('deleted')->eq(0)
            ->andWhere($titleField)->in($titles)
            ->andWhere($dateField)->ge($date)->fi()
            ->beginIF($condition)->andWhere($condition)->fi()
            ->fetchPairs();

        if($duplicate and is_string($titles)) return array('stop' => true, 'duplicate' => key($duplicate));
        if($duplicate and is_array($titles))
        {
            foreach($titles as $i => $title)
            {
                if(in_array($title, $duplicate)) unset($titles[$i]);
            }
            $data->$titleField = $titles;
        }
        return array('stop' => false, 'data' => $data);
    }

    /**
     * Append order by.
     *
     * @param  string $orderBy
     * @param  string $append
     * @access public
     * @return string
     */
    public function appendOrder($orderBy, $append = 'id')
    {
        if(empty($orderBy)) return $append;

        list($firstOrder) = explode(',', $orderBy);
        $sort = strpos($firstOrder, '_') === false ? '_asc' : strstr($firstOrder, '_');
        return strpos($orderBy, $append) === false ? $orderBy . ',' . $append . $sort : $orderBy;
    }

    /**
     * Check field exists
     *
     * @param  string    $table
     * @param  string    $field
     * @access public
     * @return bool
     */
    public function checkField($table, $field)
    {
        $fields   = $this->dao->query("DESC $table")->fetchAll();
        $hasField = false;
        foreach($fields as $fieldObj)
        {
            if($field == $fieldObj->Field)
            {
                $hasField = true;
                break;
            }
        }
        return $hasField;
    }

    /**
     * Check safe file.
     *
     * @access public
     * @return string|false
     */
    public function checkSafeFile()
    {
        if($this->app->getModuleName() == 'upgrade' and $this->session->upgrading) return false;

        $statusFile = $this->app->getAppRoot() . 'www' . DIRECTORY_SEPARATOR . 'ok.txt';
        return (!is_file($statusFile) or (time() - filemtime($statusFile)) > 3600) ? $statusFile : false;
    }

    /**
     * Check upgrade's status file is ok or not.
     *
     * @access public
     * @return void
     */
    public function checkUpgradeStatus()
    {
        $statusFile = $this->checkSafeFile();
        if($statusFile)
        {
            $this->app->loadLang('upgrade');
            $cmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? $this->lang->upgrade->createFileWinCMD : $this->lang->upgrade->createFileLinuxCMD;
            $cmd = sprintf($cmd, $statusFile);

            echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body>";
            echo "<table align='center' style='margin-top:100px; border:1px solid gray; font-size:14px;padding:8px;'><tr><td>";
            printf($this->lang->upgrade->setStatusFile, $cmd, $statusFile);
            die('</td></tr></table></body></html>');
        }
    }

    /**
     * Check the user has permission to access this method, if not, locate to the login page or deny page.
     *
     * @access public
     * @return void
     */
    public function checkPriv()
    {
        $this->loadExtension('flow')->mergeLangFromFlow();
        $module = $this->app->getModuleName();
        $method = $this->app->getMethodName();
        if($this->app->isFlow)
        {
            $module = $this->app->rawModule;
            $method = $this->app->rawMethod;
        }

        if(!empty($this->app->user->modifyPassword) and (($module != 'my' or $method != 'changepassword') and ($module != 'user' or $method != 'logout'))) die(js::locate(helper::createLink('my', 'changepassword', '', '', true)));
        if($this->isOpenMethod($module, $method)) return true;
        if(!$this->loadModel('user')->isLogon() and $this->server->php_auth_user) $this->user->identifyByPhpAuth();
        if(!$this->loadModel('user')->isLogon() and $this->cookie->za) $this->user->identifyByCookie();

        if(isset($this->app->user))
        {
            $this->app->user = $this->session->user;

            $inProject = (isset($this->lang->navGroup->$module) && $this->lang->navGroup->$module == 'project');
            if(!defined('IN_UPGRADE') and $inProject)
            {
                /* Check program priv. */
                if($this->session->project and strpos(",{$this->app->user->view->projects},", ",{$this->session->project},") === false and !$this->app->user->admin) $this->loadModel('project')->accessDenied();
                $this->resetProgramPriv($module, $method);
                if(!commonModel::hasPriv($module, $method)) $this->deny($module, $method, false);
            }

            if(!commonModel::hasPriv($module, $method)) $this->deny($module, $method);
        }
        else
        {
            $referer  = helper::safe64Encode($this->app->getURI(true));
            $this->app->loadConfig('cas');
            $loginMod = empty($this->config->cas->turnon) ? 'user' : 'cas';
            die(js::locate(helper::createLink($loginMod, 'login', "referer=$referer")));
        }
    }

    /**
     * Check the user has permisson of one method of one module.
     *
     * @param  string $module
     * @param  string $method
     * @static
     * @access public
     * @return bool
     */
    public static function hasPriv($module, $method, $object = null)
    {
        global $app, $lang;
        $module = strtolower($module);
        $method = strtolower($method);
        $means = common::getSpecialMethods($module, $method);//相关权限设置
        $module = $means[0];
        $method = $means[1];
        if(in_array(strtolower($module .'-'. $method), self::$openURIs)) return true;
        /* Check the parent object is closed. */
        if(!empty($method) and strpos('close|batchclose', $method) === false and !commonModel::canBeChanged($module, $object)) return false;

        /* Check is the super admin or not. */
        if(!empty($app->user->admin) || strpos($app->company->admins, ",{$app->user->account},") !== false) return true;
        /* If is the program admin, have all program privs. */
        $inProject = isset($lang->navGroup->$module) && $lang->navGroup->$module == 'project';
        if($inProject && $app->session->project && strpos(",{$app->user->rights['projects']},", ",{$app->session->project},") !== false) return true;
        /* If module is project and method is execution, check for all execution privilege. */
        if($module == 'project' and $method == 'execution')
        {
            $module = 'execution';
            $method = 'all';
        }

        /* If not super admin, check the rights. */
        $rights = $app->user->rights['rights'];
        $acls   = $app->user->rights['acls'];
        if((($app->user->account != 'guest') or ($app->company->guest and $app->user->account == 'guest')) and $module == 'report' and $method == 'annualdata') return true;
        if(isset($rights[$module][$method]))
        {
            if(!commonModel::hasDBPriv($object, $module, $method)) return false;
            if(empty($acls['views'])) return true;
            $menu = isset($lang->navGroup->$module) ? $lang->navGroup->$module : $module;
            $menu = strtolower($menu);
            if($menu != 'qa' and !isset($lang->$menu->menu)) return true;
            if($menu == 'my' or $menu == 'index' or $module == 'tree') return true;
            if($module == 'company' and $method == 'dynamic') return true;
            if($module == 'action' and $method == 'editcomment') return true;
            if($module == 'action' and $method == 'comment') return true;
            if(!isset($acls['views'][$menu])) return false;

            return true;
        }

        return false;
    }

    /**
     * Reset program priv.
     *
     * @param  string $module
     * @param  string $method
     * @static
     * @access public
     * @return void
     */
    public function resetProgramPriv($module, $method)
    {
        /* Get user program priv. */
        if(!$this->app->session->project) return;
        $program       = $this->dao->findByID($this->app->session->project)->from(TABLE_PROJECT)->fetch();
        $programRights = $this->dao->select('t3.module, t3.method')->from(TABLE_GROUP)->alias('t1')
            ->leftJoin(TABLE_USERGROUP)->alias('t2')->on('t1.id = t2.group')
            ->leftJoin(TABLE_GROUPPRIV)->alias('t3')->on('t2.group=t3.group')
            ->where('t1.project')->eq($program->id)
            ->andWhere('t2.account')->eq($this->app->user->account)
            ->fetchAll();

        /* Group priv by module the same as rights. */
        $programRightGroup = array();
        foreach($programRights as $programRight) $programRightGroup[$programRight->module][$programRight->method] = 1;

        /* Reset priv by program privway. */
        $this->app->user->rights = $this->loadModel('user')->authorize($this->app->user->account);
        $rights = $this->app->user->rights['rights'];
        if($program->auth == 'extend') $this->app->user->rights['rights'] = array_merge_recursive($programRightGroup, $rights);
        if($program->auth == 'reset')
        {
            /* If priv way is reset, unset common program priv, and cover by program priv. */
            foreach($rights as $moduleKey => $methods)
            {
                if(in_array($moduleKey, $this->config->programPriv->waterfall)) unset($rights[$moduleKey]);
            }

            $this->app->user->rights['rights'] = array_merge($rights, $programRightGroup);
        }
    }

    /**
     * Check db priv.
     *
     * @param  object $object
     * @param  string $module
     * @param  string $method
     * @static
     * @access public
     * @return void
     */
    public static function hasDBPriv($object, $module = '', $method = '')
    {
        global $app;

        if(!empty($app->user->admin)) return true;
        if($module == 'todo' and ($method == 'create' or $method == 'batchcreate')) return true;
        if($module == 'effort' and ($method == 'batchcreate' or $method == 'createforobject')) return true;

        /* Limited execution. */
        $limitedExecution = false;
        if(!empty($module) && $module == 'task' && !empty($object->execution) or
            !empty($module) && $module == 'execution' && !empty($object->id)
        )
        {
            $objectID = '';
            if($module == 'execution' and !empty($object->id))  $objectID = $object->id;
            if($module == 'task' and !empty($object->execution))$objectID = $object->execution;

            $limitedExecutions = !empty($_SESSION['limitedExecutions']) ? $_SESSION['limitedExecutions'] : '';
            if($objectID and strpos(",{$limitedExecutions},", ",$objectID,") !== false) $limitedExecution = true;
        }
        if(empty($app->user->rights['rights']['my']['limited']) && !$limitedExecution) return true;

        if(!empty($method) && strpos($method, 'batch')  === 0) return false;
        if(!empty($method) && strpos($method, 'link')   === 0) return false;
        if(!empty($method) && strpos($method, 'create') === 0) return false;
        if(!empty($method) && strpos($method, 'import') === 0) return false;

        if(empty($object)) return true;

        if(!empty($object->openedBy)      && $object->openedBy     == $app->user->account or
            !empty($object->addedBy)      && $object->addedBy      == $app->user->account or
            !empty($object->account)      && $object->account      == $app->user->account or
            !empty($object->assignedTo)   && $object->assignedTo   == $app->user->account or
            !empty($object->finishedBy)   && $object->finishedBy   == $app->user->account or
            !empty($object->canceledBy)   && $object->canceledBy   == $app->user->account or
            !empty($object->closedBy)     && $object->closedBy     == $app->user->account or
            !empty($object->lastEditedBy) && $object->lastEditedBy == $app->user->account)
        {
            return true;
        }

        return false;
    }

    /**
     * Check whether IP in white list.
     *
     * @param  string $ipWhiteList
     * @access public
     * @return bool
     */
    public function checkIP($ipWhiteList = '')
    {
        $ip = helper::getRemoteIp();

        if(!$ipWhiteList) $ipWhiteList = $this->config->ipWhiteList;

        /* If the ip white list is '*'. */
        if($ipWhiteList == '*') return true;

        /* The ip is same as ip in white list. */
        if($ip == $ipWhiteList) return true;

        /* If the ip in white list is like 192.168.1.1-192.168.1.10. */
        if(strpos($ipWhiteList, '-') !== false)
        {
            list($min, $max) = explode('-', $ipWhiteList);
            $min = ip2long(trim($min));
            $max = ip2long(trim($max));
            $ip  = ip2long(trim($ip));

            return $ip >= $min and $ip <= $max;
        }

        /* If the ip in white list is in IP/CIDR format eg 127.0.0.1/24. Thanks to zcat. */
        if(strpos($ipWhiteList, '/') == false) $ipWhiteList .= '/32';
        list($ipWhiteList, $netmask) = explode('/', $ipWhiteList, 2);

        $ip          = ip2long($ip);
        $ipWhiteList = ip2long($ipWhiteList);
        $wildcard    = pow(2, (32 - $netmask)) - 1;
        $netmask     = ~ $wildcard;

        return (($ip & $netmask) == ($ipWhiteList & $netmask));
    }

    /**
     * Get the full url of the system.
     *
     * @access public
     * @return string
     */
    public static function getSysURL()
    {
        $httpType = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? 'https' : 'http';
        if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') $httpType = 'https';
        $httpHost = $_SERVER['HTTP_HOST'] ?? "127.0.0.1"; //cli模式没有HTTP_HOST
        return "$httpType://$httpHost";
    }

    /**
     * Check whether view type is tutorial
     * @access public
     * @return boolean
     */
    public static function isTutorialMode()
    {
        return (isset($_SESSION['tutorialMode']) and $_SESSION['tutorialMode']);
    }

    /**
     * Convert items to Pinyin.
     *
     * @param  array    $items
     * @static
     * @access public
     * @return array
     */
    public static function convert2Pinyin($items)
    {
        foreach ($items as $k => $v){
            $items[$k] = preg_replace("/\t/",' ',$v);
        }
        global $app;
        static $allConverted = array();
        static $pinyin;
        if(empty($pinyin)) $pinyin = $app->loadClass('pinyin');

        $sign = ' aNdAnD ';
        $notConvertedItems = array_diff($items, array_keys($allConverted));

        if($notConvertedItems)
        {
            $convertedPinYin = $pinyin->romanize(join($sign, $notConvertedItems));
            $itemsPinYin     = explode(trim($sign), $convertedPinYin);
            foreach($notConvertedItems as $item)
            {
                $itemPinYin  = array_shift($itemsPinYin);
                $wordsPinYin = explode("\t", trim($itemPinYin));

                $abbr = '';
                foreach($wordsPinYin as $i => $wordPinyin)
                {
                    if($wordPinyin) $abbr .= $wordPinyin[0];
                }

                $allConverted[$item] = strtolower(join($wordsPinYin) . ' ' . $abbr);
            }
        }

        $convertedItems = array();
        foreach($items as $item) $convertedItems[$item] = zget($allConverted, $item, null);

        return $convertedItems;
    }

    /**
     * Check an entry.
     *
     * @access public
     * @return void
     */
    public function checkEntry()
    {
        $this->loadModel('entry');
        if($this->session->valid_entry)
        {
            if(!$this->session->entry_code) $this->response('SESSION_CODE_MISSING');
            if($this->session->valid_entry != md5(md5($this->get->code) . $this->server->remote_addr)) $this->response('SESSION_VERIFY_FAILED');
            return true;
        }

        if(!$this->get->code)  $this->response('PARAM_CODE_MISSING');
        if(!$this->get->token) $this->response('PARAM_TOKEN_MISSING');

        $entry = $this->entry->getByCode($this->get->code);
        if(!$entry)                              $this->response('EMPTY_ENTRY');
        if(!$entry->key)                         $this->response('EMPTY_KEY');
        if(!$this->checkIP($entry->ip))          $this->response('IP_DENIED');
        if(!$this->checkEntryToken($entry))      $this->response('INVALID_TOKEN');
        if($entry->freePasswd == 0 and empty($entry->account)) $this->response('ACCOUNT_UNBOUND');

        $isFreepasswd = ($_GET['m'] == 'user' and strtolower($_GET['f']) == 'apilogin' and $_GET['account'] and $entry->freePasswd);
        if($isFreepasswd) $entry->account = $_GET['account'];

        $user = $this->dao->findByAccount($entry->account)->from(TABLE_USER)->andWhere('deleted')->eq(0)->fetch();
        if(!$user) $this->response('INVALID_ACCOUNT');

        $this->loadModel('user');
        $user->last   = time();
        $user->rights = $this->user->authorize($user->account);
        $user->groups = $this->user->getGroups($user->account);
        $user->view   = $this->user->grantUserView($user->account, $user->rights['acls']);
        $user->admin  = strpos($this->app->company->admins, ",{$user->account},") !== false;
        $this->session->set('user', $user);
        $this->app->user = $user;

        $this->dao->update(TABLE_USER)->set('last')->eq($user->last)->where('account')->eq($user->account)->exec();
        $this->loadModel('action')->create('user', $user->id, 'login');
        $this->loadModel('score')->create('user', 'login');

        if($isFreepasswd) die(js::locate($this->config->webRoot));

        $this->session->set('ENTRY_CODE', $this->get->code);
        $this->session->set('VALID_ENTRY', md5(md5($this->get->code) . $this->server->remote_addr));
        $this->loadModel('entry')->saveLog($entry->id, $this->server->request_uri);

        /* Add for task #5384. */
        if($_SERVER['REQUEST_METHOD'] == 'POST' and empty($_POST))
        {
            $post = file_get_contents("php://input");
            if(!empty($post)) $post  = json_decode($post, true);
            if(!empty($post)) $_POST = $post;
        }

        unset($_GET['code']);
        unset($_GET['token']);
    }

    /**
     * Check token of an entry.
     *
     * @param  object $entry
     * @access public
     * @return bool
     */
    public function checkEntryToken($entry)
    {
        return true;
        parse_str($this->server->query_String, $queryString);
        unset($queryString['token']);

        /* Change for task #5384. */
        if(isset($queryString['time']))
        {
            $timestamp = $queryString['time'];
            if(strlen($timestamp) > 10) $timestamp = substr($timestamp, 0, 10);
            if(strlen($timestamp) != 10 or $timestamp[0] >= '4') $this->response('ERROR_TIMESTAMP');

            $result = $this->get->token == md5($entry->code . $entry->key . $queryString['time']);
            if($result)
            {
                if($timestamp <= $entry->calledTime) $this->response('CALLED_TIME');
                $this->loadModel('entry')->updateTime($entry->code, $timestamp);
                unset($_GET['time']);
                return $result;
            }
        }

        $queryString = http_build_query($queryString);

        return $this->get->token == md5(md5($queryString) . $entry->key);
    }

    /**
     * Check Not CN Lang.
     *
     * @static
     * @access public
     * @return bool
     */
    public static function checkNotCN()
    {
        global $app;
        return strpos('|zh-cn|zh-tw|', '|' . $app->getClientLang() . '|') === false;
    }

    /**
     * Check the object can be changed.
     *
     * @param  string $module
     * @param  object $object
     * @static
     * @access public
     * @return bool
     */
    public static function canBeChanged($module, $object = null)
    {
        global $app, $config;
        static $productsStatus   = array();
        static $executionsStatus = array();

        /* Check the product is closed. */
        if(!empty($object->product) and is_numeric($object->product) and empty($config->CRProduct))
        {
            if(!isset($productsStatus[$object->product]))
            {
                $product = $app->control->loadModel('product')->getByID($object->product);
                $productsStatus[$object->product] = $product ? $product->status : '';
            }
            if($productsStatus[$object->product] == 'closed') return false;
        }

        /* Check the execution is closed. */
        $productModuleList = array('story', 'bug', 'testtask');
        if(!in_array($module, $productModuleList) and !empty($object->execution) and is_numeric($object->execution) and empty($config->CRExecution))
        {
            if(!isset($executionsStatus[$object->execution]))
            {
                $execution = $app->control->loadModel('execution')->getByID($object->execution);
                $executionsStatus[$object->execution] = $execution ? $execution->status : '';
            }
            if($executionsStatus[$object->execution] == 'closed') return false;
        }

        return true;
    }

    /**
     * Check object can modify.
     *
     * @param  string $type    product|Execution
     * @param  object $object
     * @static
     * @access public
     * @return bool
     */
    public static function canModify($type, $object)
    {
        global $config;

        if($type == 'product'   and empty($config->CRProduct)   and $object->status == 'closed') return false;
        if($type == 'execution' and empty($config->CRExecution) and $object->status == 'closed') return false;

        return true;
    }

    /**
     * Response.
     *
     * @param  string $code
     * @access public
     * @return void
     */
    public function response($code)
    {
        $response = new stdclass();
        $response->errcode = $this->config->entry->errcode[$code];
        $response->errmsg  = $this->lang->entry->errmsg[$code];

        die(helper::jsonEncode($response));
    }

    /**
     * Http.
     *
     * @param  string       $url
     * @param  string|array $data
     * @param  array        $options   This is option and value pair, like CURLOPT_HEADER => true. Use curl_setopt function to set options.
     * @param  array        $headers   Set request headers.
     * @static
     * @access public
     * @return string
     */
    public static function http($url, $data = null, $options = array(), $headers = array())
    {
        global $lang, $app;
        if(!extension_loaded('curl')) return json_encode(array('result' => 'fail', 'message' => $lang->error->noCurlExt));

        commonModel::$requestErrors = array();

        if(!is_array($headers)) $headers = (array)$headers;
        $headers[] = "API-RemoteIP: " . zget($_SERVER, 'REMOTE_ADDR', '');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);

        if(!empty($data))
        {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if($options) curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $errors   = curl_error($curl);
        curl_close($curl);

        $logFile = $app->getLogRoot() . 'saas.'. date('Ymd') . '.log.php';
        if(!file_exists($logFile)) file_put_contents($logFile, '<?php die(); ?' . '>');

        $fh = @fopen($logFile, 'a');
        if($fh)
        {
            fwrite($fh, date('Ymd H:i:s') . ": " . $app->getURI() . "\n");
            fwrite($fh, "url:    " . $url . "\n");
            if(!empty($data)) fwrite($fh, "data:   " . print_r($data, true) . "\n");
            fwrite($fh, "results:" . print_r($response, true) . "\n");
            if(!empty($errors)) fwrite($fh, "errors: " . $errors . "\n");
            fclose($fh);
        }

        if($errors) commonModel::$requestErrors[] = $errors;

        return $response;
    }

    /**
     * Set main menu.
     *
     * @static
     * @access public
     * @return string
     */
    public static function setMainMenu()
    {
        global $app, $lang;

        $openApp = $app->openApp;

        /* If homeMenu is not exists or unset, display menu. */
        if(!isset($lang->$openApp->homeMenu))
        {
            $lang->menu      = $lang->$openApp->menu;
            $lang->menuOrder = $lang->$openApp->menuOrder;
            return;
        }

        if($app->rawModule == $openApp && $app->rawMethod == 'create')
        {
            $lang->menu = $lang->$openApp->homeMenu;
            return;
        }

        /* If the method is in homeMenu, display homeMenu. */
        foreach($lang->$openApp->homeMenu as $menu)
        {
            $link   = is_array($menu) ? $menu['link'] : $menu;
            $params = explode('|', $link);
            $method = $params[2];

            if($method == $app->rawMethod)
            {
                $lang->menu = $lang->$openApp->homeMenu;
                return;
            }

            if(isset($menu['alias']) and in_array(strtolower($app->rawMethod), explode(',', strtolower($menu['alias']))))
            {
                $lang->menu = $lang->$openApp->homeMenu;
                return;
            }
        }

        /* Default, display menu. */
        $lang->menu      = $lang->$openApp->menu;
        $lang->menuOrder = $lang->$openApp->menuOrder;
    }

    /**
     * Get relations for two object.
     *
     * @param  varchar $atype
     * @param  int     $aid
     * @param  varchar $btype
     * @param  int     $bid
     *
     * @access public
     * @return string
     */
    public function getRelations($AType = '', $AID = 0, $BType = '', $BID = 0)
    {
        return $this->dao->select('*')->from(TABLE_RELATION)
            ->where('AType')->eq($AType)
            ->andWhere('AID')->eq($AID)
            ->andwhere('BType')->eq($BType)
            ->beginif($BID)->andwhere('BID')->eq($BID)->fi()
            ->fetchAll();
    }

    /**
     * Replace the %s of one key of a menu by objectID or $params.
     *
     * All the menus are defined in the common's language file. But there're many dynamic params, so in the defination,
     * we used %s as placeholder. These %s should be setted in one module.
     *
     * @param  string  $moduleName
     * @param  int     $objectID
     * @param  array   $params
     *
     * @access public
     * @return string
     */
    static public function setMenuVars($moduleName, $objectID, $params = array())
    {
        global $lang;

        foreach($lang->$moduleName->menu as $label => $menu)
        {
            if($moduleName == 'project' and $label == 'task') continue;

            $lang->$moduleName->menu->$label = self::setMenuVarsEx($menu, $objectID, $params, $moduleName);
            if(isset($menu['subMenu']))
            {
                foreach($menu['subMenu'] as $key1 => $subMenu)
                {
                    $lang->$moduleName->menu->{$label}['subMenu']->$key1 = self::setMenuVarsEx($subMenu, $objectID, $params, $moduleName);
                }
            }

            if(!isset($menu['dropMenu'])) continue;

            foreach($menu['dropMenu'] as $key2 => $dropMenu)
            {
                $lang->$moduleName->menu->{$label}['dropMenu']->$key2 = self::setMenuVarsEx($dropMenu, $objectID, $params);

                if(!isset($dropMenu['subMenu'])) continue;

                foreach($dropMenu['subMenu'] as $key3 => $subMenu)
                {
                    $lang->$moduleName->menu->{$label}['dropMenu']->$key3 = self::setMenuVarsEx($subMenu, $objectID, $params);
                }
            }
        }

        /* If objectID is set, cannot use homeMenu. */
        unset($lang->$moduleName->homeMenu);
    }

    /*
     * Replace the %s of one key of a menu by objectID or $params.
     * @param  object  $menu
     * @param  int     $objectID
     * @param  array   $params
     */
    static public function setMenuVarsEx($menu, $objectID, $params = array(), $moduleName = '')
    {
        if(is_array($menu))
        {
            if(!isset($menu['link'])) return $menu;

            $link = $menu['link'];

            $specialMenu   = array();
            $specialMenu[] = '实验室缺陷|project|bug|projectID=%s&applicationID=%s&productID=%s';
            $specialMenu[] = '套件|project|testsuite|projectID=%s&applicationID=%s&productID=%s';
            $specialMenu[] = '用例|project|testcase|projectID=%s&applicationID=%s&productID=%s';
            $specialMenu[] = '测试单|project|testtask|projectID=%s&applicationID=%s&productID=%s';
            $specialMenu[] = '测试报告|project|testreport|projectID=%s&applicationID=%s&productID=%s';

            if(in_array($link, $specialMenu))
            {
                if(empty($params)) $params = array('projectID' => $objectID, 'applicationID' => 0, 'productID' => 'all');
                $menu['link'] = vsprintf($link, $params);
            }
            elseif($moduleName == 'qa')
            {
                $link = $menu['link'];
                $menu['link'] = vsprintf($link, $params);
            }
            else
            {
                $link = sprintf($menu['link'], $objectID);
                $menu['link'] = vsprintf($link, $params);
            }
        }
        else
        {
            $menu = sprintf($menu, $objectID);
            $menu = vsprintf($menu, $params);
        }

        return $menu;
    }

    /*
     * 相关权限设置
     */
    public static function getSpecialMethods($module, $method)
    {
        //关联版本与管理产品权限相同
        if($module == 'project' && $method == 'manageproductplans'){
            $method = 'manageproducts';
            return [$module, $method];
        }
        if($module == 'projectplan' && $method == 'relationexec'){
            $module = 'project';
            $method = 'manageproducts';
            return [$module, $method];
        }
        return [$module, $method];
    }

    public function getActionLines($actions, $users)
    {
        $this->loadModel('action');
        $i = 1;
        $list  = [];
        foreach($actions as $action){
            $line = '';
            $i ++;
            $action->actor = zget($users, $action->actor);
            if($action->action == 'assigned') $action->extra = zget($users, $action->extra);
            if(strpos($action->actor, ':') !== false) $action->actor = substr($action->actor, strpos($action->actor, ':') + 1);

            $line .= $this->action->returnAction($action);
            if(!empty($action->history)){
                $line .= $this->action->printChanges($action->objectType, $action->history);
            }
            if(strlen(trim(($action->comment))) != 0) {

                if (isset($defaultComment)) {
                    $line .= strip_tags($action->comment) == $action->comment ? nl2br($action->comment) : $action->comment;
                } else {
                    $line .= strip_tags($action->comment) == $action->comment ? nl2br($action->comment) : $action->comment;
                }

            }
            $info['time'] = $action->date;
            $info['line'] = strip_tags(br2nl($line));
            $list[] = $info;
        }
        return $list;
    }

    /**
     * @Notes: 检查时间控件
     * @Date: 2023/5/26
     * @Time: 15:58
     * @Interface checkJkDateTime
     * @param $datetime
     * @return bool
     */
    public  function checkJkDateTime($datetime)
    {
        if(empty($datetime)) return false;
        if($datetime == '0000-00-00') return false;
        if($datetime == '0000-00-00 00:00:00') return false;
        $date = substr($datetime, 0, 10);
        if(preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) return checkdate($parts[2], $parts[3], $parts[1]);
        return false;
    }

    /**
     * 通过介质发送文件
     * @param $fileList
     * @return void
     */
    public function sendFileBySftp($fileList,$type='',$code=''){
        if(empty($fileList)){
            return [];
        }
        $config = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('qzSftpList')->fetchPairs('key');
        $conn = ssh2_connect($config['host'], $config['port']);   //登陆远程服务器
        //用户名密码验证
        if(!ssh2_auth_password($conn, $config['username'], $config['password'])) {
            dao::$errors[''] = $this->lang->sftperror;
            return false;
        }
        $sftp = ssh2_sftp($conn);
        $remoteFileList = array();
        foreach ($fileList as $file){
            if (file_exists($file->realPath) and !empty($file->realPath)) {
                $targetPath = date('/Y-m-d/');
                if($code != ''){
                    $targetPath = $targetPath.$code.'/';
                }
                $lastName = $file->title;
                $remotePath = $config['filedirectory'] . $targetPath . $lastName;
                $targetPath = dirname($remotePath);
                $res = ssh2_sftp_mkdir($sftp, $targetPath, 0777, true);
                $resource = "ssh2.sftp://".intval($sftp) . $remotePath;
                $res = copy($file->realPath, $resource);   //将文件复制到远程
                if($res) {
                    $md5 = md5_file($file->realPath);
                    if ($this->checkMd5Qz($remotePath, $md5, $config['checkmd5url']) == 1) {
                        if($type == 'secondorder'){
                            array_push($remoteFileList, array('url' => $remotePath, 'md5' => $md5, 'name' => $lastName));
                        }else if($type == 'copyrightqz'){
                            array_push($remoteFileList, array('url' => $remotePath, 'md5Url' => $md5, 'fileName' => $lastName));
                        }else if($type == 'problem'){
                            array_push($remoteFileList, array('url' => $remotePath, 'md5Url' => $md5, 'fileName' => $lastName));
                        }else{
                            array_push($remoteFileList, array('url' => $remotePath, 'md5' => $md5, 'name' => $lastName));
                        }
                    }else{
                        dao::$errors[''] = $this->lang->md5error;
                        return false;
                    }
                }else{
                    dao::$errors[''] = $this->lang->neterror;
                    return false;
                }
            }else{
                dao::$errors[''] = $file->title.$this->lang->fileerror;
                return false;
            }
        }
        return $remoteFileList;
    }

    /**
     * 远程校验md5值
     * @param $filePath
     * @param $md5
     * @return int|mixed
     */
    public function checkMd5Qz($filePath, $md5, $url)
    {
        $curl = $url;
        $data['filePath'] = $filePath;
        $data['md5'] = $md5;
        $res = $this->curl($curl, $data);
        $info = json_decode($res, 2);
        return $info['result'] ?? 0;
    }

    /**
     * 远程访问接口
     * @param $filePath
     * @param $md5
     * @return int|mixed
     */
    public function curl($url, $data, $method = 'POST', $dataType = 'json')
    {
        $curl = curl_init();

        if($dataType == 'json')
        {
            $headers[] = 'Content-Type: application/json;charset=utf-8';
            if(!empty($data)) $data = json_encode($data);
        }
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);

        if(!empty($data))
        {
            if($method == 'POST')  curl_setopt($curl, CURLOPT_POST, true);
            if($method == 'PUT')  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            if($method == 'PATCH') curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * 获取授权待处理人
     * @param $module
     * @param $dealUser
     * @return void
     */
    public function getAuthorizer($module, $dealUser, $status, $authorizeStatus){
        $newDealUser = $dealUser;
        if(in_array($status, array_keys($authorizeStatus))){
            $dealUserList = explode(',',$dealUser);
            $newDealUserList = explode(',',$newDealUser);
            $moduleQuery = '( 1    AND  FIND_IN_SET("'.$module.'",objectType))';
            foreach($dealUserList as $dealUserObj){
                $authorizationList = $this->dao->select("*")->from(TABLE_AUTHORIZATION)->where('authorizer')->eq($dealUserObj)->andWhere($moduleQuery)->andWhere('enabled')->eq(2)->andWhere('deleted')->ne(2)->fetchAll();
                if(!empty($authorizationList)){
                    foreach ($authorizationList as $authorization){
                        if($authorization->permanently == '2' || (strtotime(helper::now()) >= strtotime($authorization->startTime) && strtotime(helper::now()) <= strtotime("+1 day", strtotime($authorization->endTime)))){
                            $authorizedPersonList = explode(',' , trim($authorization->authorizedPerson,','));
                            foreach ($authorizedPersonList as $authorizedPerson){
                                if(!in_array($authorizedPerson, $newDealUserList)){
                                    array_push($newDealUserList, $authorizedPerson);
                                }
                            }
                        }
                    }
                }
            }
            if(empty(array_diff($newDealUserList, $dealUserList)) && empty(array_diff($dealUserList, $newDealUserList))){
                return $dealUser;
            }
            return trim($this->getAuthorizer($module, implode(',' , $newDealUserList), $status, $authorizeStatus),',');
        }
        return $dealUser;
    }

    /**
     * 获取被授权
     * @param $module
     * @param $dealUser
     * @return void
     */
    public function getOriginalAuthorizer($module, $dealUser){
        $dealUserList = explode(',',$dealUser);
        $newDealUserList = explode(',',$dealUser);
        $moduleQuery = '( 1    AND  FIND_IN_SET("'.$module.'",objectType))';
        foreach($dealUserList as $dealUserObj){
            $query = '( 1    AND  FIND_IN_SET("'.$dealUserObj.'",authorizedPerson))';
            $authorizationList = $this->dao->select("*")->from(TABLE_AUTHORIZATION)->where($query)->andWhere($moduleQuery)->andWhere('enabled')->eq(2)->andWhere('deleted')->ne(2)->fetchAll();
            if(!empty($authorizationList)){
                foreach ($authorizationList as $authorization){
                    if($authorization->permanently == '2' || (strtotime(helper::now()) >= strtotime($authorization->startTime) && strtotime(helper::now()) <= strtotime("+1 day", strtotime($authorization->endTime)))){
                        if(!in_array($authorization->authorizer, $newDealUserList)){
                            array_push($newDealUserList, $authorization->authorizer);
                        }
                    }
                }
            }
        }
        if(empty(array_diff($newDealUserList, $dealUserList)) && empty(array_diff($dealUserList, $newDealUserList))){
            return $dealUser;
        }
        return trim($this->getOriginalAuthorizer($module, implode(',',$newDealUserList)),',');
    }

    /**
     * 授权管理转化审批
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $result
     * @param $comment
     * @param $stage
     * @param $extra
     * @param $is_all_check_pass
     * @return string
     */
    public function check($objectType, $objectID, $version, $result, $comment, $stage = '', $extra = null, $is_all_check_pass = true, $reviewsOriginal, $reviews, $reviewAuthorize)
    {
        //查询是否有待审核的节点
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        //tangfei 增加审核时间
        $lastDealDate = helper::now();
        if(!$extra) $extra = new stdClass();

        //授权管理转换
        if(!in_array($this->app->user->account, $reviewsOriginal)){
            $extra = new stdClass();
            $newReview = $reviewAuthorize;
            $extra->proxy = $this->app->user->account;
            $extra->reviewerList = $reviews;
        }else{
            $newReview = $this->app->user->account;
        }

        //修改当前审核人的状态为操作状态
        $this->dao->update(TABLE_REVIEWER)
            ->set('status')->eq($result)
            ->set('comment')->eq($comment)
            ->set('extra')->eq(json_encode($extra))
            ->set('reviewTime')->eq($lastDealDate)
            ->where('node')->eq($node->id)
            ->andWhere('status')->eq('pending') //当前状态
            ->beginIF($objectType != 'projectplanyear')->andWhere('reviewer')->eq($newReview) //当前审核人
            ->exec();
        //查询该节点下所有的审核人
        $reviews = $this->dao->select('*')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->fetchAll();

        //是否评审验证节点
        $isReviewVerify = false;
        if(($node->objectType == 'review') && ($node->nodeCode == 'verify')){
            $isReviewVerify = true;
        }

        if($isReviewVerify){ //评审环节的验证操作单独处理
            //审核节点的审核结果
            $nodeResult = $result;
            if($is_all_check_pass){ //需要全部审核
                //默认需要全部审核通过
                $all = true;
                foreach($reviews as $review) {
                    if($review->status == 'pending') {
                        $all = false;
                        break;
                    }
                }
                //要求全部审核通过时才算真正审核通过，此时还有部分人未审核，不修改审核节点状态
                if(!$all) {
                    $nodeResult = 'part';
                }else{ //全部审核通过
                    foreach($reviews as $review) {
                        if($review->status == 'reject') {
                            $nodeResult = 'reject';
                            break;
                        }
                    }
                    //修改节点审核状态
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($nodeResult)
                        ->where('id')->eq($node->id)
                        ->exec();
                }

            }else{ //一人审核通过即可
                $unCheckReviews = [];
                foreach ($reviews as $review) {
                    if ($review->status == 'pending') {
                        $unCheckReviews[] = $review->id; //未审核的人
                    }
                }
                if($unCheckReviews){ //审核通过时，有一人审核通过即可，其他人不用审核
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                        ->where('id')->in($unCheckReviews)
                        ->exec();
                }

                //修改节点审核状态
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($nodeResult)
                    ->where('id')->eq($node->id)
                    ->exec();
            }
            //如果节点的评审结果
            if($nodeResult == 'reject' || $nodeResult == 'suspend') {
                // 如果拒绝了，当前和以后的节点涉及到的评审人都设为ignore，不需要评审了
                $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq($objectType)
                    ->andWhere('objectID')->eq($objectID)
                    ->andWhere('version')->eq($version)
                    ->andWhere('status')->in(array('wait', 'pending'))
                    ->orderBy('stage,id')
                    ->fetchAll();
                $ns = array();
                foreach($nodes as $node) $ns[] = $node->id;
                if(!empty($ns)){
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                        ->where('node')->in($ns)
                        ->andWhere('status')->in(array('wait', 'pending'))
                        ->exec();
                }
            }
            return $nodeResult;
        }else{
            //如果审核结果是通过
            if($result == 'pass') {
                if($is_all_check_pass){ //需要全部审核通过
                    //默认需要全部审核通过
                    $all = true;
                    foreach($reviews as $review)
                    {
                        if($review->status != 'pass')
                        {
                            $all = false;
                            break;
                        }
                    }
                    //要求全部审核通过时才算真正审核通过，此时还有部分人未审核，不修改审核节点状态
                    if(!$all) return 'part';
                }else{ //该节点一人审核通过即可
                    $unCheckReviews = [];
                    foreach ($reviews as $review) {
                        if ($review->status != 'pass') {
                            $unCheckReviews[] = $review->id; //未审核的人
                        }
                    }
                    if($unCheckReviews){ //审核通过时，有一人审核通过即可，其他人不用审核
                        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                            ->where('id')->in($unCheckReviews)
                            ->exec();
                    }
                }
            }


            //修改节点审核状态
            $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($result)
                ->where('id')->eq($node->id)
                ->exec();
            //审核状态是拒绝或者挂起
            if($result == 'reject' || $result == 'suspend')
            {
                // 如果拒绝了，当前和以后的节点涉及到的评审人都设为ignore，不需要评审了
                $nodes = $this->dao->select('*')->from(TABLE_REVIEWNODE)
                    ->where('objectType')->eq($objectType)
                    ->andWhere('objectID')->eq($objectID)
                    ->andWhere('version')->eq($version)
                    ->orderBy('stage,id')
                    ->fetchAll();
                $ns = array();
                foreach($nodes as $node) $ns[] = $node->id;
                if(!empty($ns)){
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('ignore')
                        ->where('node')->in($ns)
                        ->andWhere('status')->in(array('wait', 'pending'))
                        ->exec();
                }
            }
            return $result;
        }
    }
    /**
     * 是否有发送消息权限
     * @param $type
     * @param $module
     * @param $action
     * @return bool
     */
    public function isSetMessage($type, $module, $action)
    {
        $this->app->loadConfig('message');
        $messageSetting = $this->config->message->setting;
        if(is_string($messageSetting)){
            $messageSetting = json_decode($messageSetting, true);
        }

        if(isset($messageSetting[$type])) {
            $actions = $messageSetting[$type]['setting'];
            if(isset($actions[$module]) && in_array($action, $actions[$module])) {
                return true;
            }
        }

        return false;
    }

    /**
     * 判断是否是处理用户
     *
     * @param $dealUsers
     * @param $account
     * @return bool
     */
    public static function isDealUser($dealUsers, $account){
        $isDealUser = false;
        if(!($dealUsers && $account)){
            return $isDealUser;
        }
        if($account == 'admin'){
            $isDealUser = true;
            return $isDealUser;
        }

        if(!is_array($dealUsers)){
            $dealUsers = explode(',', $dealUsers);
        }
        if(in_array($account, $dealUsers)){
            $isDealUser = true;
        }
        return $isDealUser;
    }

    public static function create_guid($namespace = '') {
        static $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= ($_SERVER['REQUEST_TIME'] ?? time());
        $data .= ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $data .= ($_SERVER['LOCAL_ADDR'] ?? '127.0.0.1');
        $data .= ($_SERVER['LOCAL_PORT'] ?? '80');
        $data .= ($_SERVER['REMOTE_ADDR'] ?? 'localhost');
        $data .= ($_SERVER['REMOTE_PORT'] ?? '8080');
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) .
            '-' .
            substr($hash, 8, 4) .
            '-' .
            substr($hash, 12, 4) .
            '-' .
            substr($hash, 16, 4) .
            '-' .
            substr($hash, 20, 12);
        return $guid;
    }

    /**
     * 获得语言配置项列表
     *
     * @param $module
     * @param $section
     * @return array
     */
    public function getLangDataList($module, $section){
        $data = [];
        if(!($module && $section)){
            return $data;
        }
        $ret =  $this->dao->select('*')->from(TABLE_LANG)
            ->where('module')->eq($module)
            ->andWhere('section')->eq($section)
            ->fetchAll();
        if($ret){
            foreach ($ret as $val){
                if($val->extendInfo){
                    $val->extendInfo = json_decode($val->extendInfo);
                }
            }
            $data = $ret;
        }
        return $data;
    }
}

class common extends commonModel
{
}

