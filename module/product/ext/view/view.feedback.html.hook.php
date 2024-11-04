<?php
$html  = "<th><i class='icon icon-person icon-sm'></i> {$lang->product->feedback}</th>";
$html .= '<td><strong>' . zget($users, $product->feedback) . '</strong></td>';
?>
<script>
    $('.detail:eq(1)').find('.detail-content').find('tr:eq(1)').find('td').after(<?php echo json_encode($html)?>);
</script>
