<?php
$title_tag = isset($link_tag) ? $link_tag : 'h4';
$post_link_meta = get_post_meta(get_the_ID(), "mkdf_post_link_link_meta", true);

if (curly_mkdf_get_blog_module() == 'list') {
    $post_link = get_the_permalink();
} else {
    if (!empty($post_link_meta)) {
        $post_link = esc_html($post_link_meta);
    }
}
?>

<div class="mkdf-post-link-holder">

    <?php if (isset($post_link) && $post_link != '') : ?>
        <a itemprop="url" href="<?php echo esc_url($post_link); ?>" title="<?php the_title_attribute(); ?>" target="_blank">
    <?php endif; ?>

        <div class="mkdf-post-link-holder-inner">
            <?php echo '<' . esc_attr($title_tag); ?> itemprop="name" class="mkdf-link-title mkdf-post-title">
            <?php echo get_the_title(); ?>
            <?php echo '</' . esc_attr($title_tag); ?>>
        </div>

    <?php if (isset($post_link) && $post_link != '') : ?>
        </a>
    <?php endif; ?>

</div>