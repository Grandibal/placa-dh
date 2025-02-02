$(document).ready(function() {

    $('#yearDropdown, #monthDropdown').on('change', function() {
        $(this).closest('form').submit();
    });

    $('#btn-reset').on('click', function() {
        var today = new Date();
        var year = today.getFullYear();
        var month = today.getMonth() + 1;

        $('#yearDropdown').val(year);
        $('#monthDropdown').val(month);
    });

});
