<?php
/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Views-1.6.1-Types-1.5.7/toolset-forms/classes/class.taxonomyhierarchical.php $
 * $LastChangedDate: 2014-04-18 12:07:10 +0200 (Fri, 18 Apr 2014) $
 * $LastChangedRevision: 21640 $
 * $LastChangedBy: marcin $
 *
 */

include_once 'class.textfield.php';

class WPToolset_Field_Taxonomyhierarchical extends WPToolset_Field_Textfield
{
    protected $child;
    protected $names;
    protected $values = array();
    protected $valuesId = array();
    protected $objValues;

    public function init()
    {
        global $post;

        $this->objValues = array();
        if (isset($post)) {
            $terms = wp_get_post_terms($post->ID, $this->getName(), array("fields" => "all"));
            foreach ($terms as $n => $term) {
                $this->values[] = $term->slug;
                $this->valuesId[] = $term->term_id;
                $this->objValues[$term->slug] = $term;
            }
        }

        $all = $this->buildTerms(get_terms($this->getName(),array('hide_empty'=>0,'fields'=>'all')));

        $childs=array();
        $names=array();
        foreach ($all as $term) {
            $names[$term['term_id']]=$term['name'];
            if (!isset($childs[$term['parent']]) || !is_array($childs[$term['parent']]))
                $childs[$term['parent']]=array();
            $childs[$term['parent']][]=$term['term_id'];
        }

        $this->childs = $childs;
        $this->names = $names;
    }

    public function enqueueScripts()
    {
    }

    public function enqueueStyles()
    {
    }

    public function metaform()
    {
        $res = '';
        $metaform = array();
        if ( array_key_exists( 'display', $this->_data ) && 'select' == $this->_data['display'] ) {
            $res = $this->buildSelect();
        } else {
            $res = $this->buildCheckboxes(0, $this->childs, $this->names, $metaform);
        }
        $this->set_metaform($res);

        return $metaform;
    }

    private function buildTerms($obj_terms)
    {
        $tax_terms=array();
        foreach ($obj_terms as $term) {
            $tax_terms[]=array(
                'name'=>$term->name,
                'count'=>$term->count,
                'parent'=>$term->parent,
                'term_taxonomy_id'=>$term->term_taxonomy_id,
                'term_id'=>$term->term_id
            );
        }
        return $tax_terms;
    }

    private function buildSelect()
    {
        echo '<select>';
        printf('<option value="">%s</option>', __('None') );
        echo $this->getOptions();
        echo '</select>';
    }

    private function getOptions($index = 0, $level = 0)
    {
        if ( !isset($this->childs[$index]) || empty( $this->childs[$index] ) ) {
            return;
        }
        $content = '';
        foreach( $this->childs[$index] as $one ) {
            $content .= sprintf(
                '<option value="%d">%s%s</option>',
                $one,
                str_repeat('&nbsp;', 2*$level ),
                $this->names[$one]
            );
            if ( isset($this->childs[$one]) && count($this->childs[$one])) {
                $content .= $this->getOptions( $one, $level + 1 );
            }
        }
        return $content;
    }

    private function buildCheckboxes($index, &$childs, &$names, &$metaform, $ischild=false)
    {
        if (isset($childs[$index])) {
            foreach ($childs[$index] as $tid) {
                $name = $names[$tid];
                if (false) {
                ?>
                    <div style='position:relative;line-height:0.9em;margin:2px 0;<?php if ($tid!=0) echo 'margin-left:15px'; ?>' class='myzebra-taxonomy-hierarchical-checkbox'>
                        <label class='myzebra-style-label'><input type='checkbox' name='<?php echo $name; ?>' value='<?php echo $tid; ?>' <?php if (isset($values[$tid])) echo 'checked="checked"'; ?> /><span class="myzebra-checkbox-replace"></span>
                            <span class='myzebra-checkbox-label-span' style='position:relative;font-size:12px;display:inline-block;margin:0;padding:0;margin-left:15px'><?php echo $names[$tid]; ?></span></label>
                        <?php
                        if (isset($childs[$tid]))
                            echo $this->buildCheckboxes($tid,$childs,$names,$metaform);
                        ?>
                    </div>
                <?php
                }

                $metaform[] = array(
                            '#type' => 'checkbox',
                            '#title' => $names[$tid],
                            '#description' => '',
                            '#name' => $this->getName()."[]",
                            '#value' => $tid,
                            '#default_value' => in_array($tid, $this->valuesId),
                            '#attributes' => array(
                                'style' => 'float:left;'.($ischild ? 'margin-left:15px;' : '')
                            ),
                            '#validate' => $this->getValidationData(),
                        );

                if (isset($childs[$tid])) {
                    $metaform = $this->buildCheckboxes($tid,$childs,$names, $metaform, true);
                }

            }
        }
        return $metaform;
    }
}
