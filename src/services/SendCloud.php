<?php

namespace statikbe\sendcloud\services;

use craft\base\Component;
use craft\commerce\services\Countries;
use statikbe\sendcloud\SendCloud as Plugin;
use Picqer\Carriers\SendCloud\SendCloud as Api;
use Picqer\Carriers\SendCloud\Connection;

class SendCloud extends Component
{

    private $api;

    public function init()
    {
        $key = Plugin::getInstance()->getSettings()->sendcloudKey;
        $secret = Plugin::getInstance()->getSettings()->sendcloudSecret;
        $connection = new Connection($key, $secret);
        $this->api = new Api($connection);

    }

    public function createPackage($order)
    {
        $shippingAddress = $order->getShippingAddress();
        $parcel = $this->api->parcels();

        $parcel->name = $shippingAddress->firstName . ' ' . $shippingAddress->lastName;
        $parcel->email = $order->email;
        $parcel->company_name = $shippingAddress->businessName;
        $parcel->address = $shippingAddress->address1;
        $parcel->address2 = $shippingAddress->address2;
        $parcel->city = $shippingAddress->city;
        $parcel->postal_code = $shippingAddress->zipCode;

        $countries = new Countries();
        $country = $countries->getCountryById($shippingAddress->countryId);
        $parcel->country = $country->iso;

        $parcel->order_number = $order->reference;

        try {
            $parcel->save();
        } catch (SendCloudApiException $e) {
            throw new Exception($e->getMessage());
        }
    }

}