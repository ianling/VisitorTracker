$(function() {
    var ref = "Referrer: <a href='"+document.referrer+"'>"+document.referrer+"</a>";
    if (ref.indexOf("'></a>") > -1)
        ref = "Referrer: N/A";

    $('#referrer').html(ref);
    $('#res').html("Resolution: "+screen.width+"x"+screen.height);
    $('#currentPage').html("Landed: <a href='"+window.location.href+"'>"+window.location.href+"</a>");

});