<?php

use App\Currency;
use App\Pricing;
use App\Product;
use WHMCS\Product\Group;

require __DIR__ . '/../../../init.php';
require 'app/Currency.php';
require 'app/Pricing.php';
require 'app/Product.php';

$currencyCode = getCurrencyCodes();

$groups = Group::orderBy('order')->get();
foreach ($groups as $group) {

    $products = Product::whereGid($group->id)
        ->whereRetired(false)
        ->orderBy('order')
        ->get();

    foreach ($products as $product) {
        $data[$product->id]['group'] = $group->name;
        $data[$product->id]['name']  = $product->name;
        $pricing                     = Pricing::whereRelid($product->id)
            ->whereType('product')
            ->get();
        foreach ($pricing as $price) {
            $data[$product->id][$currencyCode[$price->currency]]['setup']   = $price->msetupfee;
            $data[$product->id][$currencyCode[$price->currency]]['monthly'] = $price->monthly;
            $data[$product->id][$currencyCode[$price->currency]]['annually'] = $price->annually;
        }
    }

//    if (!$data) dd('issue');

    echo "<pre>";
    echo(json_encode($data, JSON_PRETTY_PRINT));
    echo "</pre>";

}

function getCurrencyCodes()
{
    $currencyCode = [];
    $currencies   = Currency::all();
    foreach ($currencies as $currency) {
        $currencyCode[$currency->id] = $currency->code;
    }
    return $currencyCode;
}