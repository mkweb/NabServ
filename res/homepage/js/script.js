var openappdialog = null;
var translations = new Array();
var saveAppResponse = null;

$(document).ready(function() {

	initTranslations();
	initNotifications();

	if($('#maintabs').length > 0) {

		var current = 0;

		if($('#maintab-current').val() > 0) {

			current = $('#maintab-current').val();
		}

		$('#maintabs').tabs({ selected: current });
	}

	$('.input').focus(function() {
		
		if(undefined != $(this).attr('rel') && $(this).val() == $(this).attr('rel')) {

			this.value = '';
			$(this).focus();
		}
	}).blur(function() {
		
		if(undefined != $(this).attr('rel') && $(this).val() == '') {

			$(this).val($(this).attr('rel'));
		}
	});

	$('.appimage_available').hover(function() {

		var code = $(this).attr('rel');
		var details = $('#availapp_desc_' + code);

		if(details.length > 0) {

			$(details).css('display', 'block');
		}
	}, function() {

		var code = $(this).attr('rel');
		var details = $('#availapp_desc_' + code);

		if(details.length > 0) {

			$(details).css('display', 'none');
		}
	}).mousemove(function(e) {

		var code = $(this).attr('rel');
		var details = $('#availapp_desc_' + code);

		if(details.length > 0) {

			$(details).css('left', e.pageX);
			$(details).css('top', e.pageY);
		}
	});

	$('.removelayer').hover(function() {

		$(this).html('<img src="/res/apps/images/layer_remove.png" style="width: 100px; height: 100px;" />');
		$(this).attr('rel', $(this).attr('title'));
		$(this).attr('title', translate('label.remove'));
	}, function() {
		
		$(this).html(' ');
		$(this).attr('title', $(this).attr('rel'));
		$(this).attr('rel', '');
	});

	var loc = location.href;
	var tmp = loc.split('&');

	if(tmp[2] != undefined) {

		if(tmp[2].split('#').length == 2) {

			var key = tmp[2].split('=')[0];
			var value = tmp[2].split('=')[1].split('#')[0];
			var addition = tmp[2].split('#')[1];

			if(key == 'section' && value == 'apps' && addition.length > 0) {

				configapp(addition);
			}
		}
	}
});

function initTranslations() {

	translations = eval('(' + $('#translations').html() + ')');
}

function translate(key) {

	if(undefined != translations[key]) {

		return translations[key];
	}

	return null;
}

function setFlash(msg) {

	var div = $('<div class="flash" style="display: none; z-index: 999999">' + msg + '</div>');
	$('#notification').append(div);

	$('#notification .flash').fadeIn();

	initNotifications();
}

function setError(msg) {

	var div = $('<div class="error" style="display: none; z-index: 999999">' + msg + '</div>');
	$('#notification').append(div);

	$('#notification .error').fadeIn();

	initNotifications();
}

function initNotifications() {

	if($('.flash').length > 0) {

		window.setTimeout(function() {

			$('.flash').fadeOut();
		}, 2000);
	}
}

var sniffinterval;
var lastrfid;
var lastrfidts;

function chooseRabit(msg) {

	var rabits = eval('(' + $('#rabits').html() + ')');

	var cnt = 0;
	for(r in rabits) {

		cnt++;
	}

	if(cnt == 0) {

		nabAlert(translate('thing.add.error.nothing'));

		return false;

	} else 	if(cnt == 1) {

		if(rabits[r].serial != undefined) {

			$('#serial').val(rabits[r].serial);
			$('#name').val(rabits[r].name);
			$('#token').val(rabits[r].token);

			addThing();
			return true;
		} else {

			nabAlert(translate('error.default'));

			return false;
		}
	}

	var dialog_id = 'choose_rabit_' + Math.random(1111, 9999).toString().split('.')[1];

	var html = '<div title="' + translate('rabit.please_choose') + '" id="' + dialog_id + '">';
	html += msg;
	html += '<br /><br />';

	for(i in rabits) {

		html += '<a href="javascript://" onclick="finishChooseRabit(\'' + dialog_id + '\', \'' + rabits[i].serial + '\', \'' + rabits[i].name + '\', \'' + rabits[i].token + '\');" class="rabitimage img80" style="background-image: url(\'' + BASE_URL + '/vl/image.php?d=nabaztag,green,80,' + rabits[i].name + '\');"></a>		';
	}

	html += '</div>';

	$(html).dialog({
		modal: true
	});
}

