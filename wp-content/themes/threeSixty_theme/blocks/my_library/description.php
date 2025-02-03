<!-- description filed -->
<?php
$description = get_field('description'); ?>
<?php if ($description): ?>
  <div class="body description white-color"><?= $description ?></div>
<?php endif; ?>
