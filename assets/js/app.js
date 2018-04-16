$(document).ready(function() {
    $('#contacts').DataTable({
        'columnDefs': [
            {
                targets: [ 0, 1, 2 ],
                className: 'mdl-data-table__cell--non-numeric'
            },
            {
                'sortable': false,
                'targets': [-1]
            },
            {
                'searchable': false,
                'targets': [-1]
            }
        ]
    });
});