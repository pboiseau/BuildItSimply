var config = {
    'root': '/',
    'dev_root': '/builditsimply',
    'request': $(location).attr('pathname').split('/')
}

/**
 *    Ajax request to get all database skills
 *    @param callback function show skills
 **/
function getSkills(callback) {
    $.ajax({
        url: config.dev_root + '/skills/all',
        type: 'GET'
    }).done(function (data, status, xhr) {
        callback.call(this, data);
    }).fail(function (xhr, status, error) {
        console.log(error);
    });
}

/**
 *    Callback on input click to get and show skills
 *    @param function getSkills
 **/
if (config.request[config.request.length - 1] === "profile") {

    /**
     * Get all skills from the database by ajax call
     */
    getSkills(function (data) {
        var skills = [];
        $.each(data.skills, function (key, value) {
            skills.push(data.skills[key].name);
        });

        $('#skills').tokenfield({
            autocomplete: {
                source: skills,
                delay: 100
            },
            showAutocompleteOnFocus: false,
            createTokensOnBlur: true,
            inputType: 'text',
            minLength: 1,
            limit: 25
        }).on('tokenfield:removedtoken', function (e) {
            $.ajax({
                url: config.dev_root + "/skills/freelance/delete",
                type: 'POST',
                data: {name: e.attrs.value}
            }).done(function (data, status, xhr) {
                //$('#info').append(data.delete.message).fadeIn();
            }).fail(function (xhr, status, error) {
                console.log(error);
            });
        });
    });

    /**
     * Get coordonate of an address using Google Maps API
     */
    function getCoordonate() {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode(
            {address: $('#city').val()},
            function (result, status) {
                if (status === 'OK') {
                    $('#getcoordonate').removeClass().addClass('btn btn-success');
                    $('#submit').prop('disabled', false);
                } else {
                    $('#getcoordonate').removeClass().addClass('btn btn-danger');
                    $('#submit').prop('disabled', true);
                }
                if (result.length > 0) {
                    var coordonate = result[0].geometry.location;
                    // console.log(coordonate.lat() + " - " + coordonate.lng());
                    $('input[name="account[lat]"]').val(coordonate.lat());
                    $('input[name="account[lng]"]').val(coordonate.lng());
                }
            }
        );
    }

    /**
     * Event Listener
     */
    $('#getcoordonate').click(getCoordonate);
    for (var i = 0; i < allEdit.length; i++) {
        allEdit[i].addEventListener('click', editInput, false);
        $(allEdit[i]).prev()[0].addEventListener('blur', focusOut, false);
    }

}

/**
 *    Tokenfield for project targets
 **/
$('#targets').tokenfield({
    minLength: 2,
    limit: 10,
    createTokensOnBlur: true
});
