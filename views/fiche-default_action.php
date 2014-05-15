<h1>Liste des employés</h1>

<table>
	<thead>
		<tr>
			<th>Nom</th>
			<th>Prenom</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($fiches as $f) : ?>
			<tr>
				<th><?= $f['nom'] ?></th>
				<th><?= $f['prenom'] ?></th>
				<th><a href='<?= $root_www ?>fiche/afficher/?id=<?= $f['id'] ?>'><button>Consuler</button></a></th>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
