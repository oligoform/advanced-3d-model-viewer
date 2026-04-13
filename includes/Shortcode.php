<?php 
namespace A3DMV;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Shortcode{

    private $plugin_name;
    private $version;
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function a3dmv_model_viewer($atts){
        $attrs = (shortcode_atts( array(
            'id'        => null,
            "model_url"  => null,
            "poster_url"  => null,
            "alignment"  => "left",
            "align" => null,
            "auto_rotate" => false,
            "loading" => 'eager',
            "mouse_interaction" => true,
            "bg_color" => 'transparent',
            "height" => '400px',
            "width" => '100%',
            "class_name" => null
        ), $atts, 'a3dmv_model_viewer'));

        extract($attrs);

        if($id !== null){
            $id = absint($id); // Ensure ID is a positive integer
            if ($id <= 0) {
                return '';
            }

            $post_type = get_post_type($id);

            if($post_type !== 'a3dmv-viewer'){
                return '';
            }

            // Check post status and permissions to prevent private/draft post disclosure
            $post = get_post($id);

            if(!$post){
                return '';
            }

            // Only allow published posts or posts the current user can edit
            if($post->post_status !== 'publish' && !current_user_can('edit_post', $id)){
                return '';
            }

            $blocks = parse_blocks($post->post_content);
            if(empty($blocks)){
                return '';
            }
            return render_block($blocks[0]);
        }

        $block = $this->generate_advanced_model_viewer_to_block($attrs);

        return render_block($block);

    }


    public function generate_advanced_model_viewer_to_block($attrs){
        extract($attrs);
        
        return [
            'blockName' => 'a3dmv/model-viewer',
            'attrs' => [
                'clientId' => wp_unique_id('a3dmv'),
                'align' => esc_attr($align),
                'alignment' => esc_attr($alignment),
                'model' => [
                    'model_url' => esc_url_raw($model_url),
                    'poster_url' => esc_url_raw($poster_url),
                ],
                'attrs' => [
                    'auto-rotate' => $auto_rotate === 'true',
                    'camera-controls' => $mouse_interaction === 'true',
                    'loading' => $loading
                ],
                'style' => [
                    'height' => esc_html($height),
                    'width' => esc_html($width),
                    'bgColor' => esc_attr($bg_color),
                ],
                'className' => esc_attr($class_name)
            ],
            'innerBlocks' => [],
            'innerHTML' => '',
            'innerContent' => []
        ];
    }

}