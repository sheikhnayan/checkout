

// ----- SCRIPT BOUNDARY -----


                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', null);
            

// ----- SCRIPT BOUNDARY -----



// ----- SCRIPT BOUNDARY -----


            // Guest counter - robust override to fix double-fire / missed-click bug.
            // main.js's updateDisplay() calls checkEligibility() which is undefined and
            // throws mid-function, leaving state inconsistent. This replaces the global
            // increments/decrements with safe versions and uses a single delegated
            // click handler with a click guard to prevent double firing.
            (function () {
                var guestCounts = { men: 0, women: 0 };
                var lastClickAt = 0;

                function readDom() {
                    var menEl = document.getElementById('menCount');
                    var womenEl = document.getElementById('womenCount');
                    if (menEl) guestCounts.men = parseInt(menEl.textContent, 10) || 0;
                    if (womenEl) guestCounts.women = parseInt(womenEl.textContent, 10) || 0;
                }
                function writeDom() {
                    var menEl = document.getElementById('menCount');
                    var womenEl = document.getElementById('womenCount');
                    var totalEl = document.getElementById('totalCount');
                    var menHidden = document.getElementById('men_count');
                    var womenHidden = document.getElementById('women_count');
                    if (menEl) menEl.textContent = guestCounts.men;
                    if (womenEl) womenEl.textContent = guestCounts.women;
                    if (totalEl) totalEl.textContent = guestCounts.men + guestCounts.women;
                    if (menHidden) menHidden.value = guestCounts.men;
                    if (womenHidden) womenHidden.value = guestCounts.women;
                }
                window.increments = function (type) {
                    if (type !== 'men' && type !== 'women') return;
                    readDom();
                    guestCounts[type] += 1;
                    writeDom();
                };
                window.decrements = function (type) {
                    if (type !== 'men' && type !== 'women') return;
                    readDom();
                    if (guestCounts[type] > 0) guestCounts[type] -= 1;
                    writeDom();
                };

                // Remove inline onclick handlers so the buttons fire exactly once via
                // our delegated handler below (prevents both onclick and a stale
                // listener from firing in the same tap).
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.guest-qty-btn').forEach(function (btn) {
                        btn.removeAttribute('onclick');
                    });
                    readDom();
                    writeDom();
                });

                // Single delegated click handler with a 200ms guard to coalesce any
                // duplicate fire (e.g. touchend + click on some mobile browsers).
                document.addEventListener('click', function (e) {
                    var btn = e.target.closest('.guest-qty-btn');
                    if (!btn) return;
                    e.preventDefault();
                    e.stopPropagation();
                    var now = Date.now();
                    if (now - lastClickAt < 200) return; // ignore rapid duplicate
                    lastClickAt = now;
                    var type = btn.getAttribute('data-type');
                    var action = btn.getAttribute('data-action');
                    if (!type || !action) return;
                    if (action === 'inc') window.increments(type);
                    else if (action === 'dec') window.decrements(type);
                });
            })();

            // Cart toast: show a notification when an item is added (helpful on mobile
            // where the cart sidebar is below the fold).
            (function () {
                var hideTimer = null;
                window.showToast = function (title, sub, iconClass) {
                    var toast = document.getElementById('cv-cart-toast');
                    if (!toast) return;
                    var titleEl = toast.querySelector('.cv-toast-title');
                    var subEl = document.getElementById('cv-cart-toast-sub');
                    var iconEl = toast.querySelector('.cv-toast-icon i');
                    if (titleEl) titleEl.textContent = title || 'Notice';
                    if (subEl) subEl.textContent = sub || '';
                    if (iconEl) iconEl.className = iconClass || 'fas fa-check';
                    toast.classList.add('is-visible');
                    if (hideTimer) clearTimeout(hideTimer);
                    hideTimer = setTimeout(function () { window.hideCartToast(); }, 4000);
                };
                window.showCartToast = function (packageName, guests) {
                    var qty = parseInt(guests, 10) || 1;
                    var label = qty + (qty === 1 ? ' guest' : ' guests');
                    window.showToast('Added to cart!', packageName ? (packageName + ' · ' + label) : label, 'fas fa-check');
                };
                window.hideCartToast = function () {
                    var toast = document.getElementById('cv-cart-toast');
                    if (!toast) return;
                    toast.classList.remove('is-visible');
                    if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
                };
            })();

            // Inject inline info icons into Service Fee / Tax / Gratuity rows so the
            // row's ::after stays free for the custom hover tooltip.
            (function () {
                function inject() {
                    var rows = document.querySelectorAll(
                        '#cv-order-sidebar .pricing-shell .default-service-charge, ' +
                        '#cv-order-sidebar .pricing-shell .default-sales-tax, ' +
                        '#cv-order-sidebar .pricing-shell .default-gratuity'
                    );
                    rows.forEach(function (row) {
                        if (!row.hasAttribute('data-tip')) return;
                        if (row.querySelector('.cv-row-info-icon')) return;
                        var labelSpan = row.querySelector('span');
                        if (!labelSpan) return;
                        var icon = document.createElement('span');
                        icon.className = 'cv-row-info-icon';
                        icon.textContent = 'i';
                        icon.setAttribute('aria-hidden', 'true');
                        labelSpan.appendChild(icon);
                    });
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', inject);
                } else {
                    inject();
                }
                // Re-inject after JS moves pricing-shell into the sidebar.
                setTimeout(inject, 50);
                setTimeout(inject, 500);
            })();
        

// ----- SCRIPT BOUNDARY -----



// ----- SCRIPT BOUNDARY -----



// ----- SCRIPT BOUNDARY -----


            function showCheckoutProcessingOverlay() {
                var overlay = document.getElementById('checkout-processing-overlay');
                if (!overlay) {
                    return;
                }

                overlay.classList.add('is-visible');
                overlay.setAttribute('aria-hidden', 'false');

                var submitButton = document.getElementById('submitBtn');
                if (submitButton) {
                    if (!submitButton.dataset.defaultText) {
                        submitButton.dataset.defaultText = submitButton.textContent;
                    }
                    submitButton.disabled = true;
                    submitButton.textContent = 'Processing...';
                }
            }

            function hideCheckoutProcessingOverlay() {
                var overlay = document.getElementById('checkout-processing-overlay');
                if (!overlay) {
                    return;
                }

                overlay.classList.remove('is-visible');
                overlay.setAttribute('aria-hidden', 'true');

                var submitButton = document.getElementById('submitBtn');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.dataset.defaultText || 'Complete Purchase';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                var form = document.getElementById('payment-form');
                if (!form) {
                    return;
                }

                form.addEventListener('submit', function(event) {
                    window.setTimeout(function() {
                        if (!event.defaultPrevented) {
                            showCheckoutProcessingOverlay();
                        }
                    }, 0);
                });
            });
        

// ----- SCRIPT BOUNDARY -----


            // --- Shareable Link Refinement ---
            function openPackageTab() {
                // If reservation tabs exist, switch to package tab
                var packageTab = $("nav .tab[data-name='package']");
                if(packageTab.length) {
                    packageTab.trigger('click');
                } else {
                    // If no nav, just show .package section
                    $('.guest').hide();
                    $('.package').show();
                }
            }
            // --- Shareable Link Logic ---
            function getCurrentSelections() {
                // Get selected package
                var packageId = $('#package_id').val() || '';
                // Get selected add-ons (comma separated)
                var addons = $('#addons').val() || '';
                // Get guest count
                var guests = $('.package_number_of_guest').val() || '';
                // Get use date
                var useDate = $('.package_use_date').val() || '';
                return { packageId, addons, guests, useDate };
            }

            function setSelectionsFromParams(params) {
                    // Always open package tab if package param exists
                    if(params.package) {
                        openPackageTab();
                    }
                    // Open all packages (simulate click on all .vip-btn)
                    setTimeout(function() {
                        $('.vip-btn').each(function(){
                            if(!$(this).text().toLowerCase().includes('added')) {
                                $(this).trigger('click');
                            }
                        });
                        // Set package selection and guest count
                        if(params.package) {
                            var sel = $('.package_number_of_guestss[data-id="'+params.package+'"]');
                            if(params.guests && sel.length) {
                                sel.val(params.guests).trigger('change');
                            }
                        }
                        // Show all add-ons and check those in params
                        if(params.addons) {
                            var ids = params.addons.split(',');
                            // Show add-ons section
                            $('.addons').show();
                            ids.forEach(function(id) {
                                var cb = $('.addons-list input[type="checkbox"]#'+id);
                                if(cb.length && !cb.prop('checked')) {
                                    cb.prop('checked', true).trigger('click');
                                }
                            });
                        }
                        // Show cost breakdown
                        $('.dynamic-price').show();
                        $('.default-price').hide();
                        $('.default-total').show();
                    }, 700);
                        // Keep selected date synced to hidden checkout field.
                        var desiredDate = params.use_date || '';
                        if ($('#package_use_date option[value="' + desiredDate + '"]').length) {
                            $('#package_use_date').val(desiredDate);
                        } else {
                            $('#package_use_date').val('');
                        }
                        $('.package_use_date').val($('#package_use_date').val());
            }

            function getUrlWithSelections() {
                var sel = getCurrentSelections();
                var url = window.location.origin + window.location.pathname + '?package=' + encodeURIComponent(sel.packageId) + '&addons=' + encodeURIComponent(sel.addons) + '&guests=' + encodeURIComponent(sel.guests) + '&use_date=' + encodeURIComponent(sel.useDate);
                return url;
            }

            // --- End Shareable Link Logic ---

                // --- End Shareable Link Refinement ---
            $('#businessExpenseCheckbox').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#businessFields').slideDown();
                } else {
                    $('#businessFields').slideUp();
                }
            });
        

// ----- SCRIPT BOUNDARY -----


            $(function () {
                function isThisWeek(date) {
                    const now = new Date();
                    const input = new Date(date);
                    const first = now.getDate() - now.getDay();
                    const last = first + 6;
                    const weekStart = new Date(now.setDate(first));
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(now.setDate(last));
                    weekEnd.setHours(23, 59, 59, 999);
                    return input >= weekStart && input <= weekEnd;
                }
                function isThisMonth(date) {
                    const now = new Date();
                    const input = new Date(date);
                    return input.getMonth() === now.getMonth() && input.getFullYear() === now.getFullYear();
                }
                function isThisYear(date) {
                    const now = new Date();
                    const input = new Date(date);
                    return input.getFullYear() === now.getFullYear();
                }
                $('.event-filter').on('click', function () {
                    const filter = $(this).data('filter');
                    $('.event-filter').removeClass('active');
                    $(this).addClass('active');
                    $('#events-list .event-card-item').each(function () {
                        const date = $(this).data('date');
                        let show = false;
                        if (filter === 'week') show = isThisWeek(date);
                        if (filter === 'month') show = isThisMonth(date);
                        if (filter === 'year') show = isThisYear(date);
                        $(this).toggle(show);
                    });
                });
                // Optionally, trigger default filter (e.g., show all or this week)
                $('.event-filter[data-filter="year"]').trigger('click');
            });
        

