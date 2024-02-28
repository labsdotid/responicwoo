<?php

namespace Responicwoo;

class Setting
{
    private $column = '';

    private $column_name = '';

    private $tabbing = [];

    private $current_tab;

    public function __construct()
    {
        $tabs = wc_get_order_statuses();

        $this->column = array_key_first($tabs);

        if (isset($_GET['column']) && isset($tabs[$_GET['column']])) {
            $this->column = sanitize_text_field($_GET['column']);
        }

        $this->column_name = sanitize_text_field($tabs[$this->column]);


        foreach ($tabs as $key => $label) {
            $current_args = $_GET;
            unset($current_args['q']);
            $args = wp_parse_args(
                [
                    'column' => $key
                ],
                $current_args
            );

            $url = admin_url('admin.php' . add_query_arg($args, ''));
            $class = $key == $this->column ? 'current' : '';
            $this->tabbing[] = '<li><a class="' . $class . '" href="' . $url . '">' . $label . '</a></li>';
        }
    }

    public function add_menu_page()
    {
        add_menu_page(
            __('Responicwoo', 'responicwoo'),
            __('Responicwoo', 'responicwoo'),
            'manage_options',
            'responicwoo',
            [$this, 'page'],
            '',
            6
        );
    }

    public function on_save()
    {
        if (isset($_POST['save']) && isset($_POST['__nonce'])) :
            if (wp_verify_nonce($_POST['__nonce'], 'responicwoo')) :
                unset($_POST['save']);
                unset($_POST['__nonce']);
                unset($_POST['_wp_http_referer']);

                foreach ($_POST as $key => $value) {
                    \update_option($key, $value);
                }

                \flush_rewrite_rules();

                add_action('admin_notices', function () {
                    echo '<div id="message" class="updated notice notice-success"><p><strong>' . __('Your settings have been saved.', 'responicwoo') . '</strong></p></div>';
                });
            endif;
        endif;
    }

