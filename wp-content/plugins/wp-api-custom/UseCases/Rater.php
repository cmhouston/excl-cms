<?php

namespace WP_API_Custom\UseCases
{
    use WP_API_Custom\Ports\DataTransfer\Rating;
    use WP_API_Custom\Wrappers\WordPressWrapper;

    class Rater
    {
        /** @var WordPressWrapper */
        private $wordpress;

        public function __construct(WordPressWrapper $wordpress)
        {
            $this->wordpress = $wordpress;
        }

        public function getRating($postId)
        {
            return $this->getPostRatingWithCurrentUser($postId);
        }

        public function createOrUpdateRating($data, $postId)
        {
            if ($data == null || $data['userRating'] == null){
                return $this->wordpress->createError("Invalid data");
            }

            /** @var \WP_User $currentUser */
            $currentUser = $this->wordpress->wp_get_current_user();
            $this->wordpress->update_post_meta($postId, Rating::getRatingKey($currentUser->ID), $data['userRating']);
            return $this->getPostRatingWithCurrentUser($postId);
        }

        public function deleteRating($postId)
        {
            /** @var \WP_User $currentUser */
            $currentUser = $this->wordpress->wp_get_current_user();
            $this->wordpress->delete_post_meta($postId, Rating::getRatingKey($currentUser->ID));
            return $this->getPostRatingWithCurrentUser($postId);
        }

        #region Helpers

        /**
         * @param int $postId
         * @return \stdClass
         */
        private function getPostRatingWithCurrentUser($postId)
        {
            /** @var Rating[] */
            $postRatings = $this->getRatingsFromPostMeta($postId);

            /** @var \WP_User $currentUser */
            $currentUser = $this->wordpress->wp_get_current_user();

            /** @var Rating $userRating */
            $userRating = null;
            $ratingNumbers = array();
            foreach($postRatings as $postRating){
                array_push($ratingNumbers, $postRating->value);

                if ($postRating->userId == $currentUser->ID){
                    $userRating = $postRating;
                }
            }

            return $this->buildRatingReturnObject(
                $this->calculateAverageRating($ratingNumbers),
                count($postRatings),
                $userRating
            );
        }

        /**
         * @param float $average
         * @param int $count
         * @param Rating $userRating
         * @return \stdClass
         */
        private function buildRatingReturnObject($average, $count, $userRating)
        {
            $returnValue = new \stdClass();
            $returnValue->average = $average;
            $returnValue->count = $count;
            if ($userRating == null ||  $userRating->value == null){
                $returnValue->userRating = null;
            } else {
                $returnValue->userRating = (int)$userRating->value;
            }
            return $returnValue;
        }

        /**
         * @param int $postId
         * @return Rating[]
         */
        private function getRatingsFromPostMeta($postId)
        {
            /** @var \WP_Post $post */
            $postMeta = $this->wordpress->get_post_meta($postId);

            /** @var Rating[] */
            $postRatings = array();
            foreach($postMeta as $metaKey => $metaValue){
                if ($this->startsWith($metaKey, Rating::RATING_PREFIX)){
                    array_push($postRatings, Rating::fromKeyValue($metaKey, $metaValue));
                }
            }
            return $postRatings;
        }

        /**
         * @param int[] $ratings
         * @return float
         */
        public function calculateAverageRating($ratings)
        {
            /** @var int $sum */
            $sum = 0;
            foreach($ratings as $rating){
                $sum += (int) $rating;
            }
            /** @var float $averageRating */
            if ($sum == 0){
                $averageRating = 0.0;
            } else{
                $averageRating = $sum / count($ratings);
            }
            return $averageRating;
        }

        /**
         * @param string $haystack
         * @param string $needle
         * @return bool
         */
        private function startsWith($haystack, $needle)
        {
            return $needle === "" || strpos($haystack, $needle) === 0;
        }

        #endregion
    }
}