document.addEventListener('DOMContentLoaded', function () {
    var flashMessages = document.querySelectorAll('.flash');
    flashMessages.forEach(function (msg) {
        setTimeout(function () {
            msg.style.opacity = '0';
            msg.style.transition = 'opacity 0.3s';
            setTimeout(function () { msg.remove(); }, 300);
        }, 4000);
    });

    var paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            paymentOptions.forEach(function (o) { o.classList.remove('active'); });
            option.classList.add('active');
            option.querySelector('input[type="radio"]').checked = true;
        });
    });

    var addressInput = document.getElementById('address');
    var suggestionsBox = document.getElementById('address-suggestions');
    var cityInput = document.getElementById('city');
    var postalInput = document.getElementById('postal');
    var debounceTimer = null;

    if (addressInput && suggestionsBox) {
        addressInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var query = addressInput.value.trim();

            if (query.length < 3) {
                suggestionsBox.classList.remove('active');
                suggestionsBox.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(function () {
                fetch('https://api-adresse.data.gouv.fr/search/?q=' + encodeURIComponent(query) + '&limit=5')
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        suggestionsBox.innerHTML = '';
                        if (data.features && data.features.length > 0) {
                            data.features.forEach(function (feature) {
                                var props = feature.properties;
                                var item = document.createElement('div');
                                item.className = 'autocomplete-item';
                                item.innerHTML = props.name + ' <small>' + props.postcode + ' ' + props.city + '</small>';
                                item.addEventListener('click', function () {
                                    addressInput.value = props.name;
                                    if (cityInput) cityInput.value = props.city;
                                    if (postalInput) postalInput.value = props.postcode;
                                    suggestionsBox.classList.remove('active');
                                    suggestionsBox.innerHTML = '';
                                });
                                suggestionsBox.appendChild(item);
                            });
                            suggestionsBox.classList.add('active');
                        } else {
                            suggestionsBox.classList.remove('active');
                        }
                    })
                    .catch(function () {
                        suggestionsBox.classList.remove('active');
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!addressInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.remove('active');
            }
        });
    }
});
