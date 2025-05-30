<?php

use WPML\Element\API\Languages;
use WPML\FP\Logic;
use WPML\FP\Lst;
use WPML\User\LanguagePairs\ILanguagePairs;

/**
 * Class WPML_Language_Pair_Records
 *
 * Language pairs are stored as user meta as an array of the form
 * array( $from_lang => array( $to_lang_1 => '1', $to_lang_2 => '1' )
 */
class WPML_Language_Pair_Records implements ILanguagePairs {

	private $meta_key;

	/** @var WPML_Language_Records $language_records */
	private $language_records;

	/** @var array|null  */
	private $active_language_codes;

	/**
	 * @param wpdb                  $wpdb
	 * @param WPML_Language_Records $language_records
	 * @param array|null            $active_language_codes
	 */
	public function __construct( wpdb $wpdb, WPML_Language_Records $language_records, $active_language_codes = null ) {
		$this->meta_key              = $wpdb->prefix . 'language_pairs';
		$this->language_records      = $language_records;
		$this->active_language_codes = $active_language_codes ?: Lst::pluck( 'code', Languages::getActive() );
	}

	/**
	 * @param int   $user_id
	 * @param array $language_pairs
	 *
	 * Language pairs are an array of the form
	 * array( $from_lang => array( $to_lang_1, $to_lang_2 )
	 */
	public function store( $user_id, $language_pairs ) {
		$language_pairs = $this->convert_to_storage_format( $language_pairs );
		update_user_meta( $user_id, $this->meta_key, $language_pairs );
	}

	/**
	 * @param int   $user_id
	 * @param array $language_pairs
	 *
	 * Stores only the language pairs that are active.
	 */
	public function store_active( $user_id, $language_pairs ) {
		$language_pairs = wpml_collect( $language_pairs )
			->mapWithKeys(
				function( $to, $from ) {
					if ( ! $this->active_language_codes || ! in_array( $from, $this->active_language_codes, true ) ) {
						return [];
					}
					return [ $from => array_intersect( $to, $this->active_language_codes ) ];
				}
			)
			->filter( Logic::complement( Logic::isEmpty() ) )
			->toArray();
		$this->store( $user_id, $language_pairs );
	}

	/**
	 * @param int $user_id
	 */
	public function remove_all( $user_id ) {
		delete_user_meta( $user_id, $this->meta_key );
	}

	/**
	 * @param int $user_id
	 * @return array
	 *
	 * Language pairs are returned in an array of the form
	 * array( $from_lang => array( $to_lang_1, $to_lang_2 )
	 */
	public function get( $user_id ) {
		$language_pairs = get_user_meta( $user_id, $this->meta_key, true );
		if ( ! $language_pairs ) {
			$language_pairs = array();
		}
		return $this->convert_from_storage_format( $language_pairs );
	}

	public function convert_to_storage_format( $language_pairs ) {
		if ( $this->is_in_storage_format( $language_pairs ) ) {
			return $language_pairs;
		}

		foreach ( $language_pairs as $from => $to_langs ) {
			$targets = array();
			foreach ( $to_langs as $lang ) {
				$targets[ $lang ] = 1;
			}
			$language_pairs[ $from ] = $targets;
		}

		return $language_pairs;
	}

	private function is_in_storage_format( $language_pairs ) {
		$first_from = reset( $language_pairs );

		if ( $first_from && ! is_numeric( key( $first_from ) ) ) {
			return true;
		}

		return false;
	}

	private function convert_from_storage_format( array $language_pairs ) {
		foreach ( $language_pairs as $from => $to_langs ) {
			if ( $this->language_records->is_valid( $from ) ) {
				$language_pairs[ $from ] = array();
				foreach ( array_keys( $to_langs ) as $lang ) {
					if ( $this->language_records->is_valid( $lang ) ) {
						$language_pairs[ $from ][] = $lang;
					}
				}
			} else {
				unset( $language_pairs[ $from ] );
			}
		}
		return $language_pairs;
	}

	/**
	 * @param int $user_id
	 */
	public function remove_invalid_language_pairs( $user_id ) {
		$language_pairs = $this->get( $user_id );
		$this->store( $user_id, $language_pairs );
	}

}
