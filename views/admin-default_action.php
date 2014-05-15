<div class="center large">
	<h1>Veuillez saisir vos identifiants</h1>
	<form action='<?= $root_www ?>admin/panel/' method='post'>
		<div class="line">
			<label>Login</label>
			<input name='login' type="text"/>
		</div>
		<div class="line">
			<label>Mot de passe</label>
			<input name='pass' type="password"/>
		</div>
		<div class="line"><input type='submit' value='Valider'></div>
	</form>
</div>