function finishChooseRabit(dialog_id, serial, name, token) {

	if($('#' + dialog_id).length > 0) {

		$('#serial').val(serial);
		$('#name').val(name);
		$('#token').val(token);

		$('#' + dialog_id).dialog('close');

		addThing();
	}
}

function addThing() {

	var serial = $('#serial').val();

	if(serial == '') {

		chooseRabit(translate('rabit.please_choose.long'));
		return false;
	}

	var config = null;

	var url = BASE_URL + '/?page=json&m=getconfig&sn=' + $('#serial').val() + '&token=' + $('#token').val();

	$.ajax({
		url: url,
		async: false,
		success: function(config) {

			config = eval('(' + config + ')');

			if(null != config) {

				if(undefined != config['lastrfid']) {

					lastrfid = config['lastrfid'];
				}
				if(undefined != config['lastrfidts']) {

					lastrfidts = config['lastrfidts'];
				}
			}

			var html = '<div id="thingsniffer">' + translate('thing.please.hold.before').split('%name').join($('#name').val()) + '.<br /><br /><center><img src="/res/homepage/images/loading.gif" /></center></div>';

			$('<div title="' + translate('thing.add.title') + '">' + html + '</div>').dialog({

				modal: true,
				button: {

					'[LANG:label.cancel]' : function() {

						if(null != sniffinterval) window.clearTimeout(sniffinterval);
						$(this).dialog('close');
					}
				}
			});

			sniffinterval = window.setInterval('sniffThing()', 1000);
		}
	});

}

function chooseThing() {

	var things = getThings();

console.log(things);

	// TODO
	var html = '<ul>';
	for(i in things) {
	}
	html += '</ul>';
}

function sniffThing() {

	var url = BASE_URL + '/?page=json&m=getconfig&sn=' + $('#serial').val() + '&token=' + $('#token').val();

	$.ajax({
		url: url,
		async: false,
		success: function(config) {

			config = eval('(' + config + ')');

			if(null != config) {

				if(undefined != config['lastrfidts'] && lastrfidts != config['lastrfidts']) {

					window.clearInterval(sniffinterval);

					var currentthings = eval('(' + $('#currentthings').html() + ')');
					
					var id = config['lastrfid'];

					for(i in currentthings) {

						if(currentthings[i].id == id) {

							$('#thingsniffer').parent().dialog('close');

							nabAlert(translate('thing.known'));
							return false;
						}
					}

					$('#thingsniffer').dialog({
						buttons: {
							'[LANG:label.add]' : function() {
								saveThing(id);
							}
						}
					});

					$('#thingsniffer').html('<b>' + translate('thing.found') + '<br /><br /></b>' + translate('thing.pls.give.name') + ': <input type="text" id="thing-name" /><br /><br />');
				}
			}
		}
	});
}

function saveThing(id) {

	var name = $('#thing-name').val().trim();
	
	if(name.length < 1) {

		nabAlert(translate('thing.pls.give.name') + '.');
		return false;
	}

	location.href = BASE_URL + '/?page=things&add=' + id + '&name=' + name;
}

