<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr" class="no-js">
	<head>
		<?php
			include 'partial/_head_meta.php';
			include 'partial/_head_css.php';
			include 'partial/_head_js.php';
		?>
		<title>TLH Technologies - Internet de demain</title>
	</head>
	<body>
		<div id="Wrapper">
			<?php
				include 'partial/_header.php';
			?>
			<div id="MainBody" class="container">
				<div id="MainRow" class="row">
					<div id="MainContent" class="col-xs-12">
						<?php
							include 'partial/_msg_box.php';
						?>
						<div class="content">
							<?= $content ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
			include 'partial/_footer.php';
			include 'partial/_foot_js.php';
		?>
	</body>
</html>