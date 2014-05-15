<h1>Fiche de <?= $fiche_prenom ?> <?= $fiche_nom ?></h1>

<form method='post' action='<?= $root_www ?>fiche/modifier/'>
	<input type='hidden' name='id' value='<?= $fiche_id ?>'>
	<div class='line'>
		<label>Civilité</label>
		<?php if ($fiche_tel_disabled) : ?> 
			<select name='civilite'>
				<option <?php if ($fiche_civilite == 'M') echo "selected='selected'" ?> >M</option>
				<option <?php if ($fiche_civilite == 'Mme') echo "selected='selected'" ?> >Mme</option>
				<option <?php if ($fiche_civilite == 'Mlle') echo "selected='selected'" ?> >Mlle</option>
			</select>
		<?php else: ?>	
			<input type='text' disabled='disabled' value='<?= $fiche_civilite ?>'/>
		<?php endif; ?>
	</div>
	<div class='line'>
		<label>Nom</label>
		<input type='text' disabled='disabled' value='<?= $fiche_nom ?>'/>
	</div>
	<div class='line'>
		<label>Prénom</label>
		
		<input type='text' disabled='disabled' value='<?= $fiche_prenom ?>'/>
	</div>
	<div class='line'>
		<label>Email</label>
		<input type='text' <?= $fiche_email_disabled ?> value='<?= $fiche_email ?>'/>
	</div>
	<div class='line'>
		<label>Tel</label>
		<input type='text' <?= $fiche_tel_disabled ?> value='<?= $fiche_tel ?>'/>
	</div>
	<div class='line'>
		<input type='submit' value='Modifier'/>
	</div>
</form>