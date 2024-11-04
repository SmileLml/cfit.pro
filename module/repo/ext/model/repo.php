<?php
/**
 * Get review.
 *
 * @param  int    $repoID
 * @param  string $entry
 * @param  string $revision
 * @access public
 * @return array
 */
public function getReview($repoID, $entry, $revision)
{
    return $this->loadExtension('repo')->getReview($repoID, $entry, $revision);
}

/**
 * Get bugs by repo.
 *
 * @param  int    $repoID
 * @param  string $browseType
 * @param  string $orderBy
 * @param  object $pager
 * @access public
 * @return array
 */
public function getBugsByRepo($repoID, $browseType, $orderBy, $pager)
{
    return $this->loadExtension('repo')->getBugsByRepo($repoID, $browseType, $orderBy, $pager);
}

/**
 * Get project pairs.
 *
 * @param  int    $product
 * @param  int    $branch
 * @access public
 * @return array
 */
public function getExecutionPairs($product, $branch = 0)
{
    return $this->loadExtension('repo')->getExecutionPairs($product, $branch);
}

/**
 * Save bug.
 *
 * @param  int    $repoID
 * @param  string $file
 * @param  int    $v1
 * @param  int    $v2
 * @access public
 * @return array
 */
public function saveBug($repoID, $file, $v1, $v2)
{
    return $this->loadExtension('repo')->saveBug($repoID, $file, $v1, $v2);
}

/**
 * Update bug.
 *
 * @param  int    $bugID
 * @param  string $title
 * @access public
 * @return string
 */
public function updateBug($bugID, $title)
{
    return $this->loadExtension('repo')->updateBug($bugID, $title);
}

/**
 * Update comment.
 *
 * @param  int    $commentID
 * @param  string $comment
 * @access public
 * @return string
 */
public function updateComment($commentID, $comment)
{
    return $this->loadExtension('repo')->updateComment($commentID, $comment);
}

/**
 * Delete comment.
 *
 * @param  int    $commentID
 * @access public
 * @return void
 */
public function deleteComment($commentID)
{
    return $this->loadExtension('repo')->deleteComment($commentID);
}

/**
 * Get last review info.
 *
 * @param  string $entry
 * @access public
 * @return object
 */
public function getLastReviewInfo($entry)
{
    return $this->loadExtension('repo')->getLastReviewInfo($entry);
}
