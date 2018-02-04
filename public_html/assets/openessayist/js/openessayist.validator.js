/*!
  OpenEssayist JS: form.validator | © The Open University (IET).
*/

// From: 'user/draft.submit.twig'

window.jQuery(function ($) {
  $.blockUI.defaults.baseZ = 1100; // Z-index?

  var $form = $('form#post_essay');
  var $essay = $form.find('textarea#text');
  var $counts = $form.find('[ name = counts ]');
  var minlength = $essay.data('minlength') || 200;
  var maxlength = $essay.data('maxlength') || (200 * 1000); // Characters. Big!

  console.warn('Essay validator (min/maxlength chars):', minlength, maxlength, $form, $essay, $counts);

  $form.validate({
    rules: {
      text:
      {
        required: true,
        minlength: minlength,
        maxlength: maxlength
      }
    },
    submitHandler: function (form) {
      var essay = $essay.val();
      var wcount = essay.match(/[^\s]+/g).length;
      var time = wcount / 100;
      var msg = '';

      if (time < 30) {
        msg = 'it will take 15 to 30 seconds...';
      } else if (time < 60) {
        msg = 'it will take up to one minute ...';
      } else {
        msg = 'it will take one or two minutes ...';
      }

      $.blockUI({ message: '<div class="ajax alert alert-info">Starting Analysis. Please wait, ' + msg + '</div>' });
      form.submit();
    },
    highlight: function (element) {
      $(element).closest('.control-group').not('.help-block').removeClass('success').addClass('error');
    },
    success: function (element) {
      element
        .text('OK!').addClass('valid')
        .closest('.control-group').not('.help-block').removeClass('error').addClass('success');
    }
  });

  // NDF, 02-February-2018.
  window.Countable.on($essay.get(0), function (counter) {
    console.warn('Counts:', counter);

    $counts.val(JSON.stringify(counter));
  },
  { hardReturns: true });
});

// End.
