/*!
  OpenEssayist JS: core | © The Open University (IET).
*/

var openEssayist = window.openEssayist || {};

/**
 *
 * @param msg
 * @returns {myClass.doSomething}
 */
openEssayist.log4j = function (action, data) {
  var $ = window.jQuery;
  var CFG = openEssayist.config;

  console.warn('log4j:', action, data);

  $.ajax({
    type: 'POST',
    method: 'post',
    url: CFG.log_url, // URL and function to call
    data: {
      'action': action,
      'data': data
    },
    // contentType : "application/json; charset=utf-8",
    // processData : true,
    dataType: 'json',
    success: function (msg, status) {
      // Set Response outcome
      console.log(msg);
      // location.reload();
    },
    error: function (xhr, msg, e) {
      // this should only fire if the ajax call did not happen or
      // there was an unhandled exception
      console.error(msg);
      // $('body').modalmanager('loading');
    }
  });
};

/** Self-reflective (SR) tool.
 */
// openEssayist.prototype = {};

window.openEssayist.showSRTool = function () {
  var $ = window.jQuery;
  var OES = window.openEssayist;

  $.pnotify.defaults.history = false;

  var optsQ1 = {
    title: 'Self-reflective tool',
    text: 'Do you think this page is helping you to revise your draft?' +
        "<p style='margin-top:6px;'>" +
        "<button class='btn btn-small' id='reflect1-yes'><i class='icon icon-ok'></i> Yes</button>" +
        "<button class='btn btn-small' id='reflect1-no'><i class='icon icon-remove'></i> No</button></p>",
    addclass: 'stack-bottomright',
    stack: $.pnotify.defaults.openessayist,
    hide: false,
    closer_hover: false,
    sticker_hover: false,
    sticker: false,
    icon: 'icon-large icon-comments',
    before_open: function (pnotify) {
      var $button = $(pnotify.closer);

      $(pnotify.text_container).attr('role', 'alert');

      $button.attr({
        tabindex: 0,
        role: 'button',
        title: 'Close this notice'
      }).addClass('btn btn-mini');
    },
    after_open: function (pnotify) {
      var location = null; // TODO: <<< ???

      $('#reflect1-yes').click(function () {
        OES.log4j('REPORT.USEFULNESS', { result: 1, url: $(location).attr('pathname') });
        pnotify.pnotify_remove();
      });

      $('#reflect1-no').click(function () {
        OES.log4j('REPORT.USEFULNESS', { result: 0, url: $(location).attr('pathname') });
        pnotify.pnotify_remove();
      });
    }
  };

  if (this._selfreport) {
    this._selfreport.pnotify_remove();
  }
  this._selfreport = $.pnotify(optsQ1);
};

window.jQuery(function ($) {
  $('#rd-comparisons img').click(function () {
    // var thmb = this;
    var src = this.src;
    var versionName = this.getAttribute('data-version-name');

    $('.rd-comparison-image').fadeOut(200, function () {
      $(this).fadeIn(200)[0].src = src;
    });

    if (versionName.length > 0) {
      versionName = " - '" + versionName + "'";
    }

    $('.version-number-comparison').html(this.id + ' ' + versionName);
  });
});
