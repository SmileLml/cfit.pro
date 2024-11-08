/**
 * Set role when select an account.
 * 
 * @param  string $account 
 * @param  int    $roleID 
 * @access public
 * @return void
 */
function setRole(account, roleID)
{
    role    = roles[account];       // get role according the account.
    roleOBJ = $('#role' + roleID);  // get role object.
    roleOBJ.val(role)               // set the role.
}

function addItem(obj)
{
    var item = $('#addItem').html().replace(/%i%/g, i);
    $(obj).closest('tr').after('<tr class="addedItem">' + item  + '</tr>');
   
    var accounts = $('#hours' + i).closest('tr').find('select:first');
    accounts.trigger('liszt:updated');
    accounts.chosen();

    var roles = $('#hours' + i).closest('tr').find('select:last');
    roles.trigger('liszt:updated');
    roles.chosen();
    
    i ++;
}

function deleteItem(obj)
{
    $(obj).closest('tr').remove();
}

function setDeptUsers(obj)
{
    dept = $(obj).val(); // Get dept ID.
    link = createLink('project', 'manageMembers', 'projectID=' + projectID + '&dept=' + dept); // Create manageMembers link.
    location.href=link;
}
