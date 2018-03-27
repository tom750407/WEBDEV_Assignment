/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    google.charts.load('current', {packages: ['map'], "mapsApiKey": "AIzaSyCowE35iCkaDqWdyz5_7-gjaX8SlU02H2w"});
    google.charts.setOnLoadCallback(googleChart);

    displayTime();
});



function displayTime() {
    let slider = document.getElementById("times");
    let output = document.getElementById("time");
    let minutes = (slider.value - Math.floor(slider.value));

    minutes = minutes * 60;
    if (minutes === 0) {
        minutes = "00";
    }
    //display initial output
    output.innerHTML = (slider.value < 10 ? "0" : "") + Math.floor(slider.value) + ":" + minutes + ":00";
    googleChart();
}

function googleChart() {
    function dateFormat(date) {
        //formate date to DD/MM/YYYY
        let day = date.getDate();
        day = (day < 10 ? "0" : "") + day;
        let month = date.getMonth() + 1;
        month = (month < 10 ? "0" : "") + month;
        let dateToString = day + "/" + month + "/" + date.getFullYear();
        return dateToString;
    }

    function datetry(date) {
        var parts = date.split('-');
        var mydate = new Date(parts[0], parts[1] - 1, parts[2]);
        return mydate;
    }

    let url = "Map_Chart.php";

    //params
    let date = $('#dates').val();
    let dateCurrent = datetry(date);
    let dateCurrentString = dateFormat(dateCurrent);
    dateCurrent.setDate(dateCurrent.getDate() + 1);
//    let dateNextString = dateFormat(dateCurrent);

//    console.log("current " + dateCurrentString);
//    console.log("next " + dateNextString);

    let time = $('#time').html();
//    console.log(time);

    let params = "?date=" + dateCurrentString + "&time=" + time;
    let json = $.ajax({
        url: url + params,
        dataType: "json",
        async: false
    }).responseText;

    //google maps
//    console.log(json);
    var data = new google.visualization.DataTable(json);
//    var dataView = new google.visualization.DataView(data);
//    dataView.setColumns([
//        0, 1,
//        {
//            calc: function (data, row) {
//                let date = document.getElementById("dates");
//                let time = document.getElementById("times");
//                return date + " " + time;
//
//            },
//            type: 'string',
//            role: 'tooltip'
//        }
//    ]);

    var options = {
        title: 'Air Quality:',
        showTooltip: true,
        showInfoWindow: true
    };

    let map = new google.visualization.Map(document.getElementById("chart"));

    map.draw(data, options);

}

