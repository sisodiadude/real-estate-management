(function ($) {
    "use strict"

    // Clock pickers
    var input = $('#single-input').clockpicker({
        placement: 'bottom',
        align: 'left',
        autoclose: true,
        'default': 'now'
    });

    $('.clockpicker').clockpicker({
        donetext: 'Done',
        autoclose: true,
        afterDone: function () {
            // let selectedTime = $(this).find('input').val();
            // console.log("Time selected:", selectedTime);
        }
    }).find('input').change(function () {
        if (!this.value.trim()) {
            this.value = "19:00"; // Set default value if empty
        }
    });

    $('#check-minutes').on('click', function (e) {
        // Have to stop propagation here
        e.stopPropagation();
        input.clockpicker('show').clockpicker('toggleView', 'minutes');
    });

})(jQuery)
