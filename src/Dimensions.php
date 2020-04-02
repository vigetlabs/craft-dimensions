<?php
/**
 * Dimensions plugin for Craft CMS 3.x
 *
 * Replicate the Craft Commerce Dimensions fields as a standalone field type.
 *
 * @link      https://www.viget.com/
 * @copyright Copyright (c) 2020 Trevor Davis
 */

namespace viget\dimensions;

use viget\dimensions\fields\DimensionsField;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 * Class Dimensions
 *
 * @author    Trevor Davis
 * @package   Dimensions
 * @since     1.0.0
 *
 */
class Dimensions extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Dimensions
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = DimensionsField::class;
            }
        );

        Craft::info(
        	'Dimensions plugin loaded',
            __METHOD__
        );
    }
}