// ----- SCRIPT BOUNDARY -----


            // ======= CART SYSTEM ======= Define immediately in global scope
            window.cart = [];
            window.cartCoupon = window.cartCoupon || null;
            window.eventCapacityState = {
                limit: null,
                remaining: null
            };

            // Ensure cart is always an array
            function ensureCartArray() {
                if (!Array.isArray(window.cart)) {
                    console.warn('window.cart was not an array, resetting');
                    window.cart = [];
                }
            }

            function formatCurrency(value) {
                return '$' + new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(Number(value) || 0);
            }

            function syncCheckoutCartFields() {
                var form = document.getElementById('payment-form');
                if (!form || !Array.isArray(window.cart) || !window.cart.length) {
                    return;
                }

                var cartField = form.querySelector('#cart_items');
                var packageField = form.querySelector('#package_id');
                var guestField = form.querySelector('.package_number_of_guest');
                var addonsField = form.querySelector('#addons');
                var firstItem = window.cart[0];
                var totalGuests = window.cart.reduce(function(sum, item) {
                    return sum + (parseInt(item.guests, 10) || 1);
                }, 0);
                var addonNames = window.cart.reduce(function(all, item) {
                    return all.concat(Array.isArray(item.addons) ? item.addons : []);
                }, []).map(function(addon) {
                    return addon.name + ' ($' + addon.price + ')';
                });

                if (cartField) {
                    cartField.value = JSON.stringify(window.cart);
                }
                if (packageField && firstItem) {
                    packageField.value = firstItem.packageId || packageField.value;
                }
                if (guestField) {
                    guestField.value = totalGuests || 1;
                }
                if (addonsField) {
                    addonsField.value = addonNames.join(', ');
                }
            }

            function cartRequiresTransportation() {
                ensureCartArray();
                return window.cart.some(function(pkg) {
                    return pkg.transportation === true || pkg.transportation === 1 || pkg.transportation === '1';
                });
            }

            function syncTransportationStateFromCart() {
                window.requiresTransportation = cartRequiresTransportation();
                const transportationFields = $('#transport-form').find('input, select, textarea');
                const transportationPhoneField = $('input[name="transportation_phone"]');
                const transportationAddressField = $('input[name="transportation_address"]');
                const transportationPickupTimeField = $('input[name="transportation_pickup_time"]');
                const transportationGuestField = $('input[name="transportation_guest"]');
                const pickupDateField = $('input[name="package_use_date"]');
                const driverNotificationConsentWrap = $('.driver-notification-consent-wrap');
                const driverNotificationConsentInputs = $('.driver-notification-consent-input');
                if (window.requiresTransportation) {
                    $('#step-2 .step-title').text('Transportation');
                    $('#next-to-transport').text('Next: Transportation Details');
                    transportationFields.prop('disabled', false);
                    transportationPhoneField.prop('required', true).attr('aria-required', 'true');
                    transportationAddressField.prop('required', true).attr('aria-required', 'true');
                    transportationPickupTimeField.prop('required', true).attr('aria-required', 'true');
                    transportationGuestField.prop('required', true).attr('aria-required', 'true');
                    if (!Number.isFinite(parseInt(transportationGuestField.val(), 10)) || parseInt(transportationGuestField.val(), 10) < 1) {
                        transportationGuestField.val('1');
                    }
                    pickupDateField.prop('required', true).attr('aria-required', 'true');
                    driverNotificationConsentWrap.css('display', 'flex');
                    driverNotificationConsentInputs.prop('required', true).attr('aria-required', 'true');
                } else {
                    $('#step-2 .step-title').text('Confirmation');
                    $('#next-to-transport').text('Next: Transportation Confirmation');
                    transportationFields.prop('disabled', true);
                    transportationPhoneField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    transportationAddressField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    transportationPickupTimeField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    transportationGuestField.prop('required', false).removeClass('required-field').removeAttr('aria-required').val('0');
                    pickupDateField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    driverNotificationConsentWrap.hide();
                    driverNotificationConsentInputs.prop('checked', false).prop('required', false).removeAttr('aria-required');
                }
            }

            function parseMultipleFlag(value) {
                return value === true || value === 1 || value === '1' || value === 'true';
            }

            function getPackageMultipleFromDom(packageId) {
                var multipleValue = $('.package_number_of_guestss[data-id="' + packageId + '"]').first().data('multiple');
                return parseMultipleFlag(multipleValue);
            }

            function getBillableGuests(pkg) {
                return parseMultipleFlag(pkg.isMultiple) ? (parseInt(pkg.guests) || 1) : 1;
            }

            function getSelectedUseDate() {
                return String($('#package_use_date').val() || $('.package_use_date').val() || '').trim();
            }

            window.getSelectedUseDate = getSelectedUseDate;

            function showReservationDateError(message) {
                var text = String(message || 'Please select a reservation date.').trim();
                $('#package_use_date').addClass('required-field').attr('aria-invalid', 'true');
                $('#package_use_date_error').text(text).show();
            }

            function clearReservationDateError() {
                $('#package_use_date').removeClass('required-field').removeAttr('aria-invalid');
                $('#package_use_date_error').hide();
            }

            function ensureReservationDateSelected() {
                var selectedDate = window.getSelectedUseDate();
                if (selectedDate) {
                    clearReservationDateError();
                    return true;
                }

                showReservationDateError('Please select a reservation date above before continuing.');
                if (typeof window.showToast === 'function') {
                    window.showToast('Must Choose Date', 'Please select a reservation date to continue.', 'fas fa-calendar-alt');
                }
                var dateCard = document.querySelector('.hero-date-card');
                if (dateCard && typeof dateCard.scrollIntoView === 'function') {
                    dateCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                $('#package_use_date').trigger('focus');
                return false;
            }

            function getCartAttendeeCount(excludedPackageId) {
                ensureCartArray();
                return window.cart.reduce(function(sum, pkg) {
                    if (excludedPackageId !== undefined && excludedPackageId !== null && String(pkg.packageId) === String(excludedPackageId)) {
                        return sum;
                    }

                    return sum + (parseInt(pkg.guests, 10) || 1);
                }, 0);
            }

            function syncUseDateField() {
                var selected = window.getSelectedUseDate();
                if (selected) {
                    $('.package_use_date').val(selected);
                } else {
                    $('.package_use_date').val('');
                }
            }

            window.syncUseDateField = syncUseDateField;

            function clearGuestFieldError($field) {
                var $control = $field.closest('.vip-guest-control');
                $control.find('.package-guest-error').hide().text('');
                $field.removeClass('required-field').removeAttr('aria-invalid');
            }

            function showGuestFieldError($field, message) {
                var $control = $field.closest('.vip-guest-control');
                $control.find('.package-guest-error').text(message || 'The quantity you entered is unavailable for the selected date. Please choose a lower number.').show();
                $field.addClass('required-field').attr('aria-invalid', 'true');
            }

            function updateGuestSelectOptions($field, maxSelectable, soldOutMessage) {
                var currentVal = $field.val();
                var hasPlaceholder = !currentVal || currentVal === '';
                var current = parseInt(currentVal, 10) || 1;
                var safeMax = Math.max(0, parseInt(maxSelectable, 10) || 0);
                var isTicketInput = $field.is('input[type="number"]');
                var isTicketSelect = $field.hasClass('ticket-select-lazy');
                var $control = $field.closest('.vip-guest-control');
                var $inputWrap = $control.find('.package-guest-input-wrap');
                var $soldOut = $control.find('.package-soldout');
                var html = '';

                clearGuestFieldError($field);

                if (safeMax <= 0) {
                    $inputWrap.hide();
                    $soldOut.text(soldOutMessage || 'Sold Out for Selected Date').show();
                    $field.val('1').prop('disabled', true);
                    return;
                }

                $soldOut.hide();
                $inputWrap.show();

                if (isTicketInput) {
                    var safeValue = Math.min(Math.max(current, 1), safeMax);
                    $field.prop('disabled', false);
                    $field.attr('min', '1');
                    $field.attr('step', '1');
                    $field.attr('max', String(safeMax));
                    $field.val(String(safeValue));
                    return;
                }

                if (isTicketSelect) {
                    var showMax = Math.min(15, safeMax);
                    $field.data('ticket-max', safeMax).attr('data-ticket-max', safeMax);
                    var ticketHtml = '<option value=""># of Tickets</option>';
                    for (var i = 1; i <= showMax; i++) {
                        ticketHtml += '<option value="' + i + '">' + i + ' ' + (i === 1 ? 'ticket' : 'tickets') + '</option>';
                    }
                    $field.html(ticketHtml);
                    if (hasPlaceholder) {
                        $field.val('');
                    } else {
                        var safeValue = Math.min(Math.max(current, 1), safeMax);
                        $field.val(String(safeValue));
                    }
                    $field.prop('disabled', false);
                    return;
                }

                html += '<option value=""># of Guests</option>';
                for (var i = 1; i <= safeMax; i++) {
                    html += '<option value="' + i + '">' + i + ' ' + (i === 1 ? 'guest' : 'guests') + '</option>';
                }

                $field.html(html);
                if (hasPlaceholder) {
                    $field.val('');
                } else {
                    $field.val(String(Math.min(current, safeMax)));
                }
                $field.prop('disabled', false);
            }

            window.clearGuestFieldError = clearGuestFieldError;
            window.showGuestFieldError = showGuestFieldError;
            window.updateGuestSelectOptions = updateGuestSelectOptions;
            window.parseMultipleFlag = parseMultipleFlag;

            // Ticket select lazy-load: append next 15 options when scrolled to bottom
            $(document).on('scroll', '.ticket-select-lazy', function () {
                var $sel = $(this);
                var shownMax = $sel.find('option').length;
                var totalMax = parseInt($sel.data('ticket-max'), 10) || shownMax;
                if (shownMax >= totalMax) { return; }
                var el = this;
                if (el.scrollHeight - el.scrollTop - el.clientHeight < 40) {
                    var nextMax = Math.min(shownMax + 15, totalMax);
                    for (var i = shownMax + 1; i <= nextMax; i++) {
                        $sel.append('<option value="' + i + '">' + i + '</option>');
                    }
                }
            });
            $(document).on('keydown', '.ticket-select-lazy', function (e) {
                if (e.key !== 'ArrowDown') { return; }
                var $sel = $(this);
                var shownMax = $sel.find('option').length;
                var totalMax = parseInt($sel.data('ticket-max'), 10) || shownMax;
                if (shownMax >= totalMax) { return; }
                if (parseInt($sel.val(), 10) >= shownMax) {
                    var nextMax = Math.min(shownMax + 15, totalMax);
                    for (var i = shownMax + 1; i <= nextMax; i++) {
                        $sel.append('<option value="' + i + '">' + i + '</option>');
                    }
                }
            });

            function refreshEventPackageSelectionLimits(showAlertWhenReduced) {
                var useDate = window.getSelectedUseDate();
                $('.package_number_of_guestss').each(function() {
                    var $field = $(this);
                    var packageId = $field.data('id');
                    var previous = parseInt($field.val(), 10) || 1;

                    $.get('/' + null + '/package/' + packageId + '/capacity', { use_date: useDate })
                        .done(function(response) {
                            var endpointMax = parseInt(response.max_select, 10);
                            if (!Number.isFinite(endpointMax)) {
                                endpointMax = parseInt(response.capacity, 10);
                            }
                            if (!Number.isFinite(endpointMax)) {
                                endpointMax = 1;
                            }

                            var cartRemaining = endpointMax;
                            if (response.event_remaining !== null && response.event_remaining !== undefined) {
                                var eventRemaining = parseInt(response.event_remaining, 10);
                                if (Number.isFinite(eventRemaining)) {
                                    cartRemaining = Math.min(cartRemaining, Math.max(eventRemaining - getCartAttendeeCount(packageId), 0));
                                }
                            }

                            updateGuestSelectOptions($field, cartRemaining, response.message || 'Sold Out for Selected Date');

                            var reducedTo = parseInt($field.val(), 10) || 1;
                            var existingCartPackage = window.cart.find(function(pkg) { return String(pkg.packageId) === String(packageId); });
                            if (existingCartPackage && (parseInt(existingCartPackage.guests, 10) || 1) !== reducedTo) {
                                existingCartPackage.guests = reducedTo;
                                syncCheckoutCartFields();
                                window.renderCart();
                                window.calculateCartTotal();
                            }

                            if (showAlertWhenReduced && previous > reducedTo) {
                                alert('Your guest count was adjusted to match current availability for the selected date.');
                            }

                            var $button = $('.vip-btn[data-id="' + packageId + '"]');
                            setPackageButtonState($button, cartRemaining <= 0, cartRemaining <= 0 ? 'Sold Out' : ($button.data('default-label') || 'Add to Cart'));
                        });
                });
            }

            function setPackageButtonState($button, disabled, label) {
                if (!$button.length) {
                    return;
                }

                if (!$button.data('default-label')) {
                    $button.data('default-label', ($button.attr('data-default-label') || $button.text() || 'Add to Cart').trim());
                }

                $button.prop('disabled', disabled);
                $button.text(label || $button.data('default-label'));
            }

            function syncEventCapacityUi() {
                return;
            }

            function resetCartForDateChange() {
                ensureCartArray();
                if (!window.cart.length) {
                    return;
                }

                window.cart = [];
                window.cartCoupon = null;

                $('#cart-list').html('');
                $('#cart-total').text('');
                $('#cart-coupon').html('');
                $('#cart-section').hide();
                $('#shareLinkContainer').hide();
                $('#shareableLink').val('').hide();
                $('#promo_code').val('');
                $('#applyPromoBtn').prop('disabled', false);
                $('.promo_code').val('');
                $('#package_id').val('');
                $('#addons').val('');
                $('.package_number_of_guest').val('2');
                $('.package_number_of_guestss').val('2');
                $('.vip-card').removeClass('selected');

                syncCheckoutCartFields();
                window.calculateCartTotal();
                syncTransportationStateFromCart();
                syncEventCapacityUi();
            }

            window.addPackageToCart = function(packageId, packageName, packagePrice, guests, addons, transportation, isMultiple) {
                console.log('addPackageToCart called', packageId, packageName);
                ensureCartArray();
                var normalizedGuests = parseInt(guests, 10) || 1;
                var useDate = window.getSelectedUseDate();

                if (!ensureReservationDateSelected()) {
                    return Promise.resolve(false);
                }

                // Check daily limits for this package
                return $.get('/' + null + '/package/' + packageId + '/capacity', { use_date: useDate, requested_quantity: normalizedGuests })
                    .then(function(response) {
                        if (!response.available) {
                            alert(response.message || 'This package is currently unavailable for the selected date.');
                            return false;
                        }

                        var effectiveMax = parseInt(response.max_select, 10);
                        if (!Number.isFinite(effectiveMax)) {
                            effectiveMax = parseInt(response.capacity, 10) || 0;
                        }
                        if (response.event_remaining !== null && response.event_remaining !== undefined) {
                            var eventRemaining = parseInt(response.event_remaining, 10);
                            if (Number.isFinite(eventRemaining)) {
                                effectiveMax = Math.min(effectiveMax, Math.max(eventRemaining - getCartAttendeeCount(packageId), 0));
                            }
                        }

                        if (normalizedGuests > effectiveMax) {
                            var $field = $('.package_number_of_guestss[data-id="' + packageId + '"]');
                            updateGuestSelectOptions($field, effectiveMax, response.message || 'Sold Out for Selected Date');
                            showGuestFieldError($field, response.message || ('Only ' + Math.max(effectiveMax, 0) + ' guests can be selected for this package/date.'));
                            refreshEventPackageSelectionLimits(true);
                            return false;
                        }

                        var packageType = ($('.package_number_of_guestss[data-id="' + packageId + '"]').data('package-type') || 'table');
                        var existing = window.cart.find(function(p) { return p.packageId == packageId; });
                        if (!existing) {
                            window.cart.push({
                                packageId: packageId,
                                packageName: packageName,
                                packagePrice: parseFloat(packagePrice),
                                guests: normalizedGuests,
                                isMultiple: parseMultipleFlag(isMultiple),
                                addons: addons || [],
                                transportation: transportation,
                                packageType: packageType
                            });
                        } else {
                            existing.packageName = packageName;
                            existing.packagePrice = parseFloat(packagePrice);
                            existing.guests = normalizedGuests;
                            existing.isMultiple = parseMultipleFlag(isMultiple);
                            existing.addons = addons || [];
                            existing.transportation = transportation;
                            existing.packageType = packageType;
                        }

                        $('#cart-section').show();
                        $('#shareLinkContainer').show();
                        window.renderCart();
                        syncCheckoutCartFields();
                        window.calculateCartTotal();
                        syncTransportationStateFromCart();
                        syncEventCapacityUi();
                        refreshEventPackageSelectionLimits(false);
                        if (typeof window.showCartToast === 'function') {
                            window.showCartToast(packageName, normalizedGuests);
                        }
                        return true;
                    })
                    .catch(function() {
                        alert('Error checking package availability. Please try again.');
                        return false;
                    });
            };

            window.removePackageFromCart = function(packageId) {
                ensureCartArray();
                window.cart = window.cart.filter(p => p.packageId != packageId);
                if (window.cart.length === 0) {
                    $('#cart-section').hide();
                }
                window.renderCart();
                syncCheckoutCartFields();
                window.calculateCartTotal();
                syncTransportationStateFromCart();
                syncEventCapacityUi();
            };

            window.renderCart = function() {
                ensureCartArray();
                if (!window.cart.length) {
                    $('#cart-list').html('');
                    return;
                }
                var html = '';
                window.cart.forEach(function(pkg) {
                    var billableGuests = getBillableGuests(pkg);
                    var unitPrice = parseFloat(pkg.packagePrice) || 0;
                    var lineTotal = unitPrice * billableGuests;
                    var priceLine = parseMultipleFlag(pkg.isMultiple)
                        ? (formatCurrency(unitPrice) + ' &times; ' + (parseInt(pkg.guests, 10) || 1) + ' = ' + formatCurrency(lineTotal))
                        : formatCurrency(lineTotal);
                    var guestQty = parseInt(pkg.guests, 10) || 1;
                    var isTicketPkg = pkg.packageType === 'ticket';
                    var guestLabel = guestQty + (isTicketPkg ? (guestQty === 1 ? ' Ticket' : ' Tickets') : (guestQty === 1 ? ' Guest' : ' Guests'));
                    html += '<div class="cart-line">';
                    html += '<div class="cart-line-main">';
                    html += '<div style="flex:1;min-width:0;"><div class="cart-item-name">' + pkg.packageName + '</div><div class="cart-line-guests">' + guestLabel + '</div></div>';
                    html += '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;"><div class="cart-item-price">' + priceLine + '</div><button onclick="window.removePackageFromCart(' + pkg.packageId + ')" class="cart-remove-btn">Remove</button></div>';
                    html += '</div>';
                    if (pkg.addons.length > 0) {
                        html += '<div class="cart-addons" style="color: #a774ff !important;">Add-ons: ' + pkg.addons.map(function(a) { return a.name + ((parseInt(a.qty, 10) || 1) > 1 ? (' x' + (parseInt(a.qty, 10) || 1)) : '') + ' (' + formatCurrency(a.price) + ')'; }).join(', ') + '</div>';
                    }
                    html += '</div>';
                });
                $('#cart-list').html(html);
                syncCheckoutCartFields();
            };

            window.calculateCartTotal = function() {
                ensureCartArray();
                var subtotal = 0;
                window.cart.forEach(function(pkg) {
                    subtotal += pkg.packagePrice * getBillableGuests(pkg);
                    pkg.addons.forEach(function(addon) {
                        subtotal += addon.price;
                    });
                });

                var service_charge = parseFloat($('#service_charge').val()) || 0;
                var sales_tax = parseFloat($('#sales_tax').val()) || 0;
                var gratuity = parseFloat($('#gratuity').val()) || 0;
                var couponDiscount = 0;

                if (window.cartCoupon) {
                    if (window.cartCoupon.type === 'percentage') {
                        couponDiscount = (subtotal / 100) * window.cartCoupon.discount;
                    } else {
                        couponDiscount = window.cartCoupon.discount;
                    }
                }

                couponDiscount = Math.min(Math.max(couponDiscount, 0), subtotal);

                var discountedSubtotal = subtotal - couponDiscount;
                var service_charge_price = (null != "0") ? (discountedSubtotal / 100) * service_charge : 0;
                var gratuited_price = (null != "0") ? (discountedSubtotal / 100) * gratuity : 0;
                var sales_tax_price = (null != "0") ? ((discountedSubtotal + service_charge_price + gratuited_price) / 100) * sales_tax : 0;

                var processingFeeBase = discountedSubtotal + service_charge_price;
                var amountAfterCoupon = processingFeeBase + sales_tax_price + gratuited_price;
                var processingFee = parseFloat($('#processing_fee').val()) || 0;
                var processingFeeType = ($('#processing_fee_type').val() || 'percentage').toLowerCase();
                var processingFeeAmount = processingFeeType === 'flat'
                    ? processingFee
                    : (processingFeeBase / 100) * processingFee;
                var grandTotal = amountAfterCoupon + processingFeeAmount;
                var refundableRate = parseFloat($('#refundable').val()) || 0;
                var refundableAmount = (grandTotal / 100) * refundableRate;

                $('.default-package-price > span:last-child').text(formatCurrency(subtotal));
                $('.default-service-charge > span:last-child').text(formatCurrency(service_charge_price));
                $('.default-sales-tax > span:last-child').text(formatCurrency(sales_tax_price));
                $('.default-gratuity > span:last-child').text(formatCurrency(gratuited_price));

                if (window.cartCoupon && couponDiscount > 0) {
                    if ($('.default-promo-discount').length === 0) {
                        $('.default-package-price').after('<div style="font-size: inherit !important; color: #22c55e !important; font-weight: 700 !important;" class="default-promo-discount">Promo Code Discount: <span style="font-size: inherit !important; color: #22c55e !important; font-weight: 700 !important;">$0.00</span></div>');
                    }
                    $('.default-promo-discount span').text('-' + formatCurrency(couponDiscount));
                    $('.default-package-price').after($('.default-promo-discount'));
                } else {
                    $('.default-promo-discount').remove();
                }

                if (processingFeeAmount > 0) {
                    if ($('.default-processing-fee').length === 0) {
                        $('.default-gratuity').after('<div style="font-size: 12px;" class="default-processing-fee" data-tip="Covers secure payment and transaction processing costs.">Processing Fee: <span>$0.00</span></div>');
                    }
                    $('.default-processing-fee span').text(formatCurrency(processingFeeAmount));
                } else {
                    $('.default-processing-fee').remove();
                }

                $('#cart-total').text('');

                if (window.cartCoupon) {
                    $('#cart-coupon').html('Coupon "' + window.cartCoupon.code + '" applied: -' + formatCurrency(couponDiscount));
                } else {
                    $('#cart-coupon').html('');
                }

                $('.payment_total').val(grandTotal.toFixed(2));
                $('#subtotal').val(refundableRate > 0 ? refundableAmount.toFixed(2) : grandTotal.toFixed(2));
                $('#commission_base_amount').val(Math.max(subtotal - couponDiscount, 0).toFixed(2));
                $('.default-refundable .refundable-amount').text(formatCurrency(refundableAmount));
                $('.default-due .due-amount').text(formatCurrency(grandTotal - refundableAmount));
                $('.default-deposit > span:last-child').text(formatCurrency(grandTotal));
                $('.default-total > span:last-child').text(formatCurrency(grandTotal));
                $('.discounted_amount').val(couponDiscount.toFixed(2));

                // Update Due Today (Deposit) box: show deposit amount + Due on Arrival
                if (refundableRate > 0) {
                    $('#cv-deposit-display').text(formatCurrency(refundableAmount));
                    $('#cv-due-on-arrival').text(formatCurrency(Math.max(grandTotal - refundableAmount, 0)));
                } else {
                    $('#cv-deposit-display').text(formatCurrency(grandTotal));
                }
            };
            console.log('Cart functions initialized:', typeof window.addPackageToCart);

            // Shareable link functions
            function getCurrentSelections() {
                var data = {
                    cart: window.cart,
                    coupon: window.cartCoupon ? window.cartCoupon.code : null
                };
                return data;
            }

            function setSelectionsFromParams() {
                var params = new URLSearchParams(window.location.search);
                var cartParam = params.get('cart');
                var couponParam = params.get('coupon');

                if (cartParam) {
                    openPackageTab();
                    try {
                        var decoded = JSON.parse(decodeURIComponent(cartParam));
                        window.cart = decoded.map(function(pkg) {
                            if (typeof pkg.isMultiple === 'undefined') {
                                pkg.isMultiple = getPackageMultipleFromDom(pkg.packageId);
                            }
                            return pkg;
                        });
                        if (window.cart.length > 0) {
                            $('#package_id').val(window.cart[0].packageId);
                            $('.package_number_of_guest').val(window.cart[0].guests);
                            window.cart.forEach(function(pkg) {
                                $('.package_number_of_guestss[data-id="' + pkg.packageId + '"]').val(pkg.guests || 1);
                                $('#pkg-card-' + pkg.packageId).addClass('selected');
                            });
                            $('#cart-section').show();
                            $('#shareLinkContainer').show();
                            window.renderCart();
                            window.calculateCartTotal();
                            syncTransportationStateFromCart();
                            syncEventCapacityUi();
                            $('.dynamic-price').show();
                            $('.default-price').hide();
                            $('#checkout-steps').show();
                            showStep(1);
                        }
                    } catch(e) {
                        console.error('Failed to parse cart param', e);
                    }
                }

                if (couponParam) {
                    $('#promo_code').val(couponParam);
                    $('#applyPromoBtn').trigger('click');
                }
            }

            function getUrlWithSelections() {
                var data = getCurrentSelections();
                var params = new URLSearchParams();
                if (data.cart.length > 0) {
                    params.set('cart', encodeURIComponent(JSON.stringify(data.cart)));
                }
                if (data.coupon) {
                    params.set('coupon', data.coupon);
                }
                return window.location.origin + window.location.pathname + '?' + params.toString();
            }

            $(document).ready(function() {
                function showCopyTooltip() {
                    const tooltip = $('#copyTooltip');
                    tooltip.text('Link copied!').show();
                    setTimeout(function() {
                        tooltip.hide();
                    }, 2000);
                }

                function getShareableUrl() {
                    var existing = String($('#shareableLink').val() || '').trim();
                    return existing || getUrlWithSelections();
                }

                function revealShareActions() {
                    $('#shareActions').css('display', 'flex');
                }

                function copyShareUrl(url) {
                    navigator.clipboard.writeText(url).then(function() {
                        showCopyTooltip();
                        alert('Link copied!');
                    }).catch(function() {
                        $('#shareableLink').val(url).show().trigger('focus').select();
                        revealShareActions();
                        alert('Link ready. Press Ctrl+C to copy.');
                    });
                }

                setSelectionsFromParams();

                $('#generateShareLink').on('click', function() {
                    if (window.cart.length === 0) {
                        alert('Please add at least one package to cart');
                        return;
                    }
                    
                    var selections = getCurrentSelections();
                    
                    $.ajax({
                        url: '/cart/share',
                        type: 'POST',
                        data: {
                            cart: JSON.stringify(selections.cart),
                            website_slug: null,
                            event_name: null,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                $('#shareableLink').val(res.short_url).show();
                                revealShareActions();
                                navigator.clipboard.writeText(res.short_url).then(function() {
                                    showCopyTooltip();
                                }).catch(function() {
                                    $('#shareableLink').select();
                                });
                            } else {
                                const fallbackUrl = getUrlWithSelections();
                                $('#shareableLink').val(fallbackUrl).show();
                                revealShareActions();
                                $('#shareableLink').select();
                            }
                        },
                        error: function(err) {
                            const fallbackUrl = getUrlWithSelections();
                            $('#shareableLink').val(fallbackUrl).show();
                            revealShareActions();
                            $('#shareableLink').select();
                            console.error(err);
                        }
                    });
                });

                $(document).on('click', '#shareActions .checkout-share-btn', function() {
                    var mode = String($(this).data('share') || '').toLowerCase();
                    var url = getShareableUrl();

                    if (!url) {
                        alert('Please generate a shareable link first.');
                        return;
                    }

                    if (mode === 'email') {
                        window.location.href = 'mailto:?subject=' + encodeURIComponent('Checkout Link') + '&body=' + encodeURIComponent(url);
                        return;
                    }

                    if (mode === 'whatsapp') {
                        window.open('https://wa.me/?text=' + encodeURIComponent(url), '_blank', 'noopener');
                        return;
                    }

                    if (mode === 'facebook') {
                        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank', 'noopener');
                        return;
                    }

                    if (mode === 'copy') {
                        copyShareUrl(url);
                    }
                });

                syncEventCapacityUi();

                // Copy to clipboard when clicking the shareable link field
                $('#shareableLink').on('click', function() {
                    const url = $(this).val();
                    navigator.clipboard.writeText(url).then(function() {
                        showCopyTooltip();
                    }).catch(function(err) {
                        console.error('Failed to copy:', err);
                        $('#shareableLink').select();
                    });
                });

                if (String($('#shareableLink').val() || '').trim()) {
                    revealShareActions();
                }

            });

            // ======= END CART SYSTEM =======

            // Auto-populate hidden payment fields when moving to payment step
            function populatePaymentFields() {
                $('#hidden_payment_phone').val($('input[name="package_phone"]').val());
                $('#hidden_payment_email').val($('input[name="package_email"]').val());
                $('#hidden_payment_month').val($('select[name="package_month"]').val());
                $('#hidden_payment_day').val($('select[name="package_day"]').val());
                $('#hidden_payment_year').val($('select[name="package_year"]').val());
            }
            
            // Copy package holder info to payment info (for visible fields only)
            $(document).on('click', '.same-as-info', function () {
                // Text fields - only copy visible fields now
                $("input[name='payment_first_name']").val($("input[name='package_first_name']").val());
                $("input[name='payment_last_name']").val($("input[name='package_last_name']").val());
                // Hidden fields are auto-populated when moving to payment step
                populatePaymentFields();
            });
            
            // Copy package holder info to transportation info
            $(document).on('click', '.same-as-info-transport', function () {
                $('input[name="transportation_phone"]').val($('input[name="package_phone"]').val());
            });
            // Populate country select
            function populateCountrySelect(selectId) {
                const countries = [
                    'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Italy', 'Spain', 'Netherlands', 'Brazil', 'India', 'China', 'Japan', 'South Korea', 'Mexico', 'Russia', 'South Africa', 'New Zealand', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Ireland', 'Switzerland', 'Austria', 'Belgium', 'Portugal', 'Poland', 'Turkey', 'Argentina', 'Chile', 'Colombia', 'Czech Republic', 'Greece', 'Hungary', 'Iceland', 'Indonesia', 'Israel', 'Malaysia', 'Philippines', 'Saudi Arabia', 'Singapore', 'Slovakia', 'Thailand', 'Ukraine', 'United Arab Emirates', 'Vietnam', 'Egypt', 'Morocco', 'Nigeria', 'Pakistan', 'Romania', 'Serbia', 'Croatia', 'Slovenia', 'Bulgaria', 'Estonia', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Monaco', 'Montenegro', 'Qatar', 'Kuwait', 'Oman', 'Bahrain', 'Jordan', 'Lebanon', 'Cyprus', 'Georgia', 'Kazakhstan', 'Uzbekistan', 'Bangladesh', 'Sri Lanka', 'Nepal', 'Cambodia', 'Laos', 'Myanmar', 'Mongolia', 'Afghanistan', 'Albania', 'Armenia', 'Azerbaijan', 'Belarus', 'Bosnia and Herzegovina', 'Botswana', 'Brunei', 'Burkina Faso', 'Burundi', 'Cameroon', 'Cape Verde', 'Central African Republic', 'Chad', 'Comoros', 'Congo', 'Costa Rica', 'Cuba', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia', 'Fiji', 'Gabon', 'Gambia', 'Ghana', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras', 'Jamaica', 'Kenya', 'Kiribati', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Madagascar', 'Malawi', 'Maldives', 'Mali', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Micronesia', 'Moldova', 'Mozambique', 'Namibia', 'Nauru', 'Nicaragua', 'Niger', 'North Korea', 'North Macedonia', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Senegal', 'Seychelles', 'Sierra Leone', 'Solomon Islands', 'Somalia', 'South Sudan', 'Sudan', 'Suriname', 'Syria', 'Tajikistan', 'Tanzania', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Uruguay', 'Vanuatu', 'Vatican City', 'Venezuela', 'Yemen', 'Zambia', 'Zimbabwe'
                ];
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Select Country</option>';
                countries.forEach(function (country) {
                    select.innerHTML += `<option value="${country}">${country}</option>`;
                });
            }

            function populateCountrySelect2(selectId) {
                const countries = [
                    'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Italy', 'Spain', 'Netherlands', 'Brazil', 'India', 'China', 'Japan', 'South Korea', 'Mexico', 'Russia', 'South Africa', 'New Zealand', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Ireland', 'Switzerland', 'Austria', 'Belgium', 'Portugal', 'Poland', 'Turkey', 'Argentina', 'Chile', 'Colombia', 'Czech Republic', 'Greece', 'Hungary', 'Iceland', 'Indonesia', 'Israel', 'Malaysia', 'Philippines', 'Saudi Arabia', 'Singapore', 'Slovakia', 'Thailand', 'Ukraine', 'United Arab Emirates', 'Vietnam', 'Egypt', 'Morocco', 'Nigeria', 'Pakistan', 'Romania', 'Serbia', 'Croatia', 'Slovenia', 'Bulgaria', 'Estonia', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Monaco', 'Montenegro', 'Qatar', 'Kuwait', 'Oman', 'Bahrain', 'Jordan', 'Lebanon', 'Cyprus', 'Georgia', 'Kazakhstan', 'Uzbekistan', 'Bangladesh', 'Sri Lanka', 'Nepal', 'Cambodia', 'Laos', 'Myanmar', 'Mongolia', 'Afghanistan', 'Albania', 'Armenia', 'Azerbaijan', 'Belarus', 'Bosnia and Herzegovina', 'Botswana', 'Brunei', 'Burkina Faso', 'Burundi', 'Cameroon', 'Cape Verde', 'Central African Republic', 'Chad', 'Comoros', 'Congo', 'Costa Rica', 'Cuba', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia', 'Fiji', 'Gabon', 'Gambia', 'Ghana', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras', 'Jamaica', 'Kenya', 'Kiribati', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Madagascar', 'Malawi', 'Maldives', 'Mali', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Micronesia', 'Moldova', 'Mozambique', 'Namibia', 'Nauru', 'Nicaragua', 'Niger', 'North Korea', 'North Macedonia', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Senegal', 'Seychelles', 'Sierra Leone', 'Solomon Islands', 'Somalia', 'South Sudan', 'Sudan', 'Suriname', 'Syria', 'Tajikistan', 'Tanzania', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Uruguay', 'Vanuatu', 'Vatican City', 'Venezuela', 'Yemen', 'Zambia', 'Zimbabwe'
                ];
                const select = document.getElementById(selectId);
                if (!select) return;
                select.innerHTML = '<option value="">Select Country</option>';
                countries.forEach(function (country) {
                    select.innerHTML += `<option value="${country}">${country}</option>`;
                });
            }
            
            // Function to force Safari/iOS select styling after JavaScript population
            function forceSafariSelectStyling() {
                // Target all select fields that are JavaScript-generated
                const selectIds = ['country', 'country2', 'st-pv', 'dob-month', 'dob-day', 'dob-year', 
                                 'package-dob-month', 'package-dob-day', 'package-dob-year',
                                 'payment-dob-month', 'payment-dob-day', 'payment-dob-year',
                                 'payment-dob-month2', 'payment-dob-day2', 'payment-dob-year2'];
                
                selectIds.forEach(function(id) {
                    const element = document.getElementById(id);
                    if (element) {
                        // Force re-apply CSS styles for Safari/iOS
                        element.style.setProperty('-webkit-appearance', 'none', 'important');
                        element.style.setProperty('-moz-appearance', 'none', 'important');
                        element.style.setProperty('appearance', 'none', 'important');
                        element.style.setProperty('background-color', 'transparent', 'important');
                        element.style.setProperty('background-image', 'url("data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'white\'><path d=\'M7 10l5 5 5-5z\'/></svg>")', 'important');
                        element.style.setProperty('background-repeat', 'no-repeat', 'important');
                        element.style.setProperty('background-position', 'right 15px center', 'important');
                        element.style.setProperty('background-size', '20px', 'important');
                        element.style.setProperty('padding', '12px 45px 12px 15px', 'important');
                        element.style.setProperty('border', '1px solid #9797a0', 'important');
                        element.style.setProperty('border-radius', '10px', 'important');
                        element.style.setProperty('color', '#fff', 'important');
                        element.style.setProperty('font-size', '16px', 'important');
                        element.style.setProperty('min-height', '45px', 'important');
                        element.style.setProperty('line-height', '1.5', 'important');
                        
                        // Special handling for DOB fields (smaller arrows)
                        if (id.includes('dob')) {
                            element.style.setProperty('padding', '12px 30px 12px 15px', 'important');
                            element.style.setProperty('background-size', '15px', 'important');
                            element.style.setProperty('background-position', 'right 10px center', 'important');
                            element.style.setProperty('text-align', 'center', 'important');
                        }
                    }
                });
            }
            
            // On DOM ready, also populate country select
            $(function () {
                populateCountrySelect('country');
                populateCountrySelect2('country2');
                
                // Apply styling after population with a slight delay for Safari
                setTimeout(function() {
                    forceSafariSelectStyling();
                }, 100);
            });
            // Populate DOB selects for all three sections
            function populateDOBSelects(monthId, dayId, yearId) {
                const monthSelect = document.getElementById(monthId);
                const daySelect = document.getElementById(dayId);
                const yearSelect = document.getElementById(yearId);

                // Check if elements exist before trying to populate them
                if (!monthSelect || !daySelect || !yearSelect) {
                    return; // Elements don't exist, skip population
                }

                // Months 1-12 (with "Month" placeholder)
                monthSelect.innerHTML = '<option value="" disabled selected hidden>Month</option>';
                for (let m = 1; m <= 12; m++) {
                    monthSelect.innerHTML += `<option value="${m.toString().padStart(2, '0')}">${m.toString().padStart(2, '0')}</option>`;
                }
                // Days 1-31 (with "Day" placeholder)
                daySelect.innerHTML = '<option value="" disabled selected hidden>Day</option>';
                for (let d = 1; d <= 31; d++) {
                    daySelect.innerHTML += `<option value="${d.toString().padStart(2, '0')}">${d.toString().padStart(2, '0')}</option>`;
                }
                // Years: current year to (current year - 100) (with "Year" placeholder)
                const currentYear = new Date().getFullYear();
                yearSelect.innerHTML = '<option value="" disabled selected hidden>Year</option>';
                for (let y = currentYear; y >= currentYear - 100; y--) {
                    yearSelect.innerHTML += `<option value="${y}">${y}</option>`;
                }
            }
            // On DOM ready
            $(function () {
                populateDOBSelects('dob-month', 'dob-day', 'dob-year');
                populateDOBSelects('package-dob-month', 'package-dob-day', 'package-dob-year');
                populateDOBSelects('payment-dob-month', 'payment-dob-day', 'payment-dob-year');
                populateDOBSelects('payment-dob-month2', 'payment-dob-day2', 'payment-dob-year2');
                
                // Apply styling after population with a slight delay for Safari
                setTimeout(function() {
                    forceSafariSelectStyling();
                }, 100);
            });


            window.pendingPackageSelection = null;

            function escapeAddonHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function openAddonSelectionModal(selection) {
                var addons = selection.addons || [];
                var html = '';

                if (!addons.length) {
                    html = '<p style="margin:0;opacity:.8;">No add-ons available for this package. Click confirm to continue.</p>';
                } else {
                    var existingCartPkg = Array.isArray(window.cart) ? window.cart.find(function(p) { return p.packageId == selection.packageId; }) : null;
                    var existingAddons = existingCartPkg ? (existingCartPkg.addons || []) : [];
                    addons.forEach(function(addon) {
                        var unitPrice = parseFloat(addon.price || 0);
                        var existingAddon = existingAddons.find(function(a) { return String(a.id) === String(addon.id); });
                        var currentQty = existingAddon ? (parseInt(existingAddon.qty, 10) || (existingAddon.price > 0 ? Math.round(existingAddon.price / unitPrice) : 1)) : 0;
                        if (!Number.isFinite(currentQty) || currentQty < 0) {
                            currentQty = 0;
                        }
                        var description = String(addon.description || '').trim();
                        var descriptionHtml = description ? ('<small class="addon-modal-desc">' + escapeAddonHtml(description) + '</small>') : '';
                        var lineTotal = unitPrice * currentQty;
                        html += '<div class="addon-modal-row">'
                            + '<span class="addon-modal-label">' + escapeAddonHtml(addon.name) + '<span class="addon-modal-unit">' + formatCurrency(unitPrice) + '/ea</span>' + descriptionHtml + '<small class="addon-line-total">Line total: <span class="addon-line-total-value" data-id="' + addon.id + '">' + formatCurrency(lineTotal) + '</span></small></span>'
                            + '<span class="addon-qty-stepper">'
                            + '<button type="button" class="addon-qty-btn addon-qty-dec" data-id="' + addon.id + '">&#8722;</button>'
                            + '<span class="addon-qty-val" data-id="' + addon.id + '" data-name="' + escapeAddonHtml(addon.name) + '" data-price="' + unitPrice + '">' + currentQty + '</span>'
                            + '<button type="button" class="addon-qty-btn addon-qty-inc" data-id="' + addon.id + '">+</button>'
                            + '</span>'
                            + '</div>';
                    });
                }

                $('#addonSelectionModalTitle').text('Select Add-ons for ' + (selection.pkgName || selection.packageName));
                $('#addonSelectionModalBody').html(html);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addonSelectionModal')).show();
            }

            $(document).ready(function () {
                window.lastSelectedUseDate = (typeof window.getSelectedUseDate === 'function')
                    ? window.getSelectedUseDate()
                    : String($('#package_use_date').val() || $('.package_use_date').val() || '').trim();
                if (typeof window.syncUseDateField === 'function') {
                    window.syncUseDateField();
                }
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.forEach(function (popoverTriggerEl) {
                    bootstrap.Popover.getOrCreateInstance(popoverTriggerEl, {
                        trigger: 'focus hover',
                        html: true,
                        sanitize: true,
                        container: 'body'
                    });
                });

                $(document).on('click', '.package-category-tile', function() {
                    var $tile = $(this);
                    var $target = $tile.closest('.package-category-wrap').find('.package-category-group').first();
                    var isOpen = $tile.hasClass('active');

                    if (!$target.length) {
                        var targetSelector = String($tile.data('target') || '');
                        var targetId = targetSelector.replace(/^#/, '');
                        $target = targetId ? $('#' + targetId) : $();
                    }

                    $('.package-category-tile').removeClass('active');
                    $('.package-category-group').stop(true, true).slideUp(180);

                    if (!isOpen && $target.length) {
                        $tile.addClass('active');
                        $target.stop(true, true).slideDown(180);
                    }
                });

                $(document).on('click', '.vip-btn', function () {
                    var $btn = $(this);
                    var packageId = $btn.data('id');
                    var packageName = $btn.data('name');
                    var packagePrice = parseFloat($btn.data('price'));
                    var $guestSelect = $('.package_number_of_guestss[data-id="' + packageId + '"]');
                    var guestValue = $guestSelect.val();
                    var isMultiple = parseMultipleFlag($guestSelect.data('multiple'));

                    if (!ensureReservationDateSelected()) {
                        return;
                    }

                    if (!guestValue) {
                        var fieldLabel = $guestSelect.find('option:first').text();
                        alert('Please select ' + fieldLabel);
                        return;
                    }

                    var guests = parseInt(guestValue) || 1;

                    $('.vip-card').removeClass('selected');
                    $btn.closest('.vip-card').addClass('selected');

                    $.ajax({
                        url: "/null/addons/" + packageId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            window.pendingPackageSelection = {
                                packageId: packageId,
                                packageName: packageName,
                                packagePrice: packagePrice,
                                guests: guests,
                                isMultiple: isMultiple,
                                transportation: ($btn.data('transportation') == 1),
                                addons: Array.isArray(res) ? res : []
                            };

                            openAddonSelectionModal(window.pendingPackageSelection);
                        }
                    });
                });

                $('#addonModalConfirmBtn').on('click', function() {
                    if (!window.pendingPackageSelection) {
                        return;
                    }

                    var selection = window.pendingPackageSelection;
                    var selectedAddons = [];

                    $('#addonSelectionModalBody .addon-qty-val').each(function() {
                        var qty = parseInt($(this).text(), 10) || 0;
                        if (qty > 0) {
                            var unitPrice = parseFloat($(this).data('price'));
                            selectedAddons.push({
                                id: $(this).data('id'),
                                name: $(this).data('name'),
                                unit_price: unitPrice,
                                price: unitPrice * qty,
                                qty: qty
                            });
                        }
                    });

                    window.addPackageToCart(
                        selection.packageId,
                        selection.packageName,
                        selection.packagePrice,
                        selection.guests,
                        selectedAddons,
                        selection.transportation,
                        selection.isMultiple
                    ).then(function(added) {
                        if (!added) {
                            return;
                        }

                        $('#package_id').val(selection.packageId);
                        $('#addons').val(selectedAddons.map(function(addon) { return addon.id; }).join(','));
                        $('.package_number_of_guest').val(selection.guests);
                        $('.dynamic-price').show();
                        $('.default-price').hide();
                        $('#checkout-steps').show();
                        syncTransportationStateFromCart();
                        showStep(1);

                        bootstrap.Modal.getOrCreateInstance(document.getElementById('addonSelectionModal')).hide();
                        window.pendingPackageSelection = null;
                    });
                });

                $(document).on('click', '#addonSelectionModalBody .addon-qty-dec', function() {
                    var id = $(this).data('id');
                    var valEl = $('#addonSelectionModalBody .addon-qty-val[data-id="' + id + '"]');
                    var current = parseInt(valEl.text(), 10) || 0;
                    var next = current > 0 ? current - 1 : 0;
                    valEl.text(next);
                    var unitPrice = parseFloat(valEl.data('price')) || 0;
                    $('#addonSelectionModalBody .addon-line-total-value[data-id="' + id + '"]').text(formatCurrency(unitPrice * next));
                });

                $(document).on('click', '#addonSelectionModalBody .addon-qty-inc', function() {
                    var id = $(this).data('id');
                    var valEl = $('#addonSelectionModalBody .addon-qty-val[data-id="' + id + '"]');
                    var current = parseInt(valEl.text(), 10) || 0;
                    var next = current + 1;
                    valEl.text(next);
                    var unitPrice = parseFloat(valEl.data('price')) || 0;
                    $('#addonSelectionModalBody .addon-line-total-value[data-id="' + id + '"]').text(formatCurrency(unitPrice * next));
                });

                $(document).on('change', '#package_use_date', function() {
                    var previousDate = String(window.lastSelectedUseDate || '').trim();
                    var currentDate = (typeof window.getSelectedUseDate === 'function')
                        ? window.getSelectedUseDate()
                        : String($('#package_use_date').val() || $('.package_use_date').val() || '').trim();

                    if (previousDate && currentDate && previousDate !== currentDate && Array.isArray(window.cart) && window.cart.length) {
                        resetCartForDateChange();
                        alert('Cart was reset because reservation date changed. Please add packages again for the new date.');
                    }

                    window.lastSelectedUseDate = currentDate;
                    clearReservationDateError();
                    if (typeof window.syncUseDateField === 'function') {
                        window.syncUseDateField();
                    }
                    refreshEventPackageSelectionLimits(true);
                });

                setTimeout(function() {
                    refreshEventPackageSelectionLimits(false);
                }, 150);
            });
            
            // Step Management Functions
            let currentStep = 1;
            
            function showStep(stepNumber) {
                // Hide all sections
                $('.checkout-section').removeClass('active').hide();
                
                // Show target section
                $('#section-' + stepNumber).addClass('active').show();
                
                // Update step indicators
                $('.step').removeClass('active completed');
                for (let i = 1; i < stepNumber; i++) {
                    $('#step-' + i).addClass('completed');
                }
                $('#step-' + stepNumber).addClass('active');
                
                currentStep = stepNumber;
                syncTransportationStateFromCart();
                
                // Handle transportation logic for step 2
                if (stepNumber === 2) {
                    if (window.requiresTransportation) {
                        $('#transport-form').show();
                        $('#transport-confirmation').hide();
                    } else {
                        $('#transport-form').hide();
                        $('#transport-confirmation').show();
                    }
                }

                // On mobile, scroll to the top of the new step
                if (window.innerWidth < 992) {
                    setTimeout(function() {
                        var el = document.getElementById('section-' + stepNumber);
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }, 50);
                }
            }
            
            function validateStep(stepNumber) {
                let isValid = true;
                const requiredFields = [];
                let firstInvalidField = null;
                let alertMessage = 'Please fill in all required fields.';
                
                if (stepNumber === 1) {
                    // Validate package holder info
                    requiredFields.push(
                        '[name="package_first_name"]',
                        '[name="package_last_name"]',
                        '[name="package_phone"]',
                        '[name="package_email"]',
                        '[name="package_month"]',
                        '[name="package_day"]',
                        '[name="package_year"]'
                    );
                } else if (stepNumber === 2 && window.requiresTransportation) {
                    // Validate transportation form
                    requiredFields.push(
                        '[name="package_use_date"]',
                        '[name="transportation_pickup_time"]',
                        '[name="transportation_address"]',
                        '[name="transportation_phone"]'
                    );
                } else if (stepNumber === 2 && !window.requiresTransportation) {
                    // Validate transportation confirmation checkbox
                    if (!$('#transportation_part').is(':checked')) {
                        alert('Please confirm your transportation arrangement.');
                        return false;
                    }
                }
                
                // Check required fields
                requiredFields.forEach(function(selector) {
                    const field = $(selector);
                    if (!field.val() || field.val().trim() === '') {
                        field.addClass('required-field');
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    } else {
                        field.removeClass('required-field');
                    }
                });

                if (isValid && stepNumber === 2 && window.requiresTransportation && typeof validateTransportationScheduleClient === 'function') {
                    const scheduleValidation = validateTransportationScheduleClient();
                    if (!scheduleValidation.valid) {
                        isValid = false;
                        firstInvalidField = scheduleValidation.field || firstInvalidField;
                        alertMessage = scheduleValidation.message;
                    }
                }

                if (stepNumber === 2 && window.requiresTransportation) {
                    const transportationGuestField = $('[name="transportation_guest"]');
                    const transportationGuestValue = parseInt(transportationGuestField.val(), 10);
                    if (!Number.isFinite(transportationGuestValue) || transportationGuestValue < 1) {
                        transportationGuestField.addClass('required-field');
                        isValid = false;
                        firstInvalidField = firstInvalidField || transportationGuestField;
                        alertMessage = 'Please enter Number of Guest(s) in Transportation (minimum 1).';
                    }
                }
                
                if (!isValid && stepNumber === 2 && window.requiresTransportation && alertMessage === 'Please fill in all required fields.') {
                    alertMessage = 'Please complete the required transportation details before proceeding.';
                }

                if (!isValid) {
                    alert(alertMessage);
                    if (firstInvalidField && firstInvalidField.length) {
                        firstInvalidField.trigger('focus');
                        firstInvalidField[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
                
                return isValid;
            }
            
            // Navigation Event Handlers
            $(document).ready(function() {
                
                // Next to Transportation
                $('#next-to-transport').click(function() {
                    if (validateStep(1)) {
                        showStep(2);
                    }
                });
                
                // Previous to Package from Transportation confirmation
                $('#prev-to-package').click(function() {
                    showStep(1);
                });
                
                // Previous to Package from Transportation form  
                $('#prev-to-package-from-form').click(function() {
                    showStep(1);
                });
                
                // Next to Payment from Transportation confirmation
                $('#next-to-payment-from-confirm').click(function() {
                    if (validateStep(2)) {
                        populatePaymentFields();
                        showStep(3);
                    }
                });
                
                // Next to Payment from Transportation form
                $('#next-to-payment').click(function() {
                    if (validateStep(2)) {
                        populatePaymentFields();
                        showStep(3);
                    }
                });
                
                // Previous to Transportation from Payment
                $('#prev-to-transport').click(function() {
                    showStep(2);
                });
                
                // Remove required field styling on input
                $(document).on('input change', 'input, select, textarea', function() {
                    $(this).removeClass('required-field');
                });
            });
            
        

// ----- SCRIPT BOUNDARY -----


            function openModal() {
                // Get the description from the clicked addon
                const description = event.target.closest('.addon-item').querySelector('label').getAttribute('data-description');
                const title = event.target.closest('.addon-item').querySelector('label').getAttribute('data-title');

                $('.modal-title').text(title);
                $('.modal-body').html(`<p style="color: #000 !important;">${description}</p>`);
                $('.modal').modal('show');

            }

            function openPackageModal() {

                // Get the description from the clicked package
                const description = event.target.closest('.vip-card').querySelector('.items').getAttribute('data-description');
                const title = event.target.closest('.vip-card').querySelector('.items').getAttribute('data-title');

                $('.modal-title').text(title);
                $('.modal-body').html(`<p style="color: #000 !important;">${description}</p>`);
                $('.modal').modal('show');

            }

            function addToTotal(price, name, id) {
                // This function is now handled by cart system
                // Keeping for backward compatibility
            }

            function transportation(){
                console.log('sss');
                if (event.target.checked) {
                    $('.transport').show();
                }else{
                    $('.transport').hide();
                }
            }
        

// ----- SCRIPT BOUNDARY -----


            $('.package_number_of_guestss').on('change', function() {
                var $field = $(this);
                var selectedValue = parseInt($field.val(), 10) || 1;
                var packageId = $field.data('id');
                var useDate = (typeof window.getSelectedUseDate === 'function')
                    ? window.getSelectedUseDate()
                    : String($('#package_use_date').val() || $('.package_use_date').val() || '').trim();

                $.get('/' + null + '/package/' + packageId + '/capacity', {
                    use_date: useDate,
                    requested_quantity: selectedValue
                }).done(function(response) {
                    var maxSelectable = parseInt(response.max_select, 10);
                    if (!Number.isFinite(maxSelectable)) {
                        maxSelectable = parseInt(response.capacity, 10) || 1;
                    }

                    if (selectedValue > maxSelectable) {
                        if (typeof window.updateGuestSelectOptions === 'function') {
                            window.updateGuestSelectOptions($field, maxSelectable, response.message || 'Sold Out!');
                        }
                        if (typeof window.showGuestFieldError === 'function') {
                            window.showGuestFieldError($field, response.message || 'The selected quantity is not available for this date.');
                        }
                        return;
                    }

                    if (typeof window.clearGuestFieldError === 'function') {
                        window.clearGuestFieldError($field);
                    }
                    $('.package_number_of_guest').val(String(selectedValue));

                    var pkg = window.cart.find(function(p) { return String(p.packageId) === String(packageId); });
                    if (pkg) {
                        pkg.guests = selectedValue;
                        pkg.isMultiple = (typeof window.parseMultipleFlag === 'function')
                            ? window.parseMultipleFlag($field.data('multiple'))
                            : ($field.data('multiple') === true || $field.data('multiple') === 1 || $field.data('multiple') === '1' || $field.data('multiple') === 'true');
                        window.renderCart();
                        window.calculateCartTotal();
                    }

                    syncEventCapacityUi();
                }).fail(function() {
                    if (typeof window.showGuestFieldError === 'function') {
                        window.showGuestFieldError($field, 'Could not verify availability right now. Please try again.');
                    }
                });
            });

            $(document).on('input', '.package_number_of_guestss[type="number"]', function() {
                var $field = $(this);
                var entered = parseInt($field.val(), 10);
                var maxAllowed = parseInt($field.attr('max'), 10);

                if (!Number.isFinite(entered) || entered < 1) {
                    $field.val('1');
                    return;
                }

                if (Number.isFinite(maxAllowed) && maxAllowed > 0 && entered > maxAllowed) {
                    $field.val(String(maxAllowed));
                }
            });
        

// ----- SCRIPT BOUNDARY -----


            // Coupon logic for cart
            $('#applyPromoBtn').on('click', function() {
                let code = $('#promo_code').val().trim();
                if (!code) return;

                var promoSource = null;
                var ownerSlug = null;
                var cartItems = Array.isArray(window.cart) ? window.cart : [];
                var packageIds = [];
                var subtotal = 0;
                var totalQty = 0;

                cartItems.forEach(function(pkg) {
                    var pkgId = parseInt(pkg.packageId, 10) || 0;
                    if (pkgId > 0 && packageIds.indexOf(pkgId) === -1) {
                        packageIds.push(pkgId);
                    }

                    var guests = parseInt(pkg.guests, 10) || 1;
                    var billableGuests = (pkg.isMultiple === true || pkg.isMultiple === 1 || pkg.isMultiple === '1') ? guests : 1;
                    subtotal += (parseFloat(pkg.packagePrice) || 0) * billableGuests;
                    subtotal += (pkg.addons || []).reduce(function(sum, addon) { return sum + (parseFloat(addon.price) || 0); }, 0);
                    totalQty += guests;
                });

                $.get('/' + null + '/check/' + encodeURIComponent(code), {
                    source: promoSource,
                    owner_slug: ownerSlug,
                    package_ids: packageIds.join(','),
                    subtotal: subtotal.toFixed(2),
                    total_qty: totalQty
                }, function(res) {
                    if (res.valid === false || res.valid === "false") {
                        window.cartCoupon = null;
                        alert(res.message || 'Invalid promo code');
                        window.calculateCartTotal();
                    } else {
                        window.cartCoupon = {
                            code: code,
                            id: res.id,
                            discount: parseFloat(res.discount),
                            type: res.type || 'percentage'
                        };
                        $('#applyPromoBtn').prop('disabled', true);
                        $('.promo_code').val(res.id);
                        window.calculateCartTotal();
                    }
                });
            });
        

// ----- SCRIPT BOUNDARY -----


            // Replace this with your country select's ID
            const countrySelectId = 'country';
            const stateSelectId = 'st-pv';

            // Listen for country change
            $(document).on('change', `#${countrySelectId}`, function () {
                const country = $(this).val();
                const $state = $(`#${stateSelectId}`);
                $state.html('<option value="">Loading...</option>');
                if (!country) {
                    $state.html('<option value="">Select State/Province</option>');
                    return;
                }
                // Example API for US states: https://countriesnow.space/api/v0.1/countries/states
                // You can use another API if you prefer
                $.ajax({
                    url: 'https://countriesnow.space/api/v0.1/countries/states',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ country: country }),
                    success: function (res) {
                        if (res && res.data && res.data.states && res.data.states.length > 0) {
                            let options = '<option value="null" selected disabled>Select State/Province</option>';
                            res.data.states.forEach(function (state) {
                                options += `<option value="${state.name}">${state.name}</option>`;
                            });
                            $state.html(options);
                        } else {
                            $state.html('<option value="null" selected disabled>No states found</option>');
                        }
                    },
                    error: function () {
                        $state.html('<option value="null" selected disabled>Error loading states</option>');
                    }
                });
            });
        

// ----- SCRIPT BOUNDARY -----


            // Auto-discount logic: wrap calculateCartTotal to fetch and apply automatic discounts
            (function () {
                var _origCalcCartTotal = window.calculateCartTotal;
                var _autoDiscountTimer = null;

                var promoSource = null;
                var ownerSlug = null;
                var siteSlug = null;

                function fetchAutoDiscount() {
                    var cartItems = Array.isArray(window.cart) ? window.cart : [];
                    if (cartItems.length === 0) {
                        if (window.cartCoupon && window.cartCoupon.isAutomatic) {
                            window.cartCoupon = null;
                            _origCalcCartTotal();
                        }
                        return;
                    }
                    var packageIds = [];
                    var subtotal = 0;
                    var totalQty = 0;
                    cartItems.forEach(function (pkg) {
                        var pkgId = parseInt(pkg.packageId, 10) || 0;
                        if (pkgId > 0 && packageIds.indexOf(pkgId) === -1) packageIds.push(pkgId);
                        var guests = parseInt(pkg.guests, 10) || 1;
                        var billable = (pkg.isMultiple === true || pkg.isMultiple === 1 || pkg.isMultiple === '1') ? guests : 1;
                        subtotal += (parseFloat(pkg.packagePrice) || 0) * billable;
                        subtotal += (pkg.addons || []).reduce(function (s, a) { return s + (parseFloat(a.price) || 0); }, 0);
                        totalQty += guests;
                    });
                    $.get('/' + siteSlug + '/auto-discounts', {
                        source: promoSource,
                        owner_slug: ownerSlug,
                        package_ids: packageIds.join(','),
                        subtotal: subtotal.toFixed(2),
                        total_qty: totalQty
                    }, function (res) {
                        if (res.valid) {
                            window.cartCoupon = {
                                code: res.name,
                                id: res.id,
                                discount: parseFloat(res.discount),
                                type: res.type || 'percentage',
                                isAutomatic: true
                            };
                        } else if (window.cartCoupon && window.cartCoupon.isAutomatic) {
                            window.cartCoupon = null;
                        }
                        _origCalcCartTotal();
                    });
                }

                window.calculateCartTotal = function () {
                    _origCalcCartTotal();
                    // Only trigger auto-discount fetch when no manual coupon is active
                    if (!window.cartCoupon || window.cartCoupon.isAutomatic) {
                        clearTimeout(_autoDiscountTimer);
                        _autoDiscountTimer = setTimeout(fetchAutoDiscount, 400);
                    }
                };
            })();
        

// ----- SCRIPT BOUNDARY -----



// ----- SCRIPT BOUNDARY -----


            flatpickr("#package_use_date", {
                dateFormat: "Y-m-d",
                defaultDate: null,
                minDate: "today",
                allowInput: false,
                clickOpens: true,
                onChange: function(selectedDates, dateStr) {
                    document.getElementById('package_use_date').value = dateStr;
                }
            });
        

// ----- SCRIPT BOUNDARY -----


            function prepareCheckoutCartPayload(form) {
                syncCheckoutCartFields();
            }

            (function initRawCardNumberFormatting() {
                function detectCardMeta(digits) {
                    var number = String(digits || '');

                    if (/^3[47]/.test(number)) {
                        return { maxLen: 15, validLens: [15], grouping: [4, 6, 5] }; // Amex
                    }
                    if (/^3(?:0[0-5]|[68])/.test(number)) {
                        return { maxLen: 14, validLens: [14], grouping: [4, 6, 4] }; // Diners
                    }
                    if (/^(5[1-5]|2[2-7])/.test(number)) {
                        return { maxLen: 16, validLens: [16], grouping: [4, 4, 4, 4] }; // Mastercard
                    }
                    if (/^(6011|65|64[4-9])/.test(number)) {
                        return { maxLen: 19, validLens: [16, 19], grouping: [4, 4, 4, 4, 3] }; // Discover
                    }
                    if (/^4/.test(number)) {
                        return { maxLen: 19, validLens: [13, 16, 19], grouping: [4, 4, 4, 4, 3] }; // Visa
                    }
                    if (/^35/.test(number)) {
                        return { maxLen: 19, validLens: [16, 17, 18, 19], grouping: [4, 4, 4, 4, 3] }; // JCB
                    }

                    return { maxLen: 19, validLens: [13, 14, 15, 16, 17, 18, 19], grouping: [4, 4, 4, 4, 3] };
                }

                function formatWithGrouping(digits, grouping) {
                    var cursor = 0;
                    var parts = [];

                    for (var i = 0; i < grouping.length && cursor < digits.length; i++) {
                        var size = grouping[i];
                        var chunk = digits.slice(cursor, cursor + size);
                        if (!chunk) {
                            break;
                        }
                        parts.push(chunk);
                        cursor += size;
                    }

                    if (cursor < digits.length) {
                        parts.push(digits.slice(cursor));
                    }

                    return parts.join(' ');
                }

                function applyMask(input) {
                    if (!input) {
                        return;
                    }

                    var digits = String(input.value || '').replace(/\D/g, '');
                    var meta = detectCardMeta(digits);
                    var maxDigits = Math.min(meta.maxLen, 16);
                    var allowedLengths = meta.validLens.filter(function(len) { return len <= maxDigits; });

                    if (allowedLengths.length === 0) {
                        allowedLengths = [maxDigits];
                    }

                    if (digits.length > maxDigits) {
                        digits = digits.slice(0, maxDigits);
                    }

                    input.value = formatWithGrouping(digits, meta.grouping);
                    input.maxLength = formatWithGrouping(new Array(maxDigits + 1).join('9'), meta.grouping).length;
                    input.setAttribute('inputmode', 'numeric');
                    input.setAttribute('autocomplete', 'cc-number');
                    input.setCustomValidity('');

                    if (digits.length > 0 && allowedLengths.indexOf(digits.length) === -1) {
                        input.setCustomValidity('Please enter a valid card number.');
                    }
                }

                function bindField(input) {
                    if (!input || input.dataset.cardFormatBound === '1') {
                        return;
                    }

                    input.dataset.cardFormatBound = '1';
                    applyMask(input);
                    input.addEventListener('input', function() { applyMask(input); });
                    input.addEventListener('blur', function() { applyMask(input); });
                }

                var cardFields = document.querySelectorAll('input[name="card_number"]');
                cardFields.forEach(function(field) { bindField(field); });

                var form = document.getElementById('payment-form');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        var inputs = form.querySelectorAll('input[name="card_number"]');
                        var hasInvalid = false;

                        inputs.forEach(function(input) {
                            applyMask(input);
                            if (!input.checkValidity()) {
                                hasInvalid = true;
                            }
                            input.value = String(input.value || '').replace(/\D/g, '');
                        });

                        if (hasInvalid) {
                            event.preventDefault();
                            var first = inputs[0];
                            if (first) {
                                first.reportValidity();
                            }
                        }
                    });
                }
            })();

            document.getElementById('payment-form')?.addEventListener('submit', function(e) {
                if (!ensureReservationDateSelected()) {
                    e.preventDefault();
                    return;
                }
                prepareCheckoutCartPayload(this);
            });

            const transportationSchedule = {
                startTime: null,
                endTime: null,
            };

            function parseTimeToMinutes(timeValue) {
                if (!timeValue) {
                    return null;
                }

                const trimmedValue = String(timeValue).trim();
                const twelveHourMatch = trimmedValue.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
                if (twelveHourMatch) {
                    let hours = parseInt(twelveHourMatch[1], 10) % 12;
                    const minutes = parseInt(twelveHourMatch[2], 10);
                    if (twelveHourMatch[3].toUpperCase() === 'PM') {
                        hours += 12;
                    }

                    return (hours * 60) + minutes;
                }

                const twentyFourHourMatch = trimmedValue.match(/^(\d{1,2}):(\d{2})$/);
                if (twentyFourHourMatch) {
                    return (parseInt(twentyFourHourMatch[1], 10) * 60) + parseInt(twentyFourHourMatch[2], 10);
                }

                return null;
            }

            function isTimeWithinOperatingHours(timeValue) {
                const pickupMinutes = parseTimeToMinutes(timeValue);
                if (pickupMinutes === null) {
                    return false;
                }

                const startMinutes = parseTimeToMinutes(transportationSchedule.startTime);
                const endMinutes = parseTimeToMinutes(transportationSchedule.endTime);

                if (startMinutes === null || endMinutes === null) {
                    return true;
                }

                if (endMinutes < startMinutes) {
                    return pickupMinutes >= startMinutes || pickupMinutes <= endMinutes;
                }

                return pickupMinutes >= startMinutes && pickupMinutes <= endMinutes;
            }

            function validateTransportationScheduleClient() {
                const pickupTimeField = $('[name="transportation_pickup_time"]');
                const pickupTime = pickupTimeField.val().trim();

                if (!pickupTime) {
                    pickupTimeField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupTimeField,
                        message: 'Please complete the required transportation details before proceeding.'
                    };
                }

                if (!isTimeWithinOperatingHours(pickupTime)) {
                    pickupTimeField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupTimeField,
                        message: 'Pickup time must be within the club operating hours.'
                    };
                }

                return { valid: true, field: null, message: '' };
            }

            // Flatpickr time picker for pick-up time — visual picker on all devices including iOS.
            // Pick-up time picker: desktop uses Flatpickr, mobile uses the native time control.
            (function () {
                var el = document.querySelector('input[name="transportation_pickup_time"]');
                if (!el) return;
                function to24h(t) {
                    if (!t) return null;
                    var m = String(t).trim().match(/^(\d{1,2}):(\d{2})\s*(AM|PM)?$/i);
                    if (!m) return null;
                    var hh = parseInt(m[1], 10);
                    var mm = parseInt(m[2], 10);
                    if (m[3]) {
                        var mer = m[3].toUpperCase();
                        if (mer === 'PM' && hh < 12) hh += 12;
                        else if (mer === 'AM' && hh === 12) hh = 0;
                    }
                    return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
                }
                var minT = to24h(typeof transportationSchedule !== 'undefined' ? transportationSchedule.startTime : null);
                var maxT = to24h(typeof transportationSchedule !== 'undefined' ? transportationSchedule.endTime : null);
                var isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
                    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

                if (isMobileDevice) {
                    el.type = 'time';
                    el.removeAttribute('readonly');
                    el.step = 900;
                    if (minT) el.min = minT;
                    if (maxT) el.max = maxT;
                    el.addEventListener('input', function () {
                        $(el).removeClass('required-field');
                    });
                    return;
                }

                el.type = 'text';
                el.setAttribute('readonly', 'readonly');
                if (typeof flatpickr === 'undefined') {
                    el.type = 'time';
                    el.removeAttribute('readonly');
                    if (minT) el.min = minT;
                    if (maxT) el.max = maxT;
                    el.step = 900;
                    return;
                }

                flatpickr(el, {
                    enableTime: true,
                    noCalendar: true,
                    time_24hr: false,
                    minuteIncrement: 15,
                    dateFormat: 'H:i',
                    allowInput: false,
                    onChange: function () {
                        $(el).removeClass('required-field');
                    },
                    minTime: minT || undefined,
                    maxTime: maxT || undefined
                });
            })();

            // Keep hidden use-date in sync with actual selected reservation date.
            if (typeof window.syncUseDateField === 'function') {
                window.syncUseDateField();
            } else {
                $('.package_use_date').val(String($('#package_use_date').val() || '').trim());
            }
        

