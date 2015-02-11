
$(function () {
    $("[data-toggle='tooltip']").tooltip();
    $("[data-toggle='colorpicker']").colorpicker();

    bootbox.setDefaults({
        locale: $('html').attr('lang')
    });

    /* bootbox alert before form submit */
    $("[data-submit-confirm-text]").click(function(e){
        var $el = $(this);
        e.preventDefault();
        var confirmText = $el.attr('data-submit-confirm-text');
        bootbox.confirm(confirmText, function(result) {
            if (result) {
                $el.closest('form').submit();
            }
        });
    });
});