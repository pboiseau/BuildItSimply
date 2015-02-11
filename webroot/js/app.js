var config = {
	'dev_root': '/builditsimply',
	'root': '/',
	'request': $(location).attr('pathname').split('/')
}

/**
*	Ajax request to get all database skills
*	@param callback function show skills
**/
function getSkills(callback){
	$.ajax({
		url: config.dev_root + '/skills/all',
		type: 'GET'
	}).done(function(data, status, xhr){
		callback.call(this, data);
	}).fail(function(xhr, status, error){
		console.log(error);
	});
}

/**
*	Callback on input click to get and show skills
*	@param function getSkills
**/
if(config.request[config.request.length-1] === "profile"){
	getSkills(function(data){
		var skills = [];
		$.each(data.skills, function(key, value){
			skills.push(data.skills[key].name);
		});

		$('#skills').tokenfield({
			autocomplete: {
				source: skills,
				delay: 100
			},
			showAutocompleteOnFocus: true,
			inputType: 'text',
			limit: 25
		});
	});
}