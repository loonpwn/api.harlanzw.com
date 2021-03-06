<?php

abstract class WPES_Abstract_Document_Builder {

	abstract public function get_id($args);
	abstract public function get_type($args);
	abstract public function doc($args);
	abstract public function update($args);
	abstract public function is_indexable($args);

	public function filter_index_name($index_name, $args) {
		return $index_name;
	}

	public function get_parent_id($args) {
		return false;
	}

	public function get_routing_id($args) {
		return $this->get_parent_id($args);
	}

	//Get the list of related (often child) docs that should be
	// reindexed when one of these docs gets reindexed
	// return array( 'post' => array( 1, 2, 3 ), 'comment' => array( 4, 5, 6 ) )
	public function get_coupled_docs($args) {
		return false;
	}

	//Is this entire set of docs disabled from being indexed
	// eg this blog is spam, so none of its posts should be indexed
	public function is_indexing_enabled($args) {
		return true;
	}


}
