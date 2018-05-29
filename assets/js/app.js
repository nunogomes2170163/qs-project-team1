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

    $('.js-remove-contact').click(function() {
        $(this).closest('.single-conflict').remove();
        if ($('.single-conflict').find('[name="name"]').length <= 2) {
            $('.js-remove-contact').parent().remove();
        }
    });

    $('.js-save-conflict').click(function() {
        const resolvedConflict = {};
        const selectedNameRadio = $('[type="radio"][name="name"]:checked');
        const firstName = selectedNameRadio.parent().find('span').text().split(' ')[0];
        const lastName = selectedNameRadio.parent().find('span').text().split(' ')[1];
        const photoRadio = $('[type="radio"][name="photo"]:checked').parent().find('img').attr('src');
        const birthdayRadio = $('[type="radio"][name="birthday"]:checked').parent().find('span').text();
        let emailString = '';
        $('[name="email"]:checked').each(function (index) {
            if (index < $('[name="email"]:checked').length - 1) {
                emailString += $(this).parent().find('span').text() + '|';
            } else {
                emailString += $(this).parent().find('span').text();
            }
        });
        let phoneString = '';
        $('[name="phone"]:checked').each(function (index) {
            if (index < $('[name="phone"]:checked').length - 1) {
                phoneString += $(this).parent().find('span').text() + '|';
            } else {
                phoneString += $(this).parent().find('span').text();
            }
        });
        let addressString = '';
        $('[name="address"]:checked').each(function (index) {
            if (index < $('[name="address"]:checked').length - 1) {
                addressString += $(this).parent().find('span').text() + '|';
            } else {
                addressString += $(this).parent().find('span').text();
            }
        });
        let occupationString = '';
        $('[name="occupation"]:checked').each(function (index) {
            if (index < $('[name="occupation"]:checked').length - 1) {
                occupationString += $(this).parent().find('span').text() + '|';
            } else {
                occupationString += $(this).parent().find('span').text();
            }
        });
        let companyString = '';
        $('[name="company"]:checked').each(function (index) {
            if (index < $('[name="company"]:checked').length - 1) {
                companyString += $(this).parent().find('span').text() + '|';
            } else {
                companyString += $(this).parent().find('span').text();
            }
        });
        let cityString = '';
        $('[name="city"]:checked').each(function (index) {
            if (index < $('[name="city"]:checked').length - 1) {
                cityString += $(this).parent().find('span').text() + '|';
            } else {
                cityString += $(this).parent().find('span').text();
            }
        });
        let guids = '';
        $('.single-conflict').each(function (index) {
            if (index < $('.single-conflict').find('[name="name"]').length - 1) {
                guids += $('.single-conflict').find('[name="name"]').eq(index).val() + '|';
            } else {
                guids += $('.single-conflict').find('[name="name"]').eq(index).val();
            }
        });
        resolvedConflict.Guid = guids;
        resolvedConflict.Birthday = birthdayRadio;
        resolvedConflict.City = cityString;
        resolvedConflict.Company = companyString;
        resolvedConflict.Email = emailString;
        resolvedConflict.GivenName = firstName;
        resolvedConflict.Occupation = occupationString;
        resolvedConflict.Phone = phoneString;
        resolvedConflict.PhotoUrl = photoRadio;
        resolvedConflict.StreetAddress = addressString;
        resolvedConflict.Surname = lastName;
        $.ajax({
            data: resolvedConflict,
            url: 'solve_conflict.php',
            method: 'POST',
            success: function(contactsString) {
                window.location.href = 'resolve_conflicts.php';
            },
            fail: function(error) {
                alert("An error occured fetching the filtered data");
            },
        });
    });

    initializeDataTable();
});