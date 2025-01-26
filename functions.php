<?php
// Подключение стилей и скриптов
function yegeland_enqueue_styles()
{
    wp_enqueue_style('yegeland-style', get_template_directory_uri() . '/assets/css/style.min.css', [], '1.0.0', 'all');
    wp_enqueue_script('yegeland-scripts', get_template_directory_uri() . '/assets/js/app.js', ['jquery'], '1.0.0', true);

    // Подключение данных для AJAX
    wp_localize_script('yegeland-scripts', 'likeDislikeData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('like_dislike_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'yegeland_enqueue_styles');

// Настройка темы
function yegeland_theme_setup()
{
    // Поддержка миниатюр
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'yegeland_theme_setup');

// Разрешить загрузку SVG
function allow_svg_upload($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg_upload');

// Создание таблицы для рейтинга
function create_likes_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'post_likes';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        post_id BIGINT(20) UNSIGNED NOT NULL,
        ip_address VARCHAR(100) NOT NULL,
        vote TINYINT(1) NOT NULL,
        UNIQUE KEY post_ip (post_id, ip_address)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_setup_theme', 'create_likes_table');

// AJAX обработчик для рейтинга
function handle_like_dislike()
{
    check_ajax_referer('like_dislike_nonce', 'nonce');

    global $wpdb;
    $post_id = intval($_POST['post_id']);
    $vote = intval($_POST['vote']);
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Тест с разными ip
    // $ip_address = '192.168.' . rand(0, 255) . '.' . rand(0, 255); 

    $table_name = $wpdb->prefix . 'post_likes';

    $existing_vote = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE post_id = %d AND ip_address = %s",
        $post_id,
        $ip_address
    ));

    if ($existing_vote) {
        if ($existing_vote->vote == $vote) {
            $wpdb->delete($table_name, ['id' => $existing_vote->id]);
        } else {
            $wpdb->update($table_name, ['vote' => $vote], ['id' => $existing_vote->id]);
        }
    } else {
        $wpdb->insert($table_name, [
            'post_id' => $post_id,
            'ip_address' => $ip_address,
            'vote' => $vote,
        ]);
    }

    $rating = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(vote) FROM $table_name WHERE post_id = %d",
        $post_id
    ));

    wp_send_json(['rating' => $rating]);
}
add_action('wp_ajax_like_dislike', 'handle_like_dislike');
add_action('wp_ajax_nopriv_like_dislike', 'handle_like_dislike');
