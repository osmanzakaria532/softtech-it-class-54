<?php

add_shortcode('gallery', 'comet_gallery_shortcode');
function comet_gallery_shortcode($attr)
{
    $post = get_post();

    static $instance = 0;
    ++$instance;

    if (! empty($attr['ids'])) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if (empty($attr['orderby'])) {
            $attr['orderby'] = 'post__in';
        }
        $attr['include'] = $attr['ids'];
    }

    /**
     * Filters the default gallery shortcode output.
     *
     * If the filtered output isn't empty, it will be used instead of generating
     * the default gallery template.
     *
     * @since 2.5.0
     * @since 4.2.0 The `$instance` parameter was added.
     *
     * @see gallery_shortcode()
     *
     * @param string $output   The gallery output. Default empty.
     * @param array  $attr     Attributes of the gallery shortcode.
     * @param int    $instance Unique numeric ID of this gallery shortcode instance.
     */
    $output = apply_filters('post_gallery', '', $attr, $instance);

    if (! empty($output)) {
        return $output;
    }

    $html5 = current_theme_supports('html5', 'gallery');
    $atts  = shortcode_atts(
        array(
            'order'      => 'ASC',
            'orderby'    => 'menu_order ID',
            'id'         => $post ? $post->ID : 0,
            'itemtag'    => $html5 ? 'figure' : 'li',
            'icontag'    => $html5 ? 'div' : 'dt',
            'captiontag' => $html5 ? 'figcaption' : 'dd',
            'columns'    => 3,
            'size'       => 'thumbnail',
            'include'    => '',
            'exclude'    => '',
            'link'       => '',
        ),
        $attr,
        'gallery'
    );

    $id = (int) $atts['id'];

    if (! empty($atts['include'])) {
        $_attachments = get_posts(
            array(
                'include'        => $atts['include'],
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'order'          => $atts['order'],
                'orderby'        => $atts['orderby'],
            )
        );

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif (! empty($atts['exclude'])) {
        $post_parent_id = $id;
        $attachments    = get_children(
            array(
                'post_parent'    => $id,
                'exclude'        => $atts['exclude'],
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'order'          => $atts['order'],
                'orderby'        => $atts['orderby'],
            )
        );
    } else {
        $post_parent_id = $id;
        $attachments    = get_children(
            array(
                'post_parent'    => $id,
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'order'          => $atts['order'],
                'orderby'        => $atts['orderby'],
            )
        );
    }

    if (! empty($post_parent_id)) {
        $post_parent = get_post($post_parent_id);

        // Terminate the shortcode execution if the user cannot read the post or it is password-protected.
        if (
            ! is_post_publicly_viewable($post_parent->ID) && ! current_user_can('read_post', $post_parent->ID)
            || post_password_required($post_parent)
        ) {
            return '';
        }
    }

    if (empty($attachments)) {
        return '';
    }

    if (is_feed()) {
        $output = "\n";
        foreach ($attachments as $att_id => $attachment) {
            if (! empty($atts['link'])) {
                if ('none' === $atts['link']) {
                    $output .= wp_get_attachment_image($att_id, $atts['size'], false, $attr);
                } else {
                    $output .= wp_get_attachment_link($att_id, $atts['size'], false);
                }
            } else {
                $output .= wp_get_attachment_link($att_id, $atts['size'], true);
            }
            $output .= "\n";
        }
        return $output;
    }

    $itemtag    = tag_escape($atts['itemtag']);
    $captiontag = tag_escape($atts['captiontag']);
    $icontag    = tag_escape($atts['icontag']);
    $valid_tags = wp_kses_allowed_html('post');
    if (! isset($valid_tags[$itemtag])) {
        $itemtag = 'li';
    }
    if (! isset($valid_tags[$captiontag])) {
        $captiontag = 'dd';
    }
    if (! isset($valid_tags[$icontag])) {
        $icontag = 'dt';
    }

    $columns   = (int) $atts['columns'];
    $itemwidth = $columns > 0 ? floor(100 / $columns) : 100;
    $float     = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = '';

    /**
     * Filters whether to print default gallery styles.
     *
     * @since 3.1.0
     *
     * @param bool $print Whether to print default gallery styles.
     *                    Defaults to false if the theme supports HTML5 galleries.
     *                    Otherwise, defaults to true.
     */
    if (apply_filters('use_default_gallery_style', ! $html5)) {
        $type_attr = current_theme_supports('html5', 'style') ? '' : ' type="text/css"';

        $gallery_style = "
		<style{$type_attr}>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>\n\t\t";
    }

    $size_class  = sanitize_html_class(is_array($atts['size']) ? implode('x', $atts['size']) : $atts['size']);
    // $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
    $gallery_div = ' <div data-options="{&quot;animation&quot;: &quot;slide&quot;, &quot;controlNav&quot;: true"
        class="flexslider nav-outside"> <ul class="slides">';

    /**
     * Filters the default gallery shortcode CSS styles.
     *
     * @since 2.5.0
     *
     * @param string $gallery_style Default CSS styles and opening HTML div container
     *                              for the gallery shortcode output.
     */
    $output = apply_filters('gallery_style', $gallery_style . $gallery_div);

    $i = 0;

    foreach ($attachments as $id => $attachment) {

        $attr = (trim($attachment->post_excerpt)) ? array('aria-describedby' => "$selector-$id") : '';

        if (! empty($atts['link']) && 'file' === $atts['link']) {
            $image_output = wp_get_attachment_link($id, $atts['size'], false, false, false, $attr);
        } elseif (! empty($atts['link']) && 'none' === $atts['link']) {
            $image_output = wp_get_attachment_image($id, $atts['size'], false, $attr);
        } else {
            $image_output = wp_get_attachment_link($id, $atts['size'], true, false, false, $attr);
        }

        $image_meta = wp_get_attachment_metadata($id);

        $orientation = '';

        if (isset($image_meta['height'], $image_meta['width'])) {
            $orientation = ($image_meta['height'] > $image_meta['width']) ? 'portrait' : 'landscape';
        }

        $output .= "<{$itemtag}>";
        $output .= $image_output;

        if ($captiontag && trim($attachment->post_excerpt)) {
            $output .= "
				<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
        }

        $output .= "</{$itemtag}>";

        // if (! $html5 && $columns > 0 && 0 === ++$i % $columns) {
        //     $output .= '<br style="clear: both" />';
        // }
    }

    if (! $html5 && $columns > 0 && 0 !== $i % $columns) {
        $output .= "
			<br style='clear: both' />";
    }

    $output .= "
		</ul></div>\n";

    return $output;
}

// <?php


// add_shortcode('gallery', 'comet_gallery');

// function comet_gallery($attr, $content){

// 	$att = shortcode_atts(array(
// 		'ids' => ''
// 	), $attr);

// 	extract($att);

// 	$idd = explode(',', $ids);


// 	ob_start();
?>
// <div data-options="{&quot;animation&quot;: &quot;slide&quot;, &quot;controlNav&quot;: true"
    class="flexslider nav-outside">
    // <ul class="slides">
        // <?php foreach ($idd as $id) : ?>

        // <?php $sujan = wp_get_attachment_image_src($id, 'full'); ?>


        // <li><img src="<?php echo $sujan[0]; ?>" /></li>
        // <?php endforeach; ?>
        // </ul>
    // </div>

// <?php return ob_get_clean();



// }