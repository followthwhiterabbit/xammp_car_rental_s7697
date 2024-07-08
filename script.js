$(document).ready(function() {
    $('.car-row').click(function() {
        var carId = $(this).data('car-id');
        $('#details-' + carId).toggle();
    });

    $('.rent-button').click(function(event) {
        event.stopPropagation(); // Prevents the click event from propagating to the row
    });
});
