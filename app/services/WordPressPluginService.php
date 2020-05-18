<?php
namespace App\services;

use App\models\WPASearch;
use Illuminate\Support\Str;
use IvoPetkov\HTML5DOMDocument;
use IvoPetkov\HTML5DOMElement;
use WP_Error;

class WordPressPluginService {

    public $slug;
    public $seo;
    public $meta;

    const QUERY_PLUGIN_ENDPOINT = 'https://wordpress.org/plugins/wp-json/plugins/v1/query-plugins/';

    /**
     * WordPressPluginService constructor.
     */
    public function __construct($slug) {
        $this->slug = $slug;
    }

    public function fetch_all() {
        $this->get_plugin_meta();
        $this->get_seo();
        return $this;
    }

    public function es_index() {
        return es_index_plugin($this->meta);
    }

    public function get_seo() {
        $seo = (new \App\services\Seo())->analyze('https://wordpress.org/plugins/' . $this->slug . '/');
        $this->seo = $seo;
        $this->meta->id = str_replace('post-', '', $seo['id']);
        return $seo;
    }

    public function get_plugin_meta() {
        WPASearch::create([
            'post_title' => 'Search: ' . $this->slug
        ]);

        require_once ABSPATH . '/wp-admin/includes/plugin-install.php';

        /** Prepare our query */
        $this->meta = plugins_api('plugin_information', array('slug' => $this->slug, 'fields' => [
            'description' => true,
            'reviews' => true,
            'banners' => true,
            'icons' => true,
            'active_installs' => true,
            'group' => true,
            'contributors' => true,
        ]));

        if ($this->meta instanceof WP_Error) {
            return false;
        }

//        $installs = json_decode(file_get_contents('https://api.wordpress.org/stats/plugin/1.0/downloads.php?slug=' . $this->slug . '&historical_summary=13'))->all_time;
        $readme_file = [
            'readme.txt',
            'README.txt',
            'README.MD'
        ];

        foreach ($readme_file as $file) {
            $url = 'https://plugins.svn.wordpress.org/' . $this->slug . '/trunk/' . $file;
            if (@file_get_contents($url)) {
                $readme = new \App\Helpers\ReadmeParser($url);
                break;
            }
        }

        if (empty($readme)) {
            return $this->meta;
        }

        $this->meta->excerpt = $readme->short_description;
        // Print the entire match result
        $this->meta->description = $this->meta->sections['description'];
        return $this->meta;
    }

    protected function tokenize($string) {
        mb_internal_encoding('UTF-8');
        $sentences = preg_split('/[^\s|\pL]/umi', $string, -1, PREG_SPLIT_NO_EMPTY);
        $sentence = $sentences[0];
        $words = array_map(
            'mb_strtolower',
            preg_split('/[^\pL+]/umi', $sentence, -1, PREG_SPLIT_NO_EMPTY)
        );
        return $words;
    }

    public function get_plugin_recommendations() {


        $recommendations[] = 'Always try to improve your active installs. These will scale up all search terms the most.';

        $five_star_rating = ($this->meta->rating / 20);
        if ($five_star_rating != 5) {
            $recommendations[] = 'Higher ratings will scale results accordingly. Currently: ' . $five_star_rating;
        }

        if ($this->meta->support_threads_resolved > 0) {
            $resolved_percentage = round($this->meta->support_threads_resolved / $this->meta->support_threads, 2) * 100;
            if ($resolved_percentage <= 75) {
                $recommendations[] = 'Increase support threads resolved. These will scale rankings. Currently: ' . $this->meta->support_threads_resolved . '/' . $this->meta->support_threads . '  Resolved: ' . $resolved_percentage . '%';
            }
        } else {
            $recommendations[] = 'Have at least one resolved support thread';
        }
        return $recommendations;
    }

    public function get_search_term_rank($search_term) {
        $rank = null;

        $results = [];

        $ch = curl_init();

        for ($page = 1; $page <= 5; $page++) {
            curl_setopt($ch, CURLOPT_URL, 'https://wordpress.org/plugins/search/' . Str::slug($search_term) . '/page/' . $page);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/79.0.3945.79 Chrome/79.0.3945.79 Safari/537.36');
            $html = curl_exec($ch);

            $dom = new HTML5DOMDocument();
            $dom->loadHTML($html, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);

            $elements = $dom->querySelectorAll('.plugin-card');

            /** @var HTML5DOMElement $childNode */
            $i = 0;
            foreach ($elements as $childNode) {
                $i++;
                $results[$i] = str_replace('/', '', str_replace(['https://en-au.wordpress.org/plugins/', 'https://wordpress.org/plugins/'], '', $childNode->querySelector('a')->getAttribute('href')));
                if ($childNode->classList->contains($this->seo['id'])) {
                    $rank = $i;
                    break;
                }
            }

            $pageNumbers = $dom->querySelectorAll('.page-numbers');

            if ($pageNumbers->length <= 0) {
                $total = $elements->length;
                break;
            }

            $total = $pageNumbers->item($pageNumbers->length - 2)->innerHTML * 20;
            if (is_int($rank)) {
                break;
            }
            // 500 ms
            usleep(500000);
        }
        curl_close($ch);

        return [
            'results' => $results,
            'total' => $total,
            'rank' => $rank ?? 'Not Found'
        ];
    }

