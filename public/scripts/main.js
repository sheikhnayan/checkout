// This file is intentionally left blank.


// Tab switching via the cv-access-card buttons (Free Ride & Entry / Packages)
function applyAccessTab(names) {
  const guestSection = document.querySelector('.guest');
  const packageContent = document.querySelector('.package');

  if (names === 'guest') {
    if (guestSection) guestSection.style.display = 'block';
    if (packageContent) packageContent.style.display = 'none';
  } else {
    if (guestSection) guestSection.style.display = 'none';
    if (packageContent) packageContent.style.display = 'block';
  }
}

document.querySelectorAll('.cv-access-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.cv-access-tab').forEach(t => t.classList.remove('is-active'));
    this.classList.add('is-active');
    applyAccessTab(this.dataset.name);
  });
});

// Initial state - read from the access tab marked is-active
const activeAccessTab = document.querySelector('.cv-access-tab.is-active');
if (activeAccessTab) {
  applyAccessTab(activeAccessTab.dataset.name);
}

// Example: Highlight "Added" package button
document.querySelectorAll('.vip-packages button').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.vip-packages button').forEach(b => b.textContent = 'Add Package');
        this.textContent = 'Added';
    });
});






// guest count script
  let counts = { women: 0, men: 0 };


    function updateDisplay() {
      document.getElementById('womenCount').textContent = counts.women;
      document.getElementById('menCount').textContent = counts.men;
      document.getElementById('totalCount').textContent = counts.women + counts.men;
      // Update hidden fields
      var menHidden = document.getElementById('men_count');
      var womenHidden = document.getElementById('women_count');
      if (menHidden) menHidden.value = counts.men;
      if (womenHidden) womenHidden.value = counts.women;
      checkEligibility();
    }


    function increments(type) {
      if (type === 'total') return; // prevent manual total++
      counts[type]++;
      updateDisplay();
    }


    function decrements(type) {
      if (counts[type] > 0) {
        counts[type]--;
        updateDisplay();
      }
    }


    function resets() {
      counts.women = 0;
      counts.men = 0;
      updateDisplay();
    }

    // function checkEligibility() {
    //   const sms = document.getElementById('smsConsent').checked;
    //   const terms = document.getElementById('termsConsent').checked;
    //   const total = counts.women + counts.men;
    //   const btn = document.getElementById('submitBtn');

    //   if (sms && terms && total > 0) {
    //     btn.classList.add('active');
    //     btn.disabled = false;
    //   } else {
    //     btn.classList.remove('active');
    //     btn.disabled = true;
    //   }
    // }

    // function checkEligibility_two() {
    //   const sms = document.getElementById('smsConsent_two').checked;
    //   const terms = document.getElementById('termsConsent_two').checked;
    //   const total = counts.women + counts.men;
    //   const btn = document.getElementById('submitBtn_two');

    //   if (sms && terms && total > 0) {
    //     btn.classList.add('active');
    //     btn.disabled = false;
    //   } else {
    //     btn.classList.remove('active');
    //     btn.disabled = true;
    //   }
    // }

    // document.getElementById('smsConsent').addEventListener('change', checkEligibility);
    // document.getElementById('termsConsent').addEventListener('change', checkEligibility);
    // document.getElementById('smsConsent_two').addEventListener('change', checkEligibility_two);

    function isIosMobileDevice() {
      return /iPad|iPhone|iPod/.test(navigator.userAgent) ||
        (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    }

    function openMobileTimePicker(field) {
      if (!field) {
        return;
      }

      try {
        if (typeof field.showPicker === 'function') {
          field.showPicker();
          return;
        }
      } catch (e) {
        // ignore
      }

      if (field._flatpickr && typeof field._flatpickr.open === 'function') {
        try {
          field._flatpickr.open();
          return;
        } catch (e) {
          // ignore
        }
      }

      try {
        field.focus();
      } catch (e) {
        // ignore
      }
    }

    function patchCheckoutValidateStep() {
      if (typeof window.validateStep !== 'function') {
        return;
      }

      var originalValidateStep = window.validateStep;
      window.validateStep = function () {
        var result = originalValidateStep.apply(this, arguments);

        if (!result && arguments[0] === 2 && isIosMobileDevice()) {
          setTimeout(function () {
            var pickup = document.querySelector('input[name="transportation_pickup_time"]');
            var arrival = document.querySelector('input[name="transportation_arrival_time"]');
            var invalidField = document.querySelector('.required-field');
            var timeField = null;

            if (invalidField && (invalidField === pickup || invalidField === arrival || invalidField.matches('input[name="transportation_pickup_time"], input[name="transportation_arrival_time"], #Pick-up-time, #Arrival-time'))) {
              timeField = invalidField;
            } else if (pickup && pickup.classList.contains('required-field')) {
              timeField = pickup;
            } else if (arrival && arrival.classList.contains('required-field')) {
              timeField = arrival;
            }

            if (timeField) {
              openMobileTimePicker(timeField);
            }
          }, 120);
        }

        return result;
      };
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', patchCheckoutValidateStep);
    } else {
      patchCheckoutValidateStep();
    }
    // document.getElementById('termsConsent_two').addEventListener('change', checkEligibility_two);
