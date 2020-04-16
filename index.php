<?php

//$queryString = "shop-id=111&scid=222";
$queryString = null;
?>

<div style="display: flex; justify-content: space-between">

  <?php if (false): ?>
      <div>
          <iframe width="485" height="640" style="border: 0"
                  src="http://fedosino.ru/zapiski/form.php"></iframe>
      </div>
  <?php endif; ?>

  <?php if (true): ?>
      <div>
          <iframe width="485" height="640" style="border: 0"
                  src="./form.php<?= ($queryString ? "?{$queryString}" : "") ?>"></iframe>
      </div>
  <?php endif; ?>

  <?php if (false): ?>
      <div>
          <iframe width="320" height="820" style="border: 0"
                  src="./form.php<?= ($queryString ? "?{$queryString}" : "") ?>"></iframe>
      </div>
  <?php endif; ?>

</div>
