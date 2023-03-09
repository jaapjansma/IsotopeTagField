<?php
/**
 * Copyright (C) 2023  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Krabo\IsotopeTagFieldBundle\Backend\Widget;

use Codefog\TagsBundle\Widget\TagsWidget;
use Haste\Util\Debug;
use Krabo\IsotopeTagFieldBundle\TagManager;

class IsoTagWidget extends TagsWidget {

  /**
   * @var string
   */
  protected $tagsSource;

  public function addAttributes($attributes = null) {
    $this->addAssets();
    $this->tagsSource = $this->strTable . '.' . $this->strField;
    $this->tagsManager = new TagManager($this->tagsSource);
    $attributes['tagsManager'] = NULL;
    parent::addAttributes($attributes);
  }

  private function addAssets() {
    $GLOBALS['TL_CSS'][] = Debug::uncompressedFile('bundles/codefogtags/selectize.min.css');
    $GLOBALS['TL_CSS'][] = Debug::uncompressedFile('bundles/codefogtags/backend.min.css');

    // Add the jQuery
    if (!isset($GLOBALS['TL_JAVASCRIPT']) || !preg_grep("/^assets\/jquery\/js\/jquery(\.min)?\.js$/", $GLOBALS['TL_JAVASCRIPT'])) {
      $GLOBALS['TL_JAVASCRIPT'][] = Debug::uncompressedFile('assets/jquery/js/jquery.min.js');
    }

    // Add jQuery UI to make the widget sortable if needed
    // @see https://jqueryui.com/download/#!version=1.12.1&themeParams=none&components=101000000100000010000000010000000000000000000000
    $GLOBALS['TL_CSS'][] = Debug::uncompressedFile('bundles/codefogtags/jquery-ui.min.css');
    $GLOBALS['TL_JAVASCRIPT'][] = Debug::uncompressedFile('bundles/codefogtags/jquery-ui.min.js');

    $GLOBALS['TL_JAVASCRIPT'][] = Debug::uncompressedFile('bundles/codefogtags/selectize.min.js');
    $GLOBALS['TL_JAVASCRIPT'][] = Debug::uncompressedFile('bundles/codefogtags/widget.min.js');
    $GLOBALS['TL_JAVASCRIPT'][] = Debug::uncompressedFile('bundles/codefogtags/backend.min.js');
  }

}