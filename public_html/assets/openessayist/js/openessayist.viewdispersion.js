/*!
  OpenEssayist JS: viewDispersion | © The Open University (IET).
*/

// From: 'templates/drafts/view.dispersion.twig'

// <script type="text/javascript">
/*
 * TO DO: DataTable internal function _fnBindAction needs to be changed to that one
 *   for better accessibility (keep focus on clicked header, intercept keydown rather than keypress)
 */

window.$.fn.DataTable._fnBindAction = function (n, oData, fn) {
  var $ = window.jQuery;

  $(n)
    .bind('click.DT', oData, function (e) {
      // NVL: DO NOT REMOVE FOCUS
      // n.blur(); // Remove focus outline for mouse users
      fn(e);
    })
    .bind('keydown.DT', oData, function (e) {
      // NVL: changed from keypress to keydown
      if (e.which === 13) {
        fn(e);
      }
    })
    .bind('selectstart.DT', function () {
      /* Take the brutal approach to cancelling text selection */
      return false;
    });
};

(function (W, $, openEssayist) {
// $(document).ready(function() {
  var chart = null;
  var chartOptions = null;

  var Highcharts = window.Highcharts;
  var colorbrewer = window.colorbrewer;

  // var $series = DISP.$series; // {{ series|json_encode|raw }}; // NDF:

  $('#data-table').dataTable({
    bPaginate: false,
    bLengthChange: false,
    bFilter: true,
    bSort: true,
    bInfo: false,
    bAutoWidth: true,
    aaSorting: [[ 1, 'asc' ]],
    aoColumnDefs: [ ]
  });

  $('#data-table tbody').removeAttr('role').removeAttr('aria-live').removeAttr('aria-relevant');

  $('.btn-setting').click(function () {
    var data = $(this).data('option-value');
    var zoomtype = chartOptions.chart.zoomType;
    if (data === zoomtype) {
      return;
    }

    chartOptions.chart.zoomType = data;
    chart = new Highcharts.Chart(chartOptions);
  });

  $('#zoom-reset').click(function () {
    chart.zoomOut();
  });

  var sections = {
    '????': '',
    '#+s:c#': 'Conclusion',
    '#+s:d_i#': 'Discussion',
    '#+s:d#': 'Discussion',
    '#+s:s#': 'Summary',
    '#+s:i#': 'Introduction',
    '#-s:t#': 'Title',
    '#+s:p#': 'Preface',
    '#-s:h#': 'Heading',
    '#-s:n#': 'Numerics',
    '#-s:q#': 'Assignment',
    '#-s:p#': 'Punctuation'
  };

  $('#resizer').resizable({
    ghost: false,
    animate: false,
    // On resize, set the chart size to that of the
    // resizer minus padding. If your chart has a lot of data or other
    // content, the redrawing might be slow. In that case, we recommend
    // that you use the 'stop' event instead of 'resize'.
    resize: function () {
      if (chart) {
        chart.setSize(this.offsetWidth - 20, this.offsetHeight - 20, false);
      }
    }
  });

  function hex2rgba (x, a) {
    var r = x && x.replace('#', '').match(/../g);
    var g = [];
    var i;

    for (i in r) { g.push(parseInt(r[ i ], 16)); }
    g.push(a);
    return 'rgba(' + g.join() + ')';
  }

  W._OE_getColor = getColor; // NDF:

  function getColor (tag) {
    var idx = $.inArray(tag, Object.keys(sections));

    idx = (idx === -1) ? 2 : idx;
    var clr = colorbrewer.Paired[ 11 ][ idx ];
    var clr2 = hex2rgba(clr, '0.2');
    return clr2;
  }

  var tt = getColor('#+s:i#');
  // var myCats = DISP.myCats; // {{ categories|json_encode|raw }}; // NDF:

  openEssayist._VD_initialize = function (DISP) {
  chartOptions = {
    credits: {
      enabled: false
    },
    legend: {
      enabled: false,
      useHTML: true,
      itemStyle: {
        color: '#000000'
      }
    },
    exporting: {
      enabled: false,
      sourceWidth: 1175,
      sourceHeight: 825
    },
    chart: {
      backgroundColor: null,
      renderTo: 'container',
      type: 'scatter',
      zoomType: 'x',
      reflow: true,
      resetZoomButton: {
        theme: {
          display: 'none'
        }
      }
    },
    title: {
      text: 'Occurrence of key words & key phrases throughout the essay',
      useHTML: true
    },
    tooltip: {
      followPointer: true,
      followTouchMove: true,
      hideDelay: 100,

      formatter: function () {
        return '<b>' + DISP.myCats[ this.point.y ] + '</b><br>' + this.series.name;
      }
    },
    xAxis: [{ // master axis
      min: 0,
      title: {
        enabled: false,
        text: 'word count'
      },
      labels: {
        enabled: false
      },
      minRange: 200,
      startOnTick: false,
      endOnTick: false,
      showLastLabel: true,
      plotBands: DISP.plotBands // NDF:
      /* plotBands: [
        {% for key,band in structure %}
          {
            from: {{ band.from }},
            to: {{ band.to }},
            color: getColor('{{ band.tag }}'),
             thickness: '5%'
          },
        {% endfor %}
      ] */
    } /* ,{ // slave axis
      linkedTo:0 ,
      minRange: 50,
      opposite: false,
        title: {
          enabled: false,
            text: 'Essay Structure'
      },
      // tickPositions: {{ ticks|json_encode|raw }}, // NDF:
      labels: {
        enabled: false,
         staggerLines: 1,
         rotation: -90,
        formatter: function() {
          var gg = DISP.formatterGg; // {{ tags|json_encode|raw }} // NDF:
          return gg[this.value];},
        },
    } */ ],
    yAxis: {
      minRange: 10,
      title: {
        enabled: false,
        text: null
      },
      min: 0,

      startOnTick: true,
      endOnTick: true,
      tickInterval: 1,
      minorTickInterval: 1,
      type: 'category',
      categories: DISP.myCats // myCats, // NDF:
    },
    plotOptions: {
      series: {
        stickyTracking: false
      },
      scatter: {
        marker: {
          symbol: 'diamond',
          radius: 5,
          states: {
            hover: {
              enabled: true,
              lineColor: 'rgb(100,100,100)'
            }
          }
        },
        states: {
          hover: {
            marker: {
              enabled: false
            }
          }
        }
      }
    },
    series: DISP.$series // NDF:
  };

  chart = new Highcharts.Chart(chartOptions);

  // $(".highcharts-container svg")
  //  .prepend("<title>xxxx</title>");

  /* $('.highcharts-legend')
    .attr('role', "navigation")
    .attr("aria-label", "Legend")
    .attr("aria-controls", "container");

  $('.highcharts-legend-item').each(function()
    {
      var cnt = $(this).find("span").html();
      //if (!cnt) return;

      $(this).attr('role', "checkbox")
          .attr("title",'Show or hide key words from '+cnt)
          .attr("tabindex", "0")
          .click(function() {
            $(this).focus();
            });
    }); */

  $.each(DISP.$series, function (index, item) {
    var that = item;

    $('#legend_bullet').append(
      $('<button/>', {
        type: 'button',
        title: 'Show or hide key words from ' + item.name,
        text: item.name,
        id: 'legend-tag' + index,
        'data-tag': item.tag,
        'class': 'btn active'
      })
        .attr('role', 'checkbox')
        .attr('aria-checked', 'true')
        .prepend($('<div/>')
          .addClass('legendcolor')
          .attr('aria-hidden', 'true')
          .css('background-color', item.color)
        ).click(function () {
          var chart = $('#container').highcharts();
          var series = chart.series[ index ];

          $(this).attr('aria-checked', !series.visible);

          if (series.visible) {
            series.hide();
          } else {
            series.show();
          }
        }) // click ()
    ); // append()
  });

};

// });
})(window, window.jQuery, window.openEssayist);

// </script>
// {% endblock %}
