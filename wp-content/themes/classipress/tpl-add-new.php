<?php
/*
Template Name: Add New Listing
*/


// grabs the user info and puts into vars
global $current_user, $app_abbr;

?>


<script type='text/javascript'>
// <![CDATA[
jQuery(document).ready(function(){

	/* setup the form validation */
	jQuery('#mainform').validate({
		errorClass: 'invalid',
		errorPlacement: function(error, element) {
			if (element.attr('type') == 'checkbox' || element.attr('type') == 'radio') {
				element.closest('ol').after(error);
			} else {
				offset = element.offset();
				error.insertBefore(element)
				error.addClass('message');  // add a class to the wrapper
				error.css('position', 'absolute');
				error.css('left', offset.left + element.outerWidth());
				error.css('top', offset.top);
			}
        }

	});

	/* setup the tooltip */
    jQuery("#mainform a").easyTooltip();

});


/* General Trim Function  */
function trim (str) {
    var	str = str.replace(/^\s\s*/, ''),
            ws = /\s/,
            i = str.length;
    while (ws.test(str.charAt(--i)));
    return str.slice(0, i + 1);
}

/* Used for enabling the image for uploads */
function enableNextImage($a, $i) {
    jQuery('#upload'+$i).removeAttr('disabled');
}

// ]]>
</script>

<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<!-- full block -->
			<div class="shadowblock_out">

				<div class="shadowblock">

					<?php
						// check and make sure the form was submitted from step1 and the session value exists
						if( isset($_POST['step1']) ) {

							include_once(TEMPLATEPATH . '/includes/forms/step2.php');

						} elseif( isset($_POST['step2']) ) {

							include_once(TEMPLATEPATH . '/includes/forms/step3.php');

						} else {

							// create a unique ID for this new ad order
							// uniqid requires a param for php 4.3 or earlier. added for 3.0.1
							$order_id  = uniqid(rand(10,1000), false);
							include_once(TEMPLATEPATH . '/includes/forms/step1.php');

						}
					?>

				</div><!-- /shadowblock -->

			</div><!-- /shadowblock_out -->

			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
