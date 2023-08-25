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

namespace Krabo\IsotopeTagFieldBundle\Isotope\Model\Attribute;

use Codefog\TagsBundle\Manager\DefaultManager;
use Contao\System;
use Isotope\Collection\AttributeOption;
use Isotope\Interfaces\IsotopeAttributeWithOptions;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Model\Attribute;
use UnexpectedValueException;

class Tag extends Attribute implements IsotopeAttributeWithOptions {

  public function prepareOptionsWizard($objWidget, $arrColumns) {
    return $arrColumns;
  }

  /**
   * Returns the options source
   *
   * @return string
   */
  public function getOptionsSource() {
    return $this->optionsSource;
  }

  /**
   * Get field options
   *
   * @param IsotopeProduct $objProduct
   *
   * @return array
   */
  public function getOptionsForWidget(IsotopeProduct $objProduct = NULL) {
    return [];
  }

  /**
   * Get AttributeOption models for current attribute
   *
   * @param IsotopeProduct $objProduct
   *
   * @return AttributeOption
   */
  public function getOptionsFromManager(IsotopeProduct $objProduct = NULL) {
    throw new UnexpectedValueException(
      static::$strTable.'.'.$this->field_name . ' does not use options manager'
    );
  }

  /**
   * Return true if attribute can have prices
   *
   * @return bool
   */
  public function canHavePrices() {
    return false;
  }


  /**
   * @inheritdoc
   */
  public function saveToDCA(array &$arrData)
  {
    $this->multiple = true;
    parent::saveToDCA($arrData);
    $arrData['fields'][$this->field_name]['sql'] = 'text NULL';
    $arrData['fields'][$this->field_name]['attributes']['fe_filter'] = true;
    $arrData['fields'][$this->field_name]['eval']['csv'] = ',';
    /** @var \Codefog\TagsBundle\ManagerRegistry $managerRegistry */
    $managerRegistry = System::getContainer()->get('codefog_tags.manager_registry');
    $tagName = 'tl_iso_product.'.$this->field_name;
    $tagSource = 'tl_iso_product.'.$this->field_name;
    $managerRegistry->add(new DefaultManager($tagName, [$tagSource]), $tagName);
    //$arrData['fields'][$this->field_name]['eval']['tagsManager'] = $tagName;
    $arrData['fields'][$this->field_name]['eval']['tagsCreate'] = true;
  }

  public function getOptionsForProductFilter(array $arrValues) {
    $options = [];
    foreach($arrValues as $k=>$v) {
      $options[$k] = [
        'value' => $v,
        'label' => $v,
      ];
    }
    uasort($options, function($value1, $value2) {
      if (isset($value1['label']) && isset($value2['label'])) {
        return strcmp($value1['value'], $value2['value']);
      }
      return 0;
    });
    return $options;
  }


  /**
   * @inheritdoc
   */
  public function generate(IsotopeProduct $objProduct, array $arrOptions = array())
  {
    if ($this->rte == '') {
      return nl2br($objProduct->{$this->field_name});
    } else {
      return parent::generate($objProduct);
    }
  }

  /**
   * @inheritdoc
   */
  public function getBackendWidget()
  {
    if (!isset($GLOBALS['BE_FFL']['KraboIsotopeTags'])) {
      throw new \LogicException('Backend widget for attribute type "' . $this->type . '" does not exist.');
    }

    return $GLOBALS['BE_FFL']['KraboIsotopeTags'];
  }


}
