<?php

namespace api\v01;

    class WordPressWrapper
    {
        public function add_filter($param1, $param2)
        {
            add_filter($param1, $param2);
        }

        public function wp_get_current_user()
        {
            return wp_get_current_user();
        }

        public function update_post_meta($param1, $param2, $param3)
        {
            update_post_meta($param1, $param2, $param3);
        }

        public function delete_post_meta($param1, $param2)
        {
            delete_post_meta($param1, $param2);
        }

        public function get_post_meta($param1)
        {
            return get_post_meta($param1);
        }

        public function createError($param1, $param2 = null)
        {
            return new \WP_Error($param1, $param2);
        }

        public function get_comments($param1)
        {
            return get_comments($param1);
        }

        public function wp_insert_comment($param1)
        {
            return wp_insert_comment($param1);
        }

        public function wp_update_comment($param1)
        {
            return wp_update_comment($param1);
        }

        public function get_comment($param1)
        {
            return get_comment($param1);
        }

        public function get_option($param1)
        {
            return get_option($param1);
        }

        public function current_time($param1)
        {
            return current_time($param1);
        }

        public function get_user_meta($param1)
        {
            return get_user_meta($param1);
        }

        public function get_user_by($param1, $param2)
        {
            return get_user_by($param1, $param2);
        }

    }
