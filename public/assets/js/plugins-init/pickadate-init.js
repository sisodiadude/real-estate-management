(function ($) {
    "use strict"
    $('.pickdate-picker').each(function () {
        var $this = $(this);
        $this.pickadate({
            format: 'yyyy-mm-dd',
            formatSubmit: 'yyyy-mm-dd',  // Ensure correct format for submission
            hiddenName: true
        });
    });

})(jQuery);
