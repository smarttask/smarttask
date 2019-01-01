(function ($) {

    jQuery(document).ready(function () {

        let searchCountryBtn = $('#searchCountry'),
            messages = jQuery('.messages'),
            zipCode,
            countryListNonce,
            errors = [],
            countryChosen;

        searchCountryBtn.on('click', function () {
            messages.html('');
            errors = [];


            zipCode = jQuery('#zipCode').val();
            countryChosen = jQuery('#countries');
            countryListNonce = jQuery('#countryListNonce').val();

            if (zipCode !== null && zipCode.length <= 3) {
                errors.push('Zip code should contain at least 4 numbers.');
            }

            if (countryChosen.val() !== null && countryChosen.val().length <= 0) {
                errors.push('Please Choose Country');
            }

            if (countryListNonce !== null && countryListNonce.length <= 0) {
                errors.push('Some Error found. Please refresh the page.');
            }

            if (errors.length === 0) {

                $.ajax({
                    context: this,
                    url: SmartObj.ajaxurl,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'searchCountry',
                        zipCode: zipCode,
                        countryChosen: countryChosen.val(),
                        countryListNonce: SmartObj.ajax_nonce
                    },
                    beforeSend: function () {
                        $(this).text('loading..');
                    },
                    complete: function () {
                        $(this).text('Search');
                    },
                    success: function (json) {

                        if (json['error']) {

                            messages.html(json['error']);

                        } else if (json['not_found']) {
                            let countryCode = countryChosen.find('option:selected').data('country');

                            if (countryCode)
                                getCountryFromApi(zipCode, countryCode, countryChosen.val());

                        } else if (json['success']) {

                            let html = '<ul>';
                            for (let a = 0; a < json.success.length; a++) {
                                html += '<li>Place: ' + json.success[a]['place'] + '</li>';
                                html += '<li>Name:' + json.success[a]['latitude'] + '</li>';
                                html += '<li>Latitude:' + json.success[a]['longitude'] + '</li>';
                                html += '<li>Longitude:' + json.success[a]['name'] + '</li>';
                                html += '<li>Zip Code:' + json.success[a]['zip'] + '</li>';
                            }
                            html += '</ul>';
                            messages.html(html);

                        } else {
                            //
                        }
                    }
                });

            } else {
                let html = '<ul>';
                for (let a = 0; a < errors.length; a++) {
                    html += '<li>' + errors[a] + '</li>';
                }
                html += '</ul>';
                messages.html(html);
            }

        });
    });

    function getCountryFromApi(zipCode, countryCode, countryID) {
        let messages = jQuery('.messages');
        try {

            var client = new XMLHttpRequest();
            client.onreadystatechange = function () {
                if (client.readyState == 4 && client.status == 200) {
                    let response = JSON.parse(client.responseText);
                    if (response.places) {
                        if (response.places.length > 0) {
                            messages.html("");
                            let html = '<ul>';

                            for (let a = 0; a < response.places.length; a++) {
                                html += '<ul>';
                                html += '<li>Place: ' + response.places[a]['place name'] + '</li>';
                                html += '<li>Name: ' + response.places[a]['place name'] + '</li>';
                                html += '<li>Latitude: ' + response.places[a].latitude + '</li>';
                                html += '<li>Longitude: ' + response.places[a].longitude + '</li>';
                                html += '<li>Zip Code: ' + zipCode + '</li>';
                                html += '<ul>';
                            }

                            html += '</ul>';
                            messages.html(html);
                            saveNewCountry(response, zipCode, countryID);
                        }
                    } else {
                        alert('country not found');
                    }
                } else if (client.readyState == 4 && client.status == 404) {
                    alert('country not found (404)');
                }
            }

            client.open("GET", 'http://api.zippopotam.us/' + countryCode + '/' + zipCode, true);
            client.send();
        } catch (e) {
            console.log(e.toString());
        }
    }

    function saveNewCountry(response, zipCode, countryID) {

        $.ajax({
            context: this,
            url: SmartObj.ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: {action: 'add_new_country', places: response, zipCode, countryID: countryID},
            success: function (json) {
                if (json['error']) {
                    alert('country not found');
                } else if (json['success']) {
                    alert(json['success']);
                }
            }
        });
    }

})(jQuery);
