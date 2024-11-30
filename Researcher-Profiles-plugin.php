<?php
/*
* Plugin Name: Researcher Profiles - Manage Academic Social Links
* Plugin URI: https://github.com/yysofiyan/Researcher-Profiles
* Author: Yanyan Sofiyan
* Author URI: https://github.com/yysofiyan
* Description: WordPress plugin designed to make it easier for academics and researchers to manage and present their profiles professionally. With this plugin, users can easily add, edit and organize their academic social links, such as Scopus, Google Scholar, ORCID, Wos profiles and more.
* Text Domain: researcher-profile-manage-academic-social-links
* License: https://www.gnu.org/licenses/gpl-3.0.html
* Version: 1.1
* Requires PHP: 7.4 
* Tested up to: WordPress 6.7
*
*/

// Menambahkan field untuk link sosial media di profil
function spp_add_social_links_fields($user) {
    ?>
    <h3>
    <?php esc_html_e("Researcher Profiles", "researcher-profiles"); ?>
</h3>
<p>
    <a href="https://github.com/yysofiyan/Researcher-Profiles" target="_blank" rel="noopener noreferrer">
    Author URI: https://github.com/yysofiyan
    </a>
</p>
    <table class="form-table">
        <?php
        $fields = [
            'social_garuda' => __("Garuda Link", "researcher-profiles"),
            'social_orcid' => __("ORCID Link", "researcher-profiles"),
            'social_scholar' => __("Google Scholar Link", "researcher-profiles"),
            'social_scopus' => __("Scopus Link", "researcher-profiles"),
            'social_publon' => __("Publon Link", "researcher-profiles"),
            'social_wos' => __("Web of Science Link", "researcher-profiles"),
            'social_github' => __("GitHub Link", "researcher-profiles"),
            'social_sinta' => __("Sinta Link", "researcher-profiles"),
        ];

        foreach ($fields as $key => $label) {
            $value = esc_url(get_the_author_meta($key, $user->ID));
            ?>
            <tr>
                <th><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
                <td>
                    <input type="url" name="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text" />
                    <?php if (!empty($value)): ?>
                        <p><a href="<?php echo esc_url($value); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Preview Link', 'researcher-profiles'); ?></a></p>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}

// Menyimpan data sosial media
function spp_save_social_links_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    $fields = ['social_garuda', 'social_orcid', 'social_scholar', 'social_scopus', 'social_publon', 'social_wos', 'social_github','social_sinta'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_user_meta($user_id, $field, esc_url_raw($_POST[$field]));
        }
    }
}

// Tambahkan ke halaman profil pengguna
add_action('show_user_profile', 'spp_add_social_links_fields');
add_action('edit_user_profile', 'spp_add_social_links_fields');
add_action('personal_options_update', 'spp_save_social_links_fields');
add_action('edit_user_profile_update', 'spp_save_social_links_fields');

// Menampilkan Author Box di artikel
function spp_display_author_box($content) {
    if (is_singular('post')) {
        $author_id = get_the_author_meta('ID');
        $author_name = get_the_author();
        $author_avatar = get_avatar_url($author_id, ['size' => 200]);  // Sesuaikan ukuran foto
        $author_bio = get_the_author_meta('description', $author_id);
        

        // Data sosial media
        $social_profiles = [
            'Garuda' => [
                'link' => get_the_author_meta('social_garuda', $author_id),
                'icon' => plugins_url('assets/icons/garuda.png', __FILE__),
            ],
            'ORCID' => [
                'link' => get_the_author_meta('social_orcid', $author_id),
                'icon' => plugins_url('assets/icons/orcid.png', __FILE__),
            ],
            'Google Scholar' => [
                'link' => get_the_author_meta('social_scholar', $author_id),
                'icon' => plugins_url('assets/icons/scholar.png', __FILE__),
            ],
            'Scopus' => [
                'link' => get_the_author_meta('social_scopus', $author_id),
                'icon' => plugins_url('assets/icons/scopus.png', __FILE__),
            ],
            'Publon' => [
                'link' => get_the_author_meta('social_publon', $author_id),
                'icon' => plugins_url('assets/icons/publon.svg', __FILE__),
            ],
            'Web of Science' => [
                'link' => get_the_author_meta('social_wos', $author_id),
                'icon' => plugins_url('assets/icons/wos.png', __FILE__),
            ],
            'GitHub' => [
                'link' => get_the_author_meta('social_github', $author_id),
                'icon' => plugins_url('assets/icons/github.png', __FILE__),
            ],
            'Sinta' => [
                'link' => get_the_author_meta('social_sinta', $author_id),
                'icon' => plugins_url('assets/icons/sinta.png', __FILE__),
            ]
        ];

        $social_links_html = '';
        foreach ($social_profiles as $platform => $data) {
            if (!empty($data['link'])) {
                $social_links_html .= '<a href="' . esc_url($data['link']) . '" target="_blank" rel="noopener noreferrer">';
                $social_links_html .= '<img src="' . esc_url($data['icon']) . '" alt="' . esc_attr($platform) . '" width="24" height="24" />';
                $social_links_html .= '</a> ';
            }
        }

        $content .= '
        <div class="author-box">
            <div class="author-avatar">
                <img src="' . esc_url($author_avatar) . '" alt="' . esc_attr($author_name) . '">
            </div>
            <div class="author-info">
                <h3>' . esc_html($author_name) . '</h3>
                <p>' . esc_html($author_bio) . '</p>
                <div class="author-social">' . $social_links_html . '</div>
            </div>
        </div>';
    }

    return $content;
}
add_filter('the_content', 'spp_display_author_box');

// Mengatur ukuran avatar default menjadi 200px
add_filter('avatar_size', function($size) {
    return 200; // Ganti dengan ukuran yang Anda inginkan
});

// Memuat file CSS
function spp_enqueue_styles() {
    wp_enqueue_style('spp-author-box-style', plugins_url('assets/css/custom-style.css', __FILE__), [], '1.1');
}
// Pastikan fungsi enqueue dipanggil dengan benar
add_action('wp_enqueue_scripts', 'spp_enqueue_styles');
// add_action('wp_enqueue_scripts', 'spp_enqueue_custom_styles');

//log
// if (function_exists('spp_enqueue_custom_styles')) {
    error_log('Fungsi spp_enqueue_custom_styles ditemukan.');
//} else {
    error_log('Fungsi spp_enqueue_custom_styles tidak ditemukan.');
//}
