function confirmBatchUnlink(actionLink)
{
    if(confirm(confirmUnlink)) setFormAction(actionLink);
    return false;
}