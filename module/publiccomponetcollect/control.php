<?php
/**
 * The control file of release module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: control.php 4178 2013-01-20 09:32:11Z wwccss $
 * @link        http://www.zentao.net
 */
class publiccomponetcollect extends control
{

    public function browse()
    {
        $this->view->title = $this->lang->publiccomponetcollect->common;
        $this->display();
       /* echo js::confirm('原有的CJDP需求、Mojito需求、COAS需求功能已合并到【组件管理】-【公共技术组件需求收集】菜单下，查询原有需求或者提交新的需求，请点击【前往查看】按钮',
              $this->createLink('cjdpf','browse').'#app=componentmanage',

            $this->createLink('demandcollection','browse').'#app=demandcollection'
        );*/
    }

}