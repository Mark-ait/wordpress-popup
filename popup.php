<?php
/*
Plugin Name: 弹窗插件
Description: 一个简单的弹窗插件
Version: 1.0
Author: Oaklee
Plugin URI: https://ooize.com
Author URI: https://ooize.com
*/

// 插件初始化
function custom_popup_enqueue_scripts() {
    ?>
    <style>
   /* 前台弹窗样式 */
    #custom-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    .custom-popup-content {
        background-color: #ffffff;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        max-width: 500px;
        width: 90%;
        margin: auto;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, opacity 0.3s ease;
        transform: translateY(-10px);
        opacity: 0;
    }
    #custom-popup.show .custom-popup-content {
        transform: translateY(0);
        opacity: 1;
    }
    .custom-popup-content img.popup-image {
        width: 100%;
        height: auto;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    #popup-button {
        background-color: #002FA7;
        border: none;
        color: #fff;
        padding: 10px 40px;
        text-align: center;
        text-decoration: none;
        font-size: 16px;
        border-radius: 16px;
        margin: 10px 5px;
        transition: background-color 0.4s;
        cursor: pointer;
    }
    #popup-button:hover {
        background-color: #002fa7;
    }
    </style>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    var popup = document.getElementById('custom-popup');
    var okButton = document.getElementById('popup-button');

    if (popup && okButton && !document.cookie.includes('custom_popup_shown=1')) {
        popup.classList.add('show');
        popup.style.display = 'flex';

        okButton.addEventListener('click', function() {
            popup.style.display = 'none';
            document.cookie = "custom_popup_shown=1; max-age=86400; path=/";
        });
    }
});
</script>

    <?php
}
add_action('wp_head', 'custom_popup_enqueue_scripts');

// 创建后台菜单
function custom_popup_menu() {
    add_menu_page('弹窗设置', '弹窗设置', 'manage_options', 'custom-popup-settings', 'custom_popup_settings_page', 'dashicons-admin-generic', 80);
}
add_action('admin_menu', 'custom_popup_menu');

// 后台设置页面内容
function custom_popup_settings_page() {
    ?>
    <div class="wrap">
        <h1>弹窗设置</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_popup_options_group');
            do_settings_sections('custom-popup-settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Popup Title</th>
                    <td><input type="text" name="custom_popup_title" value="<?php echo esc_attr(get_option('custom_popup_title')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Popup Content</th>
                    <td>
                        <?php
                        wp_editor(
                            get_option('custom_popup_content'), // 默认内容
                            'custom_popup_content', // 编辑器 ID
                            array(
                                'textarea_name' => 'custom_popup_content', // 表单字段名
                                'textarea_rows' => 10, // 行数
                                'teeny' => true, // 简化工具栏
                            )
                        );
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Popup Image URL</th>
                    <td><input type="text" name="custom_popup_image" value="<?php echo esc_attr(get_option('custom_popup_image')); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <style>
    /* 后台设置页面样式 */
    .wrap {
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .form-table th {
        width: 25%;
        padding: 12px;
        text-align: left;
        background-color: #f1f1f1;
        border: 1px solid #ddd;
        border-radius: 5px 0 0 5px;
    }
    .form-table td {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 0 5px 5px 0;
    }
    .regular-text, .large-text {
        width: 100%;
        box-sizing: border-box;
    }
    .regular-text {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 8px;
    }
    .large-text {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 8px;
        min-height: 150px;
    }
    .wp-editor-area {
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    </style>
    <?php
}


// 注册插件设置
function custom_popup_register_settings() {
    register_setting('custom_popup_options_group', 'custom_popup_title');
    register_setting('custom_popup_options_group', 'custom_popup_content');
    register_setting('custom_popup_options_group', 'custom_popup_image');
}
add_action('admin_init', 'custom_popup_register_settings');

// 显示弹窗
function display_custom_popup() {
    if (!isset($_COOKIE['custom_popup_shown'])) {
        ?>
        <div id="custom-popup" class="custom-popup">
            <div class="custom-popup-content">
                <img src="<?php echo esc_attr(get_option('custom_popup_image')); ?>" alt="Popup Image" class="popup-image" />
                <h2><?php echo esc_html(get_option('custom_popup_title')); ?></h2>
                <p><?php echo wp_kses_post(get_option('custom_popup_content')); ?></p>
                <button id="popup-button">OK</button>
            </div>
        </div>
        <?php
    }
}
add_action('wp_footer', 'display_custom_popup');
?>
