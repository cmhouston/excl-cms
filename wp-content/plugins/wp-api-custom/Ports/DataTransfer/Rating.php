<?php

namespace WP_API_Custom\Ports\DataTransfer
{
    class Rating
    {
        /** @var int */
        public $userId;
        /** @var int */
        public $value;

        const RATING_PREFIX = 'rating_';

        /**
         * @param string $key
         * @param string $value
         * @return Rating
         */
        public static function fromKeyValue($key, $value)
        {
            $rating = new Rating();
            $userIdStartIndex = strlen(Rating::RATING_PREFIX);
            $rating->userId = substr($key, $userIdStartIndex);
            $rating->value = $value[0];
            return $rating;
        }

        /**
         * @param int $userId
         * @return string
         */
        public static function getRatingKey($userId)
        {
            return Rating::RATING_PREFIX . $userId;
        }
    }
}