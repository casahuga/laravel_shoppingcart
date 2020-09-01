<?php

namespace Gloudemans\Shoppingcart\Calculation;

use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\Contracts\Calculator;

class DefaultCalculator implements Calculator
{
    public static function getAttribute(string $attribute, CartItem $cartItem)
    {
        $decimals = config('cart.format.decimals', 2);

        switch ($attribute) {
            case 'commissionItem':
                return round($cartItem->price * ($cartItem->getCommission() / 100), $decimals);
            case 'priceCommission':
                return round($cartItem->price + $cartItem->commissionItem, $decimals);
            case 'priceCommissionTotal':
                return round($cartItem->priceCommission * $cartItem->qty, $decimals);
            case 'totalCommission':
                return round($cartItem->commissionItem * $cartItem->qty, $decimals);
            case 'taxBeforeDiscount':
                return round($cartItem->priceCommission * ($cartItem->taxRate / 100), $decimals);
            case 'totalTaxBeforeDiscount':
                return round($cartItem->taxBeforeDiscount * $cartItem->qty, $decimals);
            case 'priceTaxBeforeDiscount':
                return round($cartItem->priceCommission + $cartItem->taxBeforeDiscount, $decimals);
            case 'priceTaxBeforeDiscountTotal':
                return round($cartItem->priceTaxBeforeDiscount * $cartItem->qty, $decimals);
            case 'discount':
                if ($cartItem->getFixedDiscount() > 0) {
                    return round(($cartItem->priceTaxBeforeDiscount - $cartItem->getFixedDiscount() < 0 ) ? $cartItem->priceTaxBeforeDiscount : $cartItem->getFixedDiscount(), $decimals);
                }
                return round($cartItem->price * ($cartItem->getDiscountRate() / 100), $decimals);
            case 'discountTotal':
                return round($cartItem->discount * $cartItem->qty, $decimals);
            case 'priceDiscount':
                return round($cartItem->priceTaxBeforeDiscount - $cartItem->discount, $decimals);
            case 'priceDiscountTotal':
                return round($cartItem->priceDiscount * $cartItem->qty, $decimals);
            case 'tax':
                return round($cartItem->priceDiscount * ((100 + $cartItem->taxRate) / 100), $decimals);
            case 'priceTax':
                return round($cartItem->priceDiscount, $decimals);
            case 'priceTotal':
                return round($cartItem->priceTax * $cartItem->qty, $decimals);
            case 'subtotal':
                return max(round($cartItem->priceTax * $cartItem->qty, $decimals), 0);
            case 'taxSubtotal':
                return round($cartItem->subtotal - $cartItem->subtotalWithoutTax, $decimals);
            case 'subtotalWithoutTax':
                return round($cartItem->subtotal / ((100 + $cartItem->taxRate) / 100), $decimals);
            case 'priceTarget':
                return round(($cartItem->priceTotal - $cartItem->discountTotal) / $cartItem->qty, $decimals);
            case 'totalWithoutTax':
                return round($cartItem->subtotal / ((100 + $cartItem->taxRate) / 100), $decimals);
            case 'taxTotal':
                return round($cartItem->total * $cartItem->totalWithoutTax, $decimals);
            case 'total':
                return round($cartItem->subtotal, $decimals);
            default:
                return;
        }
    }
}