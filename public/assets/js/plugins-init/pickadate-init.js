(function ($) {
    "use strict";

    $('.pickdate-picker').each(function () {
        var $this = $(this);
        $this.pickadate({
            format: 'yyyy-mm-dd',
            formatSubmit: 'yyyy-mm-dd',
            hiddenName: true,
            selectYears: 50,  // Allows selecting years in a dropdown (50 years range)
            selectMonths: true  // Allows selecting months in a dropdown
        }).addClass('form-control'); // Apply Bootstrap styling
    });

})(jQuery);
