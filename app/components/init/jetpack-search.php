<?php

function dd($arg) {
    var_dump($arg);
    die;
}

define('WP_CORE_STABLE_BRANCH', '5.3.2');

require(dirname(dirname(__DIR__)) . '/services/wpes/wpes-loader.php');
require(dirname(dirname(__DIR__)) . '/services/site-search/jetpack-search.php');


function es_client() {
    return \Elasticsearch\ClientBuilder::create()->setHosts([
        'http://search-wpseo-mo63zaipvkzlgwszqatolr3dsy.ap-southeast-2.es.amazonaws.com:80'
    ])->build();
}

function es_delete_index() {
    es_client()->indices()->delete(['index' => 'plugins']);
}

function es_create_index_maybe() {

    $pluginIndex = new Wordpress_Plugin_Index();
    if (!es_client()->indices()->exists([
        'index' => 'plugins'
    ])) {
        $index = [
            'index' => 'plugins',
            'body' => [
                'settings' => $pluginIndex->get_settings([]),
                'mappings' => $pluginIndex->get_mappings([]),
            ]
        ];
        es_client()->indices()->create($index);
    }
}

function es_search($query) {
    $search = Jetpack_Search::instance();
    $res = $search->convert_wp_es_to_es_args([
        'query' => $query,
    ]);

    return es_client()->search([
        'body' => $res,
        'index' => 'plugins',
        'type' => 'plugin'
    ]);
}

function es_index_plugin(\App\services\WordPressPluginService $meta) {

    $postBuilder = new Plugin_Doc_Builder();

    $post_fld_bldr = new WPES_WP_Post_Field_Builder();

    es_client()->index([
        'index' => 'plugins',
        'type' => 'plugin',
        'id' => $meta->seo['id'],
        'body' => [
            'support_threads_resolved' => $meta->meta->support_threads_resolved,
            'active_installs' => $meta->meta->active_installs,
            'tested' => $post_fld_bldr->clean_float($meta->meta->tested),
            'rating' => $meta->meta->rating,
            'all_content_en' => $postBuilder->concat_all_content([
                'title' => $meta->meta->name,
                'content' => $meta->meta->description,
                'excerpt' => $meta->meta->excerpt,
            ]),
            'title_en' => $meta->meta->name,
            'excerpt_en' => $meta->meta->excerpt,
            'description_en' => $meta->meta->description,
            'taxonomy' => [
                'plugin_tags' => [
                    collect($meta->meta->tags)->values()->map(function($value) {
                        return [ 'name' => $value ];
                    })->toArray()
                ]
            ],
            'slug_text' => $meta->meta->slug,
            'author' => $meta->meta->author,
            'contributors' => collect($meta->meta->contributors)->keys()->implode(',')
        ]
    ]);
}


es_create_index_maybe();
