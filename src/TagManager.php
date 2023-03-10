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

namespace Krabo\IsotopeTagFieldBundle;

use Codefog\TagsBundle\Manager\DcaAwareInterface;
use Codefog\TagsBundle\Manager\ManagerInterface;
use Codefog\TagsBundle\Tag;
use Contao\Database;
use Contao\DataContainer;

class TagManager implements ManagerInterface, DcaAwareInterface {

  protected $source;

  protected $values = [];

  public function __construct(string $source) {
    $this->source = $source;
  }

  /**
   * Get all tags.
   */
  public function getAllTags(string $source = NULL): array {
    $values = $this->getValues($source);
    $tags = [];
    foreach($values as $v) {
      $tags[] = new Tag($v, $v);
    }
    return $tags;
  }

  /**
   * Get tags optionally filtered by values.
   */
  public function getFilteredTags(array $values, string $source = NULL): array {
    $tags = [];
    foreach($values as $v) {
      $tags[] = new Tag($v, $v);
    }
    return $tags;
  }

  /**
   * Update the DCA field.
   */
  public function updateDcaField(string $table, string $field, array &$config): void {
    // Do nothing
  }

  /**
   * Save the DCA field.
   */
  public function saveDcaField(string $value, DataContainer $dc): string {
    return $value;
  }

  /**
   * Get the filter options.
   */
  public function getFilterOptions(DataContainer $dc): array {
    return $this->getAllTags($this->source);
  }

  /**
   * Get the source records count.
   */
  public function getSourceRecordsCount(array $data, DataContainer $dc): int {
    return count($this->getValues($this->source));
  }

  /**
   * Get the top tag IDs with count.
   */
  public function getTopTagIds(): array {
    return [];
  }

  private function getValues(string $source): array {
    if (!isset($this->values[$source])) {

      [$strTable, $strField] = explode(".", $source);

      $result = Database::getInstance()
        ->execute("SELECT DISTINCT `$strTable`.`$strField` AS options FROM `$strTable`");
      $this->values[$source] = [];
      while ($result->next()) {
        if ($result->options) {
          $this->values[$source] = array_merge($this->values[$source], explode(',', $result->options));
        }
      }
    }
    $this->values[$source] = array_unique(array_filter($this->values[$source]));
    return $this->values[$source];
  }


}