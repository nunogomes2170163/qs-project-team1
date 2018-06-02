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

    function updateList() {
        $.ajax({
            data: filterState,
            url: 'filters.php',
            method: 'POST',
            success: function(contactsString) {
                window.location.reload();
            },
            fail: function(error) {
                alert("An error occured fetching the filtered data");
            },
        });
    }
});