// ----- SCRIPT BOUNDARY -----


            // Keep hidden submit value synced to selected reservation date.
            if (typeof window.syncUseDateField === 'function') {
                window.syncUseDateField();
            } else {
                $('.package_use_date').val(String($('#package_use_date').val() || '').trim());
            }
        

// ----- SCRIPT BOUNDARY -----



// ----- SCRIPT BOUNDARY -----


                    const stripe = Stripe(null);
                    const elements = stripe.elements();

                    const style = {
                        base: {
                            fontSize: '14px',
                            color: '#fff',
                            width: '100%',
                            height: '40px',
                            paddingLeft: '10px',
                            paddingRight: '10px',
                            border: '1px solid #9b9b9b',
                            backgroundColor: 'transparent',
                            borderRadius: '10px',
                        },
                    };

                    const cardNumber = elements.create('cardNumber', {style: style});
                    const cardExpiry = elements.create('cardExpiry', {style: style});
                    const cardCvc = elements.create('cardCvc', {style: style});

                    cardNumber.mount('#card_number');
                    cardExpiry.mount('#expiration_date');
                    cardCvc.mount('#cvv');

                    const form = document.getElementById('payment-form');
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();

                        if (!ensureReservationDateSelected()) {
                            hideCheckoutProcessingOverlay();
                            return;
                        }

                        prepareCheckoutCartPayload(form);
                        showCheckoutProcessingOverlay();

                        const {token, error} = await stripe.createToken(cardNumber);

                        if (error) {
                            hideCheckoutProcessingOverlay();
                            document.getElementById('card-errors').textContent = error.message;
                        } else {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.setAttribute('type', 'hidden');
                            hiddenInput.setAttribute('name', 'stripeToken');
                            hiddenInput.setAttribute('value', token.id);
                            form.appendChild(hiddenInput);
                            form.submit();
                        }
                    });
                

