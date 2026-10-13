"use strict";
const vErC = 'error';
const pErC = 'has-validation-error';
const i = document.querySelectorAll('.form-control');
i.forEach(function (inp) {
    inp.addEventListener('input', function () {
        // We can only update the error or hide it on inp.
        // Otherwise it will show when typing.
        checkValidity(inp, { inErr: false });
    })
    inp.addEventListener('invalid', function (e) {
        // prevent showing the default display
        e.preventDefault()
        // We can also create the error in invalid.
        checkValidity(inp, { inErr: true })
    })
});

function checkValidity(inp, options) {
    var inErr = options.inErr;
    var p = inp.parentNode;
    var err = p.querySelector(`.${vErC}`) || document.createElement('label');
    if (!inp.validity.valid && inp.validationMessage) {
        err.className = vErC;
        err.textContent = inp.validationMessage;
        if (inErr) {
            if (inp.validity.valueMissing && inp.getAttribute('data-value-missing')) {
                inp.setCustomValidity(inp.getAttribute('data-value-missing'));
                err.innerHTML = inp.getAttribute('data-value-missing');
            } else if (inp.validity.typeMismatch && inp.getAttribute('data-type-mismatch')) {
                inp.setCustomValidity(inp.getAttribute('data-type-mismatch'));
                err.innerHTML = inp.getAttribute('data-type-mismatch');
            } else if (inp.validity.patternMismatch && inp.getAttribute('data-pattern')) {
                inp.setCustomValidity(inp.getAttribute('data-pattern'));
                err.innerHTML = inp.getAttribute('data-pattern');
            } else if (inp.validity.tooShort && inp.getAttribute('data-min-length')) {
                inp.setCustomValidity(inp.getAttribute('data-min-length'));
                err.innerHTML = inp.getAttribute('data-min-length');
            } else if (inp.validity.tooLong && inp.getAttribute('data-max-length')) {
                inp.setCustomValidity(inp.getAttribute('data-max-length'));
                err.innerHTML = inp.getAttribute('data-max-length');
            } else if (inp.validity.stepMismatch && inp.getAttribute('data-step-mismatch')) {
                inp.setCustomValidity(inp.getAttribute('data-step-mismatch'));
                err.innerHTML = inp.getAttribute('data-step-mismatch');
            } else if (inp.validity.rangeUnderflow && inp.getAttribute('data-min')) {
                inp.setCustomValidity(inp.getAttribute('data-min'));
                err.innerHTML = inp.getAttribute('data-min');
            } else if (inp.validity.rangeOverflow && inp.getAttribute('data-max')) {
                inp.setCustomValidity(inp.getAttribute('data-max'));
                err.innerHTML = inp.getAttribute('data-max');
            } else if (inp.validity.badInput && inp.getAttribute('data-bad-input')) {
                inp.setCustomValidity(inp.getAttribute('data-bad-input'));
                err.innerHTML = inp.getAttribute('data-bad-input');
            } else if (inp.validity.customError && inp.getAttribute('data-custom-error')) {
                inp.setCustomValidity(inp.getAttribute('data-custom-error'));
                err.innerHTML = inp.getAttribute('data-custom-error');
            }
            p.append(inp, err);
            p.classList.add(pErC);
        } else {
            var hasAttr = inp.getAttribute('data-related');
            if (hasAttr) {
                var el = document.getElementById(hasAttr);
                var elP = el.parentNode;
                var elEr = elP.querySelector(`.${vErC}`)
                el.setCustomValidity('');
                elP.classList.remove(pErC);
                if (elEr != '') {
                    elEr.remove();
                }
            }
            inp.setCustomValidity('');
            p.classList.remove(pErC);
            err.remove();
        }
        if (typeof (once) != "undefined") {
            if (once == true) {
                once = false;
                $('html, body').animate({
                    scrollTop: $('.error').offset().top
                }, 1000);
            }
        }
    } else {
        inp.setCustomValidity('');
        p.classList.remove(pErC);
        err.remove();
    }
}

// remove select2 error messages
$(document).on('change', '.sl_common_bx, .datepicker', function (e) {
    this.setCustomValidity('');
    if ($(e.currentTarget).val() != '') {
        $('#' + $(this).attr('id')).parent('div').find('.error').hide();
    } else {
        $('#' + $(this).attr('id')).parent('div').find('.error').show();
    }
});

// clearing the input field
$(".onblur-clear-this-input").on("blur", function () {
    const a = document.getElementById(this.id),
        b = a.closest(".form");
    if (b) var c = b.querySelector(".onblur-clear-icon");
    c.style.display = 0 == document.getElementById(this.id).value.length ? "none" : "block", c.addEventListener("click", function () {
        a.value = "", c.style.display = "none"
    })
}), $(".onblur-clear-this-input").on("focus", function () {
    const a = document.getElementById(this.id),
        b = a.closest(".form");
    if (b) var c = b.querySelector(".onblur-clear-icon");
    c.style.display = "none"
});

