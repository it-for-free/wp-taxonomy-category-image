<?php

/*
  Plugin Name: [WGT] Taxonomy category Image // Изображение для категории таксономии
  Plugin URI: https://github.com/it-for-free/wp-taxonomy-category-image
  Description: Добавит возможность добавлять изображение к элементам таксономии // Taxonomy Category Image Field
  Version: 0.0.1
  Author: vedro-compota
  Author URI: https://github.com/it-for-free/wp-taxonomy-category-image
 */

use ItForFree\WpAddons\Core\Admin\Settings\SettingsPage\SettingsPage;
use ItForFree\WpAddons\Core\Admin\Settings\SettingsPage\Section\Field\Html\TaxonomiesCheckboxList;

$Page = new SettingsPage('iff-taxonomy-image', 
    "Плагин поля изображения для элемента таксономии ",
    "Изображение для эелмета таксономии (плагин)");
$Page->createAndAddSettingsEntity()
    ->createAndAddSection('main', 'Настройки плагина', 'Используйте форму ниже, чтобы задать настройки');

$Page->getSectionById('main')->addSectionField(
   new TaxonomiesCheckboxList($Page->getSectionById('main'), 'checked_taxonomies',
        'Выбирите типы такосономий, дя которых следует активировать плагин')
);

TaxonomyCategoryImagePlugin::init();

class TaxonomyCategoryImagePlugin
{
    public static function init()
    {
        
        $module = new TaxonomyCategoryImagePlugin();
        
        $options = get_option('iff-taxonomy-image_options');
        $taxonomies = $options['checked_taxonomies'];

        if(!empty($taxonomies)){

                foreach ($taxonomies as $taxonomy) {
                add_action($taxonomy . '_add_form_fields', [$module, 'addCategoryImage']);
                add_action($taxonomy . '_edit_form_fields', [$module, 'editCategoryImage']);
            }
        }
        
            //edit_$taxonomy
        add_action('edit_term', [$module, 'saveCategoryImage']);
        add_action('create_term',[$module, 'saveCategoryImage']);
    }
    
    //Function to add category/taxonomy image
    function addCategoryImage($taxonomy){ ?>
        <div class="form-field">
            <label for="tag-image">Изображение категории</label>
            <input type="text" name="tag-image" id="tag-image" value="" />	
            <p class="description">Кликните по этому полю чтобы изменить изображение категории таксономии.</p>
    </div>

    <?php $this->includeFieldAssets(); }


    //Function to edit category/taxonomy image
    function editCategoryImage($taxonomy){ ?>
    <tr class="form-field">
            <th scope="row" valign="top"><label for="tag-image">Изображение категории</label></th>
            <td>
            <?php 
            if(get_option('_category_image'.$taxonomy->term_id) != ''){ ?>
                    <img src="<?php echo get_option('_category_image'.$taxonomy->term_id); ?>" width="100"  height="100"/>
            <?php	
            }
            ?><br />
            <input type="text" name="tag-image" id="tag-image" value="<?php echo get_option('_category_image'.$taxonomy->term_id); ?>" />
            <p class="description">Кликните по этому полю чтобы изменить изображение категории таксономии.</p>
            </td>
    </tr>              
    <?php $this->includeFieldAssets(); }

    function includeFieldAssets(){ ?>

    <!-- In WP 5 thickbox is on page by default (so two lines below are commented): -->
    <!--<script type="text/javascript" src="<?php echo plugins_url(); ?>/wp-taxonomy-category-image/includes/thickbox.js"></script>-->
    <!--<link rel='stylesheet' id='thickbox-css'  href='<?php echo includes_url(); ?>js/thickbox/thickbox.css' type='text/css' media='all' />-->
    <script type="text/javascript">    
     jQuery(document).ready(function() {
      var fileInput = ''; 
      jQuery('#tag-image').live('click',
      function() {
        fileInput = jQuery('#tag-image');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');


        return false;
      }); 
            window.original_send_to_editor = window.send_to_editor;
      window.send_to_editor = function(html) {
        if (fileInput) {
          fileurl = jQuery('img', html).attr('src');
          if (!fileurl) {
            fileurl = jQuery(html).attr('src');
                                    fileurl = freeFromDomain(fileurl);
                                    console.log(fileurl);
          }
          jQuery(fileInput).val(fileurl);

          tb_remove();
        } else {
          window.original_send_to_editor(html);
        }
      };

            function freeFromDomain(str)
            {
                var result = str;
                console.log(str, 'str');
                if ((typeof str === 'string' || str instanceof String) 
                        && (str.indexOf('://') !== -1)) {
                    result = '/' + str.substr(str.indexOf('/', 7) + 1);
                }
                return result;
            }
        });

    </script>
    <?php }


    function saveCategoryImage($term_id){
        if(isset($_POST['tag-image'])){
            if(isset($_POST['tag-image']))
                update_option('_category_image'.$term_id, $_POST['tag-image'] );
        }
    }
    
    /**
     * Вернёт путь к изображению категории таксономии
     * @param type $term_id
     * @return type
     */
    static  public  function getImagePath($term_id)
    {
	return get_option('_category_image'.$term_id);	
    }

}