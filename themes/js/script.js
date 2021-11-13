document.observe('dom:loaded', function ()
{
	$(document).on('click', 'a.icon-heart', addVote);

	$(document).on('mouseover', '.showtooltip', function(e) 
	{
		var title = e.target.dataset.tooltip;
			title = title.gsub(/\\n/, '<br />');
		var tooltip = e.target.down('div.tooltip');
		return tooltip ? tooltip.update(title).show() : false;
	});
		
	$(document).on('mouseout', '.showtooltip', function(e)
	{
		var tooltip = e.target.down('div.tooltip');
		return tooltip ? tooltip.update().hide() : false;
	});

	const ajax = getXmlHttp();

	//$('xhrField').on('change', function() {
	//	uploadImage(this.files[0], this);
	//});

	$('editprofile').on('submit', function (e)
	{
		var time = 3000,
			form = new FormData (this),
			path = this.getAttribute('action'),
			type = this.getAttribute('method');

		var button = this.select('input[type="submit"]');
			button = button[0].disable().addClassName('wait');

		//ajax.onload = function () {
			
		//};

		ajax.onreadystatechange = function()
		{ 
			if ( this.readyState == 4 )
			{
				if ( this.status == 200 )
				{
					Modalbox.alert(this.responseText);
					var interval = setInterval(function() {
						button.enable().removeClassName('wait');
						Modalbox.hide();
						clearInterval(interval); 
					} , time);
				}
			
				if ( this.status == 500 ) 
				{
					alert(this.responseText);
					button.enable().removeClassName('wait');
				}
			}
		};
			
		ajax.onerror = function () 
		{
			alert("An error occurred during the transaction");
		};

		ajax.open (type, path); 
		ajax.setRequestHeader ('Cache-Control', 'no-cache');
		ajax.setRequestHeader ('X-Requested-With', 'XMLHttpRequest');
		ajax.send (form); e.stop();
	});

	new Fabtabs('tabs');
	new MaskedInput('#phone', '+7(999)999-9999');
});

window.onscroll = function()
{
	var menu = $('navbar');
	$('nachKopf').on('click', function(e)
	{                                 
		new Effect.ScrollTo(menu);
	});

	var y = document.viewport.getScrollOffsets().top;
	if (y < 100)
	{
		$('nachKopf').style.bottom = '-80px';
	}
	else
	{
		$('nachKopf').style.bottom = '20px';
	}
};

registerListener('load', setLazy);
registerListener('load', lazyLoad);
registerListener('scroll', lazyLoad);
