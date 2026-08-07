// Cart System JavaScript - Shared between index.blade.php and index_two.blade.php
let cart = [];
let cartCoupon = null;
let currentPackageAddons = [];

function addPackageToCart(packageId, packageName, packagePrice, guests, addons, transportation) {
    let existing = cart.find(p => p.packageId === packageId);
    if (existing) {
        existing.guests = guests;
        existing.addons = addons;
        existing.transportation = transportation;
    } else {
        cart.push({ packageId, packageName, packagePrice, guests, addons, transportation });
    }
    renderCart();
    calculateCartTotal();
}

function removePackageFromCart(packageId) {
    cart = cart.filter(p => p.packageId !== packageId);
    renderCart();
    calculateCartTotal();
}

function renderCart() {
    if (cart.length === 0) {
        $('#cart-section').hide();
        $('#shareLinkContainer').hide();
        return;
    }
    $('#cart-section').show();
    $('#shareLinkContainer').show();
    let html = '';
    cart.forEach(pkg => {
        let addonTotal = pkg.addons.reduce((sum, a) => sum + parseFloat(a.price), 0);
        html += `<div style='border-bottom:1px solid #444; padding:10px 0;'>`
            + `<strong>${pkg.packageName}</strong> x${pkg.guests} - $${(pkg.packagePrice * pkg.guests).toFixed(2)}`
            + `<button onclick='removePackageFromCart("${pkg.packageId}")' style='float:right; color:#fff; background:#c00; border:none; border-radius:5px; padding:5px 10px; cursor:pointer;'>Remove</button>`
            + `<div style='margin-left:20px; font-size:12px;'>Addons: ${pkg.addons.length ? pkg.addons.map(a => a.name + (a.qty && a.qty > 1 ? ' x' + a.qty : '') + ' ($' + parseFloat(a.price).toFixed(2) + ')').join(', ') : 'None'}</div>`
            + `</div>`;
    });
    $('#cart-list').html(html);
}

function calculateCartTotal() {
    let subtotal = 0;
    cart.forEach(pkg => {
        subtotal += (pkg.packagePrice * pkg.guests) + pkg.addons.reduce((sum, a) => sum + parseFloat(a.price), 0);
    });
    
    let gratuity = parseFloat($('#gratuity').val()) || 0;
    let refundable = parseFloat($('#refundable').val()) || 0;
    let sales_tax = parseFloat($('#sales_tax').val()) || 0;
    let service_charge = parseFloat($('#service_charge').val()) || 0;
    
    let service_charge_price = ("{{ $data->service_charge_name }}" != "0") ? (subtotal / 100) * service_charge : 0;
    let gratuited_price = ("{{ $data->gratuity_name }}" != "0") ? (subtotal / 100) * gratuity : 0;
    let sales_tax_price = ("{{ $data->sales_tax_name }}" != "0") ? ((subtotal + service_charge_price + gratuited_price) / 100) * sales_tax : 0;
    
    let grandTotal = subtotal + service_charge_price + sales_tax_price + gratuited_price;
    
    // Apply coupon discount
    let promoDiscount = 0;
    if (cartCoupon) {
        if (cartCoupon.type == 'percentage') {
            promoDiscount = (grandTotal / 100) * cartCoupon.discount;
        } else {
            promoDiscount = cartCoupon.discount;
        }
        grandTotal -= promoDiscount;
    }
    
    let refundable_price = (grandTotal / 100) * refundable;
    
    // Update displays
    $('.default-package-price span').text('$' + subtotal.toFixed(2));
    $('.default-service-charge span').text('$' + service_charge_price.toFixed(2));
    $('.default-sales-tax span').text('$' + sales_tax_price.toFixed(2));
    $('.default-gratuity span').text('$' + gratuited_price.toFixed(2));

    if (cartCoupon && promoDiscount > 0) {
        if ($('.default-promo-discount').length === 0) {
            $('.default-gratuity').after('<div style="font-size: 12px;" class="default-promo-discount">Promo Code Discount: <span>$0.00</span></div>');
        }
        $('.default-promo-discount span').text('-$' + promoDiscount.toFixed(2));
    } else {
        $('.default-promo-discount').remove();
    }
    
    $('.default-refundable span').text('$' + refundable_price.toFixed(2));
    $('.default-total span').text('$' + grandTotal.toFixed(2));
    $('.default-deposit span').text('$' + grandTotal.toFixed(2));
    $('.default-due span').text('$' + (grandTotal - refundable_price).toFixed(2));
    $('.payment_total').val(grandTotal.toFixed(2));
    $('#subtotal').val(refundable_price > 0 ? refundable_price.toFixed(2) : grandTotal.toFixed(2));
    
    $('#cart-total').text('Subtotal: $' + grandTotal.toFixed(2));
    if (cartCoupon) {
        $('#cart-coupon').text('Coupon: ' + cartCoupon.code + ' (-$' + promoDiscount.toFixed(2) + ')');
    } else {
        $('#cart-coupon').text('');
    }
}

