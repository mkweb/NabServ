<?
use \base\Lang;
?>

<div class="histlinks">
	<?= Lang::get('label.history') ?>: <a href="<?= BASE_URL ?>/"><?= Lang::get('label.overview') ?></a> > <a href="<?= BASE_URL ?>/?page=rabits"><?= Lang::get('rabits.headline'
) ?></a>
</div>

<h2 class="right"><?= Lang::get('rabits.headline' ) ?></h2>

	<hr />

<div id="nab_create" style="display: <?= (!isset($validationErrors) && $user->hasRabits() ? 'none' : 'block') ?>;">
	<h3><?= Lang::get('rabits.headline.add') ?></h3>
	<hr />
	<form action="" method="post">
		<center>
			<table style="width: 400px;">
			<thead>
				<tr>
					<th colspan="2"><?= Lang::get('label.data') ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?= Lang::get('label.name') ?>:</td>
					<td><input type="text" style="width: 260px;" class="input<?= (isset($validationErrors['name']) ? ' inputerror' : '') ?>" name="name" value="<?= $name ?>" /></td>
				</tr>
				<tr>
					<td><?= Lang::get('label.serial') ?>:</td>
					<td><input type="text" style="width: 260px;" class="input<?= (isset($validationErrors['serial']) ? ' inputerror' : '') ?>" name="serial" value="<?= $serial ?>" /></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td>
						<input type="submit" name="nab_create" class="button left" value="<?= Lang::get('label.add') ?>" />
						<? if($user->hasRabits()): ?>
							<input type="button" name="nab_create_link" class="button left" value="<?= Lang::get('label.cancel') ?>" onclick="$('#nab_create').hide(); $('#nab_create_link').show();" />
						<? endif ?>
					</td>
				</tr>
			</tfoot>
			</table>
		</center>
	</form>
</div>

<? if($user->hasRabits()): ?>

	<h3><?= Lang::get('rabits.headline2') ?></h3>
	<hr />

		<? $rabits = $user->getRabits(); ?>
		<? foreach($rabits as $rabit): ?>

			<a href="<?= BASE_URL ?>/?page=rabit&sn=<?= $rabit['serial'] ?>" class="rabitimage" style="background-image: url('<?= BASE_URL ?>/vl/image.php?d=nabaztag,green,120,<?= $rabit['name'] ?>');"></a>		

		<? endforeach ?>
			
		<a href="javascript://" onclick="$('#nab_create').show();" class="rabitimage" style="background-image: url('<?= BASE_URL ?>/vl/image.php?d=plus,blue,120');"></a>		
<? endif ?>
