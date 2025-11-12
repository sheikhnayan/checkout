// This file is intentionally left blank.


// Example: Tab switching (if you want to add Guest List functionality)
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        names =  this.dataset.name;

        if(names === 'guest') {
            document.querySelector('.guest').style.display = 'block';
            document.querySelector('.package').style.display = 'none';
        } else {
            document.querySelector('.guest').style.display = 'none';
            document.querySelector('.package').style.display = 'block';
        }
    });
});

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
    // document.getElementById('termsConsent_two').addEventListener('change', checkEligibility_two);
