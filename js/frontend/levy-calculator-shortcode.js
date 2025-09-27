document.addEventListener('DOMContentLoaded', function() {
    
    const coastalynk_leavy_calculator = {
        gtInput: document.getElementById('coastalynk-calculator-gt'),
        ntInput: document.getElementById('coastalynk-calculator-nt'),
        calculateBtn: document.getElementById('coastalynk-calculator-calculate'),
        resetBtn: document.getElementById('coastalynk-calculator-reset-btn'),
        resultElement: document.getElementById('coastalynk-calculator-result-value'),
        gtError: document.getElementById('coastalynk-calculator-gt-error'),
        ntError: document.getElementById('coastalynk-calculator-nt-error'),
        rate1: COSTALYNK_CALC_VARS.rate1,
        rate2: COSTALYNK_CALC_VARS.rate2,
        calculateLevy: function () {
            // Reset error messages
            coastalynk_leavy_calculator.gtError.style.display = 'none';
            coastalynk_leavy_calculator.ntError.style.display = 'none';
            
            // Get input values
            const gt = parseFloat(coastalynk_leavy_calculator.gtInput.value);
            const nt = parseFloat(coastalynk_leavy_calculator.ntInput.value);
            
            // Validate inputs
            let isValid = true;
            
            if (isNaN(gt) || gt < 0) {
                coastalynk_leavy_calculator.gtError.style.display = 'block';
                isValid = false;
            }
            
            if (isNaN(nt) || nt < 0) {
                coastalynk_leavy_calculator.ntError.style.display = 'block';
                isValid = false;
            }
            
            if (!isValid) {
                coastalynk_leavy_calculator.resultElement.textContent = '-';
                return;
            }
            
            // Calculate levy
            const levy = (gt * coastalynk_leavy_calculator.rate1) + (nt * coastalynk_leavy_calculator.rate2);
            
            // Display result with formatting
            coastalynk_leavy_calculator.resultElement.textContent = '₦' + levy.toFixed(2);
        },
        resetForm: function() {
            coastalynk_leavy_calculator.gtInput.value = '';
            coastalynk_leavy_calculator.ntInput.value = '';
            coastalynk_leavy_calculator.resultElement.textContent = '-';
            coastalynk_leavy_calculator.gtError.style.display = 'none';
            coastalynk_leavy_calculator.ntError.style.display = 'none';
        },
        init: function() {

            if( parseFloat(coastalynk_leavy_calculator.rate1) <= 0 ) {
                coastalynk_leavy_calculator.rate1 = 0.5;
            }

            if( parseFloat(coastalynk_leavy_calculator.rate1) <= 0 ) {
                coastalynk_leavy_calculator.rate2 = 0.3;
            }

            this.calculateBtn.addEventListener('click', coastalynk_leavy_calculator.calculateLevy);
            this.resetBtn.addEventListener('click', coastalynk_leavy_calculator.resetForm);
            
            // Allow Enter key to trigger calculation
            document.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    coastalynk_leavy_calculator.calculateLevy();
                }
            });
        }
    };

    coastalynk_leavy_calculator.init();
});