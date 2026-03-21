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
    var paypalContainer = document.getElementById('paypal-button-container');
    var submitBtn = document.getElementById('submit-btn');
    var paypalRendered = false;

    function togglePaypalButtons() {
        var checked = document.querySelector('input[name="payment_method"]:checked');
        if (!checked || !paypalContainer) return;
        if (checked.value === 'paypal') {
            paypalContainer.style.display = 'block';
            if (submitBtn) submitBtn.style.display = 'none';
            if (!paypalRendered && typeof paypal_sdk !== 'undefined') {
                paypalRendered = true;
                paypal_sdk.Buttons({
                    style: { layout: 'vertical', color: 'gold', shape: 'rect', label: 'paypal' },
                    createOrder: function (data, actions) {
                        var form = document.querySelector('.checkout-form');
                        var formData = new FormData(form);
                        formData.set('payment_method', 'paypal');
                        return fetch('/api/paypal/create', { method: 'POST', body: formData })
                            .then(function (res) { return res.json(); })
                            .then(function (data) {
                                if (data.error) { alert(data.error); throw new Error(data.error); }
                                return data.paypal_order_id;
                            });
                    },
                    onApprove: function (data, actions) {
                        return fetch('/api/paypal/capture', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ paypal_order_id: data.orderID })
                        })
                        .then(function (res) { return res.json(); })
                        .then(function (result) {
                            if (result.redirect) {
                                window.location.href = result.redirect;
                            } else if (result.error) {
                                alert(result.error);
                            }
                        });
                    },
                    onCancel: function (data) {
                        fetch('/api/paypal/cancel', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ paypal_order_id: data.orderID })
                        }).then(function (res) { return res.json(); })
                        .then(function (result) {
                            if (result.redirect) window.location.href = result.redirect;
                        });
                    },
                    onError: function (err) {
                        alert('Erreur PayPal. Veuillez réessayer ou choisir un autre mode de paiement.');
                    }
                }).render('#paypal-button-container');
            }
        } else {
            paypalContainer.style.display = 'none';
            if (submitBtn) submitBtn.style.display = 'block';
        }
    }

    paymentOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            paymentOptions.forEach(function (o) { o.classList.remove('active'); });
            option.classList.add('active');
            option.querySelector('input[type="radio"]').checked = true;
            togglePaypalButtons();
        });
    });

    togglePaypalButtons();

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

        var addr = addressInput ? addressInput.value : '';
        var cty = cityInput ? cityInput.value : '';
        fetch('/api/relay-points?postal=' + encodeURIComponent(postal) + '&carrier=' + encodeURIComponent(carrier) + '&address=' + encodeURIComponent(addr) + '&city=' + encodeURIComponent(cty))
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
                    var distHtml = point.distance ? '<span class="relay-distance">' + escapeHtml(point.distance) + '</span>' : '';
                    div.innerHTML =
                        '<input type="radio" name="relay_selection" value="' + escapeAttr(point.id) + '">' +
                        '<div class="relay-info">' +
                            '<strong>' + escapeHtml(point.name) + typeTag + distHtml + '</strong>' +
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

        var inPersonOption = document.getElementById('payment-in-person');
        if (inPersonOption) {
            var isPickup = radio.value === 'pickup';
            inPersonOption.style.display = isPickup ? 'flex' : 'none';
            if (!isPickup) {
                var inPersonRadio = inPersonOption.querySelector('input[type="radio"]');
                if (inPersonRadio && inPersonRadio.checked) {
                    inPersonRadio.checked = false;
                    var bankTransfer = document.querySelector('input[name="payment_method"][value="bank_transfer"]');
                    if (bankTransfer) {
                        bankTransfer.checked = true;
                        bankTransfer.closest('.payment-option').classList.add('active');
                    }
                }
                inPersonOption.classList.remove('active');
            }
        }

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
                                    saveCheckoutData();
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

    var checkoutFields = ['firstname', 'lastname', 'email', 'phone', 'address', 'city', 'postal'];
    var storageKey = 'vogel_checkout';

    function saveCheckoutData() {
        var data = {};
        checkoutFields.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) data[id] = el.value;
        });
        try { localStorage.setItem(storageKey, JSON.stringify(data)); } catch(e) {}
    }

    function restoreCheckoutData() {
        try {
            var saved = JSON.parse(localStorage.getItem(storageKey));
            if (!saved) return;
            checkoutFields.forEach(function (id) {
                var el = document.getElementById(id);
                if (el && saved[id] && !el.value) {
                    el.value = saved[id];
                }
            });
            unlockShipping();
        } catch(e) {}
    }

    if (document.getElementById('firstname')) {
        restoreCheckoutData();
        checkoutFields.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', saveCheckoutData);
                el.addEventListener('change', saveCheckoutData);
            }
        });
    }
});
