<h1>Liste des employés</h1>

<div class='line'>
	<a href='<?= $root_www ?>home/deconnecter/'>
		<button>
			Déconnecter
		</button>
	</a>
</div>

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
				<td><?= $f['nom'] ?></td>
				<td><?= $f['prenom'] ?></td>
				<td><a href='<?= $root_www ?>fiche/afficher/?id=<?= $f['id'] ?>'><button>Consuler</button></a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
