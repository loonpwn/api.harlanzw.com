<?php

use App\models\WPASearch;
use Illuminate\Support\Str;

if (empty($_GET['action']) || $_GET['action'] !== 'plugin-search') {
    return;
}

$plugin = $_GET['plugin-url'];
$search_term = strtolower($_GET['search-term']);


require ABSPATH . '/wp-admin/includes/plugin-install.php';

global $wpa_output;

$log = '';
$recommendations = [];

$log .= 'Getting score for plugin ' . $plugin . ' with search term: "' . $search_term . '"<br>';
/** Prepare our query */
$call_api = plugins_api('plugin_information', array('slug' => $plugin, 'fields' => [
    'description' => true,
    'reviews' => true,
    'banners' => true,
    'icons' => true,
    'active_installs' => true,
    'group' => true,
    'contributors' => true,
]));

WPASearch::create([
    'post_title' => 'Plugin: ' . $plugin,
    'post_content' => $search_term
]);

if ($call_api instanceof WP_Error) {
    $wpa_output = [
        'error' => 'Invalid plugin slug provided.',
    ];
    return;
}

$installs = json_decode(file_get_contents('https://api.wordpress.org/stats/plugin/1.0/downloads.php?slug=' . $plugin . '&historical_summary=13'))->all_time;

// get the readme.txt file
$readme = new \App\Helpers\ReadmeParser('https://plugins.svn.wordpress.org/' . $plugin . '/trunk/readme.txt');

$call_api->excerpt = $readme->short_description;
// Print the entire match result
$call_api->description = $call_api->sections['description'];

/** Check for Errors & Display the results */

$total_points = 0;

$title = $call_api->name;
$slug = $call_api->slug;
$sections = $call_api->sections;
$sections_html = collect($sections)->implode('\n');
$call_api->plugin_tags = collect($call_api->tags)->implode(' ');
$call_api->all_content = implode('\n', [$title, $slug, $sections_html]);

function tokenize($string) {
    mb_internal_encoding('UTF-8');
    $sentences = preg_split('/[^\s|\pL]/umi', $string, -1, PREG_SPLIT_NO_EMPTY);
    $sentence = $sentences[0];
    $words = array_map(
        'mb_strtolower',
        preg_split('/[^\pL+]/umi', $sentence, -1, PREG_SPLIT_NO_EMPTY)
    );
    return $words;
}

$call_api->contributors = implode(' ', array_keys($call_api->contributors));
$call_api->title_ngram = tokenize($call_api->name);

$matching_fields = array(
    'all_content'
);
$boost_phrase_fields = array(
    'name',
    'excerpt',
    'description',
    'plugin_tags',
);
$boost_ngram_fields = array(
    'title_ngram'
);
$boost_title_fields = array(
    'name',
    'slug',
);
$boost_content_fields = array(
    'excerpt',
    'description',
    'plugin_tags',
);

/*
  best_fields

(default) Finds documents which match any field, but uses the _score from the best field. See best_fields.

most_fields

Finds documents which match any field and combines the _score from each field. See most_fields.

cross_fields

Treats fields with the same analyzer as though they were one big field. Looks for each word in any field. See cross_fields.

phrase

Runs a match_phrase query on each field and combines the _score from each field. See phrase and phrase_prefix.

phrase_prefix

Runs a match_phrase_prefix query on each field and combines the _score from each field. See phrase and phrase_prefix.

 */

if (Str::contains(strtolower($call_api->all_content), $search_term)) {
    $log .= 'Required Search Term is in content - 0.1 point for having the search term in content <br>';
    $total_points += 0.1;
} else {
    $log .= 'Search term was NOT in content - Score is 0 <br>';
    $recommendations[] = 'Add ' . $search_term . ' to the readme';
}


// Boost phrase fields
foreach ($boost_phrase_fields as $field) {
    if (Str::contains(strtolower($call_api->$field), $search_term)) {
        $total_points += 2;
        $log .= 'Term is within field: ' . $field . ' - 2 Point boost!<br>';
    } else {
        $log .= 'Term is not within field: ' . $field . ' - 0 Point boost<br>';
        $recommendations[] = 'Add ' . $search_term . ' to ' . $field . '. Currently: ' . $call_api->$field;
    }
}

// boost ngram fields
foreach ($boost_ngram_fields as $field) {
    foreach ($call_api->$field as $token) {
        if (Str::contains($token, $search_term)) {
            $total_points += 0.2;
            $log .= 'Term is within ngram title : ' . $token . ' - 0.2 Point boost!<br>';
        } else {
            $log .= 'Term is not within ngram title : ' . $token . ' - 0 Point boost<br>';
        }
    }
}