function configapp(code) {

	var apps = eval('(' + $('#apps').html() + ')');

	var app = null;

	for(id in apps.inuse) {

		if(apps.inuse[id].code == code) {

			app = apps.inuse[id];
		}
	}

	var html =	'<h2>' + translate('app.config.title').split('%name').join(app.name) + '</h2><br /><br />';

	var neededHtml = getNeededHtml(app.code, app.needed, app.config);

	html += 	neededHtml;
	html += 	getTriggerHtml(app.code, app.needed);

	if($('#configdialog_' + app.name).length == 0) {

		if(neededHtml.length > 0) {

			var buttons = {

				'[LANG:label.save_changes]' : function() {

					if(saveAppData(app.code)) {

                        $(this).dialog('close');
                        lastappdialog = null;

                        location.href = location.href + '#maintab-apps';
                        location.reload();
                    }
				},
				'[LANG:label.close]' : function() {

					$(this).dialog('close');
					lastappdialog = null;
				}
			}
		} else {

			var buttons = {

				'[LANG:label.close]' : function() {

					$(this).dialog('close');
					lastappdialog = null;
				}
			}
		}

		$('<div title="' + translate('app.config.title').split('%name').join(app.name) + '" id="configdialog_' + app.name + '">' + html + '</div>').dialog({
			modal: true,
			width: 740,
			height: 580,
			open: function() {

				$('.trigger').tabs();
				$('.trigger .uibutton').button();

				$('.accordion').accordion({
					autoHeight: false,
					active: false
				});
				$('.accordion .uibutton').button();
			},
			buttons: buttons
		});

		lastappdialog = $('#configdialog_' + app.name);
	} else {

		$('#configdialog_' + app.name).dialog('open');
	}

	$('.uibutton').button();

	loadCrontabs(app.code);
	loadActions(app.code);
	loadThings(app.code);
}

function loadThings(id) {

	var things = getThings();

	if(things.length > 0) {

		var html = '<table>';
		for(i in things) {

			var thing = things[i];

			var inputid = id + '-action-thing-' + thing.id;
			var inputclass = id + '-action-thing';

			html += '<tr><td><label for="' + inputid + '">' + thing.name + '</label></td><td><input type="radio" name="' + inputclass + '" class="' + inputclass + '" id="' + inputid + '" rel="' + thing.id + '" /></td></tr>';
		}
		html += '</table>';
		
		$('#' + id + '-div-things').html(html);
	}
}

function getThings() {

	var things = new Array();
	var url = BASE_URL + '/?page=json&m=getthings&sn=' + $('#serial').val() + '&token=' + $('#token').val();

	$.ajax({
		url: url,
		async: false,
		success: function(res) {

			res = eval('(' + res + ')');

			for(i in res) {

				things.push(res[i]);
			}
		}
	});

	return things;
}

function saveAppData(appcode) {

    var data = new Array();

    $('.' + appcode + '-data').each(function() {

        data[$(this).attr('id')] = $(this).val();
    });

    var query = new Array();

    for(key in data) {

        query[query.length] = key + '=' + data[key];
    }

	query = query.join(',');
	var url = BASE_URL + "/vl/app.php?sn=" + $('#serial').val() + "&token=" + $('#token').val() + "&d=config," + appcode + "," + query;

    var result = true;

	$.ajax({ 
        async: false, 
        url: url,
        success: function(res) {

            if(res != '') {

                saveAppResponse = eval('(' + res + ')');

                if(saveAppResponse.type != undefined) {

                    var type = saveAppResponse.type;

                    if(type == 'choose') {

                        var data = saveAppResponse.data;
                        var headline = saveAppResponse.headline;

                        var html = headline;
                        html += '<ul>';

                        for(key in data) {

                            var value = data[key];

                            html += '<li><a href="javascript:changeAppDataValue(\'' + appcode + '\', \'' + saveAppResponse.key + '\', \'' + value + ' (' + key + ')' + '\');">' + value + '</a></li>';
                        }

                        html += '</ul>';

                        $('<div id="dialog-choose" title="Bitte triff eine Auswahl">' + html + '</div>').dialog();
                    }

                    if(type == 'error') {

                        $('<div title="Fehler">' + saveAppResponse.reason + '</div>').dialog({
                            buttons: {
                                'Ok' : function() {
                                    $(this).dialog('close');
                                }
                            }
                        });
                    }
                }

                result = false;
            }
        }
    });

    if(result == true) {

    	setFlash(translate('changes.saved'));
    	updateImage(appcode);
    }

    return result;
}

