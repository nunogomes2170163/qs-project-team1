$(document).ready(function() {
    const STATES = {
        LINKED_IN: 'linkedin',
        FACEBOOK: 'facebook',
    };

    const filterState = {
        facebook: {
            on: $('#FACEBOOK').is(':checked'),
            source: 'facebook',
        },
        linkedin: {
            on: $('#LINKED_IN').is(':checked'),
            source: 'linkedin',
        },
    };

    function setState(field, value) {
        filterState[field].on = value;
    }

    $('.filter-chooser').change(function() {
        const isOn = $(this).is(':checked');
        setState(STATES[$(this).attr('id')], isOn);
    });
});