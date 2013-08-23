$(function() {
    var scanResults = $('.scanResults');
    $('.scan-button').click(function() {
        scanResults.html("<b>Results</b>:<br>");
        scanResults.show();
        var ipToScan = $('#ipBox').val();
        var portList = $('#portsBox').val();
        portList = portList.replace(" ", ""); //remove spaces
        var portArray = portList.split(",");
        portArray.forEach(function(part,index,theArray){
            if(part.indexOf("-") > -1)
                theArray[index] = part.split("-");
        });
        portArray.forEach(function(part,index,theArray){
            if(part instanceof Array) {
                var rangeMin = part[0];
                var rangeMax = part[1];
                if(rangeMax-rangeMin > 5) {
                    scanResults.append("ERROR: Port range too large! Can't scan from ports "+rangeMin+" to "+rangeMax+"!");
                }
                else {
                    for(;rangeMin<=rangeMax;rangeMin++) {
                        if(rangeMin>65535 || rangeMin<1) {
                            scanResults.append("ERROR: Port "+rangeMin+" is out of bounds (1-65535).");
                            continue;
                        }
                        $.ajax({
                            url: 'scanner.php',
                            data: {
                                ipToScan: ipToScan,
                                portToScan: rangeMin
                            },
                            type: 'POST'
                        }).done(function(r){
                            if(r)  //went well
                                scanResults.append(r);
                        });
                    }
                }
            }
            else { //It's a single port, not an array
                if(part > 65535 || part < 1)
                    scanResults.append("ERROR: Port "+rangeMin+" is out of bounds (1-65535).");
                else {
                    $.ajax({
                        url: 'scanner.php',
                        data: {
                            ipToScan: ipToScan,
                            portToScan: part
                        },
                        type: 'POST'
                    }).done(function(r){
                            if(r) //went well
                                scanResults.append(r);
                        });
                }
            }

        });
    });
});