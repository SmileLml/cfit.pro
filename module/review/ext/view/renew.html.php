<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<?php include 'renewForm.html.php';?>
<?php include '../../../common/view/footer.html.php';?>
<?php
js::set('status', $review->status);
js::set('deptId', $review->createdDept);
js::set('reviewer', $review->reviewer);
js::set('reviewId', $review->id);
js::set('owner', $review->owner);
js::set('meetingCode', $review->meetingCode);
js::set('type', $review->type);
js::set('bearDept', $bearDept);
?>

