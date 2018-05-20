$(document).ready(function() {
    let dataTable = null;

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

    function refreshDataTable(contacts) {
        if (dataTable) dataTable.destroy();
        $('#contacts tbody tr').remove();
        Object.keys(contacts).forEach((key) => {
            const contact = contacts[key];
            $('#contacts tbody').append(`<tr><td>${contact.Guid}</td><td>${contact.GivenName} ${contact.Surname}</td><td><a href=get_contact.php?guid=${contact.Guid}>Open</a></td></tr>`);
        });
        initializeDataTable();
    }

    function initializeDataTable() {
        dataTable = $('#contacts').DataTable({
            order: [[ 1, "asc" ]],
            'columnDefs': [
                {
                    targets: [ 0, 1, 2 ],
                    className: 'mdl-data-table__cell--non-numeric'
                },
                {
                    'sortable': false,
                    'targets': [0, 2]
                },
                {
                    'searchable': false,
                    'targets': [0, 2]
                }
            ]
        });
    }

    $('.filter-chooser').change(function() {
        const isOn = $(this).is(':checked');
        setState(STATES[$(this).attr('id')], isOn);
        updateList();
    });

    initializeDataTable();
});