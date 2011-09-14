<? if(!is_null($tablename)): ?>

	<? pr($table); ?>

<? else: ?>

<form action="" method="get">
	<input type="hidden" name="page" value="lookup" />
	Tablename: <input type="text" name="tablename" value="" />
	<input type="submit" value="Show" />
</form>

<? endif ?>
