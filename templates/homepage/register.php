<?
use \base\Lang;
?>

<div id="register">
	<h2><?= Lang::get('register.headline') ?></h2>
	<hr />

	<form action="" method="post">
		<table style="margin: auto;">
		<thead>
			<tr>
				<th colspan="2"><?= Lang::get('register.label.your_data') ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= Lang::get('label.username') ?>:</td>
				<td><input type="text" class="input" name="username" value="<?= $_POST['username'] ?>" /></td>
			</tr>
			<tr>
				<td><?= Lang::get('label.password') ?>:</td>
				<td><input type="password" class="input" name="password" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="register" value="<?= Lang::get('label.register') ?>" class="button" /></td>
			</tr>
		</tfoot>
		</table>
	</form>
</div>
