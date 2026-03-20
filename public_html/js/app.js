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
    var shippingBlocks = document.querySelectorAll('.shipping-block');
    var summaryBox = document.querySelector('.order-summary');
    var shippingDisplay = document.getElementById('shipping-cost-display');
    var totalDisplay = document.getElementById('order-total-display');
    var postalInput = document.getElementById('postal');

    if (shippingOptions.length > 0 && summaryBox) {
        var subtotal = parseFloat(summaryBox.dataset.subtotal) || 0;

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
            var needsRelay = radio.dataset.relay === '1';

            if (needsRelay) {
                block.classList.add('relay-open');
                var relayPostalInput = block.querySelector('.relay-postal-input');
                if (relayPostalInput && postalInput && postalInput.value.length >= 5) {
                    relayPostalInput.value = postalInput.value;
                    autoSearchRelay(block);
                }
            }

            document.getElementById('relay-point-id').value = '';
            document.getElementById('relay-point-name').value = '';
            document.getElementById('relay-point-address').value = '';

            updateShippingTotal();
        }

        shippingOptions.forEach(function (option) {
            option.addEventListener('click', function (e) {
                e.preventDefault();
                handleShippingChange(option);
            });
        });

        document.querySelectorAll('.relay-search-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var block = btn.closest('.shipping-block');
                searchRelayForBlock(block);
            });
        });

        document.querySelectorAll('.relay-postal-input').forEach(function (input) {
            input.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var block = input.closest('.shipping-block');
                    searchRelayForBlock(block);
                }
            });
        });

        updateShippingTotal();
    }

    function autoSearchRelay(block) {
        var postalVal = block.querySelector('.relay-postal-input').value;
        if (postalVal.length >= 5) searchRelayForBlock(block);
    }

    function searchRelayForBlock(block) {
        var postalVal = block.querySelector('.relay-postal-input').value.trim();
        var carrier = block.querySelector('.relay-panel').dataset.carrier;
        var results = block.querySelector('.relay-results');
        var loading = block.querySelector('.relay-loading');

        if (postalVal.length < 5) {
            alert('Entrez un code postal valide (5 chiffres).');
            return;
        }

        loading.style.display = 'block';
        results.innerHTML = '';

        fetch('/api/relay-points?postal=' + encodeURIComponent(postalVal) + '&carrier=' + encodeURIComponent(carrier))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                loading.style.display = 'none';
                if (data.error) {
                    results.innerHTML = '<p style="padding:8px;color:var(--text-light);font-size:0.9rem;">' + data.error + '</p>';
                    return;
                }
                if (!data.points || data.points.length === 0) {
                    results.innerHTML = '<p style="padding:8px;color:var(--text-light);font-size:0.9rem;">Aucun point relais trouvé.</p>';
                    return;
                }
                data.points.forEach(function (point, idx) {
                    var div = document.createElement('label');
                    div.className = 'relay-item';
                    div.innerHTML =
                        '<input type="radio" name="relay_selection" value="' + escapeAttr(point.id) + '">' +
                        '<div class="relay-info">' +
                            '<strong>' + escapeHtml(point.name) + '</strong>' +
                            '<span class="relay-address">' + escapeHtml(point.address) + '</span>' +
                            (point.hours ? '<span class="relay-hours">' + escapeHtml(point.hours) + '</span>' : '') +
                        '</div>';
                    div.addEventListener('click', function () {
                        results.querySelectorAll('.relay-item').forEach(function (r) { r.classList.remove('selected'); });
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
                results.innerHTML = '<p style="padding:8px;color:var(--error);font-size:0.9rem;">Erreur de connexion.</p>';
            });
    }

    function formatEur(val) {
        return val.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' \u20ac';
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function escapeAttr(str) {
        return (str || '').replace(/"/g, '&quot;');
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