// Update addon checkboxes to refresh cart when changed
$(document).on('change', '.termsConsent', function() {
    let packageId = $('#package_id').val();
    if (packageId) {
        let pkg = cart.find(p => p.packageId == packageId);
        if (pkg) {
            let addons = [];
            $('.termsConsent:checked').each(function() {
                addons.push({ 
                    id: $(this).attr('id'), 
                    name: $(this).data('name'), 
                    price: parseFloat($(this).data('price')),
                    qty: 1
                });
            });
            pkg.addons = addons;
            renderCart();
            calculateCartTotal();
        }
    }
});

// Shareable Link Logic for Cart
function openPackageTab() {
    var packageTab = $("nav .tab[data-name='package']");
    if (packageTab.length) {
        packageTab.trigger('click');
    } else {
        $('.guest').hide();
        $('.package').show();
    }
}

function getCapturedFormFields() {
    var fields = {};

    // 1. Reservation Date
    var useDate = $('#package_use_date').val() || $('input[name="package_use_date"]').val() || $('.package_use_date').val() || '';
    if (useDate) fields.package_use_date = useDate;

    // 2. Transportation & Arrival Details
    var pickupTime = $('#Pick-up-time').val() || $('input[name="transportation_pickup_time"]').val() || '';
    if (pickupTime) fields.transportation_pickup_time = pickupTime;

    var pickupAddress = $('#address').val() || $('input[name="transportation_address"]').val() || '';
    if (pickupAddress) fields.transportation_address = pickupAddress;

    var arrivalTime = $('#Arrival-time').val() || $('input[name="transportation_arrival_time"]').val() || '';
    if (arrivalTime) fields.transportation_arrival_time = arrivalTime;

    var destination = $('#destination').val() || $('input[name="transportation_destination"]').val() || '';
    if (destination) fields.transportation_destination = destination;

    // 3. Host Name & Booking / Pickup Notes
    var hostName = $('#host').val() || $('[name="host_name"]').val() || $('[name="package_host_name"]').val() || $('[name="reservation_host_name"]').val() || $('[name="host"]').val() || '';
    if (hostName) fields.host_name = hostName;

    var bookingNote = $('#note').val() || $('[name="reservation_description"]').val() || $('[name="package_note"]').val() || $('[name="transportation_note"]').val() || $('[name="notes"]').val() || $('[name="special_requests"]').val() || '';
    if (bookingNote) fields.booking_note = bookingNote;

    // 4. DOB Fields (Month, Day, Year)
    var dobMonth = $('#dob-month').val() || $('#package-dob-month').val() || $('#payment-dob-month').val() || $('[name="dob_month"]').val() || $('[name="package_dob_month"]').val() || $('[name="reservation_dob_month"]').val() || $('[name="reservation_month"]').val() || '';
    if (dobMonth) fields.dob_month = dobMonth;

    var dobDay = $('#dob-day').val() || $('#package-dob-day').val() || $('#payment-dob-day').val() || $('[name="dob_day"]').val() || $('[name="package_dob_day"]').val() || $('[name="reservation_dob_day"]').val() || $('[name="reservation_day"]').val() || '';
    if (dobDay) fields.dob_day = dobDay;

    var dobYear = $('#dob-year').val() || $('#package-dob-year').val() || $('#payment-dob-year').val() || $('[name="dob_year"]').val() || $('[name="package_dob_year"]').val() || $('[name="reservation_dob_year"]').val() || $('[name="reservation_year"]').val() || '';
    if (dobYear) fields.dob_year = dobYear;

    // 5. Customer Contact & Personal Information (including prefixed field names)
    var fieldNames = [
        'first_name', 'package_first_name', 'reservation_first_name', 'payment_first_name',
        'last_name', 'package_last_name', 'reservation_last_name', 'payment_last_name',
        'name',
        'email', 'package_email', 'reservation_email', 'payment_email',
        'phone', 'package_phone', 'reservation_phone', 'payment_phone',
        'gender', 'package_gender', 'reservation_gender',
        'country', 'package_country', 'reservation_country',
        'state', 'package_state', 'reservation_state', 'st-pv', 'state_province',
        'city', 'package_city', 'reservation_city',
        'zip', 'package_zip', 'reservation_zip', 'postal_code',
        'hotel_staying', 'package_hotel_staying', 'reservation_hotel_staying', 'hotel',
        'business_company', 'business_vat', 'business_address'
    ];

    fieldNames.forEach(function(n) {
        var el = $('[name="' + n + '"], #' + n);
        if (el.length && el.val()) {
            fields[n] = el.val();
        }
    });

    if ($('#businessExpenseCheckbox').length) {
        fields.businessExpenseCheckbox = $('#businessExpenseCheckbox').is(':checked');
    }

    // Custom Form Fields
    $('[name^="custom_fields"]').each(function() {
        var name = $(this).attr('name');
        var val = $(this).val();
        if (name && val) {
            fields[name] = val;
        }
    });

    return fields;
}