// ----- SCRIPT BOUNDARY -----


            document.addEventListener('DOMContentLoaded', function() {
                const mobileQuery = window.matchMedia('(max-width: 768px)');
                const collapsibleBlocks = document.querySelectorAll('[data-mobile-collapsible]');

                function refreshCollapsibleBlock(block) {
                    const content = block.querySelector('.story-copy-collapsible');
                    const toggle = block.querySelector('.story-copy-toggle');

                    if (!content || !toggle) {
                        return;
                    }

                    if (!mobileQuery.matches) {
                        block.classList.remove('is-collapsed');
                        block.classList.remove('is-expanded');
                        toggle.style.display = 'none';
                        toggle.textContent = 'See more';
                        toggle.setAttribute('aria-expanded', 'true');
                        return;
                    }

                    if (!block.classList.contains('is-expanded')) {
                        block.classList.add('is-collapsed');
                    }

                    requestAnimationFrame(function() {
                        const isOverflowing = content.scrollHeight > content.clientHeight + 1;

                        if (!isOverflowing && !block.classList.contains('is-expanded')) {
                            block.classList.remove('is-collapsed');
                            toggle.style.display = 'none';
                            return;
                        }

                        toggle.style.display = 'inline-flex';
                        toggle.textContent = block.classList.contains('is-expanded') ? 'See less' : 'See more';
                        toggle.setAttribute('aria-expanded', block.classList.contains('is-expanded') ? 'true' : 'false');
                    });
                }

                collapsibleBlocks.forEach(function(block) {
                    const toggle = block.querySelector('.story-copy-toggle');

                    if (!toggle) {
                        return;
                    }

                    toggle.addEventListener('click', function() {
                        block.classList.toggle('is-expanded');
                        block.classList.toggle('is-collapsed', !block.classList.contains('is-expanded'));
                        refreshCollapsibleBlock(block);
                    });

                    refreshCollapsibleBlock(block);
                });

                if (typeof mobileQuery.addEventListener === 'function') {
                    mobileQuery.addEventListener('change', function() {
                        collapsibleBlocks.forEach(refreshCollapsibleBlock);
                    });
                } else if (typeof mobileQuery.addListener === 'function') {
                    mobileQuery.addListener(function() {
                        collapsibleBlocks.forEach(refreshCollapsibleBlock);
                    });
                }
            });
        

