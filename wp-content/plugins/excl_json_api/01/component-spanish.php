/*namespace api\v01;

require_once(dirname(__FILE__) . '/iexcl_type.php');

class EXCL_Component_Spanish implements iExcl_Type {
    protected $slug = 'component';
    protected $plural_slug = 'components';
    protected $children_hierarchy = array(
        'component' => array(
            'attributes' => array (
                    array('ID-spanish' => 'id'),
                    array('post-title-spanish' => 'name'),
                    'posts'
                ),
            'children' => array('component-post')
        ),
        'component-post' => array(
            'attributes' => array(
                array('ID-spanish' => 'id'),
                array('post-title-spanish' => 'name'),
                'section-spanish',
                'parts-spanish',
                array('social-liking-spanish' => 'liking'),
                array('social-sharing-text-spanish' => 'text_sharing'),
                array('social-sharing-image-spanish' => 'image_sharing'),
                array('social-commenting-spanish' => 'commenting'),
                array('categories-spanish' => 'section'),
                array('default-social-media-message-spanish' => 'social_media_message'),
                'like_count-spanish',
                array('post-image-spanish' => 'image'),
                'comments'
            ),
            'children' => array('part'),
            'name' => 'posts'
        ),
        'part' => array(
            'attributes' => array(
                array('ID-spanish' => 'id'),
                array('post-title-spanish' => 'name'),
                array('part-type-spanish' => 'type'),
                array('part-video-spanish' => 'video'),
                array('part-image-spanish' => 'image'),
                array('part-body-spanish' => 'body')
            ),
            'children' => array()
        )
    );

    public function get_slug_Spanish() {
        return $this->slug;
    }

    public function get_children_hierarchy_Spanish() {
        return $this->children_hierarchy;
    }
}*/