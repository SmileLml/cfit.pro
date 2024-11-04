<?php
/**
 * The control file of report module of zentaopms.
 *
 * @copyright   copyright 2009-2020 青岛易软天创网络科技有限公司(qingdao nature easy soft network technology co,ltd, www.cnezsoft.com)
 * @license     zpl (http://zpl.pub/page/zplv12.html)
 * @author      chunsheng wang <chunsheng@cnezsoft.com>
 * @package     report
 * @link        https://www.zentao.net
 */
include '../../control.php';
class myReport extends report
{
    /**
     * Edit report.
     *
     * @param  int    $reportID
     * @param  string $from
     * @access public
     * @return void
     */
    public function editReport($reportID = 0, $from = '')
    {
        if($from) $this->lang->navGroup->report = 'system';

        $report = $this->report->getReportByID($reportID);
        if(!$report) die(js::alert($this->lang->crystal->errorNoReport));

        if($_POST)
        {
            $data = fixer::input('post')->join('module', ',')->remove('desc,name')->get();
            if(!isset($data->module)) $data->module = '';

            $names = $this->post->name;
            $checkName = false;
            foreach($names as $langKey => $name)
            {
                $name = trim($name);
                if(!empty($name)) $checkName = true;
                $names[$langKey] = htmlspecialchars($name);
            }
            if(!$checkName) die(js::alert($this->lang->crystal->emptyName));
            $data->name = json_encode($names);

            $descs = $this->post->desc;
            foreach($descs as $langKey => $desc) $descs[$langKey] = strip_tags($desc, $this->config->allowedTags);
            $data->desc = json_encode($descs);

            $this->dao->update(TABLE_REPORT)->data($data)->where('id')->eq($reportID)->autocheck()
                ->batchCheck('code', 'notempty')
                ->check('code', 'unique', "id != $reportID")
                ->exec();

            if(dao::isError()) die(js::error(dao::getError()));
            die(js::reload('parent.parent'));
        }

        $this->view->report = $report;
        $this->display();
    }
}
