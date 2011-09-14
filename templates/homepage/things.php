<?
use \base\Lang;
?>

<div class="histlinks">
	<?= Lang::get('label.history') ?>: <a href="<?= BASE_URL ?>/"><?= Lang::get('label.overview') ?></a> > <a href="<?= BASE_URL ?>/?page=things">Deine Dinge</a>
</div>

<h2 class="right"><?= Lang::get('things.headline') ?></h2>
<hr />

<input type="hidden" id="serial" value="<?= $serial ?>" />
<input type="hidden" id="token" value="<?= $token ?>" />
<input type="hidden" id="name" value="<?= $name ?>" />

<div id="rabits" style="display: none;"><?= json_encode($rabits) ?></div>
<div id="currentthings" style="display: none;"><?= json_encode($things) ?></div>

<? if(is_array($things)): ?>

	<? foreach($things as $thing): ?>

			<a href="javascript://" onclick="confirmRemoveThing('<?= $thing['id'] ?>');" class="rabitimage img100 removelayer" style="background-image: url('<?= BASE_URL ?>/vl/image.php?d=nanoztag,blue,100,<?= $thing['name'] ?>');"></a>

	<? endforeach ?>

<? endif ?>

<a href="javascript://" onclick="addThing();" class="rabitimage img100" style="background-image: url('<?= BASE_URL ?>/vl/image.php?d=plus,blue,100');"></a>
