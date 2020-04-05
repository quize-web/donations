<?php
//$queryString = "shop-id=333&scid=444";
$queryString = null;
?>

<div style="display: flex; justify-content: space-between">

    <div>
        <iframe width="485" height="640" style="border: 0"
                src="./form.php<?= ($queryString ? "?{$queryString}" : "") ?>"></iframe>
    </div>

    <div>
        <iframe width="320" height="820" style="border: 0"
                src="./form.php<?= ($queryString ? "?{$queryString}" : "") ?>"></iframe>
    </div>

</div>
