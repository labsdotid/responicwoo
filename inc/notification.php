<?php

namespace Responicwoo;

class Notification
{
    public function new_order($order_id, $status_from, $status_to, $instance)
    {
        if (!get_option('responicwoo_enable')) return;

        $wc_order = wc_get_order($order_id);

        if (!$wc_order) return;

        $customer = new woo_Customer($wc_order);
        $order = new woo_Order($wc_order);

        $data = array_merge($customer->toArray(), $order->toArray());

        $this->for_customer($wc_order->get_billing_phone(), $data, $status_to);
        $this->for_admin($data, $status_to);
    }

    public function order_status_changed($order_id, $status_from, $status_to, $instance)
    {
        error_log('responic_notif_started');
        if (!get_option('responicwoo_enable')) return;
        error_log('responic_notif_enable');

        $wc_order = wc_get_order($order_id);

        if (!$wc_order) return;
        error_log('responic_notif : wc_order valid');

        $customer = new woo_Customer($wc_order);
        $order = new woo_Order($wc_order);

        $data = array_merge($customer->toArray(), $order->toArray());

        $this->for_customer($wc_order->get_billing_phone(), $data, $status_to);
        $this->for_admin($data, $status_to);
    }

    public function for_customer($recipient, $data, $status)
    {
        error_log('responic_notif : ready send for customer with order status ' . $status);
        $is_enable = get_option('responicwoo_customer_wc-' . $status . '_enable');
        if (!$is_enable) return;
        error_log('responic_notif : message for customer enable');

        $message_template = get_option('responicwoo_customer_wc-' . $status . '_message');
        if (empty($message_template)) return;

        error_log('responic_notif : message for customer available');

        $wa = new Whatsapp();
        error_log('responic_send_to ' . $recipient);
        $wa->to($recipient)
            ->message($message_template, $data)
            ->send();
    }

    public function for_admin($data, $status)
    {
        error_log('responic_notif : ready send for admin with order status ' . $status);
        $is_enable = get_option('responicwoo_admin_wc-' . $status . '_enable');
        if (!$is_enable) return;
        error_log('responic_notif : message for customer enable');

        $message_template = get_option('responicwoo_admin_wc-' . $status . '_message');

        if (empty($message_template)) return;

        error_log('responic_notif : message for customer available');

        $wa = new Whatsapp();
        $admin_phones = get_option('responicwoo_admin_phones');
        $recipients = explode(',', $admin_phones);

        foreach ($recipients as $recipient) {
            error_log('responic_send_to admin ' . $recipient);
            $wa->to($recipient)
                ->message($message_template, $data)
                ->send();
        }
    }
}
