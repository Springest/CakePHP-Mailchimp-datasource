<h2>Mailchimp Admin-Center</h2>
<h3><?php echo $lists['total']; ?> List(s)</h3>
<?php echo h($defaultList['id']); ?>
<?php if (!empty($lists['data'])) { ?>
	+ <?php echo implode(', ', Hash::extract($lists['data'], '{n}.id')); ?>
<?php } ?>

<h3>Default List</h3>
<?php
$stats = $defaultList['stats'];
unset($defaultList['stats']);
?>
<h3>Infos</h3>
<?php
	echo pre($defaultList);
?>


<h3>Stats</h3>
<?php
	echo pre($stats);
?>
