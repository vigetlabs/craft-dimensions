<?php
/**
 * Dimensions plugin for Craft CMS 3.x
 *
 * Replicate the Craft Commerce Dimensions fields as a standalone field type.
 *
 * @link      https://www.viget.com/
 * @copyright Copyright (c) 2020 Trevor Davis
 */

namespace viget\dimensions\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;
use craft\helpers\Json;
use craft\base\PreviewableFieldInterface;

/**
 * @author    Trevor Davis
 * @package   Dimensions
 * @since     1.0.0
 */
class DimensionsField extends Field implements PreviewableFieldInterface
{
    public $dimensionUnit = 'in';
    public $weightUnit = 'lb';

    const DIMENSION_UNITS = [
        'in' => 'Inches',
        'ft' => 'Feet',
        'mm' => 'Millimeters',
        'cm' => 'Centimeters',
        'm' => 'Meters',
    ];

    const WEIGHT_UNITS = [
        'lb' => 'Pounds',
        'g' => 'Grams',
        'kg' => 'Kilograms',
    ];

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'Dimensions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['dimensionUnit', 'string'],
            ['weightUnit', 'string'],
        ]);
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (empty($value)) return null;
        if (is_array($value)) return $value;

        return Json::decodeIfJson($value);
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'dimensions/_components/fields/settings',
            [
                'field' => $this,
                'dimensionUnits' => $this->_buildSelectOptions(self::DIMENSION_UNITS),
                'weightUnits' => $this->_buildSelectOptions(self::WEIGHT_UNITS),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        if (!$value) {
            $value = [
                'width' => null,
                'height' => null,
                'depth' => null,
                'weight' => null,
            ];
        }

        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'dimensions/_components/fields/input',
            [
                'name' => $this->handle,
                'values' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        if ($value === null) {
            return '';
        }

        $width = $value['width'] ?? null;
        $height = $value['height'] ?? null;
        $depth = $value['depth'] ?? null;
        $weight = $value['weight'] ?? null;

        $tableData = [
            $width,
            $height,
            $depth,
        ];

        $tableData = implode(' x ', array_filter($tableData));

        if ($tableData) {
            $tableData .= " {$this->dimensionUnit}";
        }

        if ($tableData && $weight) {
            $tableData .= '<br>';
        }

        if ($weight !== null) {
            $tableData .= "{$value['weight']} {$this->weightUnit}";
        }

        return $tableData ?? '';
    }

    /**
     * Build select options from key => value array
     *
     * @param array $data
     * @return array
     */
    private function _buildSelectOptions(array $data): array
    {
        $options = [
            [
                'label' => 'None',
                'value' => null,
            ],
        ];

        foreach ($data as $value => $label) {
            $options[] = [
                'label' => "{$label} ({$value})",
                'value' => $value,
            ];
        }

        return $options;
    }
}
