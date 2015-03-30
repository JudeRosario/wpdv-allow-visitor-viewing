<?php	
/*
Plugin Name: Allow Visitor Viewing
Description: Activate this add-on to allow your visitors to view the results of voting, but not vote till they login.
Plugin URI: http://premium.wpmudev.org/project/post-voting-plugin
Version: 1.0.0
Author: Jude Rosario (Incsub)
*/


class Wdpv_Allow_Visitor_Viewing {
// A handle to a codec object
	var $codec;

// Initiates the $codec variable 
	function __construct () {
		if (class_exists('Wdpv_Codec'))
			$this->codec = new Wdpv_Codec;
	}

// The starting point to this addon/class
	public static function serve () {
		$instance = new self;
		$instance->add_hooks();
	}
// Hooks into the_content currently for lack of a better hook
	private function add_hooks () {
		add_filter('the_content', array($this,'inject_disabled_widget'));
	}

	function view_only_widget ($args=array()) {
	// Does nothing if
		if (
		// Voting is disabled
			(!$this->codec->data->get_option('allow_voting'))
			||
		// Visitors can anyway vote 
			($this->codec->data->get_option('allow_visitor_voting'))
			||
		// A login link is shown instead
			($this->codec->data->get_option('show_login_link'))
			||
		// The user is logged in and can therefore vote
			(is_user_logged_in())
		) 
			return ' ';	

		$args = shortcode_atts(array(
			'standalone' => false,
			'blog_id' => false,
			'post_id' => false,
		), $args);

// Get the values of vote $count, $post_id and $blog_id if this is a multi site install
		$standalone = ('no' != $args['standalone']) ? true : false;
		$post_id = $args['post_id'] ? $args['post_id'] : get_the_ID();
		$blog_id = $this->codec->_get_blog_id($args['blog_id']);
		$count = $this->codec->model->get_votes_total($post_id, false, $blog_id);

		if (!$this->codec->_check_voting_display_restrictions($post_id)) return '';

// Applies the skin chosen by the user 
		$skin = $this->codec->data->get_option('voting_appearance');
		apply_filters('wdpv-output-before_vote_widget', '', $args, $blog_id, $post_id);

// The vote up key/button/star etc .. 
		$up_vote = "<div class='wdpv_vote_up {$skin}'><input type='hidden' value='{$post_id}' />";
		$up_vote .= "<input type='hidden' class='wdpv_blog_id' value='{$blog_id}' /></div>";
		$up_vote = apply_filters('wdpv-output-vote_up', $up_vote, $args, $blog_id, $post_id);
// The vote count / result wrapped with a hidden input tag		
		$result = "<div class='wdpv_vote_result'><span class='wdpv_vote_result_output'>{$count}</span>";
		$result .= "<input type='hidden' value='{$post_id}' />";
		$result .= "<input type='hidden' class='wdpv_blog_id' value='{$blog_id}' /></div>";
		$result = apply_filters('wdpv-output-vote_result', $result, $args, $blog_id, $post_id);
// The vote down key/button/star etc .. 		
		$down_vote = "<div class='wdpv_vote_down {$skin}'><input type='hidden' value='{$post_id}' />";
		$down_vote .= "<input type='hidden' class='wdpv_blog_id' value='{$blog_id}' /></div>";
		$down_vote = apply_filters('wdpv-output-vote_down', $down_vote, $args, $blog_id, $post_id);
				
// The return string constructed from the above 3 variables
		$ret = "<div class='wdpv_voting'>".$up_vote.$result.$down_vote."</div>";
		$ret .= $standalone ? '<div class="wdpv_clear"></div>' : '';

// Sets the icons as disabled
		$search = array("wdpv_vote_up", "wdpv_vote_down");
		$replace = array("wdpv_vote_up wdpv_disabled", "wdpv_vote_down wdpv_disabled");
		$ret = str_replace($search,$replace,$ret);

// Adds a login url to the string		
		$ret .= '<div class="wdpv_login"><a href="'.wp_login_url(get_permalink()).'">Log in </a> to vote</div>';

		return $ret;
	}

	function inject_disabled_widget ($body) {
	// A last minute kill switch to turn off this addon
		$inject = apply_filters( "inject_disabled_widget", true );
		// Do nothing if
		if (
		// We are on the Home page and front page voting is turned off	
		(is_home() && !$this->codec->data->get_option('front_page_voting'))
			||
			// This is neither a front page nor a post page
			(!is_home() && !is_singular())
			||
			// kill switch is used to turn add on off 
			!$inject
		) 
			return $body;

		if ($this->codec->has_wdpv_shortcode('no_auto', $body)) return $body;
		
// Get where the user wants to display this in his posts
		$position = $this->codec->data->get_option('voting_position');
		$widget_code = $this->view_only_widget(array('standalone' => false, 'blog_id' => false, 'post_id' => false));

// Do nothing if manual, otherwise place accordingly
		if ('manual' != $this->codec->data->get_option('voting_position'))
		{
			if ('top' == $position || 'both' == $position) {
				$body =  $widget_code . ' ' . $body;
			}
			if ('bottom' == $position || 'both' == $position) {
				$body .= " " . $widget_code ;
			}
		}
		return $body;
	}

}
// Check if the base plugin is installed before activating the addon 
add_action('plugins_loaded', 'init_wpdv_avv') ;

	function init_wpdv_avv () {
		if (class_exists('Wdpv_Codec'))
			Wdpv_Allow_Visitor_Viewing::serve();
	}