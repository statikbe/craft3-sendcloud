<?php
/**
 * SendCloud plugin for Craft CMS 3.x
 *
 * Integrate Craft Commerce with SendCloud
 *
 * @link      https://www.statik.be
 * @copyright Copyright (c) 2018 Statik
 */

namespace statikbe\sendcloud;

use Craft;
use craft\base\Plugin;
use craft\commerce\elements\Order;
use craft\commerce\events\OrderStatusEvent;
use craft\commerce\events\RegisterAvailableShippingMethodsEvent;
use craft\commerce\services\ShippingMethods;
use craft\log\FileTarget;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use statikbe\sendcloud\base\PluginTrait;
use statikbe\sendcloud\models\SendCloudShippingMethod;
use statikbe\sendcloud\models\Settings;
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
 * @author    Statik
 * @package   SendCloud
 * @since     1.0.0
 *
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class SendCloud extends Plugin
{
    // Public Properties
    // =========================================================================
    public $schemaVersion = '1.0.0';

    // Traits
    // =========================================================================
    use PluginTrait;

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

        self::$plugin = $this;
        $this->setPluginComponents();

        $fileTarget = new FileTarget([
            'logFile' => Craft::$app->path->getLogPath() . '/sendcloud.log',
            'categories' => ['sendcloud']
        ]);
        Craft::getLogger()->dispatcher->targets[] = $fileTarget;

        Event::on(
            Order::class,
            Order::EVENT_AFTER_COMPLETE_ORDER,
            function(Event $event) {
                if($this->getSettings()->shippingMethod != $event->sender->shippingMethodHandle) {
                    return false;
                }
                SendCloud::info('Order matched, off to Sendcloud');
                SendCloud::$plugin->api->createPackage($order);

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

    public static function info($message)
    {
        Craft::getLogger()->log($message, \yii\log\Logger::LEVEL_INFO, 'sendcloud');
    }
    
    public static function warning($message)
    {
        Craft::getLogger()->log($message, \yii\log\Logger::LEVEL_WARNING, 'sendcloud');
    }

    public static function error($message)
    {
        Craft::getLogger()->log($message, \yii\log\Logger::LEVEL_ERROR, 'sendcloud');
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
