function formatEmail($el)
{
    let v = $el.val() || '';

    v = v.replace(/\s+/g, '');

    v = v.toLowerCase();

    v = v.replace(/[^a-z0-9@._-]/g, '');

    const parts = v.split('@');
    if (parts.length > 2) {
        v = parts[0] + '@' + parts.slice(1).join('');
    }

    v = v.replace(/^\./, '');

    v = v.replace(/\.{2,}/g, '.');

    $el.val(v);
}
$(document).on('input blur', '.email-format', function () {
    formatEmail($(this));
});
function enforceTZ($el) {
    let v = ($el.val() || '');

    v = v.replace(/\s+/g, '').replace(/[^\d]/g, '');
    if (v.startsWith('255')) v = v.substring(3);
    if (v.startsWith('0')) v = v.substring(1);
    if (v.startsWith('0')) v = v.replace(/^0+/, '');
    v = v.substring(0, 9);
    $el.val('+255' + v);
}
$(document).on('focus input', '.force-255', function () {
    enforceTZ($(this));
});
$(document).on('keydown', '.force-255', function (e) {
    const pos = this.selectionStart;
    if ((e.key === 'Backspace' && pos <= 4) || (e.key === 'Delete' && pos < 4)) {
        e.preventDefault();
    }
});
$(function () {
    $('.force-255').each(function () {
        enforceTZ($(this));
    });
});
$(document).on('input','.eleven-character-format', function () {
    let value = $(this).val().replace(/[^0-9]/g, '');
    if (value.length > 9)
    {
        value = value.substring(0, 9);
    }
    let formatted = value;
    if (value.length > 3 && value.length <= 6)
    {
        formatted = value.substring(0, 3) + '-' + value.substring(3);
    }
    else if (value.length > 6)
    {
        formatted = value.substring(0, 3) + '-' + value.substring(3, 6) + '-' + value.substring(6);
    }
    $(this).val(formatted);
});
$('.datepicker').datepicker({
    dateFormat: 'dd-M-yy',
    changeMonth: true,
    changeYear: true,
    yearRange: 'c-50:c+20'
});
$('.datepicker-future').datepicker({
    dateFormat: 'dd-M-yy',
    changeMonth: true,
    changeYear: true,
    yearRange: 'c:c+20',
    minDate: 0
});
$(document).on('input','.vat-format', function () {
    let v = $(this).val().toUpperCase();
    v = v.replace(/[^A-Z0-9]/g, '');
    let first = v.replace(/[^0-9]/g, '').slice(0, 2);
    let restNums = v.replace(/[^0-9]/g, '').slice(2, 8);
    let letterMatch = v.match(/[A-Z]/);
    let letter = letterMatch ? letterMatch[0] : '';
    let out = first;

    if (first.length === 2) out += '-';
    out += restNums;

    if (restNums.length === 6) out += '-';
    out += letter.slice(0, 1);

    $(this).val(out.slice(0, 11));
});
$(document).on('input','.alphanumeric-format', function () {
    let value = $(this).val();
    value = value.replace(/[^a-zA-Z0-9]/g, '');
    $(this).val(value);
});
$(document).on('keypress', '.only-numbers', function (e) {
    if (e.which < 48 || e.which > 57) e.preventDefault();
});
$(document).on('input', '.only-numbers', function () {
    this.value = this.value.replace(/\D/g, '');
});
function sanitizePercent(v)
{
    v = (v || '').replace(/\s+/g, '');

    // allow only digits and dot
    v = v.replace(/[^0-9.]/g, '');

    // allow only one dot
    const parts = v.split('.');
    if (parts.length > 2) {
        v = parts[0] + '.' + parts.slice(1).join('');
    }

    let num = parseFloat(v);

    if (!isNaN(num)) {

        // clamp > 100 to 100
        if (num > 100) {
            return '100';
        }

        // if exactly 100 → force clean "100"
        if (num === 100) {
            return '100';
        }

        // below 100 → allow max 2 decimals
        const p = v.split('.');
        if (p.length === 2) {
            p[0] = p[0].replace(/^0+(?=\d)/, '');
            p[1] = p[1].slice(0, 2);
            v = p[0] + '.' + p[1];
        } else {
            v = v.replace(/^0+(?=\d)/, '');
        }
    }

    return v;
}
$(document).on('input', '.percent-2dp', function () {
    this.value = sanitizePercent(this.value);
});
$(document).on('blur', '.percent-2dp', function () {
    if (this.value.endsWith('.'))
    {
        this.value = this.value.slice(0, -1);
    }
    if (this.value.startsWith('.'))
    {
        this.value = '0' + this.value;
    }
});
$(document).on('input', '.to-uppercase', function () {
    this.value = this.value.toUpperCase();
});
$(document).on('input', '.capitalize-words', function () {
    this.value = this.value.replace(/\b\w/g, function(char) {
        return char.toUpperCase();
    });
});
