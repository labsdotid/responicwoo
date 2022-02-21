<?php

namespace Responicwoo;


class woo_Customer
{

    public $billing_name;

    public $billing_email;

    public $billing_phone;

    public $billing_address;

    public $shipping_name;

    public $shipping_phone;

    public $shipping_address;


    /**
     * __construct
     *
     * @param  mixed $order
     * @return void
     */
    public function __construct($order = false)
    {
        if (is_a($order, 'WC_Order')) {

            $first_name = $order->get_billing_first_name();
            $last_name = $order->get_billing_last_name();

            $this->billing_name       = sprintf('%s %s', $first_name, $last_name);
            $this->billing_email      = $order->get_billing_email();
            $this->billing_phone      = $order->get_billing_phone();
            $this->billing_address    = str_replace(["<br/>"], ["\n"], $order->get_formatted_billing_address());

            $first_name = $order->get_shipping_first_name();
            $last_name = $order->get_shipping_last_name();

            $this->shipping_name       = sprintf('%s %s', $first_name, $last_name);
            $this->shipping_phone      = $order->get_shipping_phone();
            $this->shipping_address    = str_replace(["<br/>"], ["\n"], $order->get_formatted_shipping_address());
        }
    }

    public function toArray()
    {
        return (array) $this;
    }
}
