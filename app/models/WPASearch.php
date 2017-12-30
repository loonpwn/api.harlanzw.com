<?php
namespace App\models;

use App\Helpers\WPPost;

/**
 * Class WPASearch
 * @package App\Models
 *
 * @property string $post_content This is the content of the message
 * @property int $WPASearch_id User ID of who receives the message. It is always required
 * @property int|null $from User ID of who has sent the message
 * @property string|null $from_source If the message is from someone or something we identity the source type here
 */
class WPASearch extends WPPost {

    const MESSAGE_TYPE = '';

    const SLUG = 'wpa-search';

    public static $slug = self::SLUG;


    /**
     * Setup our model
     */
    public static function setup() {
        self::setup_cpt();
    }

    private static function setup_cpt() {
        // CPT args.
        $args = [
            'labels'              => [
                'name' => esc_html__('WPASearches', 'wpestate'),
                'singular_name' => esc_html__('WPASearch', 'wpestate'),
            ],
            // labels are not applicable since they have no UI
            'supports'            => ['custom-fields', 'editor'],
            // support for nothing
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            // do not show
            'show_in_admin_bar'   => true,
            // do not show
            'show_in_nav_menus'   => true,
            // do not show
            'can_export'          => true,
            'has_archive'         => false,
            // will visting /my-slug/ show all posts of that type?
            'exclude_from_search' => true,
            // Should our post type appear when users search using the search form
            'publicly_queryable'  => false,
            // this allows weird things like accessing your post type by doing ?post-type=23545. This should be false unless you know what you're doing
            'rewrite'             => [],
            // not public
            'capability_type'     => 'page',
            // This is for permissions
            'show_in_rest'        => false,
            // keep off
        ];

        // Registers the CPT.
        register_post_type(self::SLUG, $args);
    }

}