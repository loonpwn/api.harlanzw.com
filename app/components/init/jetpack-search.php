<?php

use App\services\WordPressPluginService;

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
    return es_client()->indices()->delete(['index' => 'plugins']);
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

function es_document($id) {
    return es_client()->get([
        'id' => $id,
        'index' => 'plugins',
        'type' => 'plugin'
    ]);
}

function es_explain($id, $query) {
    $search = Jetpack_Search::instance();
    $res = $search->convert_wp_es_to_es_args([
        'query' => $query,
    ]);

    unset($res['size'], $res['sort'], $res['filter']);

    return es_client()->explain([
        'body' => $res,
        'id' => $id,
        'index' => 'plugins',
        'type' => 'plugin'
    ]);
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

function es_index_plugin($meta) {

    $postBuilder = new Plugin_Doc_Builder();

    $post_fld_bldr = new WPES_WP_Post_Field_Builder();

    es_client()->index([
        'index' => 'plugins',
        'type' => 'plugin',
        'id' => $meta->id,
        'body' => [
            'support_threads_resolved' => $meta->support_threads_resolved,
            'active_installs' => $meta->active_installs,
            'tested' => $post_fld_bldr->clean_float($meta->tested),
            'rating' => $meta->rating,
            'all_content_en' => $postBuilder->concat_all_content(array_merge($meta->sections, [
                'title' => $meta->name,
                'excerpt' => $meta->excerpt,
            ])),
            'title_en' => $postBuilder->clean_string($meta->name),
            'excerpt_en' => $postBuilder->clean_string($meta->excerpt),
            'description_en' => $postBuilder->clean_string($meta->description),
            'taxonomy' => [
                'plugin_tags' => [
                    collect($meta->tags)->values()->map(function($value) {
                        return [ 'name' => $value ];
                    })->toArray()
                ]
            ],
            'slug_text' => $meta->slug,
            'author' => $meta->author,
            'contributors' => collect($meta->contributors)->keys()->implode(',')
        ]
    ]);
}

function es_iterate_details($details, &$results = [], $level = 0) {
    foreach($details['details'] as $detail) {
        $description = $detail['description'];
        if ($description === 'field value function: sqrt(doc[\'rating\'].value?:2.5 * factor=0.25)') {
            $results['rating'] = $details['value'];
        } else if ($description === 'field value function: log2p(doc[\'active_installs\'].value?:1.0 * factor=0.375)') {
            $results['active_installs'] = $details['value'];
        } else if ($description === 'field value function: log2p(doc[\'support_threads_resolved\'].value?:0.5 * factor=0.25)') {
            $results['support_threads_resolved'] = $details['value'];
        } else if (\Illuminate\Support\Str::startsWith($description, 'weight(taxonomy.plugin_tags.name:')) {
            $results['tags'] = ($results['tags'] ?? 0) + $details['value'];
        } else if (\Illuminate\Support\Str::startsWith($description, 'weight(title_en:')) {
            $results['title'] = ($results['title'] ?? 0) + $details['value'];
        } else if (\Illuminate\Support\Str::startsWith($description, 'weight(excerpt_en:')) {
            $results['excerpt'] = ($results['excerpt'] ?? 0) + $details['value'];
        } else if (\Illuminate\Support\Str::startsWith($description, 'weight(description:')) {
            $results['description'] = ($results['description'] ?? 0) + $details['value'];
        } else if (\Illuminate\Support\Str::startsWith($description, 'weight(all_content_en:')) {
            $results['all_content'] = ($results['all_content'] ?? 0) + $details['value'];
        }
        if (!empty($detail['details'])) {
            es_iterate_details($detail, $results, ++$level);
        }
    }
    return $results;
}

function es_decode_explain($id, $query) {
    $details = es_explain($id, $query);
    return es_iterate_details($details['explanation']);
}

es_create_index_maybe();
