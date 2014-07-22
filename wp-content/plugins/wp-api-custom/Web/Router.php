<?php

namespace WP_API_Custom\Web
{
    use WP_API_Custom\UseCases\Rater;
    use WP_API_Custom\UseCases\Commenter;
    use WP_API_Custom\UseCases\PartnerCommenter;
    use WP_API_Custom\Wrappers\JsonServerWrapper;
    use WP_API_Custom\Wrappers\WordPressWrapper;

    class Router
    {
        /** @var WordPressWrapper */
        private $wordpress;
        /** @var JsonServerWrapper */
        private $jsonServer;
        /** @var Rater */
        private $rater;
        /** @var Commenter */
        private $commenter;
        private $partnerCommenter;

        public function __construct(WordPressWrapper $wordpress, JsonServerWrapper $jsonServer, Rater $rater, Commenter $commenter, PartnerCommenter $partnerCommenter)
        {
            $this->wordpress = $wordpress;
            $this->jsonServer = $jsonServer;
            $this->rater = $rater;
            $this->commenter = $commenter;
            $this->partnerCommenter = $partnerCommenter;
        }

        public function initialize()
        {
            $this->wordpress->add_filter('json_endpoints', array(&$this, 'registerRoutes'));
        }

        public function registerRoutes($routes)
        {
            $routes['/posts/(?P<postId>\d+)/rating'] = array(
                array(array( $this, 'getRating'), $this->jsonServer->readable() ),
                array(array( $this, 'createOrUpdateRating'), $this->jsonServer->editable() | $this->jsonServer->accept_json() ),
                array(array( $this, 'deleteRating'), $this->jsonServer->deletable() ),
            );
            $routes['/posts/(?P<postId>\d+)/newcomment'] = array(
                array( array( $this, 'createComment'), $this->jsonServer->creatable() | $this->jsonServer->accept_json())
            );
            $routes['/posts/(?P<postId>\d+)/newcomment/(?P<commentId>\d+)'] = array(
                array( array( $this, 'updateComment'), $this->jsonServer->editable() | $this->jsonServer->accept_json())
            );
            $routes['/posts/(?P<postId>\d+)/partner/comments'] = array(
                array( array( $this, 'getPartnerComments'), $this->jsonServer->readable() )
            );
            return $routes;
        }

        #region Rating

        public function getRating($postId)
        {
            $rater = $this->rater;
            return $this->doAction($postId, function($postId) use (&$rater) {
                return $rater->getRating($postId);
            });
        }

        public function createOrUpdateRating($data, $postId)
        {
            $newData = new \stdClass();
            $newData->data = $data;
            $newData->postId = $postId;
            $rater = $this->rater;
            return $this->doAction($newData, function($data) use (&$rater) {
                return $rater->createOrUpdateRating($data->data, $data->postId);
            });
        }

        public function deleteRating($postId)
        {
            $rater = $this->rater;
            return $this->doAction($postId, function($postId) use (&$rater) {
                return $rater->deleteRating($postId);
            });
        }

        public function getPartnerComments($postId)
        {
            log2('postId: ', $postId);
            $partnerCommenter = $this->partnerCommenter;
            log2('partnerCommenter: ', $partnerCommenter);
            return $this->doAction($postId, function($postId) use (&$partnerCommenter) {
                return $partnerCommenter->getPartnerComments($postId);
            });
        }

        public function createComment($data, $postId)
        {
            $newData = new \stdClass();
            $newData->data = $data;
            $newData->postId = $postId;
            $commenter = $this->commenter;
            return $this->doAction($newData, function($data) use (&$commenter) {
                return $commenter->createComment($data->data, $data->postId);
            });
        }

        public function updateComment($data, $postId)
        {
            $newData = new \stdClass();
            $newData->data = $data;
            $newData->postId = $postId;
            $commenter = $this->commenter;
            return $this->doAction($newData, function($data) use (&$commenter) {
                return $commenter->updateComment($data->data, $data->postId);
            });
        }

        #endregion

        #region Helpers

        public function doAction($data, $action)
        {
            try{
                return $action($data);
            } catch (\Exception $ex){
                log2('Error', $ex);
                return $this->wordpress->createError($ex->getCode(), $ex->getMessage());
            }
        }

        #endregion
    }

}