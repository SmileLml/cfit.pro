<?php
include '../../control.php';
class myDesign extends design
{
    /**
     * Design link commits.
     *
     * @param  int    $designID
     * @param  int    $repoID
     * @param  string $begin
     * @param  string $end
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkCommit($designID = 0, $repoID = 0, $begin = '', $end = '', $recTotal = 0, $recPerPage = 50, $pageID = 1)
    {
        $design = $this->design->getById($designID);
        $productID = $this->commonAction($design->project, $design->product, $designID);

        /* Get project and date. */
        $project = $this->loadModel('project')->getByID($design->project);
        $begin   = $begin ? date('Y-m-d', strtotime($begin)) : $project->begin;
        $end     = $end ? date('Y-m-d', strtotime($end)) : helper::today();

        /* Get the repository information through the repoID. */
        $repos  = $this->loadModel('repo')->getRepoPairs('project', $design->project);
        $repoID = $repoID ? $repoID : key($repos);

        if(empty($repoID)) die(js::locate(helper::createLink('repo', 'create', "objectID=$design->project")));

        $repo      = $this->loadModel('repo')->getRepoByID($repoID);
        $revisions = $this->repo->getCommits($repo, '', 'HEAD', '', '', $begin, $end);

        if($_POST)
        {
            $this->design->linkCommit($designID, $repoID);

            $result['result']  = 'success';
            $result['message'] = $this->lang->saveSuccess;
            $result['locate']  = 'parent';
            return $this->send($result);
        }

        /* Linked submission. */
        $linkedRevisions = array();
        $relations = $this->loadModel('common')->getRelations('design', $designID, 'commit');
        foreach($relations as $relation) $linkedRevisions[$relation->BID] = $relation->BID;

        foreach($revisions as $id => $commit)
        {
            if(isset($linkedRevisions[$commit->id])) unset($revisions[$id]);
        }

        /* Init pager. */
        $this->app->loadClass('pager', $static = true);
        $recTotal  = count($revisions);
        $pager     = new pager($recTotal, $recPerPage, $pageID);
        $revisions = array_chunk($revisions, $pager->recPerPage);

        $this->view->title      = $this->lang->design->common . $this->lang->colon . $this->lang->design->linkCommit;
        $this->view->position[] = $this->lang->design->linkCommit;

        $this->view->repos      = $repos;
        $this->view->repoID     = $repoID;
        $this->view->repo       = $repo;
        $this->view->revisions  = empty($revisions) ? $revisions : $revisions[$pageID - 1];
        $this->view->designID   = $designID;
        $this->view->begin      = $begin;
        $this->view->end        = $end;
        $this->view->design     = $this->design->getByID($designID);
        $this->view->pager      = $pager;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }
}
