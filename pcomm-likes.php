<?php
/*
Plugin Name: Partnercomm Likes
Description: Add likes to posts
Version: 0.0.1

To Add: do_shortcode("[pclikes post='".get_the_ID()."']");
*/
class PCommLikes {

    protected $prefix = "pc_";

    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action('wp_ajax_nopriv_like', array($this, 'like'));
        add_action('wp_ajax_like', array($this, 'like'));
    }

    public function init() {
        add_shortcode('pclikes', [$this, 'getLikes']);
        wp_enqueue_script('like-js', plugins_url('js/likes.js', __FILE__), array('jquery'), '', true);
        wp_localize_script('like-js', 'like_ajax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'domain'  => $_SERVER['HTTP_HOST']
            )
        );
    }

    public function like() {
        $post_id = $_POST['post'];
        $like = (int) $_POST['like'];

        $current_likes = (int) get_post_meta($post_id, $this->prefix.'like_count', true);
        $new_likes = $current_likes + $like;

        //set the post meta to the new values
        update_post_meta($post_id, $this->prefix.'like_count', $new_likes);
        $result = $new_likes;
        echo ($result > -1) ? $result : 0;
        die();
    }

    public function getLikes($atts) {
        $atts = shortcode_atts(['post'=>0], $atts);
        $post_id = (int) $atts['post'];
        if($post_id == 0) {
            echo 'Error Loading Likes';
        }
        $current_likes = (int) get_post_meta($post_id, $this->prefix.'like_count', true);

        echo "<a data-post-id='{$post_id}' class='pcLikes'>
                <span class='count'>{$current_likes}</span> 
                likes
                <i class='status fa fa-heart-o' aria-hidden='true'></i>
              </a>";
    }
}

$pcLikes = new PCommLikes();
add_shortcode('pclikes', [$pcLikes, 'getLikes']);