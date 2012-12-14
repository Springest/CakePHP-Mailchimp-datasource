<h2>Mailchimp Admin-Center</h2>
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
