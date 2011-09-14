<? if(isset($_SESSION['loggedin'])): ?>
	<div id="nab_create">
		Neuen Nabaztag hinzufügen:
		<form action="" method="post">
			<table>
			<tr>
				<td>Name:</td>
				<td><input type="text" name="name" value="<?= $request->get('name', Request::POST); ?>" /></td>
			</tr>
			<tr>
				<td>Seriennummer:</td>
				<td><input type="text" name="serial" value="<?= $request->get('serial', Request::POST); ?>" /></td>
			</tr>
			</table>
			<input type="submit" name="nab_create" value="Erstellen" />
		</form>
	</div>

	<div id="nab_list">
	<? if($user->hasRabits()): ?>

		<table>
			<tr>
				<th>Seriennummer</th>
				<th>Name</th>
				<th>Token</th>
				<th>zuletzt gesehen</th>
				<th></th>
			</tr>
			<? $rabits = $user->getRabits(); ?>
			<? foreach($rabits as $rabit): ?>
			<tr>
				<td><?= $rabit['serial'] ?></td>
				<td><?= $rabit['name'] ?></td>
				<td><?= $rabit['token'] ?></td>
				<td><?= ($rabit['lastseen'] == '' ? 'noch nie' : date('d.m.Y H:i:s', $rabit['lastseen'])) ?></td>
				<td><a href="?nab_remove=<?= $rabit['serial'] ?>">[löschen]</a></td>
			</tr>
			<? endforeach ?>
		</table>

	<? else: ?>

		Du hast noch keine Hasen registriert.

	<? endif ?>
	</div>
<? endif ?>
