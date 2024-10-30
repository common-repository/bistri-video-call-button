<?php

/*
	Plugin Name: Bistri Video Call Button
	Plugin URI: https://developers.bistri.com
	Description: Show to your visitors that you are available for a video call.
	Version: 1.2.1
	Author: Bistri
	Author URI: https://bistri.com
*/

add_action('wp_head', 'bistri_button_head');

add_action('admin_head-widgets.php', 'bistri_admin_head' );

function bistri_button_head() {
    echo "<script src=\"https://bistri.com/widgets/button/min/bistri.button.min.js?v=1.5\"></script>";
}

function bistri_admin_head() {

	$backpath = plugins_url( 'back.html', __FILE__ );

	echo "<script src=\"http://developers.bistri.com/resources/sdk/bistri.sdk.min.js?v=1.0\"></script>";
	echo "<script type=\"text/javascript\">
		var $;
		var SERVICE_ROOT_URL = \"https://bistri.com\";
		var config = {
			baseUrl: SERVICE_ROOT_URL,
			oauth: true,
			oauth_client_id: 'pwjxpl6l1l6u90kj',
			oauth_popup_mode: true,
			oauth_back_url: '$backpath'
		};

		window.bsdkInit = function(){
			if( adminpage == 'widgets-php' && typeof $ === 'undefined' && typeof jQuery === 'function' ){
				$ = jQuery;
			}
			Bsdk.init( config );
		}
		</script>";
}

