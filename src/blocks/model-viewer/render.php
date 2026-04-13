<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
extract($attributes );

$className = $className ?? '';
$blockClassName = 'wp-block-a3dmv ' . $className . "align".$align;

$allowed_alignments = array( 'left', 'center', 'right' );
$safe_alignment = in_array( $alignment, $allowed_alignments, true ) ? $alignment : 'left';

?>

<div 
    style="text-align:<?php echo esc_attr( $safe_alignment ) ?>"
    class='<?php echo esc_attr( $blockClassName ); ?>' 
    data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'
>
</div>