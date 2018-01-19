/*!
  OpenEssayist javascript | © The Open University (IET).
*/

// From: site.twig.
//<script id="ajax" type="text/javascript">

window.jQuery(function ($) {

var $modal = null;
$('#btn-notepad').on('click', function(){
	if ($modal==null)
	{
			$modal = $('#modal-notes');
			$modal.on('hidden', function () {
				{% if (auth.user.isdemo==0) %}
    			var jqxhr = $.post("{{ urlFor('profile.save.notes') }}",
    					$('#editor').html())
    				.done(function() {})
    				.fail(function() { ; })
    				.always(function() { });
				{% endif %}

    		}).find('div#editor').load(
					"{{ urlFor('profile.save.notes') }}", "",
					function(){});
    }
	$modal.modal({
		maxHeight: "300px",
		keyboard: false,
		backdrop: "static"
		});
});

});

//</script>
