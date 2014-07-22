<?php

namespace api\v01;

    //use WP_API_Custom\Ports\DataTransfer\Rating;
    // use Wrappers\WordPressWrapper;
    require_once(dirname(__FILE__). '/WordPressWrapper.php');

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
            if ($data == null || $data['comment_body'] == null){
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
                'comment_author' => $data['name'],
                'comment_author_email' => $data['email'],
                'comment_content' => $data['comment_body'],
                'comment_type' => '',
                'comment_parent' => 0,
                'user_id' => $currentUser->ID,
                'comment_date' => $time,
                'comment_approved' => $approval,
            );

            /** @var \WP_User $currentUser */
            // log2("made it to method");


            $commentId = $this->wordpress->wp_insert_comment($commentdata);
            $result = $this->wordpress->get_comment($commentId);
            // log2("comment id", $commentId);
            // log2("result", $result);
            return $result;
        }

    }
