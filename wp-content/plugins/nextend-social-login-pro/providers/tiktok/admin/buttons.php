<tr>
    <th scope="row"><?php _e('Button skin', 'nextend-facebook-connect'); ?></th>
    <td>
        <fieldset>
            <label>
                <input type="radio" name="skin"
                       value="light" <?php if ($settings->get('skin') == 'light') : ?> checked="checked" <?php endif; ?>>
                <span><?php _e('Light', 'nextend-facebook-connect'); ?></span><br/>
                <img alt="TikTok Light Skin" src="<?php echo plugins_url('admin/images/tiktok/light.png', NSL_PRO_PATH_PLUGIN) ?>"/>
            </label>
            <label>
                <input type="radio" name="skin"
                       value="dark" <?php if ($settings->get('skin') == 'dark') : ?> checked="checked" <?php endif; ?>>
                <span><?php _e('Dark', 'nextend-facebook-connect'); ?></span><br/>
                <img alt="TikTok Dark Skin" src="<?php echo plugins_url('admin/images/tiktok/dark.png', NSL_PRO_PATH_PLUGIN) ?>"/>
            </label><br>
        </fieldset>
    </td>
</tr>