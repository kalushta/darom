<?php

if ( !function_exists( 'p2p_register_connection_type' ) ) {
	define( 'P2P_TEXTDOMAIN', APP_TD );

	foreach ( array(
		'storage', 'query', 'query-post', 'query-user', 'url-query',
		'util', 'item', 'list', 'side',
		'type-factory', 'type', 'directed-type', 'indeterminate-type',
		'api', 'extra'
	) as $file ) {
		require dirname( __FILE__ ) . "/p2p-core/$file.php";
	}

	add_action( 'appthemes_first_run', array( 'P2P_Storage', 'install' ), 9 );
}