/*
* Custom validation Check rather than selector .form-control
* This method has 2 parameters, at-least 1 parameter is required 
* otherwise it will return true. Moreover, it will only
* work for required condition.
*
* @param array ids
* @param string selector
* 
* @return boolean
*/
function validationCheck(ids = [], selector = '') {

    let status = true;
    // check for selectors
    if (ids.length == 0 && selector.length == 0) {
        return status;
    }

    if (selector.length) {
        let inputElements = document.querySelectorAll(selector);

        inputElements.forEach(element => {
            element.addEventListener('input', () => {
                const value = element.value.trim();
                const parentDiv = element.closest('div');
                const errorElement = parentDiv.querySelector('.error');

                if (errorElement) {
                    value !== '' ? errorElement.remove() : parentDiv.append(errorElement);
                }
            });
            ids.push('#' + element.id);
        })
    }

    for (const key in ids) {

        const $element = $(ids[key]);

        if ($element.val().length == '') {
            if ($element.siblings('.error').length == 0) {
                $element.parent().append(`<label class="error">${requiredText}</label>`);
            }
            status = false;
        } else if ($element.hasClass('validate-date')) {
            const isValidDateResult = isValidDate($element.val(), ids[key]);
            if (!isValidDateResult) {
                status = false;
            }

            if (isValidDateResult) {
                if ($element.hasClass('date-compare')) {
                    const isStart = $element.data('start') !== undefined;
                    const isEnd = $element.data('end') !== undefined;

                    if (isStart) {
                        const endDate = $element.closest('.row').find('input[data-end]').val();
                        const startDate = $element.val();
                        const dateCompareResult = dateCompare(startDate, endDate, ids[key]);

                        if (!dateCompareResult) {
                            status = false;
                        }
                    }
                }
            }

        } else if ($element.val().length == '') {
            status = false;
        }
    }
    return status;
}

/*
* date format check according to admin preference setting.
*
* @param date dateString
* @param string datepickerElement (id of the date element)
* 
* @return boolean
*/

function isValidDate(dateString, datepickerElement) {
    const dateFormats = [
        { regex: /^\d{4}[-.\/]\d{2}[-.\/]\d{2}$/, format: 'yyyy-mm-dd' },  // 2024-10-01, 2024.10.01, 2024/10/01
        { regex: /^\d{2}[-.\/]\d{2}[-.\/]\d{4}$/, format: 'dd-mm-yyyy' },  // 01-10-2024, 01.10.2024, 01/10/2024
        { regex: /^\d{2}[-.\/]\d{2}[-.\/]\d{2}$/, format: 'mm-dd-yy' },    // 10-01-24, 10.01.24, 10/01/24
        { regex: /^\d{2}[-.\/][A-Za-z]{3}[-.\/]\d{4}$/, format: 'dd-MMM-yyyy' }, // 01-Oct-2024, 01.Oct.2024
        { regex: /^\d{4}[-.\/][A-Za-z]{3}[-.\/]\d{2}$/, format: 'yyyy-MMM-dd' }, // 2024-Oct-01
        { regex: /^\d{4}[-.\/][A-Za-z]{3}[-.\/]\d{4}$/, format: 'yyyy-MMM-yyyy' }, // 2024-Oct-2024
        { regex: /^\d{2}[-.\/][A-Za-z]{3}[-.\/]\d{2}$/, format: 'dd-MMM-yy' }, // 01-Oct-24
        { regex: /^\d{2}[-.\/][A-Za-z]{3}[-.\/]\d{2}$/, format: 'MM-MMM-yy' }, // 10-Oct-24
    ];

    // Find the matching date format
    let matchedFormat = null;
    for (let format of dateFormats) {
        if (format.regex.test(dateString)) {
            matchedFormat = format.format;
            break;
        }
    }

    if (!matchedFormat) {
        showError(datepickerElement, invalidDateMessage);
        return false;
    }

    // Clear any previous error if the date is valid
    clearError(datepickerElement);
    return true;
}

/*
* compare between two date
*
* @param date startDate
* @param date endDate
* @param string datepickerElement (id of the date element)
*
* @return boolean
*/

