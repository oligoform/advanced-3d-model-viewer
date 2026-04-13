<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
extract($attributes );

$className = $className ?? '';

$allowed_block_alignments = array( '', 'wide', 'full' );
$safe_align = in_array( $align, $allowed_block_alignments, true ) ? $align : '';

$blockClassName = 'wp-block-a3dmv'
    . ( $className ? ' ' . esc_attr( $className ) : '' )
    . ( $safe_align ? ' align' . esc_attr( $safe_align ) : '' );

$allowed_alignments = array( 'left', 'center', 'right' );
$safe_alignment = in_array( $alignment, $allowed_alignments, true ) ? $alignment : 'left';

?>

<div 
    style="text-align:<?php echo esc_attr( $safe_alignment ) ?>"
    class='<?php echo esc_attr( $blockClassName ); ?>' 
    data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'
>
</div>