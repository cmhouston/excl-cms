<?php

namespace WP_API_Custom\Wrappers
{
    class JsonServerWrapper
    {
        public function readable()
        {
            return \WP_JSON_Server::READABLE;
        }
        public function creatable()
        {
            return \WP_JSON_Server::CREATABLE;
        }
        public function editable()
        {
            return \WP_JSON_Server::EDITABLE;
        }
        public function deletable()
        {
            return \WP_JSON_Server::DELETABLE;
        }
        public function accept_json()
        {
            return \WP_JSON_Server::ACCEPT_JSON;
        }
    }
}