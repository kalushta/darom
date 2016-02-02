<?php

// Generic container for easily manipulating an ordered associative array
class APP_List {

	protected $items = array();

	function add( $id, $payload ) {
		// TODO: allow overwrite or have a replace() method ?
		$this->items[ $id ] = $payload;
	}

	function add_before( $ref_id, $id, $payload ) {
		$new_array = array();

		$found = false;
		foreach ( $this->items as $key => $value ) {
			if ( $key == $ref_id ) {
				$new_array[ $id ] = $payload;
				$found = true;
			}

			$new_array[ $key ] = $value;
		}

		if ( !$found )
			$new_array[ $id ] = $payload;

		$this->items = $new_array;
	}

	function add_after( $ref_id, $id, $payload ) {
		$new_array = array();

		$found = false;
		foreach ( $this->items as $key => $value ) {
			$new_array[ $key ] = $value;

			if ( $key == $ref_id ) {
				$new_array[ $id ] = $payload;
				$found = true;
			}
		}

		if ( !$found )
			$new_array[ $id ] = $payload;

		$this->items = $new_array;
	}

	function contains( $id ) {
		return isset( $this->items[ $id ] );
	}

	function get( $id ) {
		return $this->items[ $id ];
	}

	function get_all() {
		return $this->items;
	}

	function remove( $id ) {
		unset( $this->items[ $id ] );
	}
}


abstract class APP_Tabs_Page extends scbAdminPage {

	public $tabs;
	public $tab_sections;

	abstract protected function init_tabs();

	function __construct( $options = null ) {
		parent::__construct( false, $options );

		$this->tabs = new APP_List;
	}

	function page_loaded() {
		$this->init_tabs();

		do_action( 'tabs_' . $this->pagehook, $this );
	}

	function form_handler() {
		if ( empty( $_POST['action'] ) || ! $this->tabs->contains( $_POST['action'] ) )
			return;

		check_admin_referer( $this->nonce );

		$form_fields = array();

		foreach ( $this->tab_sections[ $_POST['action'] ] as $section )
			$form_fields = array_merge( $form_fields, $section['fields'] );

		$to_update = scbForms::validate_post_data( $form_fields, null, $this->options->get() );

		$this->options->update( $to_update );

		$this->admin_msg();
	}

	function page_head() {
?>
<style type="text/css">
.wrap h3 { margin-bottom: 0; }
.wrap .form-table + h3 { margin-top: 2em; }

td.tip { width: 16px; }
.tip-icon { margin-top: 3px; cursor: pointer; }
.tip-content { display: none; }
.tip-show { border: 1px solid #ccc; }
</style>
<?php
	}

	function page_footer() {
		parent::page_footer();
?>
<script type="text/javascript">
jQuery(function($) {
	$(document).delegate('.tip-icon', 'click', function(ev) {
		var $row = $(this).closest('tr');

		var $show = $row.next('.tip-show');

		if ( $show.length ) {
			$show.remove();
		} else {
			$show = $('<tr class="tip-show">').html(
				$('<td colspan="3">').html( $row.find('.tip-content').html() )
			);

			$row.after( $show );
		}
	});
});
</script>
<?php
	}

	function page_content() {
		if ( isset( $_GET['firstrun'] ) )
			do_action( 'appthemes_first_run' );

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

		$tabs = $this->tabs->get_all();

		if ( ! isset( $tabs[ $active_tab ] ) )
			$active_tab = key( $tabs );

		$current_url = scbUtil::get_current_url();

		echo '<h3 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab_id => $tab_title ) {
			$class = 'nav-tab';

			if ( $tab_id == $active_tab )
				$class .= ' nav-tab-active';

			$href = add_query_arg( 'tab', $tab_id, $current_url );

			echo ' ' . html( 'a', compact( 'class', 'href' ), $tab_title );
		}
		echo '</h3>';

		echo '<form method="post" action="">';
		echo '<input type="hidden" name="action" value="' . $active_tab . '" />';
		wp_nonce_field( $this->nonce );

		foreach ( $this->tab_sections[ $active_tab ] as $section ) {
			echo html( 'h3', $section['title'] );

			if ( isset( $section['renderer'] ) )
				call_user_func( $section['renderer'], $section );
			else
				$this->render_section( $section['fields'] );
		}

		echo '<p class="submit"><input type="submit" class="button-primary" value="' . esc_attr__( 'Save Changes', APP_TD ) . '" /></p>';
		echo '</form>';
	}

	private function render_section( $fields ) {
		$output = '';

		foreach ( $fields as $field ) {
			$output .= $this->table_row( $this->before_rendering_field( $field ) );
		}

		echo $this->table_wrap( $output );
	}

	public function table_row( $field ) {
		if ( empty( $field['tip'] ) ) {
			$tip = '';
		} else {
			$tip  = html( "img", array(
				'class' => 'tip-icon',
				'title' => __( 'Help', APP_TD ),
				'src' => appthemes_framework_image( 'help.png' )
			) );
			$tip .= html( "div class='tip-content'", $field['tip'] );
		}

		return html( "tr",
			html( "th scope='row'", $field['title'] ),
			html( "td class='tip'", $tip ),
			html( "td", scbForms::input( $field, $this->options->get() ) )
		);
	}

	/**
	 * Useful for adding dynamic descriptions to certain fields.
	 *
	 * @param array field arguments
	 * @return array modified field arguments
	 */
	protected function before_rendering_field( $field ) {
		return $field;
	}
}

