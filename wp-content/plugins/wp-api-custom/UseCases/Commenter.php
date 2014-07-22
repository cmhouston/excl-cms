<?php

namespace WP_API_Custom\UseCases
{
    //use WP_API_Custom\Ports\DataTransfer\Rating;
    use WP_API_Custom\Wrappers\WordPressWrapper;

    class Commenter
    {
        /** @var WordPressWrapper */
        private $wordpress;

        public function __construct(WordPressWrapper $wordpress)
        {
            $this->wordpress = $wordpress;
        }

        public function createComment($data, $postId)
        {
            if ($data == null || $data['content'] == null){
                return $this->wordpress->createError("Invalid data");
            }

            $currentUser = $this->wordpress->wp_get_current_user();
            $time = $this->wordpress->current_time('mysql');

            $commentmod = $this->wordpress->get_option('comment_moderation');

            if ($commentmod == 1){
                $approval = 0;
            }
            else {
                $approval = 1;
            }

            $commentdata = array(
                'comment_post_ID' => $postId,
                'comment_author' => $currentUser->user_firstname.' '.$currentUser->user_lastname,
                'comment_content' => $data['content'],
                'comment_type' => '',
                'comment_parent' => 0,
                'user_id' => $currentUser->ID,
                'comment_date' => $time,
                'comment_approved' => $approval,
            );

            /** @var \WP_User $currentUser */
            log2("made it to method");


            $commentId = $this->wordpress->wp_insert_comment($commentdata);
            $result = $this->wordpress->get_comment($commentId);
            log2("comment id", $commentId);
            log2("result", $result);
            return $result;
        }

        public function updateComment($data)
        {
            if ($data == null || $data['ID'] == null){
                return $this->wordpress->createError("Invalid data");
            }

            $currentUser = $this->wordpress->wp_get_current_user();
            $time = current_time('mysql');

            $commentarr = array(
                'comment_ID' => $data['ID'],
                'comment_content' => $data['content'],
                'comment_date' => $time,
            );

            $commentId = $this->wordpress->wp_update_comment($commentarr);
            return $this->$commentId;
        }

    }
}