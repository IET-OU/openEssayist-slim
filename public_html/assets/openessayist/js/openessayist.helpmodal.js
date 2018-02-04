/*!
  OpenEssayist JS: help.modal | © The Open University (IET).
*/

// From: 'site.twig'

// {% if helpontask is defined %}
// <script id="helpmodal">

window.$.pnotify.defaults.history = false;
window.$.pnotify.defaults.openessayist = {
  dir1: 'up', dir2: 'left', firstpos1: 50, firspos2: 10, spacing: 10
};

window.jQuery(function ($) {
// $(document).ready(function () {
  var W = window;
  var $helpConf = $('#helpmodal-js-config');
  var helpConf = $helpConf.data();

  console.warn('helpModal:', $helpConf, helpConf, $.pnotify.defaults);

  $('#reflect-on-task').click(function (e) {
    W.openEssayist.showSRTool();
  });

  $('#help-on-task').click(function (e) {
    var $modal = $('#help-modal');

    // create the backdrop and wait for next modal to be triggered.
    $('body').modalmanager('loading');

    $modal.load(helpConf.help_url, '', function () { // Was: "{{ urlFor('ajax.help.topic',{'topic':helpontask}) }}", "",
      $modal.modal();

      $('body').modalmanager('loading');
    });
  });

  var stackBottomRight = $.pnotify.defaults.openessayist; // {"push": "top","dir1": "up", "dir2": "left", "firstpos1": 50, "firstpos2": 10, "spacing1":10};

  $('#hint-on-task').click(function (e) {
    $('body').modalmanager('loading');

    $.pnotify_remove_all();

    $.get(helpConf.hint_url, // Was: "{{ urlFor('ajax.help.hint',{'topic':helpontask}) }}",
      function (data) {
        $('body').modalmanager('loading');
        data = $(data);

        var tt = data.find('ul.icons-ul').children().toArray();
        var time = 100;
        var l = tt.length;

        var options = {
          title: 'Hint',
          text: '<p role="alert">Sorry, no hints for this page.</p>',
          addclass: 'stack-bottomright',
          stack: stackBottomRight, // Was: stack_bottomright,
          hide: false,
          sticker: false,
          closer_hover: false,
          sticker_hover: false,
          animate_speed: 100,
          append_to: $('#ou-content'),
          animation: {
            effect_in: 'slide',
            effect_out: 'slide'
          },
          type: 'info',
          icon: 'icon-large icon-comments ',

          before_open: function (pnotify) {
            // $(pnotify.text_container).attr('aria-live', 'polite');
            // $(pnotify.text_container).attr('aria-relevant', 'all');

            $(pnotify.text_container).attr('role', 'alert');

            $(pnotify.closer).attr({
              role: 'button', tabindex: 0, title: 'Close this hint'
            }).addClass('btn btn-mini');
          },

          after_open: function (pnotify) {
            $(pnotify.closer).focus();
          },

          after_close: function (pnotify) {
            // clear the pnotify data (not sure why not done in lib).
            var pdata = $(window).data('pnotify');
            var index = pdata.indexOf(pnotify);

            pdata.splice(index, 1);
            // focus on last available notification.
            var lnotify = $(pdata).last()[ 0 ];

            if (lnotify) { $(lnotify.closer).focus(); }
          }
        };

        if (l === 0) {
          tt.push(null);
          l = tt.length;
        }

        tt.reverse();

        $.each(tt, function (key, value) {
          console.log(key + ' ' + l);

          W.setTimeout(function () {
            var locals = {};

            if (value != null) {
              locals.text = $(value).text();
            } else {
              locals.type = 'error';
            }

            var opts = $.extend({}, options, locals);

            $.pnotify(opts);
          }, time);

          time += 900;
        });

        W.setTimeout(function () {
          // update the text of the alert.
          $('.ui-pnotify-text').each(function () {
            var g = $(this).html();

            $(this).html(g);
          });
        }, 500);
      });
  });
});

// </script>
// {% endif %}