function dateCompare(startDate, endDate, datepickerElement) {
    // Get date format from global variable or use default
    const format = typeof dateFormat !== 'undefined' ? dateFormat : 'dd-mm-yyyy';
    const start = parseDate(startDate, format);
    const end = parseDate(endDate, format);

    if (isNaN(start) || isNaN(end)) {
        showError(datepickerElement, invalidDateMessage);
        return false;
    }

    let sTimeUTC = start.getTime();
    let eTimeUTC = end.getTime();

    if (sTimeUTC > eTimeUTC) {
        showError(datepickerElement, dateCompareMessage);
        return false;
    }

    if (sTimeUTC === eTimeUTC) {
        showError(datepickerElement, invalidEndDateMessage);
        return false;
    }

    clearError(datepickerElement);
    return true;
}

// Function to show the error
function showError(element, message) {
    const parentDiv = $(element).closest('div');
    let errorElement = parentDiv.find('.error');

    if (!errorElement.length) {
        parentDiv.append(`<label class="error">${message}</label>`);
    } else {
        errorElement.text(message);
    }
}

// Function to clear the error
function clearError(element) {
    const parentDiv = $(element).closest('div');
    parentDiv.find('.error').remove(); // Remove the error message if present
}

function parseDate(dateString, dateFormatParam) {
    if (!dateString) {
        return NaN;
    }

    // Use provided format parameter, or try to get from global scope, or use default
    let dateFormat = dateFormatParam;
    if (!dateFormat) {
        // Try to access global dateFormat variable (defined in blade templates)
        // Access it via window object or check if it exists in global scope
        if (typeof window !== 'undefined' && window.dateFormat) {
            dateFormat = window.dateFormat;
        } else {
            // Fallback: try to get from global scope (if not in browser environment)
            // or use default format
            dateFormat = 'dd-mm-yyyy';
        }
    }

    // Extract separator from dateFormat (could be -, /, or .)
    const separatorMatch = dateFormat.match(/[-.\/]/);
    const separator = separatorMatch ? separatorMatch[0] : '-';

    // Normalize dateFormat to lowercase for comparison
    const normalizedFormat = dateFormat.toLowerCase();

    // Extract separator from dateString
    let dateSeparator = null;
    const separators = ['-', '/', '.'];
    for (let sep of separators) {
        if (dateString.includes(sep)) {
            dateSeparator = sep;
            break;
        }
    }

    if (!dateSeparator) {
        // Try parsing directly using JavaScript's Date object
        const parsedDate = new Date(dateString);
        if (!isNaN(parsedDate)) {
            return parsedDate;
        }
        return NaN;
    }

    const dateParts = dateString.split(dateSeparator);

    if (dateParts.length !== 3) {
        return NaN;
    }

    let day, month, year;

    // Handle month abbreviation formats (dd-M-yyyy or yyyy-M-dd)
    // Check if format contains capital 'M' (month abbreviation) or if any date part is non-numeric
    const hasMonthAbbreviation = dateFormat.includes('M') || dateParts.some(part => isNaN(parseInt(part, 10)) && part.length > 0);
    if (hasMonthAbbreviation) {
        // Check if any part contains month abbreviation (non-numeric)
        const monthAbbrIndex = dateParts.findIndex(part => isNaN(parseInt(part, 10)) && part.length > 0);
        if (monthAbbrIndex !== -1) {
            [year, month, day] = handleMonthAbbreviation(dateParts, normalizedFormat);
            return new Date(year, month, day);
        }
    }

    // Handle numeric formats based on dateFormat preference
    if (normalizedFormat.startsWith('yyyy')) {
        // Format: yyyy-mm-dd, yyyy/mm/dd, yyyy.mm.dd
        year = parseInt(dateParts[0], 10);
        month = parseInt(dateParts[1], 10) - 1; // JavaScript months are 0-indexed
        day = parseInt(dateParts[2], 10);
    } else if (normalizedFormat.startsWith('dd')) {
        // Format: dd-mm-yyyy, dd/mm/yyyy, dd.mm.yyyy
        day = parseInt(dateParts[0], 10);
        month = parseInt(dateParts[1], 10) - 1; // JavaScript months are 0-indexed
        year = parseInt(dateParts[2], 10);
    } else if (normalizedFormat.startsWith('mm')) {
        // Format: mm-dd-yyyy, mm/dd/yyyy, mm.dd.yyyy
        month = parseInt(dateParts[0], 10) - 1; // JavaScript months are 0-indexed
        day = parseInt(dateParts[1], 10);
        year = parseInt(dateParts[2], 10);
    } else {
        // Fallback: try to parse as-is
        const parsedDate = new Date(dateString);
        if (!isNaN(parsedDate)) {
            return parsedDate;
        }
        return NaN;
    }

    // Validate parsed values
    if (isNaN(year) || isNaN(month) || isNaN(day)) {
        return NaN;
    }

    // Validate month range (0-11)
    if (month < 0 || month > 11) {
        return NaN;
    }

    // Validate day range (1-31, will be validated by Date constructor)
    if (day < 1 || day > 31) {
        return NaN;
    }

    const date = new Date(year, month, day);

    // Verify the date is valid (handles invalid dates like Feb 30)
    if (date.getFullYear() !== year || date.getMonth() !== month || date.getDate() !== day) {
        return NaN;
    }

    return date;
}