// ----- SCRIPT BOUNDARY -----


            // Mobile: move Order Summary between package selection and the step indicator
            // (Package Details / Transportation / Payment). Desktop: restore to its original parent.
            document.addEventListener('DOMContentLoaded', function() {
                var sidebar = document.getElementById('cv-order-sidebar');
                var stepsAnchor = document.getElementById('checkout-steps');
                if (!sidebar || !stepsAnchor) return;

                var originalParent = sidebar.parentNode;
                var originalNext = sidebar.nextSibling;
                var mq = window.matchMedia('(max-width: 991px)');

                function applySidebarPlacement() {
                    if (mq.matches) {
                        if (sidebar.parentNode !== stepsAnchor.parentNode || sidebar.nextSibling !== stepsAnchor) {
                            stepsAnchor.parentNode.insertBefore(sidebar, stepsAnchor);
                        }
                    } else {
                        if (sidebar.parentNode !== originalParent) {
                            if (originalNext && originalNext.parentNode === originalParent) {
                                originalParent.insertBefore(sidebar, originalNext);
                            } else {
                                originalParent.appendChild(sidebar);
                            }
                        }
                    }
                }

                applySidebarPlacement();
                if (typeof mq.addEventListener === 'function') {
                    mq.addEventListener('change', applySidebarPlacement);
                } else if (typeof mq.addListener === 'function') {
                    mq.addListener(applySidebarPlacement);
                }
            });
        

