<?php do_action('curly_mkdf_before_page_header'); ?>

<header class="mkdf-page-header">
    <?php do_action('curly_mkdf_after_page_header_html_open'); ?>

    <?php if ($show_fixed_wrapper) : ?>
    <div class="mkdf-fixed-wrapper">
        <?php endif; ?>

        <div class="mkdf-menu-area">
            <?php do_action('curly_mkdf_after_header_menu_area_html_open'); ?>

            <?php if ($menu_area_in_grid) : ?>
            <div class="mkdf-grid">
                <?php endif; ?>

                <div class="mkdf-vertical-align-containers">
                    <div class="mkdf-position-left"><!--
				 -->
                        <div class="mkdf-position-left-inner">
                            <?php if (!$hide_logo) {
                                curly_mkdf_get_logo();
                            } ?>
                        </div>
                    </div>
                    <div class="mkdf-position-right"><!--
				 -->
                        <div class="mkdf-position-right-inner">
                            <a href="javascript:void(0)" <?php curly_mkdf_class_attribute($fullscreen_menu_icon_class); ?>>
							<span class="mkdf-fullscreen-menu-close-icon">
								<?php echo curly_mkdf_get_fullscreen_menu_close_icon_html(); ?>
							</span>
							<span class="mkdf-fullscreen-menu-opener-icon">
								<?php echo curly_mkdf_get_fullscreen_menu_icon_html(); ?>
							</span>
                            </a>
                        </div>
                    </div>
                </div>

                <?php if ($menu_area_in_grid) : ?>
            </div>
        <?php endif; ?>
        </div>

        <?php if ($show_fixed_wrapper) { ?>
    </div>
<?php } ?>

    <?php if ($show_sticky) {
        curly_mkdf_get_sticky_header('minimal', 'header/types/header-minimal');
    } ?>

    <?php do_action('curly_mkdf_before_page_header_html_close'); ?>
</header>