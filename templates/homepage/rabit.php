<?
use \base\Lang;
?>

<div class="histlinks">
	<?= Lang::get('label.history') ?>: <a href="a<?= BASE_URL ?>/"><?= Lang::get('label.overview') ?></a> > <a href="<?= BASE_URL ?>/?page=rabits"><?= Lang::get('rabits.headline') ?></a> > <a href="<?= BASE_URL ?>/?page=rabit&sn=<?= $serial ?>"><?= $name ?></a>
</div>

<h2 class="right"><?= Lang::get('rabit.headline', array('name' => $name)) ?></h2>
<div class="spacer"></div>

<input type="hidden" id="serial" value="<?= $serial ?>" />
<input type="hidden" id="token" value="<?= $token ?>" />
<input type="hidden" id="maintab-current" value="<?= $section ?>" />

<div id="apps" style="display: none;"><?= json_encode($apps); ?></div>
<div id="crontabs" style="display: none;"><?= json_encode($crontabs) ?></div>
<div id="actions" style="display: none;"><?= json_encode($actions) ?></div>

<div id="maintabs">
	<ul>
		<li><a href="#maintab-overview"><?= Lang::get('label.overall') ?></a></li>
		<li><a href="#maintab-apps"><?= Lang::get('label.apps') ?></a></li>
		<li><a href="#maintab-message"><?= Lang::get('rabit.send_message') ?></a></li>
	</ul>

	<div id="maintab-overview">

		<table style="width: 300px;">
		<thead>
			<tr>
				<th colspan="3"><?= Lang::get('label.data') ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="100"><?= Lang::get('label.name') ?></td>
				<td id="nab-data-name"><?= $nab->getConfig('name') ?></td>
				<td width="20"><a href="javascript://" onclick="editName('<?= $nab->getSerial() ?>', '<?= $nab->getConfig('name') ?>');"><img src="/res/homepage/images/edit.png" title="<?= Lang::get('label.edit') ?>" /></a></td>
			</tr>
			<tr>
				<td><?= Lang::get('label.serial') ?></td>
				<td><?= $nab->getSerial() ?></td>
				<td></td>
			</tr>
			<tr>
				<td><?= Lang::get('label.token') ?></td>
				<td><?= $nab->getConfig('token') ?></td>
				<td></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td colspan="2"><a href="javascript://" onclick="confirmRemoveRabit('<?= $nab->getSerial() ?>');" class="button right"><?= Lang::get('rabit.remove') ?></a></td>
			</tr>
		</tfoot>
		</table>

	</div>

	<div id="maintab-apps">

		<? if(count($apps->available) > 0): ?>
			<h3><?= Lang::get('rabit.apps.available') ?></h3>
			<hr />
			<? foreach($apps->available as $app): ?>
				<a href="javascript://" onclick="confirmAddApp('<?= $app->code ?>');" class="appimage appimage_available img80" rel="<?= $app->code ?>" style="background-image: url('<?= $app->image ?>,80');">&nbsp;</a>
			<? endforeach ?>
		<? endif ?>

			<div class="spacer"></div>
		<? if(count($apps->inuse) > 0): ?>
			<h3><?= Lang::get('rabit.apps.inuse') ?></h3>
			<hr />

			<? foreach($apps->inuse as $app): ?>
				<a href="javascript://" onclick="configapp('<?= $app->code ?>');" class="appimage img100" id="image_<?= $app->code ?>" style="background-image: url('<?= $app->image ?>,100');" title="<?= ($app->valid == false ? Lang::get('rabit.app.config.complete.no') : Lang::get('rabit.app.config.complete.yes')) ?>">&nbsp;</a>
				<br />
				<span style="font-size: 10pt">
					<b><?= $app->name ?></b><br /><br /><?= $app->description ?>

					<? $data = array(); ?>
					<? foreach(array_keys((Array)$app->needed) as $needed): ?>

						<? $tmp = (Array)$app->needed; ?>
						<? $config = (Array)$app->config; ?>
						<? $key = rtrim(trim($tmp[$needed]->description), ':') ?>
						<? $value = $config[$needed]; ?>

						<? if(strlen($key) > 0): ?>
							<? $data[] = $key . ': ' . (strlen($value) < 1 ? '<i>[' . Lang::get('label.not_defined') . ']</i>' . $value : $value) ?>
						<? endif ?>
					<? endforeach ?>

					<? if(count($data) > 0): ?>

						<br />
						<br />
						<?= join('<br />', $data) ?>
					<? endif ?>

					<a href="javascript://" class="button" style="float: right" onclick="confirmRemoveApp('<?= $app->code ?>');"><?= Lang::get('label.remove') ?></a>
				</span>
				<div style="clear: both;"></div>
				<hr />
			<? endforeach ?>
		<? endif ?>
	</div>

	<div id="maintab-message">

		<form action="" method="post" id="sendmessage">
			<table style="width: 100%; margin: auto;">
				<thead>
					<tr>
						<td><?= Lang::get('rabit.send_message') ?></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><textarea name="message" value="<?= (isset($_POST['message']) ? $_POST['message'] : '') ?>" style="width: 100%; height: 120px;"></textarea></td>
					</tr>
				</tbody>
				</tfoot>
					<tr>
						<td>
							<input type="submit" class="button left" name="send_tts" value="<?= Lang::get('label.send') ?>" />
						</td>
					</tr>
				</tfoot>
			</table>
			<hr />
		</form>
	</div>