    public function get_search_term_score($search_term) {

        $log = '';
        $total_points = 0;

        $title = $this->meta->name;
        $slug = $this->meta->slug;
        $sections = $this->meta->sections;
        $sections_html = collect($sections)->implode('\n');
        $this->meta->plugin_tags = collect($this->meta->tags)->implode(' ');
        $this->meta->all_content = implode('\n', [$title, $slug, $sections_html]);


        $this->meta->contributors = implode(' ', array_keys($this->meta->contributors));
        $this->meta->title_ngram = $this->tokenize($this->meta->name);

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
        $recommendations = [];

        if (Str::contains(strtolower($this->meta->all_content), $search_term)) {
            $log .= 'Required Search Term is in content - 0.1 point for having the search term in content <br>';
            $total_points += 0.1;
        } else {
            $log .= 'Search term was NOT in content - Score is 0 <br>';
            $recommendations[] = 'Add ' . $search_term . ' anyway in your readme file.';
        }


// Boost phrase fields
        foreach ($boost_phrase_fields as $field) {
            if (Str::contains(strtolower($this->meta->$field), $search_term)) {
                $total_points += 2;
                $log .= 'Term is within field: ' . $field . ' - 2 Point boost!<br>';
            } else {
                $log .= 'Term is not within field: ' . $field . ' - 0 Point boost<br>';
                $recommendations[] = 'Add ' . $search_term . ' to ' . $field . '.';
            }
        }

// boost ngram fields
        foreach ($boost_ngram_fields as $field) {
            foreach ($this->meta->$field as $token) {
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
            if (Str::contains(strtolower($this->meta->$field), $search_term)) {
                $allocate = true;
            }
        }
        if ($allocate) {
            $total_points += 2;
            $log .= 'Term is title or slug - 2 Point boost!<br>';
        } else {
            $recommendations[] = 'Add ' . $search_term . ' to title or slug of plugin.';
            $log .= 'Term is not in title or slug - 0 Point boost<br>';
        }

// boost content fields
        $allocate = false;
        foreach ($boost_content_fields as $field) {
            // checks if either the title OR the slug have the term
            if (Str::contains(strtolower($this->meta->$field), $search_term)) {
                $allocate = true;
            }
        }
        if ($allocate) {
            $total_points += 2;
            $log .= 'Term is within content - 2 Point boost!<br>';
        } else {
            $recommendations[] = 'Add ' . $search_term . ' to excerpt, description or plugin tags. Having one provides a boost.';
            $log .= 'Term is not within content - 0 Point boost<br>';
        }

// boost content fields
        $allocate = false;
        foreach (['author', 'contributors'] as $field) {
            // checks if either the title OR the slug have the term
            if (Str::contains(strtolower($this->meta->$field), $search_term)) {
                $allocate = true;
            }
        }
        if ($allocate) {
            $total_points += 2;
            $log .= 'Term is within author or contributors - 2 Point boost!<br>';
        } else {
            $recommendations[] = 'Add ' . $search_term . ' to author or contributors name.';
            $log .= 'Term is not within author or contributors - 0 Point boost<br>';
        }

        $max_points = 14.3;

        $log .= 'Appearing in search results with points: <strong>' . $total_points . '</strong>. Now applying filtering of results.<br>';

        $log .= 'For the plugin modification time, for every 180 days the plugin hasn\'t been modified we half our score <br>';

        $log .= 'For every 0.4 versions behind the current core version <br>';


        $log .= 'For every install we have, increase the points. Active installs are: ' . $this->meta->active_installs . '<br>';
        if (empty($this->meta->active_installs)) {
            $this->meta->active_installs = 1;
        }
        $pre_score = $total_points;
        $score = log(2 + 0.375 * $this->meta->active_installs);
// no change if no active installs
        $total_points *= $score;
        $max_points *= $score;
        $log .= 'Score from ' . $pre_score . ' -> ' . $total_points . ' after applying active installs <br>';

        if ($this->meta->support_threads_resolved > 0) {
            $resolved_percentage = $this->meta->support_threads_resolved / $this->meta->support_threads;
            $log .= 'We look at support requests: ' . $this->meta->support_threads_resolved . '/' . $this->meta->support_threads . '  Resolved %: ' . $resolved_percentage . '<br>';
        } else {
            $resolved_percentage = 0.5;
            $log .= 'No support threads resolved. Setting base to 50% resolved.<br>';
            $recommendations[] = 'Have at least one resolved support thread';
        }
        $pre_score = $total_points;
// no change if no active installs
        $score = log(2 + 0.25 * $resolved_percentage);
        $total_points *= $score;
        $max_points *= $score;
        $log .= 'Score from ' . $pre_score . ' -> ' . $total_points . ' after applying support threads <br>';

        if ((int)$this->meta->rating === 0) {
            $log .= 'No ratings. Setting base rating to 2.5 <br>';
            $this->meta->rating = 50;
        }
        $five_star_rating = ($this->meta->rating / 20);
        $log .= 'We look at the ratings: ' . $this->meta->rating . '(' . $five_star_rating . ')<br>';

        $score = sqrt(0.25 * $five_star_rating);
        $pre_score = $total_points;
// no change if no active installs
        $total_points *= $score;
        $max_points *= $score;
        $log .= 'Score from ' . $pre_score . ' -> ' . $total_points . ' after applying ratings <br>';

//old_score * log(1 + factor * number_of_votes)

        $log .= '<strong>Final Score is: ' . (int)$total_points . '. Can achieve a total score of: ' . (int)$max_points . '</strong>';

        $score = round($total_points, 2);
        $max_score = round($max_points, 2);

        $percentage = round($score / $max_score, 2) * 100;

        $colour = 'success';
        if ($percentage >= 50 && $percentage <= 90) {
            $colour = 'warning';
        } elseif ($percentage < 50) {
            $colour = 'danger';
        }

        return [
            'keyword' => $search_term,
            'colour' => $colour,
            'log' => $log,
            'score' => $score,
            'percentage' => $percentage,
            'max_score' => $max_score,
            'recommendations' => $recommendations,
            'rank' => $this->get_search_term_rank($search_term)
        ];
    }


}