function getCurrentSelections() {
    var cartData = window.cart || (typeof cart !== 'undefined' ? cart : []);
    var couponCode = window.cartCoupon ? window.cartCoupon.code : (typeof cartCoupon !== 'undefined' && cartCoupon ? cartCoupon.code : '');
    var fields = getCapturedFormFields();

    var payload = {
        cart: cartData,
        coupon: couponCode,
        fields: fields
    };

    return {
        cart: JSON.stringify(payload),
        coupon: couponCode,
        fields: fields
    };
}

function setSelectionsFromParams(params) {
    if (!params || !params.cart) return;

    if (typeof openPackageTab === 'function') openPackageTab();

    try {
        var cartStr = params.cart;
        if (typeof cartStr === 'string') {
            try {
                cartStr = cartStr.replace(/\+/g, '%20');
                cartStr = decodeURIComponent(cartStr);
            } catch(e) {}
        }

        var parsed = (typeof cartStr === 'string') ? JSON.parse(cartStr) : cartStr;

        var cartItems = [];
        var couponCode = params.coupon || '';
        var formFields = {};

        if (parsed && typeof parsed === 'object' && !Array.isArray(parsed) && parsed.cart) {
            cartItems = parsed.cart;
            if (parsed.coupon) couponCode = parsed.coupon;
            if (parsed.fields) formFields = parsed.fields;
        } else if (Array.isArray(parsed)) {
            cartItems = parsed;
        }

        window.cart = cartItems.map(function(pkg) {
            if (typeof pkg.isMultiple === 'undefined' && typeof getPackageMultipleFromDom === 'function') {
                pkg.isMultiple = getPackageMultipleFromDom(pkg.packageId);
            }
            return pkg;
        });

        if (typeof cart !== 'undefined') {
            cart = window.cart;
        }

        if (typeof window.renderCart === 'function') window.renderCart();
        if (typeof window.calculateCartTotal === 'function') window.calculateCartTotal();
        if (typeof syncTransportationStateFromCart === 'function') syncTransportationStateFromCart();

        if (window.cart.length > 0) {
            $('#package_id').val(window.cart[0].packageId);
            $('.package_number_of_guest').val(window.cart[0].guests);
            window.cart.forEach(function(pkg) {
                $('.package_number_of_guestss[data-id="' + pkg.packageId + '"]').val(pkg.guests || 1);
                $('#pkg-card-' + pkg.packageId).addClass('selected');
            });
            $('#cart-section').show();
            $('#shareLinkContainer').show();
            $('.dynamic-price').show();
            $('.default-price').hide();
        }

        // Restore Promo Code
        if (couponCode) {
            $('#promo_code').val(couponCode);
            setTimeout(function() {
                $('#applyPromoBtn').trigger('click');
            }, 500);
        }

        // Restore Form Fields
        if (formFields && Object.keys(formFields).length > 0) {
            if (typeof populateDobSelects === 'function') {
                try { populateDobSelects(); } catch(e) {}
            }

            setTimeout(function() {
                Object.keys(formFields).forEach(function(key) {
                    var val = formFields[key];
                    if (typeof val === 'string' && val.indexOf('+') > 0) {
                        val = val.replace(/\+/g, ' ');
                    }
                    var target = $('[name="' + key + '"], #' + key);

                    if (target.length) {
                        if (target.is(':checkbox')) {
                            target.prop('checked', !!val).trigger('change');
                        } else if (target.is(':radio')) {
                            target.filter('[value="' + val + '"]').prop('checked', true).trigger('change');
                        } else {
                            target.val(val).trigger('change').trigger('input');
                            if (target[0] && target[0]._flatpickr) {
                                try {
                                    target[0]._flatpickr.setDate(val, true);
                                } catch(err) {}
                            }
                        }
                    }
                });

                // Email Aliases Sync
                var emailVal = formFields.email || formFields.package_email || formFields.reservation_email || formFields.payment_email || '';
                if (emailVal) {
                    if (emailVal.indexOf('+') > 0) emailVal = emailVal.replace(/\+/g, ' ');
                    $('[name="email"], [name="package_email"], [name="reservation_email"], [name="payment_email"], #email, #hidden_payment_email')
                        .val(emailVal).trigger('change').trigger('input');
                }

                // First Name Aliases Sync
                var firstNameVal = formFields.first_name || formFields.package_first_name || formFields.reservation_first_name || formFields.payment_first_name || formFields.name || '';
                if (firstNameVal) {
                    if (firstNameVal.indexOf('+') > 0) firstNameVal = firstNameVal.replace(/\+/g, ' ');
                    $('[name="first_name"], [name="package_first_name"], [name="reservation_first_name"], [name="payment_first_name"], [name="name"], #first_name')
                        .val(firstNameVal).trigger('change').trigger('input');
                }

                // Last Name Aliases Sync
                var lastNameVal = formFields.last_name || formFields.package_last_name || formFields.reservation_last_name || formFields.payment_last_name || '';
                if (lastNameVal) {
                    if (lastNameVal.indexOf('+') > 0) lastNameVal = lastNameVal.replace(/\+/g, ' ');
                    $('[name="last_name"], [name="package_last_name"], [name="reservation_last_name"], [name="payment_last_name"], #last_name')
                        .val(lastNameVal).trigger('change').trigger('input');
                }

                // Phone Aliases Sync
                var phoneVal = formFields.phone || formFields.package_phone || formFields.reservation_phone || formFields.payment_phone || '';
                if (phoneVal) {
                    if (phoneVal.indexOf('+') > 0) phoneVal = phoneVal.replace(/\+/g, ' ');
                    $('[name="phone"], [name="package_phone"], [name="reservation_phone"], [name="payment_phone"], #phone, #hidden_payment_phone')
                        .val(phoneVal).trigger('change').trigger('input');
                }

                // Host Name Aliases Sync
                var hostVal = formFields.host_name || formFields.package_host_name || formFields.reservation_host_name || formFields.host || '';
                if (hostVal) {
                    if (hostVal.indexOf('+') > 0) hostVal = hostVal.replace(/\+/g, ' ');
                    $('#host, [name="host_name"], [name="package_host_name"], [name="reservation_host_name"], [name="host"]')
                        .val(hostVal).trigger('change').trigger('input');
                }

                // Booking Note / Pickup Note Aliases Sync
                var noteVal = formFields.booking_note || formFields.reservation_description || formFields.package_note || formFields.transportation_note || formFields.notes || formFields.special_requests || '';
                if (noteVal) {
                    if (noteVal.indexOf('+') > 0) noteVal = noteVal.replace(/\+/g, ' ');
                    $('#note, [name="reservation_description"], [name="package_note"], [name="transportation_note"], [name="notes"], [name="special_requests"]')
                        .val(noteVal).trigger('change').trigger('input');
                }

                // DOB Month Sync
                var dobMonthVal = formFields.dob_month || formFields.package_dob_month || formFields.reservation_dob_month || formFields.reservation_month || formFields.payment_dob_month || '';
                if (dobMonthVal) {
                    var mStr = String(dobMonthVal).padStart(2, '0');
                    $('#dob-month, #package-dob-month, #payment-dob-month, #payment-dob-month2, [name="dob_month"], [name="package_dob_month"], [name="reservation_dob_month"], [name="reservation_month"], [name="payment_dob_month"]')
                        .val(mStr).trigger('change');
                }

                // DOB Day Sync
                var dobDayVal = formFields.dob_day || formFields.package_dob_day || formFields.reservation_dob_day || formFields.reservation_day || formFields.payment_dob_day || '';
                if (dobDayVal) {
                    var dStr = String(dobDayVal).padStart(2, '0');
                    $('#dob-day, #package-dob-day, #payment-dob-day, #payment-dob-day2, [name="dob_day"], [name="package_dob_day"], [name="reservation_dob_day"], [name="reservation_day"], [name="payment_dob_day"]')
                        .val(dStr).trigger('change');
                }

                // DOB Year Sync
                var dobYearVal = formFields.dob_year || formFields.package_dob_year || formFields.reservation_dob_year || formFields.reservation_year || formFields.payment_dob_year || '';
                if (dobYearVal) {
                    var yStr = String(dobYearVal);
                    $('#dob-year, #package-dob-year, #payment-dob-year, #payment-dob-year2, [name="dob_year"], [name="package_dob_year"], [name="reservation_dob_year"], [name="reservation_year"], [name="payment_dob_year"]')
                        .val(yStr).trigger('change');
                }

                // Special handling for package_use_date
                if (formFields.package_use_date) {
                    var dateEl = $('#package_use_date, input[name="package_use_date"], .package_use_date');
                    if (dateEl.length) {
                        dateEl.val(formFields.package_use_date).trigger('change');
                        if (dateEl[0] && dateEl[0]._flatpickr) {
                            try {
                                dateEl[0]._flatpickr.setDate(formFields.package_use_date, true);
                            } catch(e) {}
                        }
                    }
                }

                // Special handling for businessExpenseCheckbox
                if (formFields.businessExpenseCheckbox) {
                    $('#businessExpenseCheckbox').prop('checked', true).trigger('change');
                    $('#businessFields').show();
                    if (typeof setBusinessFieldsRequired === 'function') {
                        setBusinessFieldsRequired(true);
                    }
                }
            }, 300);
        }

        if (window.cart.length > 0) {
            $('#checkout-steps').show();
            if (typeof showStep === 'function') {
                showStep(1);
            }
        }
    } catch(e) {
        console.error('Error in setSelectionsFromParams:', e);
    }
}