</div>

<? if(count($apps->available) > 0): ?>
	<? foreach($apps->available as $app): ?>
		<div class="availapp_details" id="availapp_desc_<?= $app->code ?>">
			<b><?= $app->name ?></b><br /><?= $app->description ?>
		</div>
	<? endforeach ?>
<? endif ?>
<div id="trigger" style="display: none;">
	<div id="__ID___trigger" class="trigger">
		<ul>
			<li><a href="#__ID___trigger-1-crontab"><?= Lang::get('rabit.trigger.timed') ?></a></li>
			<li><a href="#__ID___trigger-2-action"><?= Lang::get('rabit.trigger.action') ?></a></li>
		</ul>
		<div id="__ID___trigger-1-crontab">
			
			<h3><?= Lang::get('rabit.trigger.timed.headline') ?></h3>
			<hr />
			
			<table style="float: left">
				<tr valign="top">
					<td><?= Lang::get('label.time') ?>:</td>
					<td>
						<select id="__ID__-time-period" onchange="crontabAction('__ID__', 'time', $(this).val());">
							<option value="60"><?= Lang::get('crontab.60.short') ?></option>
							<option value="30"><?= Lang::get('crontab.30.short') ?></option>
							<option value="15"><?= Lang::get('crontab.15.short') ?></option>
							<option value="random"><?= Lang::get('crontab.random.short') ?></option>
							<option value="exact"><?= Lang::get('crontab.exact.short') ?></option>
						</select>

						<span class="__ID__-time-period-div" id="__ID__-time-period-random-div" style="display: none;">
							<?= Lang::get('rabit.trigger.timed.random.max', array('input' => '<input type="text" id="__ID__-time-period-random" value="4" style="width: 20px;" />')) ?>
						</span>
						
						<span class="__ID__-time-period-div" id="__ID__-time-period-exact-div" style="display: none;">
							<select id="__ID__-time-period-exact-hours">
							<? for($i = 0; $i <= 23; $i++): ?>
								<option><? printf("%02s", $i); ?>
							<? endfor ?>
							</select> 
							: 
							<select id="__ID__-time-period-exact-minutes">
							<? for($i = 0; $i <= 59; $i++): ?>
								<option><? printf("%02s", $i); ?>
							<? endfor ?>
							</select>
						</span>
					</td>
				</tr>
				<tr valign="top">
					<td>Tage:</td>
					<td>
						<table>
							<tr>
								<td><input type="checkbox" class="__ID__-day" id="__ID__-day-mon" /><label for="__ID__-day-mon"><?= Lang::get('weekday.0.short') ?></label></td>
								<td><input type="checkbox" class="__ID__-day" id="__ID__-day-die" /><label for="__ID__-day-die"><?= Lang::get('weekday.1.short') ?></label></td>
								<td><input type="checkbox" class="__ID__-day" id="__ID__-day-mit" /><label for="__ID__-day-mit"><?= Lang::get('weekday.2.short') ?></label></td>
								<td><input type="checkbox" class="__ID__-day" id="__ID__-day-don" /><label for="__ID__-day-don"><?= Lang::get('weekday.3.short') ?></label></td>
								<td><input type="checkbox" class="__ID__-day" id="__ID__-day-fre" /><label for="__ID__-day-fre"><?= Lang::get('weekday.4.short') ?></label></td>
								<td><input type="checkbox" class="__ID__-day" id="__ID__-day-sam" /><label for="__ID__-day-sam"><?= Lang::get('weekday.5.short') ?></label></td>
								<td><input type="checkbox" class="__ID__-day" id="__ID__-day-son" /><label for="__ID__-day-son"><?= Lang::get('weekday.6.short') ?></label></td>
							</tr>
							<tr>
								<td colspan="7">
									<small>
										<a href="javascript://" onclick="crontabAction('__ID__', 'day', 'all');"><?= Lang::get('label.all') ?></a>
										<a href="javascript://" onclick="crontabAction('__ID__', 'day', 'none');"><?= Lang::get('label.none') ?></a>
										<a href="javascript://" onclick="crontabAction('__ID__', 'day', 'mo-fr');"><?= Lang::get('rabit.trigger.times.mo_fr') ?></a>
										<a href="javascript://" onclick="crontabAction('__ID__', 'day', 'sa-so');"><?= Lang::get('rabit.trigger.times.sa_so') ?></a>
									</small>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<input type="button" value="<?= Lang::get('rabit.trigger.timed.add') ?>" onclick="addCrontab('__ID__');" class="uibutton" style="float: right" />

			<div style="clear: both"></div>
			<div id="__ID__-crontabs-div" style="display: none;">
				<hr />
				<table id="__ID__-crontabs-table" style="width: 100%;"></table>
			</div>
		</div>
		<div id="__ID___trigger-2-action">
				
			<h3><?= Lang::get('rabit.trigger.action.headline') ?></h3>
			<hr />

			<div class="accordion" id="__ID___trigger-2-action-accordion">

				<h3><a href="javascript://"><?= Lang::get('rabit.trigger.action.head.headline') ?></a></h3>
				<div>
					<table>
					<tr>
						<td><label for="__ID__-action-head-0"><?= Lang::get('rabit.trigger.action.head.once') ?></label></td>
						<td><input type="radio" name=__ID__-action-head" id="__ID__-action-head-0" /></td>
					</tr>
					<tr>
						<td><label for="__ID__-action-head-1"><?= Lang::get('rabit.trigger.action.head.twice') ?></label></td>
						<td><input type="radio" name=__ID__-action-head" id="__ID__-action-head-1" /></td>
					</tr>
					</table>
				</div>
				<h3><a href="javascript://"><?= Lang::get('rabit.trigger.action.ear.headline') ?></a></h3>
				<div>
					<table>
					<tr>
						<td><label for="__ID__-action-ear-0"><?= Lang::get('rabit.trigger.action.ear.left') ?></label></td>
						<td><input type="radio" name=__ID__-action-ear" id="__ID__-action-ear-0" /></td>
					</tr>
					<tr>
						<td><label for="__ID__-action-ear-1"><?= Lang::get('rabit.trigger.action.ear.right') ?></label></td>
						<td><input type="radio" name=__ID__-action-ear" id="__ID__-action-ear-1" /></td>
					</tr>
					</table>
				</div>
				<h3><a href="javascript://"><?= Lang::get('rabit.trigger.action.thing.headline') ?></a></h3>
				<div id="__ID__-div-things">
					<b><?= Lang::get('rabit.trigger.thing.error.nothing') ?></b><br /><br />
					<?= Lang::get('label.do_it_here', array('path' => '?page=things')) ?>
				</div>
			</div>
			
			<input type="button" value="<?= Lang::get('rabit.add_action') ?>" onclick="addAction('__ID__');" class="uibutton" style="float: right" />
			
			<div style="clear: both"></div>
			<div id="__ID__-actions-div" style="display: none;">
				<hr />
				<table id="__ID__-actions-table" style="width: 100%;"></table>
			</div>
		</div>
	</div>
</div>
