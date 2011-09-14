<html>
<head>
	<title><?= $title ?></title>
</head>
<body>

<?= $content ?>

<? if(isset($_GET['page'])): ?>
	<hr />
	<small><a href="/tests/">zurück</a>
<? endif ?>

</body>
</html>
