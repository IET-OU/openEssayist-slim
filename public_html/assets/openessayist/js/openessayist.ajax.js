/*!
  OpenEssayist javascript | © The Open University (IET).
*/

// From: site.twig.
// <script id="ajax" type="text/javascript">

window.jQuery(function ($) {
  'use strict';

  var $conf = $('script[ data-openessayist ]');
  var $editor = $('#editor');
  var $button = $('#btn-notepad');
  var $modal = null;

  console.warn('Config:', $conf, $conf.data('user_isdemo'));

  $button.on('click', function () {
    if ($modal === null) {
      $modal = $('#modal-notes');

      $modal.on('hidden', function () {
        if (!$conf.data('user_isdemo')) {
          // {% if (auth.user.isdemo==0) %}
          $.post($conf.data('profile_save_notes'),
            $editor.html())
            .done(function () { })
            .fail(function () { })
            .always(function () { });
          // {% endif %}
        }
      }).find($editor).load(
        $conf.data('profile_save_notes'), '',
        function () {});
    }

    $modal.modal({
      maxHeight: '300px',
      keyboard: false,
      backdrop: 'static'
    });
  });
});

// </script>
