$(document).ready(function() {
    $('.car-row').click(function() {
        var carId = $(this).data('car-id');
        $('#details-' + carId).toggle();
    });

    $('.rent-button').click(function(event) {
        event.stopPropagation(); // Prevents the click event from propagating to the row
    });
});


$(document).ready(function() {
    $('.remove-car').click(function(event) {
        if (!confirm("Are you sure you want to remove this car from the list of available cars?")) {
            event.preventDefault();
        }
    });
});

