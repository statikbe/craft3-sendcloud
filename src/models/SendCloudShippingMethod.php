<?php
/**
 * SendCloud plugin for Craft CMS 3.x
 *
 * Integrate Craft Commerce with SendCloud
 *
 * @link      https://www.studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace studioespresso\sendcloud\models;

use craft\commerce\base\ShippingMethodInterface;
use craft\commerce\base\ShippingRuleInterface;
use studioespresso\sendcloud\SendCloud;

use Craft;
use craft\base\Model;

/**
 * SendCloud Shipping Method Model
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Studio Espresso
 * @package   SendCloud
 * @since     1.0.0
 */
class SendCloudShippingMethod extends Model implements ShippingMethodInterface
{
    public function getType(): string
    {
        return 'Send Cloud';
    }

    public function getId()
    {
        return null;
    }

    public function getName(): string
    {
        return 'Send Cloud';
    }

    public function getHandle(): string
    {
        return 'send-cloud';
    }

    public function getCpEditUrl(): string
    {
        return null;
    }

    public function getShippingRules(): array
    {
        return [new SendCloudShippingRule()];
    }

    public function getIsEnabled(): bool
    {
        return true;
    }


}