function changeAppDataValue(appcode, key, value) {

    $('.' + appcode + '-data#' + key).val(value.split(',').join(' '));

    $('#dialog-choose').remove();
    saveAppData(appcode);
}

function getNeededHtml(appcode, triggers, config) {

	var html = '<div class="ui-widget" style="margin-bottom: 10px;">';
	html += ' <div class="ui-widget-header" style="padding: 4px;">' + translate('label.data') + '</div>';
	html += '  <div class="ui-widget-content" style="padding: 4px;">';

	html += '<table style="width: 560px; float: left;">';

	var i = 0;
	for(id in triggers) {

		if(id != 'trigger') {

			var value = (undefined != config[id] ? config[id] : '');

			html += getHtml(appcode, id, triggers[id], value);
			i++;
		}
	}

	html += '</table>';
	html += '<div style="clear: both"></div>';

	html += ' </div>';
	html += '</div>';

	if(i > 0) {

		return html;
	} else {

		return '';
	}
}

function getTriggerHtml(appcode, triggers) {

	for(id in triggers) {

		if(id == 'trigger') {

			return '<hr />' + $('#trigger').html().split('__ID__').join(appcode);
		}
	}

	return '';
}

function getHtml(appcode, id, data, value) {

	if(id == 'trigger') {

		return '</div>' + $('#events').html().split('__ID__').join(appcode);
	}

	if(data.type == 'text') {

		return '<tr><td width="160">' + data.description + '</td><td><input type="text" style="width: 100%;" value="' + value + '" class="' + appcode + '-data" id="' + id + '" /></td></tr>';
	}
}

function crontabAction(id, key, value) {

	if(key == 'day') {

		if(value == 'all') {

			$('.' + id + '-day').each(function() { 
				this.checked = true; 
			});
		}
		
		if(value == 'none') {

			$('.' + id + '-day').each(function() { 
				this.checked = false; 
			});
		}

		if(value == 'mo-fr') {

			crontabAction(id, key, 'none');

			$('#' + id + '-day-mon').attr('checked', true);
			$('#' + id + '-day-die').attr('checked', true);
			$('#' + id + '-day-mit').attr('checked', true);
			$('#' + id + '-day-don').attr('checked', true);
			$('#' + id + '-day-fre').attr('checked', true);
		}
		
		if(value == 'sa-so') {

			crontabAction(id, key, 'none');

			$('#' + id + '-day-sam').attr('checked', true);
			$('#' + id + '-day-son').attr('checked', true);
		}
	}

	if(key == 'time') {

		$('.' + id + '-time-period-div').css('display', 'none');
		
		if(value == 'random') {

			$('#' + id + '-time-period-random-div').css('display', '');
		}
		
		if(value == 'exact') {

			$('#' + id + '-time-period-exact-div').css('display', '');
		}
	}
}

