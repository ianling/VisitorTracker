$(document).ready(function(){
    $('.exclusionsCheckbox').hide();
    $('.loadingImage').hide();
    $('.select-code-button').click(function() {
        $(this).siblings('.code').selectText();
    });

    $('.site-name').editable(function(value, settings) {
        var siteid = $(this).data('site-id');
        $.ajax({
            url: 'settings.php',
            data: {
                siteID: siteid,
                siteName: value
            },
            type: 'POST'
        });
        return(value);
    }, {
        type : 'text',
        onblur : 'submit',
        style : 'display: inline',
        tooltip : 'Click to rename'
    });

    $('.exclusions-box').on('input',function() {
       $(this).siblings('.exclusionsCheckbox').prop('checked',true);
    });

    $('.save-button').click(function() {
        var exclusionDivs = $('.exclusions');
        exclusionDivs.each(function() {
            if($(this).children('.exclusionsCheckbox:checked').length > 0) {
                var siteid = $(this).data('site-id');
                var iplist = $(this).children('.exclusions-box').val();
                $.ajax({
                   url: 'settings.php',
                    data: {
                        siteID: siteid,
                        ipList: iplist
                    },
                    type: 'POST'
                }).done(function(r){
                        $('.loadingImage').show();
                    });
            }
        });
    });
});
jQuery.fn.selectText = function(){
    var doc = document
        , element = this[0]
        , range, selection;
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();
        range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
};