<?php
/**
 * SendCloud plugin for Craft CMS 3.x
 *
 * Integrate Craft Commerce with SendCloud
 *
 * @link      https://www.studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace studioespresso\sendcloud;

use Craft;
use craft\base\Plugin;
use craft\commerce\elements\Order;
use craft\commerce\events\OrderStatusEvent;
use craft\commerce\events\RegisterAvailableShippingMethodsEvent;
use craft\commerce\services\ShippingMethods;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use studioespresso\sendcloud\models\SendCloudShippingMethod;
use studioespresso\sendcloud\models\Settings;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Studio Espresso
 * @package   SendCloud
 * @since     1.0.0
 *
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class SendCloud extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * SendCloud::$plugin
     *
     * @var SendCloud
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    protected function beforeInstall(): bool
    {
        if (!Craft::$app->plugins->isPluginInstalled('commerce')) {
            Craft::error(Craft::t(
                'send-cloud',
                'Failed to install. Craft Commerce is required.'
            ));
            return false;
        }
        if (!Craft::$app->plugins->isPluginEnabled('commerce')) {
            Craft::error(Craft::t(
                'send-cloud',
                'Failed to install. Craft Commerce is required.'
            ));
            return false;
        }
        return true;
    }

    // Public Methods
    // =========================================================================
    public function init()
    {
        parent::init();

        Event::on(
            Order::class,
            Order::EVENT_AFTER_COMPLETE_ORDER,
            function(Event $event) {
                if($this->getSettings()->shippingMethod != $event->sender->shippingMethodHandle) {
                    return false;
                }
            }
        );
        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );
    }

    // Protected Methods
    // =========================================================================
    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        $shippingMethods = new ShippingMethods;
        $methods = [];
        $methods[''] = '---';
        foreach ($shippingMethods->getAllShippingMethods() as $method) {
            $methods[$method->handle] = $method->name;
        }
        return Craft::$app->view->renderTemplate(
            'send-cloud/_settings/_index',
            [
                'settings' => $this->getSettings(),
                'shippingMethods' => $methods,
            ]
        );
    }
}
