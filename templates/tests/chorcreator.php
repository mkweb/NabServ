<?php
$slots = 20;
$serial = "001d92164ae2";
$token  = "8F25AB";

$runtest = false;
$save = false;

$saved = array();

$path = PATH_FILES .  DS . 'chor' . DS . 'choreographies' . DS . 'user';

$files = glob($path . DS . '*');

foreach($files as $file) {

	$saved[] = basename($file);
}

if(isset($_POST['save'])) {

	unset($_POST['save']);

	$data = serialize($_POST);
	$filename = $path . DS . $_POST['name'] . '.dat';

	if(file_exists($filename)) unlink($filename);
	$fh = fopen($filename, 'w');
	fputs($fh, $data);
	fclose($fh);

	header('Location: /tests/?page=chorcreator&load=' . $_POST['name'] . '.dat');
	exit;
}

if(isset($_GET['load']) && !isset($_POST['test'])) {

	$file = $path . DS . $_GET['load'];
	if(file_exists($file)) {

		$_POST = unserialize(file_get_contents($file));
		$_SERVER['REQUEST_METHOD'] = 'POST';
	}
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	if(isset($_POST['save'])) {

		$save = true;
	}

	if(isset($_POST['test'])) {

		$runtest = true;
		unset($_POST['test']);
	}

	if(isset($_POST['name'])) {

		$name = $_POST['name'];
		unset($_POST['name']);
	}

	$directions = array();
	$search = 'direction_ear_';

	foreach($_POST as $key => $value) {

		if(substr($key, 0, strlen($search)) == $search) {

			$directions[substr($key, strlen($search))] = $value;
			unset($_POST[$key]);
		}
	}

	$tempo = $_POST['tempo'];

	unset($_POST['tempo']);

	$data = array();

	foreach($_POST as $key => $value) {

		list($type, $detail, $num) = explode('_', $key);
	
		$data[$num][$type . '_' . $detail] = $value;
	}

	foreach($data as $key => $block) {
		$datafound = false;
		foreach($block as $value) {

			if($value != '') {

				$datafound = true;
			}
		}

		if(!$datafound) {
			unset($data[$key]);
		}
	}
}

if($runtest) {

	$apicall = array($tempo);

	foreach($data as $time => $d) {

		foreach($d as $key => $value) {

			list($type, $detail) = explode('_', $key);

			switch($type) {

				case 'ear':
					$dir = $directions[$detail . '_' . $time];
					$apicall[] = sprintf('%d,%s,%d,%d,0,%d', $time, 'motor', $detail, $value, $dir);
					break;

				case 'led':
					list($r, $g, $b) = explode(',', $value);
					$apicall[] = sprintf('%d,%s,%d,%d,%d,%d', $time, 'led', $detail, $r, $g, $b);
					break;
			}
		}
	}

	$apicall = join(',', $apicall);
	$url = BASE_URL . "/vl/api.php?sn=" . $serial . "&token=" . $token . "&chor=" . $apicall;
	file_get_contents($url);
}

?>

<html>
<head>
<link rel="stylesheet" media="screen" type="text/css" href="/res/tests/css/colorpicker.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script type="text/javascript" src="/res/tests/js/colorpicker.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	$('.colorbutton').each(function() {

		var col_id = '#' + this.id;
		var input_id = col_id.substr(0, col_id.length - 3);

		if($(input_id).val() != '') {

			$(col_id).css('background-color', 'rgb(' + $(input_id).val() + ')');
		}
	}).ColorPicker({

		onShow: function (colpkr) {
			
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {

			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function(hsb, hex, rgb) {

			var currentColorId = '#' + $('#currentColorId').val();
			var input = currentColorId.substr(0, currentColorId.length - 3);

			$(currentColorId).css('backgroundColor', '#' + hex);
			$(currentColorId).attr('rel', '#' + hex);

			$(input).val(rgb.r + ',' + rgb.g + ',' + rgb.b);
		},
		onBeforeShow: function () {

			$('#currentColorId').val(this.id);
		}
	});
});
</script>
<style type="text/css">
select {
	border: 1px solid #000;
	width: 40px;
	background-color: #FFF;
}
table {
	border-collapse: collapse;
}
.colorbutton {
	background-color: #FFF;
}
table {
	border: 2px solid #000;
	padding: 4px;
}
table td.firstline, table td.firstrow {
	font-family: Verdana;
	font-size: 10pt;
	font-weight: bold;
	background-color: #666;
	color: #FFF;
}
table td.firstrow {
	text-align: center;
}
</style>
</head>
<body>

