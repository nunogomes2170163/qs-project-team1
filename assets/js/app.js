$(document).ready(function() {
    $('#contacts').DataTable({
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
});