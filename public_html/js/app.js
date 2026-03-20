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

    var shippingOptions = document.querySelectorAll('.shipping-option');
    var summaryBox = document.querySelector('.order-summary');
    var shippingDisplay = document.getElementById('shipping-cost-display');
    var totalDisplay = document.getElementById('order-total-display');
    var relayPicker = document.getElementById('relay-picker');
    var relaySearchPostal = document.getElementById('relay-search-postal');
    var postalInput = document.getElementById('postal');

    if (shippingOptions.length > 0 && summaryBox) {
        var subtotal = parseFloat(summaryBox.dataset.subtotal) || 0;

        function updateShippingTotal() {
            var checked = document.querySelector('input[name="shipping_method"]:checked');
            if (!checked) return;
            var price = parseFloat(checked.dataset.price) || 0;
            if (shippingDisplay) {
                shippingDisplay.textContent = price > 0 ? formatEur(price) : 'Gratuit';
            }
            if (totalDisplay) {
                totalDisplay.textContent = formatEur(subtotal + price);
            }
        }

        function toggleRelayPicker() {
            var checked = document.querySelector('input[name="shipping_method"]:checked');
            if (!checked || !relayPicker) return;
            var needsRelay = checked.dataset.relay === '1';
            relayPicker.style.display = needsRelay ? 'block' : 'none';

            if (needsRelay && postalInput && postalInput.value.length >= 5) {
                relaySearchPostal.value = postalInput.value;
            }

            if (!needsRelay) {
                document.getElementById('relay-point-id').value = '';
                document.getElementById('relay-point-name').value = '';
                document.getElementById('relay-point-address').value = '';
                document.getElementById('relay-list').innerHTML = '';
            }
        }

        shippingOptions.forEach(function (option) {
            option.addEventListener('click', function () {
                shippingOptions.forEach(function (o) { o.classList.remove('active'); });
                option.classList.add('active');
                option.querySelector('input[type="radio"]').checked = true;
                updateShippingTotal();
                toggleRelayPicker();
            });
        });

        updateShippingTotal();
        toggleRelayPicker();
    }

    function formatEur(val) {
        return val.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' \u20ac';
    }

    var addressInput = document.getElementById('address');
    var suggestionsBox = document.getElementById('address-suggestions');
    var cityInput = document.getElementById('city');
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
                                    if (relaySearchPostal) relaySearchPostal.value = props.postcode;
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

function searchRelayPoints() {
    var postal = document.getElementById('relay-search-postal').value.trim();
    var relayList = document.getElementById('relay-list');
    var loading = document.getElementById('relay-loading');
    var shippingMethod = document.querySelector('input[name="shipping_method"]:checked');

    if (!postal || postal.length < 5) {
        alert('Veuillez entrer un code postal valide.');
        return;
    }

    loading.style.display = 'block';
    relayList.innerHTML = '';

    var carrier = shippingMethod ? shippingMethod.value : 'mondial_relay';

    fetch('/api/relay-points?postal=' + encodeURIComponent(postal) + '&carrier=' + encodeURIComponent(carrier))
        .then(function (res) { return res.json(); })
        .then(function (data) {
            loading.style.display = 'none';
            if (data.error) {
                relayList.innerHTML = '<p style="color: var(--text-light); padding: 12px;">' + data.error + '</p>';
                return;
            }
            if (!data.points || data.points.length === 0) {
                relayList.innerHTML = '<p style="color: var(--text-light); padding: 12px;">Aucun point relais trouvé pour ce code postal.</p>';
                return;
            }
            data.points.forEach(function (point) {
                var div = document.createElement('label');
                div.className = 'relay-item';
                div.innerHTML = '<input type="radio" name="relay_selection" value="' + point.id + '" data-name="' + escapeHtml(point.name) + '" data-address="' + escapeHtml(point.address) + '">' +
                    '<div class="relay-info"><strong>' + escapeHtml(point.name) + '</strong><small>' + escapeHtml(point.address) + '</small></div>';
                div.addEventListener('click', function () {
                    document.querySelectorAll('.relay-item').forEach(function (r) { r.classList.remove('selected'); });
                    div.classList.add('selected');
                    document.getElementById('relay-point-id').value = point.id;
                    document.getElementById('relay-point-name').value = point.name;
                    document.getElementById('relay-point-address').value = point.address;
                });
                relayList.appendChild(div);
            });
        })
        .catch(function () {
            loading.style.display = 'none';
            relayList.innerHTML = '<p style="color: var(--error); padding: 12px;">Erreur lors de la recherche.</p>';
        });
}

function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
