$(function() {
    var loadingImage = $('.loadingImage');
    var initialNewJS = $('.jsBox:first').text();
    $('.entryCheckbox').hide();
    loadingImage.hide();

    $('.jsBox, .ipBox').on('input', function(){
        $(this).siblings('.entryCheckbox').prop('checked',true);
    });

    $('.save-button').click(function() {
        loadingImage[0].src = '../inc/images/loading.gif';
        loadingImage.show();
        var newEntryDiv = $('.newEntry');
        var existingEntries = $('.entry');
        var totalEntriesToSave = $('.entryCheckbox:checked').length;
        var entriesCompleted = 0;

        if(newEntryDiv.children('.entryCheckbox:checked').length > 0) { //New entry is checked!
            var ip = newEntryDiv.children('.ipBox').val();
            var js = newEntryDiv.children('.jsBox').val();
            $.ajax({
                url: 'blocker.php',
                data: {
                    newIP: ip,
                    newJS: js
                },
                type: 'POST'
            }).done(function(r){
                if(r)  //went well
                    alert(r);
                entriesCompleted++;
                if(entriesCompleted === totalEntriesToSave)
                    loadingImage[0].src = '../inc/images/checkmark.png';

                existingEntries.prepend()
            });
        }
        existingEntries.each(function() {
            if($(this).children('.entryCheckbox:checked').length > 0) { //This entry has been edited!
                var thisEntry = $(this);
                var entryID = thisEntry.data('id');
                var ip = $(this).children('.ipBox').val();
                var js = $(this).children('.jsBox').val();
                $.ajax({
                    url: 'blocker.php',
                    data: {
                        id: entryID,
                        ip: ip,
                        js: js
                    },
                    type: 'POST'
                }).done(function(r){
                    if(r)  //went well
                        alert(r);
                    entriesCompleted++;
                    if(entriesCompleted === totalEntriesToSave)
                        loadingImage[0].src = '../inc/images/checkmark.png';
                    thisEntry.children('.entryCheckbox').prop('checked',false);
                });
            }
        });
        if(entriesCompleted === totalEntriesToSave)
            loadingImage[0].src = '../inc/images/checkmark.png';
    });

    $('.remove-button').click(function(){
        var parentDiv = $(this).parent();
        var entryID = parentDiv.data("id");
        var string = "DELETE ME";
        $.ajax({
            url: 'blocker.php',
            data: {
                id: entryID,
                delete: string
            },
            type: 'POST'
        }).done(function(r){
                parentDiv.remove();
        });
    });
});