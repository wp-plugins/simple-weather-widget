<?php
/**
 * Plugin Name: simple-weather-widget
 * Description: Simple weather widget.
 * Version: 1.0
 */

/**
 * Add function to widgets_init that'll load our widget.
 */
 
add_action( 'widgets_init', 'simple_weather_load_widgets' );

function simple_weather_load_widgets() {
	register_widget( 'simple_weather_widget' );
}

 
class simple_weather_widget extends WP_Widget {
/**
	 * Widget setup.
	 */
	function simple_weather_widget() {
		/* Widget settings. */
		$widget_options = array( 
		 'classname' => 'simple_weather_widget', 
		 'description' => __('A Simple widget that displays weather.') );

		/* Widget control settings. */
		$control_options = array( 
		'width' => 300, 
		'height' => 350, 
		'id_base' => 'simple_weather_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'simple_weather_widget', 'Simple Weather Widget', $widget_options, $control_options );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$woeid =($instance['woeid'] != "")?$instance['woeid'] :12799205;
 
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
        echo " \n";
        $url = 'http://weather.yahooapis.com/forecastrss?w='.$woeid; 
           
		$xml = (string)file_get_contents($url); 
		if ($xml && !empty($xml))
		{
			$xml = simplexml_load_string($xml); 
			if ($xml && is_object($xml)){
				$node = $xml->channel->item; 
				if (is_object($node)){
					$children = $node->children('http://xml.weather.yahoo.com/ns/rss/1.0'); 
					$condition = $children->condition; 
					$attributes = $condition->attributes();
					$city = $xml->channel->children('yweather', TRUE)->location->attributes()->city;				 					
			    	$description = $node->description;    
			        $imgpattern = '/src="(.*?)"/i';
			        preg_match($imgpattern, $description, $matches);
			
			         $weather = $matches[1];
			        $markup='<div style="border:1px dashed;display:block;background-color:#E6F1F6;">'. 
					         $attributes['date'].
					         $city.
					        '<img src = "' . $weather . '" />'.
					        'temperature' . 
					         $attributes['temp'] ."F".			
					         '</div>';
					echo nl2br($markup);
				}
			}
		}				
		/* After widget (defined by themes). */
		echo $after_widget;
	}
 

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['woeid'] = strip_tags( $new_instance['woeid'] );
 
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
		'title' => __('weather', 'weather'), 
		'woeid' => __('2490383', '2490383')
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Title Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','title'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Woeid : woeid Input -->
		
		<p>
			<label for="<?php echo $this->get_field_id( 'woeid' ); ?>"><?php _e('woeid:', 'woeid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'woeid' ); ?>" name="<?php echo $this->get_field_name( 'woeid' ); ?>" value="<?php echo $instance['woeid']; ?>" style="width:100%;" />
		</p>

 

	<?php
	}
	
}
?>