function addCrontab(id) {

	var current = getTempCrontab();

	var crontab = {
		id     : id,
		period : $('#' + id + '-time-period').val(),
		random : null,
		exact  : null,
		days   : {
			mon : false,
			die : false,
			mit : false,
			don : false,
			fre : false,
			sam : false,
			son : false
		},
		parse  : function() {

			if(this.period == 'random')	this.random = $('#' + this.id + '-time-period-random').val();
			if(this.period == 'exact') 	this.exact = $('#' + this.id + '-time-period-exact-hours').val() + '-' + $('#' + this.id + '-time-period-exact-minutes').val();

			if($('#' + id + '-day-mon').attr('checked'))	this.days.mon = true;
			if($('#' + id + '-day-die').attr('checked'))	this.days.die = true;
			if($('#' + id + '-day-mit').attr('checked'))	this.days.mit = true;
			if($('#' + id + '-day-don').attr('checked'))	this.days.don = true;
			if($('#' + id + '-day-fre').attr('checked'))	this.days.fre = true;
			if($('#' + id + '-day-sam').attr('checked'))	this.days.sam = true;
			if($('#' + id + '-day-son').attr('checked'))	this.days.son = true;
		}
	};

	crontab.parse();

	var result = false;
	for(i in crontab.days) {

		if(crontab.days[i]) result = true;
	}

	if(!result) {

		$('<div title="' + translate('label.error') + '"><b>' + translate('trigger.error.noday') + '</b></div>').dialog({
			buttons: {

				'Ok' : function() {
					$(this).dialog('hide').dialog('destroy');
				}
			}
		});

		return;
	}

	if(undefined == current[id]) {

		current[id] = new Array();
	}

	current[id].push(crontab);

	setTempCrontab(current);
	updateNabaztagCrontab(id);

	loadCrontabs(id);
}

function getTempActions() {

	var content = $('#actions').html().split('\\"').join('"');
	if(content.substr(0, 1) == '"') content = content.substr(1);
	if(content.substr(content.length - 1, 1) == '"') content = content.substr(0, content.length - 1);

	return eval('(' + content + ')');
}

function getTempCrontab() {

	var content = $('#crontabs').html().split('\\"').join('"');
	if(content.substr(0, 1) == '"') content = content.substr(1);
	if(content.substr(content.length - 1, 1) == '"') content = content.substr(0, content.length - 1);
	
	var url = BASE_URL + "/vl/app.php?sn=" + $('#serial').val() + "&token=" + $('#token').val() + "&d=gethomepageobj," + content;
	var result = null;

	$.ajax({
		url: url,
		async: false,
		success: function(res) {

			result = res;
		}
	});
	
	var current = eval('(' + result + ')');

	if(null == current) {

		current = new Array();
	}

	return current;
}

function setTempCrontab(current) {

	var url = BASE_URL + "/vl/app.php?sn=" + $('#serial').val() + "&token=" + $('#token').val() + "&d=getcrontab," + JSON.stringify(current);
	var result = null;

	$.ajax({
		url: url,
		async: false,
		success: function(res) {

			result = res;
		}
	});

	$('#crontabs').html(JSON.stringify(result));
}