// ----- SCRIPT BOUNDARY -----


            document.addEventListener('DOMContentLoaded', function() {
                const requestedPackageId = null;
                if (!requestedPackageId) {
                    return;
                }

                setTimeout(function() {
                    const targetButton = document.querySelector('.vip-btn[data-id="' + requestedPackageId + '"]');
                    if (targetButton) {
                        targetButton.click();
                        const steps = document.getElementById('checkout-steps');
                        if (steps) {
                            steps.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                }, 350);
            });
        

// ----- SCRIPT BOUNDARY -----


            document.addEventListener('DOMContentLoaded', function() {
                const popup = null;
                if (!popup) {
                    return;
                }

                const modalElement = document.getElementById('checkoutPopupModal');
                if (!modalElement) {
                    return;
                }

                const seenKey = 'checkout_popup_seen_' + popup.id;
                if (popup.show_once_per_session && sessionStorage.getItem(seenKey) === '1') {
                    return;
                }

                setTimeout(function() {
                    bootstrap.Modal.getOrCreateInstance(modalElement).show();
                    if (popup.show_once_per_session) {
                        sessionStorage.setItem(seenKey, '1');
                    }
                }, 450);
            });
        

// ----- SCRIPT BOUNDARY -----


            document.addEventListener('DOMContentLoaded', function() {
                // Gallery Modal Carousel
                function initGalleryCarousel() {
                    var currentSlide = 0;
                    var slides = Array.from(document.querySelectorAll('#affGalleryCarousel .aff-gallery-carousel-item'));
                    var indicators = Array.from(document.querySelectorAll('#affGalleryIndicators .aff-gallery-carousel-indicator'));
                    var prevBtn = document.getElementById('affGalleryPrev');
                    var nextBtn = document.getElementById('affGalleryNext');

                    if (slides.length === 0) return;

                    function showSlide(index) {
                        slides.forEach(slide => slide.style.display = 'none');
                        indicators.forEach(ind => ind.classList.remove('active'));

                        if (index < 0) currentSlide = slides.length - 1;
                        if (index >= slides.length) currentSlide = 0;

                        slides[currentSlide].style.display = 'flex';
                        if (indicators[currentSlide]) {
                            indicators[currentSlide].classList.add('active');
                        }
                    }

                    if (prevBtn) {
                        prevBtn.addEventListener('click', function() {
                            currentSlide--;
                            showSlide(currentSlide);
                        });
                    }

                    if (nextBtn) {
                        nextBtn.addEventListener('click', function() {
                            currentSlide++;
                            showSlide(currentSlide);
                        });
                    }

                    indicators.forEach(function(indicator) {
                        indicator.addEventListener('click', function() {
                            currentSlide = parseInt(this.getAttribute('data-index'));
                            showSlide(currentSlide);
                        });
                    });

                    // Show first slide
                    showSlide(0);

                    // Keyboard navigation
                    document.addEventListener('keydown', function(e) {
                        var modal = document.getElementById('affGalleryModal');
                        if (modal && modal.classList.contains('show')) {
                            if (e.key === 'ArrowLeft') {
                                currentSlide--;
                                showSlide(currentSlide);
                            } else if (e.key === 'ArrowRight') {
                                currentSlide++;
                                showSlide(currentSlide);
                            }
                        }
                    });
                }

                // Hero Gallery Carousel (Desktop only)
                function initHeroCarousel() {
                    var track = document.getElementById('affHeroCarouselTrack');
                    var prevBtn = document.getElementById('affHeroCarouselPrev');
                    var nextBtn = document.getElementById('affHeroCarouselNext');
                    if (!track) return;

                    var items = Array.from(track.querySelectorAll('.aff-hero-carousel-item'));
                    var currentIndex = 0;

                    if (items.length === 0) return;

                    function showItem(index) {
                        items.forEach(item => item.style.display = 'none');
                        items[index].style.display = 'flex';
                    }

                    if (prevBtn) {
                        prevBtn.addEventListener('click', function() {
                            currentIndex = (currentIndex - 1 + items.length) % items.length;
                            showItem(currentIndex);
                        });
                    }

                    if (nextBtn) {
                        nextBtn.addEventListener('click', function() {
                            currentIndex = (currentIndex + 1) % items.length;
                            showItem(currentIndex);
                        });
                    }

                    // Show first item
                    showItem(0);
                }

                initHeroCarousel();

                // Profile picture click to open gallery
                var profileBtn = document.getElementById('affProfileStoryBtn');
                if (profileBtn) {
                    profileBtn.addEventListener('click', function() {
                        var modal = document.getElementById('affGalleryModal');
                        if (modal) {
                            bootstrap.Modal.getOrCreateInstance(modal).show();
                            initGalleryCarousel();
                        }
                    });
                }
            });
        

// ----- SCRIPT BOUNDARY -----


        

// ----- SCRIPT BOUNDARY -----


            document.addEventListener('click', function(event) {
                const trigger = event.target.closest('.js-checkout-gallery-trigger');
                if (!trigger) {
                    return;
                }

                const modalElement = document.getElementById('checkoutGalleryModal');
                const modalImage = document.getElementById('checkoutGalleryModalImage');
                if (!modalElement || !modalImage) {
                    return;
                }

                modalImage.src = trigger.getAttribute('data-gallery-src') || '';
                modalImage.alt = trigger.getAttribute('data-gallery-alt') || 'Gallery image';
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            });

            document.getElementById('checkoutGalleryModal')?.addEventListener('hidden.bs.modal', function() {
                const modalImage = document.getElementById('checkoutGalleryModalImage');
                if (modalImage) {
                    modalImage.src = '';
                    modalImage.alt = '';
                }
            });
        

// ----- SCRIPT BOUNDARY -----


        (function() {
            function initSidebar() {
                var sidebarBody = document.getElementById('cv-sidebar-body');
                if (!sidebarBody) return;

                var cartSection = document.getElementById('cart-section');
                var pricingShell = document.querySelector('.pricing-shell');
                if (cartSection) sidebarBody.appendChild(cartSection);
                if (pricingShell) sidebarBody.appendChild(pricingShell);

                // Move the promo code section to AFTER the deposit box so it sits below the Due Today box.
                var depositBox = document.getElementById('cv-deposit-box');
                var promoCol = pricingShell ? pricingShell.querySelector('.dynamic-price.col-md-6') : null;
                if (depositBox && promoCol && depositBox.parentNode) {
                    depositBox.parentNode.insertBefore(promoCol, depositBox.nextSibling);
                }
            }

            function initPackageSearch() {
                var locationFilter = document.getElementById('package-location-filter-main');
                var categoriesGate = document.querySelector('.aff-location-gated');
                var noLocationMsg = document.getElementById('package-no-location-message');

                if (!locationFilter) return;

                // Hide all category tabs initially using class
                document.querySelectorAll('.package-category-tile').forEach(function(tab) {
                    tab.classList.add('hidden-tab');
                    tab.classList.remove('visible-tab');
                });

                function filterPackages() {
                    var locationId = String(locationFilter.value || '').trim();
                    console.log('Filter called with locationId:', locationId);
                    var packageHeader = document.querySelector('.aff-package-header-gated');

                    if (!locationId) {
                        // No location selected - hide everything
                        if (categoriesGate) categoriesGate.classList.remove('is-visible');
                        if (noLocationMsg) noLocationMsg.style.display = 'block';
                        if (packageHeader) packageHeader.style.display = 'none';
                        document.querySelectorAll('.package-category-tile').forEach(function(tab) {
                            tab.classList.add('hidden-tab');
                            tab.classList.remove('visible-tab');
                        });
                        return;
                    }

                    // Location selected - show categories gate and package header
                    if (categoriesGate) categoriesGate.classList.add('is-visible');
                    if (noLocationMsg) noLocationMsg.style.display = 'none';
                    if (packageHeader) packageHeader.style.display = 'flex';

                    // Hide all packages first
                    document.querySelectorAll('[id^="pkg-card-"]').forEach(function(card) {
                        card.style.display = 'none';
                    });

                    // Show only packages from selected location
                    document.querySelectorAll('[id^="pkg-card-"]').forEach(function(card) {
                        var clubId = card.getAttribute('data-club-id');
                        if (clubId) clubId = clubId.trim();
                        if (clubId && clubId === locationId) {
                            card.style.display = '';
                        }
                    });

                    // Show only categories that have packages from selected location
                    var firstVisibleTab = null;
                    document.querySelectorAll('.package-category-tile').forEach(function(tab) {
                        var targetId = tab.getAttribute('data-target');
                        var categoryId = null;
                        if (targetId) {
                            var match = targetId.match(/category-group-(.+)/);
                            if (match) {
                                categoryId = match[1];
                            }
                        }

                        if (categoryId) {
                            var groupDiv = document.getElementById('category-group-' + categoryId);
                            var hasPackagesFromLocation = false;
                            if (groupDiv) {
                                hasPackagesFromLocation = Array.from(groupDiv.querySelectorAll('[id^="pkg-card-"]'))
                                    .some(function(card) {
                                        var clubId = card.getAttribute('data-club-id');
                                        if (clubId) clubId = String(clubId).trim();
                                        return !!(clubId && clubId === locationId);
                                    });
                            }

                            if (hasPackagesFromLocation) {
                                tab.classList.remove('hidden-tab');
                                tab.classList.add('visible-tab');
                                if (!firstVisibleTab) {
                                    firstVisibleTab = tab;
                                }
                            } else {
                                tab.classList.add('hidden-tab');
                                tab.classList.remove('visible-tab');
                            }
                        }
                    });

                    // Auto-open the first visible category so selected packages are actually shown.
                    document.querySelectorAll('.package-category-group').forEach(function(group) {
                        var groupHasVisibleCard = Array.from(group.querySelectorAll('[id^="pkg-card-"]')).some(function(card) {
                            return card.style.display !== 'none';
                        });
                        group.style.display = groupHasVisibleCard ? '' : 'none';
                    });

                    if (firstVisibleTab) {
                        document.querySelectorAll('.package-category-tile').forEach(function(tab) {
                            tab.classList.toggle('active', tab === firstVisibleTab);
                        });
                    }
                }

                locationFilter.addEventListener('change', filterPackages);
                // Call filterPackages once on init to set initial state
                filterPackages();
            }

            function initMobileGalleryCarousel() {
                var track = document.getElementById('affMgcTrack');
                var dotsWrap = document.getElementById('affMgcDots');
                if (!track) return;

                var dots = dotsWrap ? Array.from(dotsWrap.querySelectorAll('.aff-mgc-dot')) : [];
                var slides = Array.from(track.querySelectorAll('.aff-mgc-slide'));
                var autoScrollInterval = null;
                var currentSlide = 0;
                var isDown = false;
                var startX;
                var scrollLeft;

                function setDot(idx) {
                    dots.forEach(function(d, i) { d.classList.toggle('is-active', i === idx); });
                }

                function startAutoScroll() {
                    clearInterval(autoScrollInterval);
                    autoScrollInterval = setInterval(function() {
                        currentSlide = (currentSlide + 1) % slides.length;
                        var slideWidth = track.offsetWidth; // Use actual track width
                        track.scrollLeft = slideWidth * currentSlide;
                        setDot(currentSlide);
                    }, 5000);
                }

                function stopAutoScroll() {
                    clearInterval(autoScrollInterval);
                }

                // Drag functionality (horizontal)
                track.addEventListener('mousedown', function(e) {
                    isDown = true;
                    startX = e.pageX - track.offsetLeft;
                    scrollLeft = track.scrollLeft;
                    track.classList.add('is-dragging');
                    stopAutoScroll();
                });

                track.addEventListener('mouseleave', function() {
                    isDown = false;
                    track.classList.remove('is-dragging');
                    startAutoScroll();
                });

                track.addEventListener('mouseup', function() {
                    isDown = false;
                    track.classList.remove('is-dragging');
                    startAutoScroll();
                });

                track.addEventListener('mousemove', function(e) {
                    if (!isDown) return;
                    e.preventDefault();
                    var x = e.pageX - track.offsetLeft;
                    var walk = (x - startX) * 0.5;
                    track.scrollLeft = scrollLeft - walk;
                });

                // Touch support (horizontal drag)
                track.addEventListener('touchstart', function(e) {
                    isDown = true;
                    startX = e.touches[0].pageX - track.offsetLeft;
                    scrollLeft = track.scrollLeft;
                    stopAutoScroll();
                }, { passive: true });

                track.addEventListener('touchend', function() {
                    isDown = false;
                    startAutoScroll();
                }, { passive: true });

                track.addEventListener('touchmove', function(e) {
                    if (!isDown) return;
                    var x = e.touches[0].pageX - track.offsetLeft;
                    var walk = (x - startX) * 1.5;
                    track.scrollLeft = scrollLeft - walk;
                }, { passive: true });

                // Scroll snap update
                track.addEventListener('scroll', function() {
                    var scrollPos = track.scrollLeft;
                    var slideWidth = track.offsetWidth; // Use actual track width
                    currentSlide = Math.round(scrollPos / slideWidth);
                    setDot(Math.min(currentSlide, slides.length - 1));
                });

                // Dot navigation
                dots.forEach(function(dot, index) {
                    dot.addEventListener('click', function() {
                        currentSlide = index;
                        var slideWidth = track.offsetWidth; // Use actual track width
                        track.scrollLeft = slideWidth * index;
                        setDot(currentSlide);
                        stopAutoScroll();
                        startAutoScroll();
                    });
                });

                setDot(0);
                if (slides.length > 1) startAutoScroll();
            }

            function initDefaultOpenCategory() {
                var $firstTile = $('.package-category-tile').first();
                if ($firstTile.length) {
                    $firstTile.trigger('click');
                }
            }

            function initSidebarDateSync() {
                var dateInput = document.getElementById('package_use_date');
                var sidebarDate = document.getElementById('cv-sidebar-date');
                if (!dateInput || !sidebarDate) return;

                function updateSidebarDate() {
                    var val = dateInput.value;
                    sidebarDate.innerHTML = '<i class="fas fa-calendar-alt" style="margin-right:4px;opacity:.6;"></i>' + (val || 'Select a date above');
                }

                dateInput.addEventListener('change', updateSidebarDate);
                dateInput.addEventListener('input', updateSidebarDate);
                updateSidebarDate();
            }

            function initReservationDatePicker() {
                var dateInput = document.getElementById('package_use_date');
                if (!dateInput) return;

                // Check if flatpickr is available
                if (typeof flatpickr === 'undefined') {
                    console.warn('Flatpickr not loaded, date picker will use browser default');
                    return;
                }

                // Destroy existing flatpickr instance if it exists
                if (dateInput._flatpickr) {
                    dateInput._flatpickr.destroy();
                }

                flatpickr(dateInput, {
                    mode: 'single',
                    minDate: 'today',
                    dateFormat: 'Y-m-d',
                    enableTime: false,
                    disableMobile: false,
                    onChange: function(selectedDates, dateStr, instance) {
                        // Trigger change event for sidebar sync
                        var event = new Event('change', { bubbles: true });
                        dateInput.dispatchEvent(event);
                    }
                });
            }

            function initClubTooltips() {
                var tooltipElements = document.querySelectorAll('.cv-club-name-badge');
                tooltipElements.forEach(function(element) {
                    new bootstrap.Tooltip(element);
                });
            }

            function initSidebarCta() {
                var ctaBtn = document.getElementById('cv-sidebar-cta');
                var cartList = document.getElementById('cart-list');
                if (!ctaBtn || !cartList || !window.MutationObserver) return;

                new MutationObserver(function() {
                    var hasItems = cartList.children.length > 0;
                    ctaBtn.disabled = !hasItems;
                    ctaBtn.style.display = hasItems ? '' : 'none';

                    var depositBox = document.getElementById('cv-deposit-box');
                    if (depositBox) depositBox.style.display = hasItems ? '' : 'none';

                    var editBtn = document.getElementById('cv-edit-cart');
                    if (editBtn) editBtn.style.display = hasItems ? '' : 'none';

                    var mobileCount = document.getElementById('cv-mobile-cart-count');
                    if (mobileCount) {
                        var count = cartList.querySelectorAll('.cart-line').length;
                        mobileCount.textContent = count + (count === 1 ? ' item' : ' items');
                    }
                }).observe(cartList, { childList: true });

                ctaBtn.addEventListener('click', function() {
                    var nextBtn = document.getElementById('next-to-transport');
                    if (nextBtn && nextBtn.style.display !== 'none') {
                        nextBtn.click();
                    }
                });

                // Deposit display is updated directly in calculateCartTotal — no observer needed.
            }

            function initMobileToggle() {
                var toggleBtn = document.getElementById('cv-mobile-cart-toggle');
                var sidebar = document.getElementById('cv-order-sidebar');
                if (!toggleBtn || !sidebar) return;

                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('cv-sidebar-open');
                });
            }

            function initHamburger() {
                var hamburger = document.getElementById('cv-hamburger');
                if (!hamburger) return;
                hamburger.addEventListener('click', function() {
                    var mobileActions = document.querySelector('.mobile-top-actions');
                    if (mobileActions) {
                        mobileActions.style.display = mobileActions.style.display === 'none' ? '' : 'none';
                    }
                });
            }

            /* ===== Dynamic checkout step indicator ===== */
            function checkPackageFormFilled() {
                var section = document.getElementById('section-1');
                if (!section) return false;
                var reqInputs = section.querySelectorAll('input[required], select[required]');
                if (reqInputs.length === 0) return false;
                for (var i = 0; i < reqInputs.length; i++) {
                    if (!reqInputs[i].value || reqInputs[i].value.trim() === '') return false;
                }
                return true;
            }

            function updateCheckoutSteps() {
                var stepEls = [
                    document.getElementById('cv-dstep-1'),
                    document.getElementById('cv-dstep-2'),
                    document.getElementById('cv-dstep-3'),
                    document.getElementById('cv-dstep-4')
                ];
                if (!stepEls[0]) return;

                stepEls.forEach(function(s) {
                    if (s) s.classList.remove('is-active', 'is-complete');
                });

                var dateInput = document.getElementById('package_use_date');
                var dateDone = !dateInput || (dateInput.value && dateInput.value.trim() !== '');

                var accessTabs = document.querySelectorAll('.cv-access-tab');
                var accessDone = accessTabs.length === 0 || !!document.querySelector('.cv-access-tab.is-active');

                var cartList = document.getElementById('cart-list');
                var cartDone = !!(cartList && cartList.children.length > 0);

                var formDone = checkPackageFormFilled();

                if (dateDone) stepEls[0].classList.add('is-complete');
                if (dateDone && accessDone) stepEls[1].classList.add('is-complete');
                if (dateDone && accessDone && cartDone) stepEls[2].classList.add('is-complete');
                if (dateDone && accessDone && cartDone && formDone) stepEls[3].classList.add('is-complete');

                if (!dateDone) stepEls[0].classList.add('is-active');
                else if (!accessDone) stepEls[1].classList.add('is-active');
                else if (!cartDone) stepEls[2].classList.add('is-active');
                else stepEls[3].classList.add('is-active');
            }
            window.updateCheckoutSteps = updateCheckoutSteps;

            function initCheckoutSteps() {
                if (!document.getElementById('cv-dstep-1')) return;
                updateCheckoutSteps();

                var dateInput = document.getElementById('package_use_date');
                if (dateInput) {
                    dateInput.addEventListener('change', updateCheckoutSteps);
                    dateInput.addEventListener('input', updateCheckoutSteps);
                }

                var cartList = document.getElementById('cart-list');
                if (cartList && window.MutationObserver) {
                    new MutationObserver(updateCheckoutSteps).observe(cartList, { childList: true });
                }

                document.addEventListener('input', function(e) {
                    if (e.target && e.target.closest && e.target.closest('#section-1')) {
                        updateCheckoutSteps();
                    }
                });
                document.addEventListener('change', function(e) {
                    if (e.target && e.target.closest && e.target.closest('#section-1')) {
                        updateCheckoutSteps();
                    }
                });
            }

            /* ===== Date Selection Notification ===== */
            function initDateNotification() {
                var dateInput = document.getElementById('package_use_date');
                if (!dateInput) return;

                dateInput.addEventListener('change', function() {
                    if (this.value && this.value.trim() !== '') {
                        var toast = document.getElementById('cv-cart-toast');
                        var title = document.querySelector('#cv-cart-toast .cv-toast-title');
                        var sub = document.getElementById('cv-cart-toast-sub');
                        var icon = document.querySelector('#cv-cart-toast .cv-toast-icon i');
                        
                        if (toast && title && sub && icon) {
                            title.textContent = 'Reservation date selected!';
                            sub.textContent = 'Choose your package';
                            icon.className = 'fas fa-calendar-check';
                            toast.classList.add('is-visible');
                            
                            setTimeout(function() {
                                toast.classList.remove('is-visible');
                            }, 3500);
                        }
                    }
                });
            }


            document.addEventListener('DOMContentLoaded', function() {
                initSidebar();
                initPackageSearch();
                initMobileGalleryCarousel();
                initClubTooltips();
                initSidebarDateSync();
                initSidebarCta();
                initHamburger();
                initCheckoutSteps();
                initDateNotification();
            });
        })();
        