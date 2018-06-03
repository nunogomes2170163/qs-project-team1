$(document).ready(function() {
    function updateList() {
        $.ajax({
            data: filterState,
            url: 'filters.php',
            method: 'POST',
            fail: function(error) {
                alert("An error occured fetching the filtered data");
            },
        });
    }

    $('.filter-chooser').change(function() {
        updateList();
    });
});