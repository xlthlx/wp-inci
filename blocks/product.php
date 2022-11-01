<?php
/**
 * Gutenberg Block Product
 *
 * @category Plugin
 * @package  Wpinci
 * @author   xlthlx <wp-inci@piccioni.london>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPL 3
 * @link     https://wordpress.org/plugins/wp-inci/
 */

use Carbon_Fields\Block;
use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Field;

require_once WPINCI_BASE_PATH . '/public/class-wp-inci-frontend.php';

/**
 * Load Carbon Fields.
 *
 * @return void
 */
function Wi_Load_cb()
{
    Carbon_Fields::boot();
}

add_action('after_setup_theme', 'Wi_Load_cb');

/**
 * Get list of products.
 *
 * @return array
 */
function Wi_Get_products(): array
{

    $results = [];

    $args = array(
    'post_type' => array( 'product' ),
    'post_status' => 'publish',
    'order' => 'ASC',
    'ordeby' => 'title'
    );

    $the_query = new WP_Query($args);

    if ($the_query->have_posts() ) {

        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $results[ get_the_ID() ] = get_the_title();
        }

    }

    wp_reset_postdata();

    return $results;
}

/**
 * Register Product block.
 *
 * @return void
 */
function Wi_Product_block()
{
    Block::make(__('Product', 'wp-inci'))
        ->add_fields(
            [
             Field::make('text', 'title', __('Custom title', 'wp-inci'))->set_help_text('Leave blank to show the product title'),
             Field::make('select', 'product', __('Select product', 'wp-inci'))->add_options('wi_get_products'),
             Field::make('checkbox', 'linked', __('Show link', 'wp-inci'))->set_option_value('yes'),
             Field::make('checkbox', 'list', __('Hide ingredient list', 'wp-inci'))->set_option_value('yes'),
            Field::make('checkbox', 'safety', __('Hide safety', 'wp-inci'))->set_option_value('yes')->set_conditional_logic(
                [
                 [
                     'field' => 'list',
                     'value' => false,
                 ]
                ] 
            ),
            ] 
        )
        ->set_category('wp-inci')
        ->set_icon('wp-inci')
        ->set_render_callback(
            function (
                $fields
            ) {
                ?>

                 <div class="block">

                     <div class="block__product">
                         <div class="wp-inci">
                <?php
                $start = '<h3>';
                $end   = '</h3>';
                $title = esc_html(get_the_title($fields['product']));

                if (isset($fields['title']) && '' !== $fields['title'] ) {
                    $title = esc_html($fields['title']);
                }

                if ($fields['linked'] ) {
                    $start = '<h3><a title="' . $title . '" href="' . get_permalink($fields['product']) . '">';
                    $end   = '</a></h3>';
                }

                echo $start . $title . $end;

                $safety = 'true';

                if (isset($fields['safety']) && true === $fields['safety'] ) {
                    $safety = 'false';
                }

                if (! $fields['list'] ) {
                    echo ( Wp_Inci_Frontend::getInstanceFrontend() )->getIngredientsTable(
                        $fields['product'],
                        $safety
                    );
                }
                ?>
                         </div>
                     </div>
                 </div>

                <?php
            }
        );
}

add_action('carbon_fields_register_fields', 'Wi_Product_block');