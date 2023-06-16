<?php
namespace Braintree;

/**
 * @property-read \Braintree\Addon[] $addOns
 * @property-read string $id
 * @property-read int|null $billingDayOfMonth
 * @property-read int $billingFrequency
 * @property-read \DateTime $createdAt
 * @property-read string $currencyIsoCode
 * @property-read string|null $description
 * @property-read \Braintree\Discount[] $discounts
 * @property-read string $name
 * @property-read int|null $numberOfBillingCycles
 * @property-read string $price
 * @property-read int|null $trialDuration
 * @property-read string|null $trialDurationUnit
 * @property-read boolean $trialPeriod
 * @property-read \DateTime $updatedAt
 */
class Plan extends Base
{
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);

        return $instance;
    }

    protected function _initialize($attributes)
    {
        $this->_attributes = $attributes;

        $addOnArray = [];
        if (isset($attributes['addOns'])) {
            foreach ($attributes['addOns'] AS $addOn) {
                $addOnArray[] = AddOn::factory($addOn);
            }
        }
        $this->_attributes['addOns'] = $addOnArray;

        $discountArray = [];
        if (isset($attributes['discounts'])) {
            foreach ($attributes['discounts'] AS $discount) {
                $discountArray[] = Discount::factory($discount);
            }
        }
        $this->_attributes['discounts'] = $discountArray;

        $planArray = [];
        if (isset($attributes['plans'])) {
            foreach ($attributes['plans'] AS $plan) {
                $planArray[] = self::factory($plan);
            }
        }
        $this->_attributes['plans'] = $planArray;
    }


    // static methods redirecting to gateway

    public static function all()
    {
        return Configuration::gateway()->plan()->all();
    }
}
