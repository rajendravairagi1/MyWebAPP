import flatpickr from 'flatpickr';

/**
 * Every `<input type="date">` in the app, turned into the same
 * flatpickr calendar everywhere — a native date input renders (and
 * lets you type) in whatever format the visitor's browser/OS locale
 * happens to be set to, which is dd/mm for some visitors and mm/dd for
 * others (typing 13 into what you assumed was the month field silently
 * becomes the day instead, in reverse). That's fine for a purely local
 * audience but not for a product sold internationally, where different
 * customers' devices disagree on it. flatpickr shows the same "day
 * first" (d-m-Y) calendar and text field to every visitor no matter
 * their locale, so it reads the same for everyone — the field's actual
 * value submitted to the server stays plain ISO (Y-m-d), so nothing
 * server-side needs to change.
 */
export function initDatePickers() {
    document.querySelectorAll('input[type="date"]:not([data-no-datepicker])').forEach((input) => {
        if (input.dataset.datepickerBound === '1') {
            return;
        }
        input.dataset.datepickerBound = '1';

        const initialValue = input.value;
        input.type = 'text';

        flatpickr(input, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd-m-Y',
            allowInput: true,
            defaultDate: initialValue || undefined,
        });
    });
}
