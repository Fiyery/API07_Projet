<?php if (isset($msg_list)) : ?>
	<div id='msg_box'>
		<?php foreach ($msg_list as $m) : ?>
			<div class='<?= $m['class'] ?>'><?= $m['msg'] ?></div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php
	$success_list = "<span>test</span>";
	$error_list = "<span>test</span>";
	$info_list = "<span>test</span>";
?>

<?php if (isset($success_list)) : ?>
	<div class='success_box'>
		<?= $success_list ?>
	</div>
<?php endif; ?>

<?php if (isset($error_list)) : ?>
	<div class='error_box'>
		<?= $error_list ?>
	</div>
<?php endif; ?>

<?php if (isset($info_list)) : ?>
	<div class='info_box'>
		<?= $info_list ?>
	</div>
<?php endif; ?>