class Bistri_Button extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'bistri_button', // Base ID
			'Bistri Video Call Button', // Name
			array( 'description' => __( 'Show to your visitors that you are available for a video call', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		$key = apply_filters( 'widget_key', $instance['key'] );
		$type = apply_filters( 'widget_type', $instance['type'] );
		$size = apply_filters( 'widget_size', $instance['size'] );
		$presence = apply_filters( 'widget_presence', $instance['presence'] );
		$nolabel = apply_filters( 'widget_showlabel', $instance['nolabel'] );
		$label = apply_filters( 'widget_label', $instance['label'] );

		$attr_key = " data-key=\"" . $key . "\"";
		$attr_type = $type == "call" ? "" : " data-type=\"conf\"";
		$attr_size = $size == "large" ? "" : " data-size=\"small\"";
		$attr_presence = $presence == "true" ? "" : " data-presence=\"false\"";
		$attr_nolabel = $nolabel == "true" ? " data-nolabel=\"true\"" : "";
		$attr_label = strlen( $label ) > 0 ? " data-label=\"" . $label . "\"" : "";

		echo "<a class=\"bistri-button\"" . $attr_key . $attr_type . $attr_size . $attr_presence . $attr_nolabel . $attr_label . "></a>";
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['key'] = strip_tags( $new_instance['key'] );
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['size'] = strip_tags( $new_instance['size'] );
		$instance['presence'] = strip_tags( $new_instance['presence'] );
		$instance['nolabel'] = strip_tags( $new_instance['nolabel'] );
		$instance['label'] = strip_tags( $new_instance['label'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'key' ] ) ) {
			$key = $instance[ 'key' ];
		}
		else {

		}

		if ( isset( $instance[ 'type' ] ) ) {
			$type = $instance[ 'type' ];
		}
		else {
			$type = "call";
		}

		if ( isset( $instance[ 'size' ] ) ) {
			$size = $instance[ 'size' ];
		}
		else {
			$size = "large";
		}

		if ( isset( $instance[ 'presence' ] ) ) {
			$presence = $instance[ 'presence' ];
		}
		else {
			$presence = "true";
		}

		if ( isset( $instance[ 'nolabel' ] ) ) {
			$nolabel = $instance[ 'nolabel' ];
		}
		else {
			$nolabel = "false";
		}

		if ( isset( $instance[ 'label' ] ) AND strlen( $instance[ 'label' ] ) > 0 ) {
			$label = $instance[ 'label' ];
		}
		else {
			$label = "Call me on Bistri";
		}

		?>
		<style>
			.bistri-button-admin input[id$=key] {
				width: 70%;
			}

			.bistri-button-admin input[type=radio] {
				margin: 0 5px 0 15px;
			}
		</style>
		<script type="text/javascript">

			var currentField;
			var labels = {
				call: "Call me on Bistri",
				conf: "Join the conference"
			}

			window.bsdkReady = function(){
				//getKey();
			}

			function getKey( id ){

				if( id ){
					fieldId = id;
				}

				Bsdk.user.getApi().getWidgetKey().done( function( result ){
					document.getElementById( fieldId ).value = result.data;
				} );
			}

			function switchLabel( val, target ){
				document.getElementById( target ).value = labels[ val ];
			}
		</script>
		<div class="bistri-button-admin">
			<h4><?php _e( '1. Get widget key' ); ?> (*)</h4>
			<p>
			<input id="<?php echo $this->get_field_id( 'key' ); ?>" name="<?php echo $this->get_field_name( 'key' ); ?>" type="text" value="<?php echo esc_attr( $key ); ?>" /><input type="button" value="Get Key" onClick="getKey( '<?php echo $this->get_field_id( 'key' ); ?>' );">
			</p>
			<p>(*): You need to have a bistri account in order to get a widget key. If you don't already have an account go to <a href="https://bistri.com" target="_blank">https://bistri.com</a>.</p>

			<h4><?php _e( '2. Configure the widget' ); ?></h4>
			<p>
			<?php _e( 'button type:' ); ?><br/>
			<input id="<?php echo $this->get_field_id( 'type1' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" type="radio" value="call" onclick="switchLabel( 'call', '<?php echo $this->get_field_id( 'label' ); ?>' )" <?php if( $type == "call" ){ ?>checked<?php } ?>/><label for="<?php echo $this->get_field_id( 'type1' ); ?>">call me</label>
			<input id="<?php echo $this->get_field_id( 'type2' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" type="radio" value="conf" onclick="switchLabel( 'conf', '<?php echo $this->get_field_id( 'label' ); ?>' )" <?php if( $type == "conf" ){ ?>checked<?php } ?>/><label for="<?php echo $this->get_field_id( 'type2' ); ?>">join the conference</label>
			</p>

			<p>
			<?php _e( 'button size:' ); ?><br/>
			<input id="<?php echo $this->get_field_id( 'size1' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" type="radio" value="large" <?php if( $size == "large" ){ ?>checked<?php } ?>/><label for="<?php echo $this->get_field_id( 'size1' ); ?>">large</label>
			<input id="<?php echo $this->get_field_id( 'size2' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" type="radio" value="small" <?php if( $size == "small" ){ ?>checked<?php } ?>/><label for="<?php echo $this->get_field_id( 'size2' ); ?>">small</label>
			</p>

			<p>
			<?php _e( 'display presence:' ); ?><br/>
			<input id="<?php echo $this->get_field_id( 'presence1' ); ?>" name="<?php echo $this->get_field_name( 'presence' ); ?>" type="radio" value="true" <?php if( $presence == "true" ){ ?>checked<?php } ?>/><label for="<?php echo $this->get_field_id( 'presence1' ); ?>">true</label>
			<input id="<?php echo $this->get_field_id( 'presence2' ); ?>" name="<?php echo $this->get_field_name( 'presence' ); ?>" type="radio" value="false" <?php if( $presence == "false" ){ ?>checked<?php } ?>/><label for="<?php echo $this->get_field_id( 'presence2' ); ?>">false</label>
			</p>

			<p>
			<?php _e( 'display label:' ); ?><br/>
			<input id="<?php echo $this->get_field_id( 'nolabel' ); ?>" name="<?php echo $this->get_field_name( 'nolabel' ); ?>" type="radio" value="false" <?php if( $nolabel == "false" ){ ?>checked<?php } ?>/><label for="<?php echo $this->get_field_id( 'nolabel1' ); ?>">true</label>
			<input id="<?php echo $this->get_field_id( 'nolabel' ); ?>" name="<?php echo $this->get_field_name( 'nolabel' ); ?>" type="radio" value="true" <?php if( $nolabel == "true" ){ ?>checked<?php } ?>/><label for="<?php echo $this->get_field_id( 'nolabel2' ); ?>">false</label>
			</p>

			<p>
			<?php _e( 'button label:' ); ?><br/>
			<input class="widefat" id="<?php echo $this->get_field_id( 'label' ); ?>" name="<?php echo $this->get_field_name( 'label' ); ?>" type="text" value="<?php echo esc_attr( $label ); ?>" />
			</p>
			<h4><?php _e( '3. Don\'t forget to save the settings' ); ?></h4>
		</div>
		<?php
	}

}

// register Bistri_Button widget
add_action( 'widgets_init', create_function( '', 'register_widget( "Bistri_Button" );' ) );

?>