<?php

/**
 * It may represent custom posts or custom taxonomies
 */
class WPML_ST_Slug_Custom_Type {
	/** @var string */
	private $name;

	/** @var bool */
	private $display_as_translated;

	/** @var string */
	private $slug;

	/** @var string */
	private $slug_translation;

	/**
	 * WPML_ST_Slug_Custom_Type constructor.
	 *
	 * @param string   $name
	 * @param bool     $display_as_translated
	 * @param string   $slug
	 * @param string   $slug_translation
	 */
	public function __construct( $name, $display_as_translated, $slug, $slug_translation ) {
		$this->name                  = $name ?: '';
		$this->display_as_translated = (bool) $display_as_translated;
		$this->slug                  = $slug ?: '';
		$this->slug_translation      = $slug_translation ?: '';
	}


	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return bool
	 */
	public function is_display_as_translated() {
		return $this->display_as_translated;
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * @return string
	 */
	public function get_slug_translation() {
		return $this->slug_translation;
	}

	/**
	 * @return bool
	 */
	public function is_using_tags() {
		$pattern = '#%([^/]+)%#';

		$slug = isset($this->slug) && is_string($this->slug) ? $this->slug : '';
		$slug_translation = isset($this->slug_translation) && is_string($this->slug_translation) ? $this->slug_translation : '';

		return preg_match($pattern, $slug) || preg_match($pattern, $slug_translation);
	}
}
