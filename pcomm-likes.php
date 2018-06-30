<?php

/*
Plugin Name: Partnercomm Likes
Description: Add likes to posts
Version: 1.2.2

Usage, with defaults:
do_shortcode('[pclikes post=' . $post->ID . ']')

Usage, hiding text and changing to thumbs up icon:
do_shortcode('[pclikes post=' . $post->ID . ' show_text=false fa_icon="thumbs-up"]');
*/

class PCommLikes
{

    protected $prefix = "pc_";

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_nopriv_like', array($this, 'like'));
        add_action('wp_ajax_like', array($this, 'like'));
    }

    public function init()
    {
        add_shortcode('pclikes', [$this, 'getLikes']);
        wp_enqueue_script('like-js', plugins_url('js/likes.js', __FILE__), array('jquery'), '', true);
        wp_localize_script('like-js', 'like_ajax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'domain' => $_SERVER['SERVER_NAME']
            )
        );
    }

    public function like()
    {
        $post_id = $_POST['post'];
        $like = (int)$_POST['like'];
        $type = $_POST['type'];
        $date = new DateTime();
        date_timezone_set($date, timezone_open('UTC'));
        $meta_key = $this->prefix . 'like_count';
        $meta_value = [
            'count' => 0,
            'date' => $date
        ];

        switch ($type) {
            case 'comment':
                $current_likes = get_comment_meta($post_id, $meta_key, true);
                $current_likes = (int)$current_likes['count'];
                $meta_value['count'] = $current_likes + $like;
                update_post_meta($post_id, $meta_key, $meta_value);
                break;
            default:
                $current_likes = get_post_meta($post_id, $meta_key, true);
                $current_likes = (int)$current_likes['count'];
                $meta_value['count'] = $current_likes + $like;
                update_post_meta($post_id, $meta_key, $meta_value);
                break;
        }
        $result = $meta_value['count'];
        echo ($result > -1) ? $result : 0;
        die();
    }

    public function getLikes($atts)
    {
        $atts = shortcode_atts([
            'post' => 0,
            'show_text' => false, // hide by default
            'show_date' => false, // hide by default
            'fa_icon' => 'heart',
        ], $atts);
        $post_id = (int)$atts['post'];

        if ($post_id == 0) {
            echo 'Error Loading Likes';
        }

        $icon = $atts['fa_icon'];

        $show_date = $like_date_formatted = $like_date_utc = '';

        $meta_key = $this->prefix . 'like_count';
        $meta_value = get_post_meta($post_id, $meta_key, true);
        if (is_array($meta_value)) {
            $current_likes = (int)$meta_value['count'];
            $like_date = $meta_value['date'];
            $like_date_utc = $like_date->format('U');
            $like_date_formatted = $like_date->format('m/d/Y h:i');
        } else {
            $current_likes = (int)$meta_value;
        }

        if ($atts['show_date'] === 'true')
            $show_date = '<span class="like-date" data-timestamp="' . $like_date_utc . '">' . $like_date_formatted . '</span>';

        $like_text = $current_likes === 1 ? 'like' : 'likes';

        $show_text = $atts['show_text'] === 'true' ? '<span class="like-text">' . $like_text . '</span>' : '';

        echo "<a data-post-id='{$post_id}' class='pcLikes' href='#'>
                <i class='status fa fa-{$icon}' aria-hidden='true'></i>
                <span class='count'>{$current_likes}</span> 
                {$show_text}
                {$show_date}
              </a>";
    }
}

$pcLikes = new PCommLikes();
add_shortcode('pclikes', [$pcLikes, 'getLikes']);
