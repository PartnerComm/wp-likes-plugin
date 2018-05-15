<?php
/*
Plugin Name: Partnercomm Likes
Description: Add likes to posts
Version: 1.2.0

Usage, with defaults:
do_shortcode('[pclikes post=' . $post->ID . ']')

Usage, hiding text and changing to thumbs up icon:
do_shortcode('[pclikes post=' . $post->ID . ' show_text=false fa_icon="thumbs-up"]');
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
                'domain'  => $_SERVER['SERVER_NAME']
            )
        );
    }

    public function like() {
        $post_id = $_POST['post'];
        $like = (int) $_POST['like'];
        $type = $_POST['type'];

        if($type == 'comment') {
            $current_likes = (int) get_comment_meta($post_id, $this->prefix.'like_count', true);
            $new_likes = $current_likes + $like;

            //set the post meta to the new values
            update_comment_meta($post_id, $this->prefix.'like_count', $new_likes);
        } else {
            $current_likes = (int) get_post_meta($post_id, $this->prefix.'like_count', true);
            $new_likes = $current_likes + $like;

            //set the post meta to the new values
            update_post_meta($post_id, $this->prefix.'like_count', $new_likes);
        }


        $result = $new_likes;
        echo ($result > -1) ? $result : 0;
        die();
    }

    public function getLikes($atts) {
        $atts = shortcode_atts([
            'post'=>0,
            'show_text' => true,
            'fa_icon' => 'heart'
        ], $atts);
        $post_id = (int) $atts['post'];
        if($post_id == 0) {
            echo 'Error Loading Likes';
        }

        $icon = $atts['fa_icon'];
        $current_likes = (int) get_post_meta($post_id, $this->prefix.'like_count', true);
        $like_text = $current_likes === 1 ? 'like' : 'likes';
        $show_text = $atts['show_text'] === 1 ? '<span class="like-text">' . $like_text . '</span>' : '';

        echo "<a data-post-id='{$post_id}' class='pcLikes' href='#'>
                <i class='status fa fa-{$icon}' aria-hidden='true'></i>
                <span class='count'>{$current_likes}</span> 
                {$show_text}
              </a>";
    }
}

$pcLikes = new PCommLikes();
add_shortcode('pclikes', [$pcLikes, 'getLikes']);