/**
 * Confirm batch delete cases.
 * 
 * @param  string $actionLink 
 * @access public
 * @return void
 */
function confirmBatchDelete(actionLink)
{
    if(confirm(batchDelete)) setFormAction(actionLink);
    return false;
}