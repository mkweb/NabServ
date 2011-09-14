<html>
<head>
	<title><?= $title ?></title>

	<link rel="stylesheet" type="text/css" href="/res/demo/css/style.css" />
</head>
<body>

<form action="" method="get">
<div id="navbar"> 
	<ul> 
	<? foreach($plugins as $plugin): ?>
		<li><a href="/demo/?pl=<?= $plugin ?>"<?= ($current == $plugin ? ' class="active"' : '') ?>><span><?= $plugin ?></span></a></li>
	<? endforeach ?>
	</ul>
</div>
<div id="content">
	<?= $content ?>
</div>
</form>

</body>
</html>
		