function getUrlWithSelections() {
    var sel = getCurrentSelections();
    var url = window.location.origin + window.location.pathname + '?cart=' + encodeURIComponent(sel.cart);
    if (sel.coupon) {
        url += '&coupon=' + encodeURIComponent(sel.coupon);
    }
    return url;
}

// Guest count handler
$('.package_number_of_guestss').on('change', function() {
    var selectedValue = $(this).val();
    $('.package_number_of_guest').val(selectedValue);
    var packageId = $(this).data('id');
    var pkg = cart.find(p => p.packageId == packageId);
    if (pkg) {
        pkg.guests = parseInt(selectedValue);
        renderCart();
        calculateCartTotal();
    }
});

// Coupon logic
$('#applyPromoBtn').on('click', function() {
    let code = $('#promo_code').val().trim();
    if (!code) return;
    $.get('/{{ $data->slug }}/check/' + encodeURIComponent(code), { source: 'club' }, function(res) {
        if (res.valid === false || res.valid === "false") {
            cartCoupon = null;
            alert('Invalid promo code');
            calculateCartTotal();
        } else {
            cartCoupon = {
                code: code,
                id: res.id,
                discount: parseFloat(res.discount),
                type: res.type || 'percentage'
            };
            $('#applyPromoBtn').prop('disabled', true);
            $('.promo_code').val(res.id);
            calculateCartTotal();
        }
    });
});

// Shareable link button click
$(document).ready(function() {
    $('#generateShareLink').on('click', function() {
        if (cart.length === 0) {
            alert('Please add at least one package to cart');
            return;
        }
        var link = getUrlWithSelections();
        $('#shareableLink').val(link).show();
    });

    // On page load, check forparams
    var urlParams = new URLSearchParams(window.location.search);
    var cartParam = urlParams.get('cart');
    var couponParam = urlParams.get('coupon');

    // Hide shareable link button if cart param is present (shared link)
    if (cartParam) {
        $('#generateShareLink').hide();
    } else {
        $('#generateShareLink').show();
    }

    // Preselect items from params
    if (cartParam || couponParam) {
        setSelectionsFromParams({
            cart: cartParam,
            coupon: couponParam
        });
        setTimeout(function() {
            if (cart.length > 0) {
                $('#checkout-steps').show();
                showStep(3); // Go to payment
            }
        }, 1500);
    }
});

// Backward compatibility - stub function
function addToTotal(price, name, id) {
    // This function is now handled by cart system
}
