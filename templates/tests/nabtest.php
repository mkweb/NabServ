<? if(!is_null($serial)): ?>

	<? pr($nabaztag); ?>

<? else: ?>

<form action="" method="get">
	<input type="hidden" name="page" value="nabtest" />
	Serial: <input type="text" name="serial" value="" />
	<input type="submit" value="Show" />
</form>

<? endif ?>