// boost title fields
$allocate = false;
foreach ($boost_title_fields as $field) {
    // checks if either the title OR the slug have the term
    if (Str::contains(strtolower($call_api->$field), $search_term)) {
        $allocate = true;
    }
}
if ($allocate) {
    $total_points += 2;
    $log .= 'Term is title or slug - 2 Point boost!<br>';
} else {
    $recommendations[] = 'Add ' . $search_term . ' to title or slug of plugin';
    $log .= 'Term is not in title or slug - 0 Point boost<br>';
}

// boost content fields
$allocate = false;
foreach ($boost_content_fields as $field) {
    // checks if either the title OR the slug have the term
    if (Str::contains(strtolower($call_api->$field), $search_term)) {
        $allocate = true;
    }
}
if ($allocate) {
    $total_points += 2;
    $log .= 'Term is within content - 2 Point boost!<br>';
} else {
    $recommendations[] = 'Add ' . $search_term . ' to excerpt, description or plugin tags';
    $log .= 'Term is not within content - 0 Point boost<br>';
}

// boost content fields
$allocate = false;
foreach (['author', 'contributors'] as $field) {
    // checks if either the title OR the slug have the term
    if (Str::contains(strtolower($call_api->$field), $search_term)) {
        $allocate = true;
    }
}
if ($allocate) {
    $total_points += 2;
    $log .= 'Term is within author or contributors - 2 Point boost!<br>';
} else {
    $recommendations[] = 'Add ' . $search_term . ' to author or contributors name. Currently: ' . $call_api->author . ' ' . $call_api->contributors;
    $log .= 'Term is not within author or contributors - 0 Point boost<br>';
}

$max_points = 14.3;

$log .= 'Appearing in search results with points: <strong>' . $total_points . '</strong>. Now applying filtering of results.<br>';

$log .= 'For the plugin modification time, for every 180 days the plugin hasn\'t been modified we half our score <br>';

$log .= 'For every 0.4 versions behind the current core version <br>';


$log .= 'For every install we have, increase the points. Active installs are: ' . $call_api->active_installs . '<br>';
if (empty($call_api->active_installs)) {
    $call_api->active_installs = 1;
}
$pre_score = $total_points;
$score = log(2 + 0.375 * $call_api->active_installs);
// no change if no active installs
$total_points *= $score;
$max_points *= $score;
$log .= 'Score from ' . $pre_score . ' -> ' . $total_points . ' after applying active installs <br>';

$resolved_percentage = $call_api->support_threads_resolved / $call_api->support_threads;
$log .= 'We look at support requests: ' . $call_api->support_threads_resolved . '/' . $call_api->support_threads . '  Resolved %: ' . $resolved_percentage . '<br>';
if (is_nan($resolved_percentage)) {
    $resolved_percentage = 0.5;
}
$pre_score = $total_points;
// no change if no active installs
$score = log(2 + 0.25 * $resolved_percentage);
$total_points *= $score;
$max_points *= $score;
$log .= 'Score from ' . $pre_score . ' -> ' . $total_points . ' after applying support threads <br>';


$five_star_rating = ($call_api->rating / 20);
$log .= 'We look at the ratings: ' . $call_api->rating . '(' . $five_star_rating . ')<br>';
if (!empty($call_api->rating)) {
    $call_api->rating = 2.5;
}
$score = sqrt(0.25 * $five_star_rating);
$pre_score = $total_points;
// no change if no active installs
$total_points *= $score;
$max_points *= $score;
$log .= 'Score from ' . $pre_score . ' -> ' . $total_points . ' after applying ratings <br>';


if ($call_api->active_installs < 10000) {
    $recommendations[] = 'Increase active installs. Currently: ' . $call_api->active_installs;
}
if ($five_star_rating <= 4) {
    $recommendations[] = 'Increase positive ratings. Currently: ' . $five_star_rating;
}

$resolved_percentage = round($resolved_percentage * 100, 2);

if ($resolved_percentage <= 75) {
    $recommendations[] = 'Increase support threads resolved. Currently: ' . $call_api->support_threads_resolved . '/' . $call_api->support_threads . '  Resolved: ' . $resolved_percentage . '%';
}

//old_score * log(1 + factor * number_of_votes)

$log .= '<strong>Final Score is: ' . (int)$total_points . '. Can achieve a total score of: ' . (int)$max_points . '</strong>';

$wpa_output = [
    'log' => $log,
    'score' => round($total_points, 2),
    'max_score' => round($max_points, 2),
    'recommendations' => $recommendations
];