function loadCrontabs(id) {

	var stringMapping = new Array();

	stringMapping['15'] = translate('crontab.15');
	stringMapping['30'] = translate('crontab.30');
	stringMapping['60'] = translate('crontab.60');
	stringMapping['random'] = translate('crontab.random');
	stringMapping['exact'] = translate('crontab.exact');
	
	$('#' + id + '-crontabs-table').empty();

	var all = getTempCrontab();

	if(undefined == all[id]) {

		all[id] = new Array();
	}

	if(all != null && all[id] != undefined) {

		if(all[id].length > 0) {

			for(i in all[id]) {

				var crontab = all[id][i];
		
				var days = '';
				for(j in crontab.days) {

					if(crontab.days[j]) {

						days += j.charAt(0).toUpperCase() + j.substr(1) + ', ';
					}
				}
				if(days.length > 0) {

					days = days.substring(0, days.length - 2);
				}

				var tr = $('<tr></tr>');
				var td1 = $('<td width="120">' + translate('label.days') + ':</td>');
				var td2 = $('<td width="260">' + days + '</td>');
				var td3 = $('<td width="100" rowspan="2">' + crontab.crontab + '</td>');
				var td4 = $('<td align="right" rowspan="2"><input type="button" class="uibutton" value="' + translate('label.remove') + '" onclick="removeCrontab(\'' + id + '\', ' + i + ');" style="float: right" /></td>');

				$(tr).append(td1);
				$(tr).append(td2);
				$(tr).append(td3);
				$(tr).append(td4);

				$('#' + id + '-crontabs-table').append(tr);

				var str = stringMapping[crontab.period];
				
				if(crontab.period == 'random') {

					str += ', ' + translate('crontab.random.long').split('%count').join(crontab.random);
				}
				
				if(crontab.period == 'exact') {

					str += ' ' + translate('crontab.exact.long').split('%h').join(crontab.exact.split('-')[0]).split('%m').join(crontab.exact.split('-')[1]);
				}
				
				var tr = $('<tr></tr>');
				var td1 = $('<td>' + translate('label.time') + ':</td>');
				var td2 = $('<td>' + str + '</td>');

				$(tr).append(td1);
				$(tr).append(td2);

				$(td1).css('border-bottom', '1px solid #666');
				$(td2).css('border-bottom', '1px solid #666');
				$(td3).css('border-bottom', '1px solid #666');
				$(td4).css('border-bottom', '1px solid #666');
				
				$('#' + id + '-crontabs-table').css('border-collapse', 'collapse');
				$('#' + id + '-crontabs-table').append(tr);

				$('.uibutton').button();
				$('#' + id + '-crontabs-div').css('display', 'block');
			}
		} else {

			$('#' + id + '-crontabs-div').css('display', 'none');
		}
	}
}

function loadActions(id) {

	var stringMapping = new Array();

	stringMapping['head-1'] = translate('trigger.head-1');
	stringMapping['head-2'] = translate('trigger.head-2');
	stringMapping['ear-left'] = translate('trigger.ear-left');
	stringMapping['ear-right'] = translate('trigger.ear-right');
	
	$('#' + id + '-actions-table').empty();

	var all = getTempActions();

	if(undefined == all[id]) {

		all[id] = new Array();
	}

	if(all != null && all[id] != undefined) {

		if(all[id].length > 0) {

			for(i in all[id]) {

				var action = all[id][i];

				var str = stringMapping[action];

				if(undefined == str) {

					if(action.substr(0, 5) == 'rfid-') {

						var thingid = action.split('-')[1];
						var things = getThings();

						for(j in things) {

							if(things[j].id == thingid) {

								str = things[j].name;
							}
						}
					}
				}
		
				var tr = $('<tr></tr>');
				var td1 = $('<td>' + str + '</td>');
				var td2 = $('<td align="right"><input type="button" class="uibutton" value="' + translate('label.remove') + '" onclick="removeAction(\'' + id + '\', ' + i + ');" style="float: right" /></td>');

				$(tr).append(td1);
				$(tr).append(td2);

				$(td1).css('border-bottom', '1px solid #666');
				$(td2).css('border-bottom', '1px solid #666');
				
				$('#' + id + '-actions-table').css('border-collapse', 'collapse');
				$('#' + id + '-actions-table').append(tr);

				$('.uibutton').button();
				$('#' + id + '-actions-div').css('display', 'block');
			}
		} else {

			$('#' + id + '-actions-div').css('display', 'none');
		}
	}
}

function removeCrontab(app, id) {

	// TODO
	if(!confirm(translate('crontab.confirm.remove'))) {

		return false;
	}

	var current = getTempCrontab();

	if(undefined != current[app]) {

		var removed = current[app].splice(id, 1);
	}

	setTempCrontab(current);
	updateNabaztagCrontab(app);

	$(lastappdialog).dialog('close');
	configapp(app);
}

function removeAction(app, id) {

	if(!confirm(translate('trigger.confirm.remove'))) {

		return false;
	}

	var current = getTempActions();

	if(undefined != current[app]) {

		var removed = current[app].splice(id, 1);
	}

	setTempActions(current);
	updateNabaztagActions(app);
	loadActions(app);
}

