$(function () {

    $.blockUI.defaults.message = '<h1>Please wait...</h1>';
    $.blockUI.defaults.overlayCSS = {
        backgroundColor: '#b8babc',
        opacity: 0.5,
        cursor: 'wait'
    };

    $('.datepicker').datepicker(
        {
            dateFormat: "dd-mm-yy"
        }
    );

    $('select').on('change', function (e) {
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;

        if (valueSelected == 'period') {
            $("#datepickers").removeClass("hidden");

        } else {
            $("#datepickers").addClass("hidden");
        }
    });

    // check if periodSelector has value 'period', if so , show datepickers
    var chosenPeriod = $("#periodSelector").val();
    if (chosenPeriod == 'period') {
        $("#datepickers").removeClass("hidden");
    } else {
        $("#datepickers").addClass("hidden");
    }

    $('.entryDetail').on('click', function () {
        var entryId = $(this).closest('td').attr('id');
        $("." + entryId).toggleClass('hidden');
        $(this).toggleClass('hidden');
        $(this).closest('td').find('.entryDetailHide').toggleClass('hidden');
    });

    $('.entryDetailHide').on('click', function () {
        var entryId = $(this).closest('td').attr('id');
        $("." + entryId).toggleClass('hidden');
        $(this).toggleClass('hidden');
        $(this).closest('td').find('.entryDetail').toggleClass('hidden');
    });

    $("#report").submit(function (event) {
        $('.results').empty();
        $.blockUI();
    });

});