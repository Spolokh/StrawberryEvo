<style>
	#orderForm fieldset {
		background: none
}#orderForm input, #orderForm textarea {
    margin: 5px 0; width: 100%
}</style>  

<form id="orderForm" action="/do/form" method="post">
	<fieldset>
		<input name="name" type="text" maxlength="50" placeholder="Ваше имя" pattern="^[А-Яа-яЁё\s]{3,50}$" required /><br />
		<input name="phone" type="tel" maxlength="50" placeholder="Ваш телефон" required /><br />
		<textarea name="comment" id="comments" placeholder="Ваше сообщение"></textarea>
		<input name="action" type="hidden" value="orderform"/>
		<div id="orderFormResult"></div>
	</fieldset>
</form>

<script>
	$('orderForm').on('submit', function(e)
	{ 
		var path = this.getAttribute('action');
		var type = this.getAttribute('method');
		var form = new FormData(this);
		var ajax = getXmlHttp();
		var time = 3000;

		ajax.onload = function()
		{
			if (this.status == 200)
			{
				$('orderFormResult').update(this.responseText).setStyle({color: 'green'});
				var interval = setInterval(function()
				{
					$('resultModal').update();
					$$('[data-dismiss="modal"]').invoke('click');
					clearInterval(interval); 
				}, time);
			}
		};
		
		ajax.open( type, file );
		ajax.setRequestHeader("Cache-Control", "no-cache");
		ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");	
		ajax.send(form); e.stop();
	});
</script>