# PComm WP Likes plugin

## Usage
With default, show 'likes' text and heart icon
```
do_shortcode('[pclikes post=' . $post->ID . ']')
```

Hide text and change to thumbs up icon:
```
do_shortcode('[pclikes post=' . $post->ID . ' show_text=false fa_icon="thumbs-up"]');
```

## Changelog


#### v.1.2

* Removing Event Bubbling

#### v.1.1

* Changed 'likes' to 'like' on output text if only 1 like.
* Added new attributes to shortcode
    * `show_text`: Defaults to true. Set to false to hide 'likes' or 'like text and only show icon and number.
    * `fa_icon`: Defaults to `heart`. Set to a font-awesome icon name (output as classname, don't include 'fa'.