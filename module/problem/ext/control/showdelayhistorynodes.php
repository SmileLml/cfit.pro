<?php

include '../../control.php';
class myProblem extends problem
{
    /**
     * 查询历史数据
     * @param $id
     */
    public function showdelayHistoryNodes($id,$type = 'problemDelay')
    {
        $res      = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq("$type")->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res, 'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodesByStage("$type", $id, $version);
            foreach ($data as $k => $v) {
                if ('wait' == $v->status || !(is_array($v->reviewers) && !empty($v->reviewers))) {
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key => $node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
        }
        $this->view->nodes = $nodes;
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->view->type  = $type;
        $this->display();
    }
}
