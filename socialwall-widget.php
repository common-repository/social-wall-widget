<?php
/*
Plugin Name: Social Wall Widget
Plugin URI: http://socialwall.com.au
Description: Widget to display your Social Wall
Author: Social Wall
Version: 1.1
Author URI: http://socialwall.com.au
*/
// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	

add_action( 'widgets_init', function(){
     register_widget( 'socialwall_widget' );
});	

// Creating the widget 
class socialwall_widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array('classname' => 'widget_socialwall', 'description' => __('Add your Social Wall by using a Widget.'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('socialwall', __('Social Wall Widget'), $widget_ops, $control_ops);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		/**
		 * Filter the content of the Text widget.
		 *
		 * @since 2.3.0
		 *
		 * @param string    $widget_socialwall The widget content.
		 * @param WP_Widget $instance    WP_Widget instance.
		 */
		$text = apply_filters( 'widget_socialwall', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
			<div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = ! empty( $new_instance['filter'] );
		return $instance;
	}

	/**
	 * @param array $instance
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Your Social Wall iFrame Code:' ); ?></label>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea></p>
		
<?php
	}
}
// Register and load the widget
function socialwall_load_widget() {
	register_widget( 'socialwall_widget' );
}
add_action( 'widgets_init', 'socialwall_load_widget' );