<? if(count($saved) > 0): ?>
<? foreach($saved as $file): ?>
<a href="/tests/?page=chorcreator&load=<?= $file ?>"><?= $file ?></a><br />
<? endforeach ?>
<hr />
<? endif ?>

<input type="hidden" id="currentColorId" />

<?php
$fields = array(
	'ear_1' => 'dropdown',
	'ear_0' => 'dropdown',
	'led_4' => 'color',
	'led_3' => 'color',
	'led_2' => 'color',
	'led_1' => 'color',
	'led_0' => 'color'
);

$names = array(
	'ear_1' => 'Ear L',
	'ear_0' => 'Ear R',
	'led_4' => 'Nose',
	'led_3' => 'Color L',
	'led_2' => 'Color C',
	'led_1' => 'Color R',
	'led_0' => 'Bottom'
);

$earvalues = array();
for($i = 0; $i <= 180; $i += 10) {
	$earvalues[] = $i;
}
?>

<form action="" method="post">

	Name: <input type="text" name="name" value="<?= (isset($name) ? $name : 'chor_' . substr(md5(microtime()), 0, 7)) ?>" /><br />
	Tempo: <input type="text" name="tempo" value="<?= (isset($tempo) ? $tempo : '10') ?>" />

	<table border="0">
	<tr>
		<td class="firstrow">&nbsp;</td>
	<? for($i = 1; $i <= $slots; $i++): ?>
		<td class="firstrow"><?= $i ?></td>
	<? endfor ?>
	</tr>
	<? foreach($fields as $name => $type): ?>

		<tr>
		<? for($i = -1; $i < $slots; $i++): ?>
				<? if($i == -1): ?>
					<td width="100" class="firstline"><?= $names[$name] ?></td>
				<? else: ?>
					<td align="center" style="background-color: #<?= ($i % 2 == 0 ? '09A87B': 'EFEFEF') ?>; width: 50px;">
						<? if($type == 'dropdown'): ?>
							<select name="<?= $name ?>_<?= $i ?>" id="<?= $name ?>_<?= $i ?>_id">
							<option value="">--</option>
							<? for($j = 0; $j <= 180; $j += 10): ?>
								<option<?= (isset($data[$i][$name]) && $data[$i][$name] != '' && $data[$i][$name] == $j ? ' selected="selected"' : '') ?>><?= $j ?></option>
							<? endfor ?>
							</select>
							<br />
							<? $id = substr($name, -1) . '_' . $i; ?>
							<select name="direction_<?= $name ?>_<?= $i ?>">
								<option value="0"<?= (isset($directions[$id]) && $directions[$id] == '0' ? ' selected="selected"' : '') ?>>F</option>
								<option value="1"<?= (isset($directions[$id]) && $directions[$id] == '1' ? ' selected="selected"' : '') ?>>B</option>
							</select>
						<? else: ?>
							<div class="colorbutton" id="<?= $name ?>_<?= $i ?>_id" style="border: 1px solid #000; width: 40px; height: 20px;"></div>
							<input type="hidden" name="<?= $name ?>_<?= $i ?>" id="<?= $name ?>_<?= $i ?>" value="<?= (isset($data[$i][$name]) ? $data[$i][$name] : '') ?>" />
						<? endif ?>
					</td>
				<? endif ?>
		<? endfor ?>
		</tr>
	<? endforeach?>
	</table>
	<input type="submit" name="test" value="Test" />
	<input type="submit" name="save" value="Save" />
</form>

</body>
</html>
