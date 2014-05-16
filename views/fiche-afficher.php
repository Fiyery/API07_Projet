<h1>Fiche de <?= $fiche_prenom ?> <?= $fiche_nom ?></h1>

<div class='line'>
	<a href='<?= $root_www ?>home/deconnecter/'>
		<button>
			Déconnecter
		</button>
	</a>
</div>

<form method='post' action='<?= $root_www ?>fiche/modifier/'>
	<input type='hidden' name='id' value='<?= $fiche_id ?>'>
	<div class='line'>
		<label>Civilité</label>
		<?php if ($fiche_civilite_disabled == FALSE) : ?> 
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

<?php if (isset($fiche_user_vaccins)):?>
	<table>
		<thead>
			<tr>
				<th>Nom</th>
				<th>Date</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($fiche_user_vaccins) == 0): ?>
				<tr>
					<td colspan=3>Aucun</td>
				</tr>
			<?php else: ?>
				<?php foreach ($fiche_user_vaccins as $v): ?>
					<tr>
						<td> <?= $v->nom ?> </td>
						<td> <?= $v->time ?> </td>
						<td>
							<a href='<?= $v->link ?>'>
								<button>Supprimer</button>
							</a>
						</td>
					</tr>
				<?php endforeach;?>
			<?php endif; ?>
		</tbody>
	</table>
	<form method='post' action='<?= $root_www ?>fiche/ajouter_vaccin/'>
		<input type='hidden' name='id_user' value='<?= $fiche_id ?>'/>
		<div class='line'>
			<label>Vaccin</label>
			<select name='id_vaccination'>
				<?php foreach ($fiche_vaccins as $v): ?>
					<option value='<?= $v->id ?>'><?= $v->nom ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class='line'>
			<label>Date</label>
			<input type='date' pattern='^\d{2}\/\d{2}\/\d{4}' placeholder='jj/mm/aaaa' name='date'/>
		</div>
		<div class='line'>
			<input type='submit' value='Ajouter'/>
		</div>
	</form> 
<?php endif; ?>
<?php if (isset($fiche_user_illness)):?>
	<table>
		<thead>
			<tr>
				<th>Nom</th>
				<th>Date</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($fiche_user_illness) == 0): ?>
				<tr>
					<td colspan=3>Aucun</td>
				</tr>
			<?php else: ?>
				<?php foreach ($fiche_user_illness as $v): ?>
					<tr>
						<td> <?= $v->nom ?> </td>
						<td> <?= $v->time ?> </td>
						<td>
							<a href='<?= $v->link ?>'>
								<button>Supprimer</button>
							</a>
						</td>
					</tr>
				<?php endforeach;?>
			<?php endif; ?>
		</tbody>
	</table>
	<form method='post' action='<?= $root_www ?>fiche/ajouter_maladie/'>
		<input type='hidden' name='id_user' value='<?= $fiche_id ?>'/>
		<div class='line'>
			<label>illness</label>
			<select name='id_maladie'>
				<?php foreach ($fiche_illness as $v): ?>
					<option value='<?= $v->id ?>'><?= $v->nom ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class='line'>
			<label>Date</label>
			<input type='date' pattern='^\d{2}\/\d{2}\/\d{4}' placeholder='jj/mm/aaaa' name='date'/>
		</div>
		<div class='line'>
			<input type='submit' value='Ajouter'/>
		</div>
	</form> 
<?php endif; ?>