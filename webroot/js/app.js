(function tokeninput(){
	$('#skills').tokenfield({
		autocomplete: {
			source: ['red','blue','green','yellow','violet','brown','purple','black','white'],
			delay: 100
		},
		showAutocompleteOnFocus: true
	})
})();