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
            + `<div style='margin-left:20px; font-size:12px;'>Addons: ${pkg.addons.length ? pkg.addons.map(a => a.name + ' ($' + a.price + ')').join(', ') : 'None'}</div>`
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
    let sales_tax_price = ("{{ $data->sales_tax_name }}" != "0") ? (subtotal / 100) * sales_tax : 0;
    let gratuited_price = ("{{ $data->gratuity_name }}" != "0") ? ((subtotal + sales_tax_price + service_charge_price) / 100) * gratuity : 0;
    
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
                    price: parseFloat($(this).data('price')) 
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

function getCurrentSelections() {
    return {
        cart: JSON.stringify(cart),
        coupon: cartCoupon ? cartCoupon.code : ''
    };
}

function setSelectionsFromParams(params) {
    if (params.cart) {
        try {
            cart = JSON.parse(decodeURIComponent(params.cart));
            renderCart();
            calculateCartTotal();
            openPackageTab();
            $('.dynamic-price').show();
            $('.default-price').hide();
            $('#checkout-steps').show();
        } catch(e) {
            console.error('Error parsing cart:', e);
        }
    }
    if (params.coupon) {
        $('#promo_code').val(params.coupon);
        setTimeout(function() {
            $('#applyPromoBtn').trigger('click');
        }, 500);
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
    $.get('/{{ $data->slug }}/check/' + encodeURIComponent(code), function(res) {
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