function updateNabaztagCrontab(id) {

	var content = $('#crontabs').html().split('\\"').join('"');
	if(content.substr(0, 1) == '"') content = content.substr(1);
	if(content.substr(content.length - 1, 1) == '"') content = content.substr(0, content.length - 1);

	var alldata = eval('(' + content + ')');
	var appdata = alldata[id];

	if(appdata == undefined) {

		appdata = new Array();
	}

	var url = BASE_URL + '/vl/app.php?sn=' + $('#serial').val() + '&token=' + $('#token').val() + '&d=config,' + id + ',event=crontab,crontab=' + JSON.stringify(appdata).split(',').join('__');

	$.ajax({ async: false, url: url });

	setFlash(translate('trigger.flash.saved'));
	updateImage(id);
}

function updateImage(id) {

	// get image urls
	var url = BASE_URL + '/vl/app.php?sn=' + $('#serial').val() + '&token=' + $('#token').val() + '&d=getall';

	$.ajax({
		url: url,
		success: function(res) {

			var data = eval('(' + res + ')');

			var image = null;
			for(i in data.inuse) {

				if(data.inuse[i].code == id) {
	
					image = 'url("' + data.inuse[i].image + ',100")';
				}
			}

			var currentImage = $('#image_' + id).css('background-image');

			if(image != currentImage) {

				$('#image_' + id).css('background-image', image);
			}
		}
	});
}

function updateNabaztagActions(id) {

	var alldata = getTempActions();
	var appdata = alldata[id];

	if(appdata == undefined) {

		appdata = new Array();
	}

	var url = BASE_URL + '/vl/app.php?sn=' + $('#serial').val() + '&token=' + $('#token').val() + '&d=config,' + id + ',event=action,action=' + JSON.stringify(appdata).split(',').join('__');
	$.ajax({ async: false, url: url });

	setFlash(translate('trigger.flash.saved'));
	updateImage(id);
}

function addAction(id) {

	var current = getTempActions();
	var newAction = null;

	if(undefined == current[id]) {

		current[id] = new Array();
	}

	var map = new Array();

	map[-1] = null;
	map[0]  = 'head';
	map[1]  = 'ear';
	map[2]  = 'thing';

	var active = map[$("#" + id + "_trigger-2-action-accordion h3").index($("#" + id + "_trigger-2-action-accordion h3.ui-state-active"))];

	if(active != null) {

		if(active == 'head') {

			var howoften = 0;

			if($('#' + id + '-action-head-0').attr('checked')) {

				howoften = 1;
			}

			if($('#' + id + '-action-head-1').attr('checked')) {

				howoften = 2;
			}

			if(howoften == 0) {

				nabAlert(translate('trigger.error.head.nocnt'));
			} else {

				newAction = 'head-' + howoften;
			}
		}

		if(active == 'ear') {

			var site = null;

			if($('#' + id + '-action-ear-0').attr('checked')) {

				site = 'left';
			}

			if($('#' + id + '-action-ear-1').attr('checked')) {

				site = 'right';
			}

			if(site == null) {

				nabAlert(translate('trigger.error.ear.which'));
			} else {

				newAction = 'ear-' + site;
			}
		}

		if(active == 'thing') {

			$('.' + id + '-action-thing').each(function() {

				if($(this).attr('checked')) {

					newAction = 'rfid-' + $(this).attr('rel');
				}
			});

			if(newAction == null) {

				nabAlert(translate('thing.error.none_choosen'));
			}
		}

	} else {

		nabAlert(translate('trigger.error.none'));
	}

	if(null != newAction) {

		var ok = true;
		for(checkid in current) {

			for(i in current[checkid]) {

				if(current[checkid][i] == newAction) {

					nabAlert(translate('trigger.error.reserved'));
					ok = false;
				}
			}
		}

		if(ok) {
			current[id].push(newAction);
		}
	}

	setTempActions(current);
	updateNabaztagActions(id);

	loadActions(id);
}

