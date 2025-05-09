<?php

namespace WPML\Compatibility\Divi\Hooks;

use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\FP\Relation;
use WPML\LIB\WP\Hooks;
use function WPML\FP\spreadArgs;

class Editor implements \IWPML_Backend_Action {

	public function add_hooks() {
		Hooks::onFilter( 'wpml_pb_is_editing_translation_with_native_editor', 10, 2 )
			->then(
				spreadArgs(
					function( $isTranslationWithNativeEditor, $translatedPostId ) {
						return $isTranslationWithNativeEditor
						|| (
							Relation::propEq( 'action', 'et_fb_ajax_save', $_POST )
							&& (int) Obj::prop( 'post_id', $_POST ) === $translatedPostId
						  );
					}
				)
			);
	}
}