    public function page()
    {
        $tabs = '';

        $_tabs = [
            'general'        => __('General', 'responicwoo'),
            'message'      => __('Message', 'responicwoo'),
        ];

        $this->current_tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'general';

        foreach ($_tabs as $key => $name) :
            $is_active = $this->current_tab == $key ? ' nav-tab-active' : '';
            $url = add_query_arg('tab', $key);
            if (isset($_GET['section'])) {
                $url = remove_query_arg('section', $url);
            }
            $tabs .= '<a href="' . $url . '" class="nav-tab' . $is_active . '">' . $name . '</a>';
        endforeach;

        $customer = new woo_Customer();
        $order = new woo_Order();

        echo '<div class="wrap">';
        echo '<h2>' . __('Responic Settings', 'salesloo') . '</h2>';
        echo '<h2 class="nav-tab-wrapper wp-clearfix">' . $tabs . '</h2>';
        echo '<form action="" method="post" enctype="multipart/form-data" style="margin-top:30px">';

        if ($this->current_tab == 'general') {
?>
            <div class="responicwoo-relative">
                <div class="responicwoo-w-1/2 responicwoo-flex responicwoo-flex-col responicwoo-space-y-10">
                    <div class="responicwoo-flex responicwoo-justify-center responicwoo-items-start responicwoo-space-x-5">
                        <div class="responicwoo-flex-none responicwoo-w-48">
                            <label>Enable</label>
                        </div>
                        <div class="responicwoo-flex-grow">
                            <div class="responicwoo-flex responicwoo-justify-start">
                                <label class="inline-flex items-center">
                                    <?php
                                    $enable = '';
                                    if (get_option('responicwoo_enable')) {
                                        $enable = 'checked="checked"';
                                    } ?>
                                    <input name="responicwoo_enable" type="hidden" value="0">
                                    <input name="responicwoo_enable" type="checkbox" class="responicwoo-form-checkbox responicwoo-rounded responicwoo-border-gray-300 responicwoo-text-blue-600 responicwoo-shadow-sm focus:responicwoo-border-blue-300 focus:responicwoo-ring focus:responicwoo-ring-offset-0 focus:responicwoo-ring-blue-200 focus:responicwoo-ring-opacity-50" value="1" <?php echo $enable; ?>>
                                    <span class="ml-2">Check for enable</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="responicwoo-flex responicwoo-justify-center responicwoo-items-start responicwoo-space-x-5">
                        <div class="responicwoo-flex-none responicwoo-w-48">
                            <label>Api Token</label>
                        </div>
                        <div class="responicwoo-flex-grow">
                            <div class="responicwoo-flex responicwoo-flex-col responicwoo-justify-start">
                                <input name="responicwoo_api_token" type="text" class="
                    responicwoo-mt-1
                    responicwoo-block
                    responicwoo-w-full
                    responicwoo-rounded-md
                    responicwoo-border-gray-300
                    focus:responicwoo-border-indigo-300 focus:responicwoo-ring focus:responicwoo-ring-indigo-200 focus:responicwoo-ring-opacity-50
                  " placeholder="" value="<?php echo get_option('responicwoo_api_token'); ?>">
                                <p class="description responicwoo-italic">Responic api key <a target="blank" class="responicwoo-text-blue-400" href=" https://panel.responic.com/account">Click here to get responic api token</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="responicwoo-flex responicwoo-justify-center responicwoo-items-start responicwoo-space-x-5">
                        <div class="responicwoo-flex-none responicwoo-w-48">
                            <label>Admin Phone</label>
                        </div>
                        <div class="responicwoo-flex-grow">
                            <div class="responicwoo-flex responicwoo-flex-col responicwoo-justify-start">
                                <textarea name="responicwoo_admin_phones" class=" responicwoo-mt-1 responicwoo-block responicwoo-w-full responicwoo-rounded-md responicwoo-border-gray-300 focus:responicwoo-border-indigo-300 focus:responicwoo-ring focus:responicwoo-ring-indigo-200 focus:responicwoo-ring-opacity-50 "><?php echo get_option('responicwoo_admin_phones'); ?></textarea>
                                <p class=" description responicwoo-italic">Sparate admin phone by comma</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        if ($this->current_tab == 'message') {
        ?>
            <div class="responicwoo-relative">
                <div class="responicwoo-w-1/2 responicwoo-flex responicwoo-flex-col responicwoo-space-y-10">
                    <div class="responicwoo-flex responicwoo-justify-start responicwoo-items-start responicwoo-space-x-5">
                        <ul class="subsubsub">
                            <?php echo implode(' | ', $this->tabbing); ?>
                        </ul>
                    </div>
                    <div class="responicwoo-flex responicwoo-justify-center responicwoo-items-start responicwoo-space-x-5">
                        <div class="responicwoo-flex-none responicwoo-w-48">
                            <h3 class="responicwoo-font-bold">Dynamic Variable</h3>
                            <div class="responicwoo-relative">
                                <div class="responicwoo-font-semibold">Customer Data</div>
                                <ul class="responicwoo-pl-5">
                                    <?php foreach ($customer->toArray() as $key => $val) : ?>
                                        <li>[<?php echo $key; ?>]</li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="responicwoo-relative responicwoo-mt-5">
                                <div class="responicwoo-font-semibold">Order Data</div>
                                <ul class="responicwoo-pl-5">
                                    <?php foreach ($order->toArray() as $key => $val) : ?>
                                        <li>[<?php echo $key; ?>]</li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="responicwoo-flex-grow">
                            <div class="responicwoo-flex responicwoo-flex-col responicwoo-space-y-5">
                                <div class="responicwoo-flex responicwoo-flex-col responicwoo-justify-start">
                                    <label class="inline-flex items-center">
                                        <?php
                                        $customer_enable = '';
                                        if (get_option('responicwoo_customer_' . $this->column . '_enable')) {
                                            $customer_enable = 'checked="checked"';
                                        } ?>
                                        <input name="responicwoo_customer_<?php echo $this->column; ?>_enable" type="hidden" value="0">
                                        <input name="responicwoo_customer_<?php echo $this->column; ?>_enable" type="checkbox" class="responicwoo-form-checkbox responicwoo-rounded responicwoo-border-gray-300 responicwoo-text-blue-600 responicwoo-shadow-sm focus:responicwoo-border-blue-300 focus:responicwoo-ring focus:responicwoo-ring-offset-0 focus:responicwoo-ring-blue-200 focus:responicwoo-ring-opacity-50" value="1" <?php echo $customer_enable; ?>>
                                        <span class="ml-2">Enable <?php echo $this->column_name; ?> Message for customer</span>
                                    </label>
                                    <textarea name="responicwoo_customer_<?php echo $this->column; ?>_message" class="
                    responicwoo-mt-1
                    responicwoo-block
                    responicwoo-w-full
                    responicwoo-rounded-md
                    responicwoo-border-gray-300
                    focus:responicwoo-border-indigo-300 focus:responicwoo-ring focus:responicwoo-ring-indigo-200 focus:responicwoo-ring-opacity-50
                  " style="min-height:300px"><?php echo get_option('responicwoo_customer_' . $this->column . '_message'); ?></textarea>
                                </div>
                                <div class="responicwoo-flex responicwoo-flex-col responicwoo-justify-start">
                                    <label class="inline-flex items-center">
                                        <?php
                                        $admin_enable = '';
                                        if (get_option('responicwoo_admin_' . $this->column . '_enable')) {
                                            $admin_enable = 'checked="checked"';
                                        } ?>
                                        <input name="responicwoo_admin_<?php echo $this->column; ?>_enable" type="hidden" value="0">
                                        <input name="responicwoo_admin_<?php echo $this->column; ?>_enable" type="checkbox" class="responicwoo-form-checkbox responicwoo-rounded responicwoo-border-gray-300 responicwoo-text-blue-600 responicwoo-shadow-sm focus:responicwoo-border-blue-300 focus:responicwoo-ring focus:responicwoo-ring-offset-0 focus:responicwoo-ring-blue-200 focus:responicwoo-ring-opacity-50" value="1" <?php echo $admin_enable; ?>>
                                        <span class="ml-2">Enable <?php echo $this->column_name; ?> Message for admin</span>
                                    </label>
                                    <textarea name="responicwoo_admin_<?php echo $this->column; ?>_message" class="
                    responicwoo-mt-1
                    responicwoo-block
                    responicwoo-w-full
                    responicwoo-rounded-md
                    responicwoo-border-gray-300
                    focus:responicwoo-border-indigo-300 focus:responicwoo-ring focus:responicwoo-ring-indigo-200 focus:responicwoo-ring-opacity-50
                  " style="min-height:300px"><?php echo get_option('responicwoo_admin_' . $this->column . '_message'); ?></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
        <div class="responicwoo-relative responicwoo-mt-10">
            <div class="responicwoo-w-1/2 responicwoo-flex responicwoo-flex-col responicwoo-space-y-10">
                <div class="responicwoo-flex responicwoo-justify-center responicwoo-items-start responicwoo-space-x-5">
                    <div class="responicwoo-flex-none responicwoo-w-48">
                        <button name="save" class="button-primary" type="submit" value="Save changes"><?php _e('Save Changes', 'responicwoo'); ?></button>
                    </div>
                    <div class="responicwoo-flex-grow">

                    </div>
                </div>
            </div>
        </div>
<?php
        wp_nonce_field('responicwoo', '__nonce');
        echo '</form>';
        echo '</div>';
    }
}