function setTempActions(data) {

	$('#actions').html(JSON.stringify(data));
}

function nabAlert(msg) {

	$('<div title="' + translate('label.error') + '"><b>' + msg + '</b></div>').dialog({
		buttons: {

			'Ok' : function() {
				$(this).dialog('hide').dialog('destroy');
			}
		}
	});
}

function editName(serial, current) {

	$('<div title="' + translate('rabit.change.name.title') + '">' + translate('rabit.change.name.message') + '<br /><br /><input type="text" id="new-nab-name" value="' + current + '" /></div>').dialog({
		modal: true,
		buttons: {
			'[LANG:label.save]' : function() {

				var newname = $('#new-nab-name').val();

				if(newname.length < 1) {

					nabAlert('Bitte gib einen Namen an');
					return false;
				}
				
				var url = BASE_URL + '/?page=json&m=changeName&sn=' + $('#serial').val() + '&token=' + $('#token').val() + '&newname=' + newname;
				
				var t = this;

				$.ajax({
					url: url,
					success: function(msg) {

						if(readResponse(msg)) {

							$('#nab-data-name').html(newname);
							$(t).dialog('close');
						}
					}
				});
			},
			'[LANG:label.cancel]' : function() {

				$(this).dialog('close');
			}
		}
	});
}

function readResponse(msg) {

	if(msg.substr(0, 9) == '__error__') {

		nabAlert(msg.substr(9));

		return false;
	}

	return true;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////
//
// Confirmations
//
function confirmAddApp(code) {

	$('<div title="' + translate('confirmation.add_app.title') + '">' + translate('confirmation.add_app.msg') + '</div>').dialog({
		modal: true,
		buttons: {
			'[LANG:label.yes]' : function() {

				location.href = BASE_URL + '/?page=rabit&sn=' + $('#serial').val() + '&useapp=' + code;
			},
			'[LANG:label.no]' : function() {

				$(this).dialog('close');
			}
		}
	});
}

function confirmRemoveApp(code) {

	$('<div title="' + translate('confirmation.remove_app.title') + '">' + translate('confirmation.remove_app.msg') + '</div>').dialog({
		modal: true,
		buttons: {
			'[LANG:label.yes]' : function() {

				location.href = BASE_URL + '/?page=rabit&sn=' + $('#serial').val() + '&removeapp=' + code;
			},
			'[LANG:label.no]' : function() {

				$(this).dialog('close');
			}
		}
	});
}

function confirmRemoveRabit(serial) {

	$('<div title="' + translate('confirmation.remove_rabit.title') + '">' + translate('confirmation.remove_rabit.msg') + '</div>').dialog({
		modal: true,
		buttons: {
			'[LANG:label.yes]' : function() {

				location.href = BASE_URL + '/?page=rabits&sn=' + $('#serial').val() + '&nab_remove=' + serial;
			},
			'[LANG:label.no]' : function() {

				$(this).dialog('close');
			}
		}
	});
}

function confirmRemoveThing(id) {

	$('<div title="' + translate('confirmation.remove_thing.title') + '">' + translate('confirmation.remove_thing.msg') + '</div>').dialog({
		modal: true,
		buttons: {
			'[LANG:label.yes]' : function() {

				location.href = BASE_URL + '/?page=things&sn=' + $('#serial').val() + '&thing_remove=' + id;
			},
			'[LANG:label.no]' : function() {

				$(this).dialog('close');
			}
		}
	});
}

function confirmLogout() {

	$('<div title="' + translate('confirmation.logout.title') + '">' + translate('confirmation.logout.msg') + '</div>').dialog({
		modal: true,
		buttons: {
			'[LANG:label.yes]' : function() {

				location.href = '/?logout=true';
			},
			'[LANG:label.no]' : function() {

				$(this).dialog('close');
			}
		}
	});
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