function getMonthFromAbbreviation(monthString) {
    const months = {
        'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'May': 4, 'Jun': 5,
        'Jul': 6, 'Aug': 7, 'Sep': 8, 'Oct': 9, 'Nov': 10, 'Dec': 11
    };
    return months[monthString.substring(0, 3)];
}

function handleMonthAbbreviation(dateParts, dateFormat) {
    const normalizedFormat = dateFormat ? dateFormat.toLowerCase() : '';
    const monthAbbrIndex = dateParts.findIndex(part => isNaN(part));

    if (monthAbbrIndex === -1) {
        return [NaN, NaN, NaN];
    }

    if (normalizedFormat.startsWith('yyyy')) {
        // Format: yyyy-M-dd
        return [
            parseInt(dateParts[0], 10),
            getMonthFromAbbreviation(dateParts[1]),
            parseInt(dateParts[2], 10)
        ];
    } else {
        // Format: dd-M-yyyy
        return [
            parseInt(dateParts[2], 10),
            getMonthFromAbbreviation(dateParts[1]),
            parseInt(dateParts[0], 10)
        ];
    }
}

(() => {
    // Utility: get allowed extensions from input attribute
    function getAllowedExtensions(input) {
        const attr = input.getAttribute('allowed-extension');
        return attr ? attr.split(',').map(e => e.trim().toLowerCase()) : [];
    }

    // Show error message for given input
    function showError(input, errorLabel, message) {
        errorLabel.textContent = message;
        errorLabel.style.display = 'inline';
        input.classList.add('has-error');
    }

    // Hide error message for given input
    function hideError(input, errorLabel) {
        errorLabel.textContent = '';
        errorLabel.style.display = 'none';
        input.classList.remove('has-error');
    }

    // Validate a single file input, return boolean valid/invalid
    function validateFile(input, errorLabel) {
        const file = input.files[0];
        const isRequired = input.hasAttribute('required');
        const allowedExtensions = getAllowedExtensions(input);

        const missingMsg = input.getAttribute('data-value-missing') || 'This file is required.';
        const typeMsg = input.getAttribute('data-error-message') ||
            `Invalid file type. Only ${allowedExtensions.join(', ').toUpperCase()} allowed.`;

        if (!file) {
            if (isRequired) {
                input.setCustomValidity(missingMsg);
                showError(input, errorLabel, missingMsg);
                return false;
            } else {
                input.setCustomValidity('');
                hideError(input, errorLabel);
                return true;
            }
        }

        const ext = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(ext)) {
            input.setCustomValidity(typeMsg);
            showError(input, errorLabel, typeMsg);
            return false;
        }

        input.setCustomValidity('');
        hideError(input, errorLabel);
        return true;
    }

    // Initialize validation on all file inputs with allowed-extension attribute
    function initFileValidation() {
        const fileInputs = document.querySelectorAll('input[type="file"][allowed-extension]');

        fileInputs.forEach(input => {
            // Create or find error label immediately after input
            let errorLabel = input.nextElementSibling;
            if (!errorLabel || !errorLabel.classList.contains('error')) {
                errorLabel = document.createElement('label');
                errorLabel.className = 'error';
                errorLabel.style.color = 'red';
                errorLabel.style.fontSize = '14px';
                errorLabel.style.display = 'none';
                input.insertAdjacentElement('afterend', errorLabel);
            }

            // Validate on change
            input.addEventListener('change', () => {
                validateFile(input, errorLabel);
            });

            // Validate on invalid event (before form submission)
            input.addEventListener('invalid', e => {
                e.preventDefault();
                validateFile(input, errorLabel);
            });

            // Attach form submit handler to prevent submit if invalid
            const form = input.form;
            if (form && !form.__fileValidatorAttached) {
                form.addEventListener('submit', e => {
                    let allValid = true;
                    fileInputs.forEach(fi => {
                        const el = fi.nextElementSibling;
                        if (!validateFile(fi, el)) {
                            allValid = false;
                        }
                    });
                    if (!allValid) {
                        e.preventDefault();
                    }
                });
                form.__fileValidatorAttached = true; // prevent multiple handlers on same form
            }
        });
    }

    // Wait for DOM content loaded before initializing
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFileValidation);
    } else {
        initFileValidation();
    }
})();
