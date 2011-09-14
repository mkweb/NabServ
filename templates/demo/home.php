<form action="" method="get">
	<? if(strlen($result) > 0): ?>
		<div class="result">
		<?= $result ?>
		</div>
	<? endif ?>

	<? if(isset($_GET['pl']) && $_GET['pl'] != ''): ?>
		<input type="hidden" name="pl" value="<?= $_GET['pl'] ?>" />
		<table>
		<tr>
			<td width="200">Serial:</td><td><input type="text" name="sn" value="<?= $serial ?>" /></td>
		</tr>
		<tr>
			<td width="200">Token:</td><td><input type="text" name="token" value="<?= $token ?>" /></td>
		</tr>

		<?= join('', $inputs) ?>
		</tr>
		</table>

		<input type="submit" name="send" value="send" />
	<? else: ?>
	Welcome
	<? endif ?>
</form>
