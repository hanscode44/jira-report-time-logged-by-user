$(function() {

    $('.datepicker').datepicker(
        {
            dateFormat: "dd-mm-yy"
        }
    );

    $('select').on('change', function (e) {
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;

        if(valueSelected == 'period'){
            $("#datepickers").removeClass("hidden");

        } else {
            $("#datepickers").addClass("hidden");
        }
    });

    // check if periodSelector has value 'period', if so , show datepickers
    var chosenPeriod = $( "#periodSelector" ).val();
    if(chosenPeriod == 'period'){
        $("#datepickers").removeClass("hidden");
    } else {
        $("#datepickers").addClass("hidden");
    }

    $('.entryDetail').on('click', function() {
        var entryId = $(this).attr('id');
        $("."+entryId).removeClass('hidden');
    })

});