/*!
  OpenEssayist JS: init | © The Open University (IET).
*/

// eslint-disable-next-line
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),  // eslint-disable-line
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) // eslint-disable-line
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');// eslint-disable-line

// From: 'base.html.twig'

// <script id="openessayist-init" type="text/javascript">
// $(document).ready(function() {

window.jQuery(function ($) {
  // var openEssayist = window.openEssayist;
  var L = window.location;
  var ga = window.ga;

  // Set the URL for the AJAX-based activity logging.
  // Was: openEssayist.LOG_URL = "{{ urlFor('ajax.log.activity') }}";
  var CFG = $('#openessayist-js-config').data();
  // openEssayist.config = $('script[ data-openessayist ]').data();
  // var CFG = openEssayist.config;
  window.openEssayist.config = CFG;

  console.warn('openEssayist config:', CFG);

  ga('create', CFG.google_analytics, 'auto');

  ga('send', 'pageview', CFG.analytics_prefix + L.pathname);

  // Activate Bootstrap tooltips.
  var $tooltip = $('[ rel = tooltip ]');

  if ($tooltip.length) {
    $tooltip.tooltip({ container: 'body' });
  }

  // fix for changing aria-expanded status on dropdown
  $('.btn[ data-toggle = collapse ]').on('click', function () {
    var clp = $(this).hasClass('collapsed');
    console.log('collapse:', clp);
    $(this).attr('aria-expanded', clp);
  });

  $('[ data-toggle = dropdown ]').on('click', function () {
    var clp = $(this).parent('.dropdown').hasClass('open');
    console.log('collapse:', clp);
    $(this).attr('aria-expanded', !clp);
  });

  // Fix for preventing scrolling behind the overlay, when in modals.
  var $modal = $('.modal');

  $modal.on('show', function () {
    var $modalInput = $('.modal-body :input');

    $('body').css('overflow', 'hidden');

    // fix for setting focus on labels containing checkbox
    $modalInput.focus(function () {
      $(this).parent('label.checkbox').css({
        outline: 'thin dotted #333333', 'outline-offset': '2px'
      });
    });

    $modalInput.blur(function () {
      $(this).parents('label.checkbox').css({
        outline: 'none', 'background-color': 'inherit'
      });
    });

    $('input:text:visible:first').focus();
  });

  $modal.on('hidden', function () {
    $('body').css('overflow', 'auto');
  });
});

// </script>
