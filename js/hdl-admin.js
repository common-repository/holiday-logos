jQuery().ready(function( $ ) {
	$( '#titlewrap input' ).addClass( 'required' );
	$( "#post" ).validate({
		rules: {
			hdlupload_image: {
			  require_from_group: [1, ".media-group"]
			},
			hdlupload_image2: {
			  require_from_group: [1, ".media-group"]
			},
			hdlvideourl: {
			  require_from_group: [1, ".media-group"]
			}
		  }
	});
});
