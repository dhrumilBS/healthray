( function( $ ) {

	// ready event
	$( function() {
		// no cookie compliance notice
		$( '.cn-notice' ).on( 'click', '.cn-no-compliance .cn-notice-dismiss', function( e ) {
			var notice_action = 'dismiss';
			var param = '';

			if ( $( e.currentTarget ).hasClass( 'cn-approve' ) )
				notice_action = 'approve';
			else if ( $( e.currentTarget ).hasClass( 'cn-delay' ) )
				notice_action = 'delay';
			else if ( $( e.delegateTarget ).hasClass( 'cn-threshold' ) ) {
				notice_action = 'threshold';

				var delay = $( e.delegateTarget ).find( '.cn-notice-text' ).data( 'delay' );

				param = parseInt( delay );
			}

			$.ajax( {
				url: cnArgsNotice.ajaxURL,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'cn_dismiss_notice',
					notice_action: notice_action,
					nonce: cnArgsNotice.nonce,
					param: param,
					cn_network: cnArgsNotice.network ? 1 : 0
				}
			} );

			$( e.delegateTarget ).slideUp( 'fast' );
		} );

		// review notice
		$( '.cn-notice' ).on( 'click', '.cn-review .button-link', function( e ) {
			var link = $( this );
			var notice_action = 'dismiss';

			if ( link.hasClass( 'cn-notice-review' ) )
				notice_action = 'review';
			else if ( link.hasClass( 'cn-notice-delay' ) )
				notice_action = 'delay';

			$.ajax( {
				url: cnArgsNotice.ajaxURL,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'cn_review_notice',
					notice_action: notice_action,
					nonce: cnArgsNotice.reviewNonce,
					cn_network: cnArgsNotice.network ? 1 : 0
				}
			} );

			$( e.delegateTarget ).slideUp( 'fast' );
		} );
	} );

} )( jQuery );