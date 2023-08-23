$(document).ready(function () {
    // Listen for input events on all input fields
    $('input[type="text"]').on("input", function () {
        var inputValue = $(this).val();
        var uppercaseValue = inputValue.toUpperCase();
        $(this).val(uppercaseValue);
    });
});
