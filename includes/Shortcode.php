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
            $id = absint($id);

            if(!current_user_can('read_post', $id)){
                return '';
            }

            $post_type = get_post_type($id);

            if($post_type !== 'a3dmv-viewer'){
                return false;
            }
            $post = get_post($id);
    
            if($post){
                $blocks = parse_blocks($post->post_content);
                return render_block($blocks[0]);
            }
            return 'something went wrong!';
        }

        $block = $this->generate_advanced_model_viewer_to_block($attrs);

        return render_block($block);

    }


    public function generate_advanced_model_viewer_to_block($attrs){
        extract($attrs);

        // Whitelist the loading attribute value
        $allowed_loading = array('eager', 'lazy', 'auto');
        $safe_loading = in_array($loading, $allowed_loading, true) ? $loading : 'eager';

        // Validate CSS dimensions (e.g. "400px", "100%", "50vh")
        $safe_height = preg_match('/^\d+(\.\d+)?(px|%|vh|vw|em|rem|pt|cm|mm|in)$/', trim($height)) ? trim($height) : '400px';
        $safe_width  = preg_match('/^\d+(\.\d+)?(px|%|vh|vw|em|rem|pt|cm|mm|in)$/', trim($width)) ? trim($width) : '100%';

        // Validate CSS color (hex, named color, rgb/rgba/hsl/hsla, or transparent)
        $safe_bg_color = preg_match('/^(#[0-9a-fA-F]{3,8}|[a-zA-Z]+|(rgb|rgba|hsl|hsla)\([^)]*\)|transparent)$/', trim($bg_color)) ? trim($bg_color) : 'transparent';

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
                    'loading' => $safe_loading,
                ],
                'style' => [
                    'height' => $safe_height,
                    'width'  => $safe_width,
                    'bgColor' => $safe_bg_color,
                ],
                'className' => esc_attr($class_name)
            ],
            'innerBlocks' => [],
            'innerHTML' => '',
            'innerContent' => []
        ];
    }

}