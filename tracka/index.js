$(function() {
    //**MAIN INDEX**
    var ref = "Referrer: <a href='"+document.referrer+"'>"+document.referrer+"</a>";
    if (ref.indexOf("'></a>") > -1)
        ref = "Referrer: N/A";

    $('#referrer').html(ref);
    $('#res').html("Resolution: "+screen.width+"x"+screen.height);

    //**settings.php**
    $('.select-code-button').click(function() {
        $(this).siblings('.code').selectText();
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