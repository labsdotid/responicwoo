<?php

namespace Responicwoo;


class Admin
{

    public function enqueue_styles()
    {
        if (get_current_screen()->id == 'toplevel_page_responicwoo') {
            wp_enqueue_style('responicwoo', RESPONICWOO_URL . '/assets/css/style.css');
        }
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('responicwoo', RESPONICWOO_URL . '/assets/js/bundle.js');
    }
}
