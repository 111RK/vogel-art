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

    var shippingSection = document.getElementById('shipping-section');
    var shippingOptions = document.querySelectorAll('.shipping-option');
    var shippingBlocks = document.querySelectorAll('.shipping-block');
    var summaryBox = document.querySelector('.order-summary');
    var shippingDisplay = document.getElementById('shipping-cost-display');
    var totalDisplay = document.getElementById('order-total-display');
    var postalInput = document.getElementById('postal');
    var cityInput = document.getElementById('city');
    var addressInput = document.getElementById('address');
    var suggestionsBox = document.getElementById('address-suggestions');
    var currentPostal = '';

    var subtotal = summaryBox ? (parseFloat(summaryBox.dataset.subtotal) || 0) : 0;

    function unlockShipping() {
        if (!shippingSection) return;
        var postal = (postalInput ? postalInput.value.trim() : '');
        var addr = (addressInput ? addressInput.value.trim() : '');
        var city = (cityInput ? cityInput.value.trim() : '');

        if (postal.length >= 5 && addr.length > 2 && city.length > 1) {
            shippingSection.className = 'shipping-section-unlocked';

            if (!document.querySelector('input[name="shipping_method"]:checked')) {
                var firstRadio = document.querySelector('input[name="shipping_method"]');
                if (firstRadio) {
                    firstRadio.checked = true;
                    firstRadio.closest('.shipping-option').classList.add('active');
                    updateShippingTotal();
                }
            }

            if (postal !== currentPostal) {
                currentPostal = postal;
                preloadRelayPoints(postal);
            }
        } else {
            shippingSection.className = 'shipping-section-locked';
        }
    }

    function preloadRelayPoints(postal) {
        document.querySelectorAll('.relay-panel').forEach(function (panel) {
            var carrier = panel.dataset.carrier;
            loadRelayPoints(panel, postal, carrier);
        });
    }

    function loadRelayPoints(panel, postal, carrier) {
        var results = panel.querySelector('.relay-results');
        var loading = panel.querySelector('.relay-loading');

        loading.style.display = 'block';
        results.innerHTML = '';

        fetch('/api/relay-points?postal=' + encodeURIComponent(postal) + '&carrier=' + encodeURIComponent(carrier))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                loading.style.display = 'none';
                if (data.error) {
                    results.innerHTML = '<p class="relay-msg">' + data.error + '</p>';
                    return;
                }
                if (!data.points || data.points.length === 0) {
                    results.innerHTML = '<p class="relay-msg">Aucun point relais trouvé.</p>';
                    return;
                }
                data.points.forEach(function (point) {
                    var div = document.createElement('label');
                    var isLocker = point.type === 'locker';
                    div.className = 'relay-item' + (isLocker ? ' relay-locker' : '');
                    var typeTag = isLocker
                        ? '<span class="relay-type-tag relay-type-locker">Locker 24/7</span>'
                        : '<span class="relay-type-tag relay-type-relay">Relais</span>';
                    div.innerHTML =
                        '<input type="radio" name="relay_selection" value="' + escapeAttr(point.id) + '">' +
                        '<div class="relay-info">' +
                            '<strong>' + escapeHtml(point.name) + typeTag + '</strong>' +
                            '<span class="relay-address">' + escapeHtml(point.address) + '</span>' +
                            (point.hours ? '<span class="relay-hours">' + escapeHtml(point.hours) + '</span>' : '') +
                        '</div>';
                    div.addEventListener('click', function () {
                        panel.querySelectorAll('.relay-item').forEach(function (r) { r.classList.remove('selected'); });
                        div.classList.add('selected');
                        document.getElementById('relay-point-id').value = point.id;
                        document.getElementById('relay-point-name').value = point.name;
                        document.getElementById('relay-point-address').value = point.address;
                    });
                    results.appendChild(div);
                });
            })
            .catch(function () {
                loading.style.display = 'none';
                results.innerHTML = '<p class="relay-msg" style="color:var(--error)">Erreur de connexion.</p>';
            });
    }

    function updateShippingTotal() {
        var checked = document.querySelector('input[name="shipping_method"]:checked');
        if (!checked) return;
        var price = parseFloat(checked.dataset.price) || 0;
        if (shippingDisplay) shippingDisplay.textContent = price > 0 ? formatEur(price) : 'Gratuit';
        if (totalDisplay) totalDisplay.textContent = formatEur(subtotal + price);
    }

    function handleShippingChange(clickedOption) {
        shippingOptions.forEach(function (o) { o.classList.remove('active'); });
        clickedOption.classList.add('active');
        clickedOption.querySelector('input[type="radio"]').checked = true;

        shippingBlocks.forEach(function (b) { b.classList.remove('relay-open'); });

        var block = clickedOption.closest('.shipping-block');
        var radio = clickedOption.querySelector('input[type="radio"]');

        if (radio.dataset.relay === '1') {
            block.classList.add('relay-open');
        }

        document.getElementById('relay-point-id').value = '';
        document.getElementById('relay-point-name').value = '';
        document.getElementById('relay-point-address').value = '';
        document.querySelectorAll('.relay-item.selected').forEach(function (r) { r.classList.remove('selected'); });

        updateShippingTotal();
    }

    shippingOptions.forEach(function (option) {
        option.addEventListener('click', function (e) {
            e.preventDefault();
            handleShippingChange(option);
        });
    });

    if (postalInput) {
        postalInput.addEventListener('input', unlockShipping);
        postalInput.addEventListener('change', unlockShipping);
    }
    if (addressInput) addressInput.addEventListener('input', unlockShipping);
    if (cityInput) cityInput.addEventListener('input', unlockShipping);

    unlockShipping();

    function formatEur(val) {
        return val.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' \u20ac';
    }

    function escapeHtml(str) {
        var d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

    function escapeAttr(str) {
        return (str || '').replace(/"/g, '&quot;');
    }

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
                                    unlockShipping();
                                });
                                suggestionsBox.appendChild(item);
                            });
                            suggestionsBox.classList.add('active');
                        } else {
                            suggestionsBox.classList.remove('active');
                        }
                    })
                    .catch(function () { suggestionsBox.classList.remove('active'); });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!addressInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.remove('active');
            }
        });
    }
});
