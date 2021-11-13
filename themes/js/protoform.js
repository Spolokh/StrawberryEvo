/**
	Protoform(class) v 2.0 23/01/2011
	Filippo Buratti 
	info [at] cssrevolt.com [dot] com 
	http://www.filippoburatti.net
*/

var Protoform = Class.create({

	initialize: function (form, options) {	

		this.options = {
			url: '',
			ajax : true,
			style: {},
			error: '<li>#{error}</li>',
			messagePosition : 'before',
			messageContainer: 'protoform-message'
		}
		
		Object.extend(this.options, options || {});
			
		this.REGEX_PATTERNS = {
			Req	 : /_Req/,
			Type : /_(Tel|Num|Int|Email|Url|Date)$/,
			Email: /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-]{2,})+\.)+([a-zA-Z0-9]{2,})+$/,
			Tel  : /^\+?([0-9]+[- ./]?[0-9]+[- ./]?[0-9]+[- ./]?[0-9]+[- ./]?[0-9]+)$/,
			Num	 : /^[-+]?\d+(\.\d+)?$/,
			Int  : /^\d+$/,
			Url  : /^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/,
			Date : /^(0[1-9]|[1-2][0-9]|3[01])\/(0[1-9]|1[0-2])\/[0-9]{2,4}$/	
			//DateEn	: /^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[01])\/[0-9]{2,4}$/
		}
		
		this.form = $(form);
		this.messageContainer = new Element('div', {'class':this.options.messageContainer}).hide();
		this.form.insert({after: this.messageContainer});
		this.formProcess = this.checkForm.bindAsEventListener(this);
		this.form.on('submit', this.formProcess);
	},

	checkForm: function(event)
	{
		event.stop(); 
		var errors	 = '';
		var faulty	 = null;
		var invalids = [];
		var template = new Template(this.options.error);
	
		this.form.getElements().each(function(formEl)
		{	
			formEl.removeAttribute('style');
			
			var value 		  = ($F(formEl)!= null)? $F(formEl): '' ;
			var fieldId		  = formEl.readAttribute('id')? formEl.readAttribute('id'): '';
			var fieldType 	  = fieldId.match( this.REGEX_PATTERNS['Type'] );
			var fieldRequired = fieldId.match( this.REGEX_PATTERNS['Req'] );
			var errorMessage  = formEl.readAttribute('title')
			? formEl.readAttribute('title')
			: formEl.readAttribute('placeholder');		
		
			if ( fieldRequired && value.blank() ) {
				
				var missing = 0;
				
				if (formEl.type.toLowerCase() == ('checkbox' || 'radio'))
				{
            		this.form.select('input[name="' +formEl.name+ '"]').each( function(inp) {
            		    if (inp.checked) {
            		       missing = 1; throw $break;
            		    }
            		} );
            	}
			
				if ( missing == 0) { //errors+= '<li>' + errorMessage + '</li>';
					errors+= template.evaluate({error: errorMessage});
					faulty = faulty || formEl;
					invalids.push(formEl);
					return;
				}
			}		
		
			if (fieldType  && !value.blank()) {				
				if ( this.checkField(value, fieldType[1]) ) { //errors += '<li>' + errorMessage + '</li>';
					errors+= template.evaluate({error: errorMessage});
					faulty = faulty || formEl;
					invalids.push(formEl);
				}
			}
		}.bind(this));
	
		if (errors == 0)
			this.options.ajax ? this.sendForm() : this.form.submit(); 	
		else	
			invalids
				.invoke ( 'setStyle', 'border:1px solid #FF0000' )
				.invoke('addClassName', 'invalid')
			
			var errorMessage = new Element ('ol', { 'class':'error' }).update(errors);
			this.messageContainer.update(errorMessage).show(); //this.messageContainer.show();
			faulty.focus();
	},
	
	checkField: function (value, type) {
		return !this.REGEX_PATTERNS[type].match(value) ? true : false;
	}, 
	
	sendForm: function(e) {

		var url 	= this.options.url? this.options.url: this.form.readAttribute('action');
		var reqType	= this.form.method || 'GET';
		var params 	= this.form.serialize();
		var myAjax 	= new Ajax.Request(url, {
			method    : reqType, 
			parameters: params,
			onCreate  : this.showLoad.bind(this), 
			onComplete: this.Response.bind(this)
		});
	},
	
	showLoad: function() {
		this.messageContainer.update('<p class="working">loading...</p>');
	},
		
	Response: function(transport){
		var response = transport.responseText;
		this.messageContainer.update(response);
		this.form.reset();
	}
});
