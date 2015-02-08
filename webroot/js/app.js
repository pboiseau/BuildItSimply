var config = {
	'dev_root': '/builditsimply',
	'root': '/'
}


$(document).ready(function(){

	function getSkills(){
		$.ajax({
			url: config.dev_root + '/skills/all',
			type: 'GET'
		}).done(function(data){
			console.log(data);
		});
	}

	getSkills();

	$('#skills').tokenfield({
		autocomplete: {
			// source: ['red','blue','green','yellow','violet','brown','purple','black','white']
			// source: [ { label: "Choice1", value: "value1" } ],
			source: function(request, response){

			},
			delay: 100
		},
		showAutocompleteOnFocus: true,
		inputType: 'text'
	});

});

