<?php

namespace Responicwoo;


class woo_Order
{
    public $sub_total;
    public $shipping_cost;
    public $shipping_method;
    public $discount;
    public $total;
    public $total_items;
    public $items;
    public $note;
    public $status;
    public $date;
    public $payment_method;


    /**
     * __construct
     *
     * @param  mixed $order
     * @return void
     */
    public function __construct($order = false)
    {
        if (is_a($order, 'Automattic\WooCommerce\Admin\Overrides\Order')) {
            $this->sub_total       = str_replace('&nbsp;', '', strip_tags($order->get_subtotal_to_display()));
            $this->discount        = str_replace('&nbsp;', '', strip_tags($order->get_discount_to_display()));
            $this->total           = str_replace('&nbsp;', '', strip_tags($order->get_formatted_order_total()));
            $this->total_item      = $order->get_item_count();
            $this->note            = $order->get_customer_note();
            $this->status          = $order->get_status();
            $this->items           = $this->items($order);
            $this->date            = $order->get_date_created()->format('d-m-Y H:i');
            $this->shipping_cost   = str_replace('&nbsp;', '', strip_tags(wc_price($order->get_shipping_total())));
            $this->shipping_method = $order->get_shipping_method();
            $this->payment_method  = $order->get_payment_method_title();
        }
    }

    public function toArray()
    {
        return (array) $this;
    }

    protected function items($order)
    {
        $output = '';
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();

            $name       = $item->get_name();
            $price      = str_replace('&nbsp;', '', strip_tags(wc_price($product->get_price())));
            $quantity   = $item->get_quantity();
            $subtotal   = str_replace('&nbsp;', '', strip_tags(wc_price($item->get_subtotal())));

            $format     = "%s\n%s x %s = %s\n";
            $output    .= sprintf(
                $format,
                $name,
                $quantity,
                $price,
                $subtotal
            );
        }

        return $output;
    }
}
