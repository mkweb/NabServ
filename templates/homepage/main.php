<?php
use \base\Lang;

$navlinks = array();
foreach($nav as $name => $href) {

	$navlinks[] = sprintf('<a href="' . BASE_URL . '%s">%s</a>', $href, $name);
}

$additionallinks = array();
if(isset($additional)) {
	foreach($additional as $id => $name) {

		$additionallinks[] = sprintf('<a href="javascript://" onclick="$(\'#%s\').show();">%s</a>', $id, $name);
	}
}

?>
<html>
<head>
	<title><?= $title ?></title>

	<link href='http://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>/res/homepage/jqueryui/css/custom-theme/jquery-ui-1.8.16.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>/res/homepage/css/style.css" />
	<? if(isset($adsense)): ?>
	<link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>/res/homepage/css/adsense.css" />
	<? endif ?>

	<script type="text/javascript">
	<!--
	var BASE_URL = '<?= BASE_URL ?>';
	// -->
	</script>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript"></script> 
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script> 
	<script src="<?= BASE_URL ?>/res/homepage/js/script.js" type="text/javascript"></script> 

	<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>/res/homepage/css/ie.css" />
	<![endif]-->
</head>
<body>

<div id="translations" style="display: none;"><?= htmlspecialchars(json_encode($translations)) ?></div>

<div id="shadow_top">
	<div class="left"></div>
	<div class="right"></div>
</div>

<div id="all">
	<div id="head">
		<div id="logo">
			<a href="/" title="Zur Startseite">
				<span style="font-size: 26pt"><?= Lang::get('main.headline') ?></span>
				<br />
				<span style="font-size: 8pt"><?= Lang::get('main.headline2') ?></span>
			</a>
		</div>
		<div id="login">
			<? if(!isset($_SESSION['loggedin'])): ?>
				<form action="" method="post">
					<table>
					<tr>
						<td align="right"><input type="text" class="input" name="username" rel="<?= Lang::get('label.username') ?>" value="<?= Lang::get('label.username') ?>" /></td>
					</tr>
					<tr>
						<td align="right"><input type="password" class="input" name="password" rel="<?= Lang::get('label.password') ?>" value="<?= Lang::get('label.password') ?>" /></td>
					</tr>
					<tr>
						<td align="right"><input type="submit" class="button" name="login" value="<?= Lang::get('label.login') ?>" /></td>
					</tr>
					</table>
				</form>
			<? else: ?>
				<br />
				<b><?= Lang::get('main.loggedin.as', array('username' => $_SESSION['user'])) ?></b><br />
				<a href="javascript://" class="button" onclick="confirmLogout();"><?= Lang::get('label.logout') ?></a>
				<br />
			<? endif ?>
		</div>
	</div>

	<div id="navi_top"></div>
	<div id="navi">
		<div>
			<?= join(' | ', $navlinks) ?>
		</div>
	</div>
	<div id="navi_bottom"></div>
	
	<? if(isset($additional)): ?>
		
		<div id="additional_links">
			<?= join(' | ', $additionallinks) ?>
		</div>

	<? endif ?>

	<div id="content">

		<? if(isset($_SESSION['errors'])): ?>
			<div class="error">
				<?= join("<br />", $_SESSION['errors']); ?>
			</div>
			<? unset($_SESSION['errors']) ?>
		<? endif ?>

		<? if(isset($_SESSION['flash'])): ?>
			<div class="flash">
				<?= join("<br />", $_SESSION['flash']); ?>
			</div>
			<? unset($_SESSION['flash']) ?>
		<? endif ?>

		<noscript>
			<div class="error"><?= Lang::get('main.error.no_js') ?></div>
		</noscript>
		<div id="notification">

		</div>

		<?= $content ?>
	</div>


	<? if(isset($adsense)): ?>
		<div style="float: right; margin: 10px 20px 0 0; padding-left: 14px; border-left: 1px solid #000;">
			<script type="text/javascript"><!--
			google_ad_client = "pub-4192245641536010";
			/* sky */
			google_ad_slot = "0570763269";
			google_ad_width = 120;
			google_ad_height = 600;
			//-->
			</script>

			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
	<? endif ?>
</div>

<div id="shadow_left"></div>
<div id="shadow_right"></div>

<div id="nab_corner"></div>

</body>
</html>
