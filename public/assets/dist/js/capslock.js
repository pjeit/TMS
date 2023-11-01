$(document).ready(function () {
    // Listen for input events on all input fields
    // $('input[type="text"]').on("input", function () {
    //     var inputValue = $(this).val();
    //     var uppercaseValue = inputValue.toUpperCase();
    //     $(this).val(uppercaseValue);
    // });
    // style="text-transform: uppercase"
    // $("textarea").on("input", function () {
    //     var inputValue = $(this).val();
    //     var uppercaseValue = inputValue.toUpperCase();
    //     $(this).val(uppercaseValue);
    // });
    $("input").focusout(function () {
        this.value = this.value.toLocaleUpperCase();
    });
    $("textarea").focusout(function () {
        this.value = this.value.toLocaleUpperCase();
    });
});
