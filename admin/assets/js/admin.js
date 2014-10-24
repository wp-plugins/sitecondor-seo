(function ($) {
	"use strict";

  // tooltips
  $(function() {
    $('.sc-table').tooltip();

    $('.sc-table th a, .sc-table a.inline-tooltip').click(function() {
      return false;
    });
  });

  // LEGEND: https://github.com/bebraw/Chart.js.legend
  function legend(parent, data) {
    parent.className = 'legend';
    var datas = data.hasOwnProperty('datasets') ? data.datasets : data;

    // remove possible children of the parent
    while(parent.hasChildNodes()) {
      parent.removeChild(parent.lastChild);
    }

    datas.forEach(function(d) {
      var title = document.createElement('span');
      title.className = 'title';
      // title.style.borderColor = d.hasOwnProperty('strokeColor') ? d.strokeColor : d.color;
      // title.style.borderStyle = 'solid';
      title.style.color = d.hasOwnProperty('strokeColor') ? d.strokeColor : d.color;
      title.style.backgroundColor = d.hasOwnProperty('fillColor') ? d.fillColor : d.color;
      parent.appendChild(title);

      var text = document.createTextNode(d.label);
      title.appendChild(text);
    });
  }

  /* CHART.JS */
	$.chartJS = function(labels, datasets) {

    var options = {
      scaleShowGridLines: true,
      bezierCurve: false,
      responsive: true,
      scaleShowLabels: true
    };

    var colorSettingsArr = [
      // 'Resources'
      {
        fillColor: "rgba(64, 127, 0, 0.2)",
        strokeColor: "rgba(64, 127, 0, 1)",
        pointColor: "rgba(64, 127, 0, 1)",
        pointHighlightStroke: "rgba(64, 127, 0, 1)"
      },
        //  'Pages'
      {
        fillColor: "rgba(102, 204, 0, 0.2)",
        strokeColor: "rgba(102, 204, 0, 1)",
        pointColor: "rgba(102, 204, 0, 1)",
        pointHighlightStroke: "rgba(102, 204, 0, 1)"
      },
      //  '404s'
      {
        fillColor: "rgba(204, 20, 20, 0.2)",
        strokeColor: "rgba(204, 20, 20, 1)",
        pointColor: "rgba(204, 20, 20, 1)",
        pointHighlightStroke: "rgba(204, 20, 20, 1)"
      },         
      //  'Redirects'
      {
        fillColor: "rgba(255, 127, 70, 0.2)",
        strokeColor: "rgba(255, 127, 70, 1)",
        pointColor: "rgba(255, 127, 70, 1)",
        pointHighlightStroke: "rgba(255, 127, 70, 1)"
      },
      //  'Other Errors'
      {
        fillColor: "rgba(255, 51, 153, 0.2)",
        strokeColor: "rgba(255, 51, 153, 1)",
        pointColor: "rgba(255, 51, 153, 1)",
        pointHighlightStroke: "rgba(255, 51, 153, 1)"                      
      },                     
      //  'Missing Titles'
      {
        fillColor: "rgba(224, 224, 0, 0.2)",
        strokeColor: "rgba(224, 224, 0, 1)",
        pointColor: "rgba(224, 224, 0, 1)",
        pointHighlightStroke: "rgba(224, 224, 0, 1)"                   
      },          
      //  'Missing H1s'
      {
        fillColor: "rgba(204, 51, 204, 0.2)",
        strokeColor: "rgba(204, 51, 204, 1)",
        pointColor: "rgba(204, 51, 205, 1)",
        pointHighlightStroke: "rgba(204, 51, 204, 1)"                                        
      },         
      //  'Missing Meta Descriptions'
      {
        fillColor: "rgba(204, 80, 0, 0.2)",
        strokeColor: "rgba(204, 80, 0, 1)",
        pointColor: "rgba(204, 80, 0, 1)",
        pointHighlightStroke: "rgba(204, 80, 0, 1)"                
      },                          
      //  'Missing Image Alternate Text'
      {
        fillColor: "rgba(204, 102, 102, 0.2)",
        strokeColor: "rgba(204, 102, 102, 1)",
        pointColor: "rgba(204, 102, 102, 1)",
        pointHighlightStroke: "rgba(204, 102, 102, 1)"   
      }                                      
    ];

    var data = {
      labels: labels,
      datasets: []
    };

    for(var i = 0; i < datasets.length; i++) {
      var colorSettings = colorSettingsArr[i % colorSettingsArr.length];
      data.datasets.push({
        label: datasets[i].label,
        data: datasets[i].data,
        fillColor: colorSettings.fillColor,
        strokeColor: colorSettings.strokeColor,
        pointColor: colorSettings.pointColor,
        pointHighlightStroke: colorSettings.pointHighlightStroke,
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff"                
      });
    }

    var ctx = document.getElementById('my-chart').getContext('2d');
    var myLineChart = new Chart(ctx).Line(data, options);

    legend(document.getElementById('legend'), data);
	}
}(jQuery));