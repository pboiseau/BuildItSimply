var config = {
    'dev_root': '/builditsimply',
    'root': '/',
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
            showAutocompleteOnFocus: true,
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

    function getCoordonate(){
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

    // Set editable field or not editable
    var allEdit = $('img[alt="edit"]');

    function editInput() {
        var inputSelected = $(this).prev()[0];
        inputSelected.removeAttribute('readonly');
        inputSelected.classList.remove("not-editable");
        inputSelected.classList.add("editable");
        inputSelected.focus();
    }

    function focusOut() {
        this.setAttribute('readonly', '');
        this.classList.remove("editable");
        this.classList.add("not-editable");
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
    limit: 10
});
