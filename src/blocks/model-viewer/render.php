<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
extract($attributes );

$className = $className ?? '';
$blockClassName = 'wp-block-a3dmv ' . $className . "align".$align;

// Validate alignment to prevent CSS injection - only allow valid CSS text-align values
$valid_alignments = array('left', 'right', 'center', 'justify', 'start', 'end', '');
$safe_alignment = in_array($alignment, $valid_alignments, true) ? $alignment : 'left';

?>

<div
    style="text-align:<?php echo esc_attr($safe_alignment)  ?>"
    class='<?php echo esc_attr( $blockClassName ); ?>'
    data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'
>
</div>