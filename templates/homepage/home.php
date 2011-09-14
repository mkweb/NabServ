<? 
use \base\Lang;

if(!isset($_SESSION['loggedin'])): 
?>

<h3><?= Lang::get('label.welcome') ?></h3>
<hr />

<?= Lang::get('home.nologin.text') ?>

<? else: ?>

	<div class="histlinks">
		<?= Lang::get('label.history') ?>: <a href="<?= BASE_URL ?>/"><?= Lang::get('label.overview') ?></a>
	</div>

	<h2 class="right"><?= Lang::get('home.login.headline') ?></h2>
	<hr />

	<h3><?= Lang::get('home.login.headline.rabits') ?></h3>
	<a href="<?= BASE_URL ?>/?page=rabits" class="button titlebutton"><?= Lang::get('label.edit') ?></a>
	<hr />

	<? if(count($rabits) > 0): ?>
		<? foreach($rabits as $rabit): ?>

			<a href="<?= BASE_URL ?>/?page=rabit&sn=<?= $rabit['serial'] ?>" class="rabitimage" style="background-image: url('<?= BASE_URL ?>/vl/image.php?d=nabaztag,orange,120,<?= $rabit['name'] ?>');"></a>		

		<? endforeach ?>
	<? else: ?>

		<h4><?= Lang::get('home.error.rabits.noneadded') ?></h4>

	<? endif ?>

	<? if($hasApps): ?>
		<div class="spacer"></div>

		<h3><?= Lang::get('home.login.headline.apps') ?></h3>
		<hr />

		<? foreach($apps as $nabname => $nabapps): ?>

			<? $serial = $nabapps['nabserial'] ?>

			<div class="spacer"></div>

			<h4><?= $nabname ?></h4>
			<? foreach($nabapps['inuse'] as $app): ?>
			
				<a href="<?= BASE_URL ?>/?page=rabit&sn=<?= $serial ?>&section=apps#<?= $app->code ?>" class="appimage appimage_available img80" rel="<?= $app->code ?>" style="background-image: url('<?= $app->image ?>,80');">&nbsp;</a>

			<? endforeach ?>
		<? endforeach ?>
	<? endif ?>

	<div class="spacer"></div>

	<h3><?= Lang::get('home.login.headline.things') ?></h3>
	<a href="<?= BASE_URL ?>/?page=things" class="button titlebutton"><?= Lang::get('label.edit') ?></a>
	<hr />

	<? if(count($things) > 0): ?>
		<? foreach($things as $thing): ?>

			<a href="<?= BASE_URL ?>/?page=things" class="rabitimage img100" style="background-image: url('<?= BASE_URL ?>/vl/image.php?d=nanoztag,blue,100,<?= $thing['name'] ?>');"></a>

		<? endforeach ?>
	<? else: ?>

		<h4><?= Lang::get('home.error.things.noneadded') ?></h4>

	<? endif ?>
<? endif ?>
