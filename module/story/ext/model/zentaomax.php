<?php
/**
 * Sync story status.
 *
 * @param  int $storyID
 * @access public
 * @return void
 */
public function close($storyID)
{
    $changes = parent::close($storyID);

    $this->syncClose($storyID);
    return $changes;
}

/**
 * Sync story status.
 *
 * @access public
 * @return void
 */
public function batchClose()
{
    $allChanges = parent::batchClose();

    foreach($allChanges as $storyID => $changes) $this->syncClose($storyID);
    return $allChanges;
}

/**
 * Sync story status.
 *
 * @param  int $storyID
 * @access public
 * @return void
 */
public function syncClose($storyID)
{
    $story = $this->getById($storyID);
    if($story->type == 'requirement') return false;

    /* Get all linked requirements.*/
    $relations = $this->getRelation($storyID, $story->type);
    if(empty($relations)) return false;

    /* Get requirement all related stories.*/
    foreach($relations as $id => $title)
    {
        $stories = $this->getRelation($id, 'requirement');

        $storiesStatus = $this->dao->select('status')->from(TABLE_STORY)
            ->where('id')->in(array_keys($stories))
            ->fetchPairs();

        $allClosed = true;
        foreach($storiesStatus as $status)
        {
            if($status != 'closed') $allClosed = false;
        }

        if($allClosed)
        {
            $data = new stdclass();
            $data->assignedTo     = 'closed';
            $data->status         = 'closed';
            $data->lastEditedBy   = $this->app->user->account;
            $data->lastEditedDate = helper::now();
            $data->assignedDate   = helper::now();
            $data->closedDate     = helper::now();
            $data->closedBy       = $this->app->user->account;

            $this->dao->update(TABLE_STORY)->data($data)
                ->autoCheck()
                ->where('id')->eq($id)->exec();
        }
    }
}

/**
 * Print cell data
 *
 * @param  object $col
 * @param  object $story
 * @param  array  $users
 * @param  array  $branches
 * @param  array  $storyStages
 * @param  array  $modulePairs
 * @param  array  $storyTasks
 * @param  array  $storyBugs
 * @param  array  $storyCases
 * @access public
 * @return void
 */
public function printCell($col, $story, $users, $branches, $storyStages, $modulePairs = array(), $storyTasks = array(), $storyBugs = array(), $storyCases = array(), $mode = 'datatable', $storyType = 'story')
{
    if($col->id == 'SRS' && $col->show)
    {   
        $link    = helper::createLink('story', 'relation', "storyID=$story->id&storyType=$story->type");
        $storySR = $this->getStoryRelationCounts($story->id, $story->type);
        echo $storySR > 0 ? '<td class="datatable-cell c-SRS">' . html::a($link, $storySR, '', 'class="iframe"') . '</td>' : '<td class="datatable-cell c-SRS">0</td>';
    }
    elseif($col->id == 'URS' && $col->show)
    {   
        $link    = helper::createLink('story', 'relation', "storyID=$story->id&storyType=$story->type");
        $storySR = $this->getStoryRelationCounts($story->id, $story->type);
        echo $storySR > 0 ? '<td class="datatable-cell c-URS">' . html::a($link, $storySR, '', 'class="iframe"') . '</td>' : '<td class="datatable-cell c-URS">0</td>';
    }
    else
    {
        parent::printCell($col, $story, $users, $branches, $storyStages, $modulePairs, $storyTasks, $storyBugs, $storyCases, $mode, $storyType);
    }
}
