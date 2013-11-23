$(document).ready(function() {
    var ipLabelSettings = $('.ipLabelSettings');
    ipLabelSettings.hide();
    $('.display-label-options').click(function(e) {
        ipLabelSettings.hide();
        $(this).siblings('.ipLabelSettings').show();
    });
    $('body').click(function(e){
        if ($(e.target).is('#container, .track, .greeting, .navigation, .totals, .siteSelection, .divider'))
            ipLabelSettings.hide();
    });

    $('.save-button').click(function() {
        var label = $(this).siblings('.iplabelbox').val();
        var bgcolor = $(this).siblings('#bgcolor').val();
        var textcolor = $(this).siblings('#textcolor').val();
        var trackEntry = $(this).parent().parent();
        var ipaddress = trackEntry.data('ip-address');
        var trackEntries = $('.track');

        $.ajax({
            url: 'settings.php',
            data: {
                label: label,
                bgcolor: bgcolor,
                textcolor: textcolor,
                ipaddress: ipaddress
            },
            type: 'POST'
        }).done(function(r){
                trackEntries.each(function() {
                    if($(this).data('ip-address') == ipaddress) {
                        $(this).css("background-color","#"+bgcolor);
                        $(this).css("color","#"+textcolor);
                        $(this).children('.display-label-options').html("["+label+"]")
                    }
                });
            });

        ipLabelSettings.hide();
    });

    $('.reset-button').click(function(){
        var trackEntry = $(this).parent().parent();
        var trackEntries = $('.track');
        var ipaddress = trackEntry.data('ip-address');
        var command = "RESET";
        $.ajax({
            url: 'settings.php',
            data: {
                ipaddress: ipaddress,
                command: command
            },
            type: 'POST'
        }).done(function(r){
                trackEntries.each(function() {
                    if($(this).data('ip-address') == ipaddress) {
                        $(this).css("background-color","#D5D5D5");
                        $(this).css("color","#000000");
                        $(this).children('.display-label-options').html("[Δ]")
                    }
                });
            });

        ipLabelSettings.hide();
    });
});

function updateBGColor(color, colorPicker) {
    var trackEntry = colorPicker.parents('.track');
    trackEntry.css("background-color",color);
}
function updateTextColor(color, colorPicker) {
    colorPicker.parents('.track').css("color",color);
}