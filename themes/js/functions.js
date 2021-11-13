
function addVote (e, a) {

    var query = $H({id: a.dataset.id}).toQueryString();
    new Ajax.Request('/ajax/ajax.vote.php', { 
        parameters: query,
        onSuccess : function(data) {
            if (data.status == 200) {
                a.update(data.responseText);
            } else alert(data.responseText);
        },
        onFailure : function(data) {
	     	alert(data.responseText);
        }
    });	e.stop();
}

function message_onkeydown(textarea, n, div){
	
	if (!n || n == 0) {
		return;
	}

	var submit = $$('input[type="submit"]');
	var ccount = n - $F(textarea).length;
	$(div).update('Осталось: ' + ccount);
	//.setStyle({color:'gray'}).innerHTML = 'Осталось: ' + c;

	if ($F(textarea).length > n) { ////
		$(div).update('вы превысили количество вводимых символов!').setStyle({color:'red'});
		submit.invoke('disable');	//Modalbox.alert('вы превысили количество вводимых символов!');
		return;
	} else submit.invoke('enable');
}

function getXmlHttp() {
	var xmlhttp;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {
			xmlhttp = false;
		}
	}
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		xmlhttp = new XMLHttpRequest();
	}   return xmlhttp;
}

function Informer()
{
	var time = 3000;
	var text = new Array();	
	var file = '/ajax/json.php';
	var ajax = getXmlHttp();
	//type

	ajax.responseType = 'json';
	ajax.open('GET', file, true);
	ajax.onload = function()
	{
		if (this.status == 200)
		{
			var json = this.response;

			for (var i in json) {
				text[i] = '<li>' +stripslashes(json[i].title)+ '</li>';
			}

			var which = Math.round(Math.random()*(text.length - 1));
			$('rotator').update(text[which]);
			setTimeout(this.onload.bind(this), time);
		} 
	};

	ajax.onerror  = function()
	{
		alert('Error');
	}
	ajax.send();
}

function xhrRequest(type, url, opts, callback) // xhr
{
    var ajax = getXmlHttp(),
        data;

    if (typeof opts === 'function') {
        callback = opts;
        opts = null;
    }

    if (type === 'POST' && opts) {
        data = new FormData();

        for (var key in opts) {
            data.append(key, JSON.stringify(opts[key]));
        }
    }

    ajax.open(type, url);
	ajax.onload = function () {
        callback(JSON.parse(ajax.response));
    };

    ajax.send(opts ? data : null);
}


function xhrParse(file, that) {
	var info = 'File: '+file.name+' ('+file.size+')' || 'No file!';
	$(that).update(info);
}

function uploadImage(file, that, size = 5)
{
	var reader,
		result,
		accept;

	accept = that.accept;
	accept.split(',');

	if ( typeof accept !== 'undefined' && !accept.includes(file.type) )
	{
		alert ('Разрешены только изображения');
		that.value = '';
		return;
	}

	if ( typeof size !== 'undefined' && file.size > size * 1024 * 1024 )
	{
		alert ('Файл должен быть не более '+ size +' Мб.');
		that.value = '';
		return;
	}

	reader = new FileReader();
	reader.onload = function(e)
	{
		result = $('srcImage');
		xhrParse(file, 'xhrStatus');
		if (result !== 'undefined')
		{
			result.writeAttribute('src', e.target.result) ;
		}
	}
	reader.onerror = function(e)
	{
		alert('Error!'); return;
	}
	reader.readAsDataURL(file);
}

function number_format(number, decimals, point, thousands_sep) 
{	// Format a number with grouped thousands
	var i, j, kw, kd, km;
	// input sanitation & defaults
	if( isNaN(decimals = Math.abs(decimals)) ) {
		decimals = 0;
	}
	if ( typeof point === undefined ) {
		point = ',';
	}
	if ( typeof thousands_sep === undefined ) {
		thousands_sep = ' ';
	}

	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";
    j = (j = i.length) > 3 ? j % 3 : 0;

	km = (j ? i.substr(0, j) + thousands_sep : '');
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	kd = (decimals ? point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : '');
	return km + kw + kd;
}

function changeLanguage (a)
{ 			
	var attribut = a.getAttribute('data-lang');
	a.toggleClassName('ru');
	 
	(attribut == 'en') ?
	a.update('Russian version').setAttribute('data-lang', 'ru'):
	a.update('English version').setAttribute('data-lang', 'en');

	$$('.full').invoke('toggle');
}

function setLazy()
{
    lazy = document.querySelectorAll('img[loading="lazy"]');
    console.log('Found '+lazy.length+' lazy images');
}

function lazyLoad()
{
	lazy.forEach(function(img)
	{
		if ( isInViewport(img) )
		{
			if (img.hasAttribute('data-src')) {
				img.src = img.getAttribute('data-src');
				img.removeAttribute('data-src');
			}
		}
	});
    
    cleanLazy();
}

function cleanLazy()
{
    lazy = Array.prototype.filter.call(lazy, function(e) 
	{ 
		return e.getAttribute('data-src');
	});
}

function isInViewport(e)
{
    var rect = e.getBoundingClientRect();
    return (
		rect.right >= 0 &&
		rect.bottom >= 0 &&
		rect.top <= (window.innerHeight || document.documentElement.clientHeight) && 
		rect.left <= (window.innerWidth || document.documentElement.clientWidth)
	);
}

function registerListener(ev, func)
{
	window.addEventListener ?
	window.addEventListener(ev, func):
	window.attachEvent('on' + ev, func);
}

function print(output){
	document.write(output);
}

function stripslashes (str) {
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Ates Goral (http://magnetiq.com)
	// +      fixed by: Mick@el
	// +   improved by: marrtins
	// +   bugfixed by: Onno Marsman
	// +   improved by: rezna
	// +   input by: Rick Waldron
	// +   reimplemented by: Brett Zamir (http://brett-zamir.me)
	// +   input by: Brant Messenger (http://www.brantmessenger.com/)
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	// *     example 1: stripslashes('Kevin\'s code');
	// *     returns 1: "Kevin's code"
	// *     example 2: stripslashes('Kevin\\\'s code');
	// *     returns 2: "Kevin\'s code"
	return (str + '').replace(/\\(.?)/g, function (s, n1) {
	  	switch (n1) {
	  		case '\\':
			return '\\';
	  	case '0':
			return '\u0000';
	  	case '':
			return '';
	  	default:
			return n1;
	 	}
	});
}
