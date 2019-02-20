<?php
/**
 * SendCloud plugin for Craft CMS 3.x
 *
 * Integrate Craft Commerce with SendCloud
 *
 * @link      https://www.statik.be
 * @copyright Copyright (c) 2018 Statik
 */

namespace statikbe\sendcloud\models;

use craft\commerce\base\ShippingMethodInterface;
use craft\commerce\base\ShippingRuleInterface;
use craft\commerce\elements\Order;
use statikbe\sendcloud\SendCloud;

use Craft;
use craft\base\Model;

/**
 * SendCloud Shipping Method Model
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Statik
 * @package   SendCloud
 * @since     1.0.0
 */
class SendCloudShippingRule extends Model implements ShippingRuleInterface
{
    public function matchOrder(Order $order): bool
    {
        return true;
    }

    public function getIsEnabled(): bool
    {
        return true;
    }

    public function getOptions()
    {
        return '';
    }

    public function getPercentageRate($shippingCategory): float
    {
        return 10;
    }

    public function getPerItemRate($shippingCategory): float
    {
        return 1;
    }

    public function getWeightRate($shippingCategory): float
    {
        return 20;
    }

    public function getBaseRate(): float
    {
        return 10;
    }

    public function getMaxRate(): float
    {
        return 100;
    }

    public function getMinRate(): float
    {
        return 0;
    }

    public function getDescription(): string
    {
        return "Send Cloud Description";
    }

}
