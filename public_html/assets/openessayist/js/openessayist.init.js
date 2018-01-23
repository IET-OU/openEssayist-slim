/*!
  OpenEssayist javascript | © The Open University (IET).
*/

// From: base.html.twig.
// <script id="openessayist-init" type="text/javascript">
// $(document).ready(function() {

window.jQuery(function ($) {

  // Set the URL for the AJAX-based activity logging
  // Was: openEssayist.LOG_URL = "{{ urlFor('ajax.log.activity') }}";
  window.openEssayist.config = $('script[ data-openessayist ]').data();

  console.warn('openEssayist config:', openEssayist.config);

  // Activate the Bootstrap's tooltips
  if ($("[rel=tooltip]").length) {
          $("[rel=tooltip]").tooltip({container: 'body'});
      }

  // fix for changing aria-expanded status on dropdown
   $(".btn[data-toggle='collapse']").on('click', function () {
       clp = $(this).hasClass("collapsed");
       console.log("collapse : " + clp);
       $(this).attr('aria-expanded',clp);
   });
   $("[data-toggle='dropdown']").on('click', function () {
       clp = $(this).parent(".dropdown").hasClass("open");
       console.log("collapse : " + clp);
       $(this).attr('aria-expanded',!clp);
   });

  // Fix for preventing scrolling behind the overlay, when in modals.
    $(".modal").on('show', function () {
      $('body').css('overflow', 'hidden');

      // fix for setting focus on labels containing checkbox
      $('.modal-body :input').focus(function() {
        $(this).parent("label.checkbox").css('outline', 'thin dotted #333333');
        $(this).parent("label.checkbox").css('outline-offset', '2px');

      });
      $('.modal-body :input').blur(function() {
        $(this).parents("label.checkbox").css('background-color', 'inherit');
        $(this).parents("label.checkbox").css('outline', 'none');
    });

      $('input:text:visible:first').focus();


    });
  $(".modal").on('hidden', function () {
      $('body').css('overflow', 'auto');
    });
});

// </script>
