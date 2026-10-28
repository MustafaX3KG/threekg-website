<?php
/** @var array $values — assigned by the renderer (Elementor) or omitted (ACF blocks). */
$values  = isset( $values ) && is_array( $values ) ? $values : array();
$heading = threekg_core_value( $values, 'heading', threekg_core_field( 'heading', "Enough range to win\nthe brief. Small enough\nto care about it." ) );
$lead = threekg_core_value( $values, 'lead', threekg_core_field( 'lead', 'ThreeKG is an independent design practice working with founders and cultural brands who would rather show than explain.' ) );
$body = threekg_core_value( $values, 'body', threekg_core_field( 'body', 'We take on a handful of projects at a time so the people you brief are the people doing the work.' ) );
$stats = threekg_core_value( $values, 'stats', threekg_core_field( 'stats', array() ) );
if ( ! is_array( $stats ) || ! $stats ) { $stats = array( array( 'value' => '48', 'label' => 'projects shipped' ), array( 'value' => '30', 'label' => 'clients worldwide' ), array( 'value' => '11', 'label' => 'industry awards' ), array( 'value' => '05', 'label' => 'years in practice' ) ); }
?>
<section class="studio" data-threekg-studio><div class="section-head reveal"><span class="mono">[05 — the studio]</span><span class="mono">est. 2021</span><h2><?php echo nl2br( esc_html( $heading ) ); ?></h2></div><div class="studio__grid"><p class="reveal studio__lead"><?php echo esc_html( $lead ); ?></p><p class="reveal studio__body"><?php echo esc_html( $body ); ?></p></div><?php $image = threekg_core_image( threekg_core_field( 'image', 0 ), 'studio__image' ); if ( $image ) { echo wp_kses_post( $image ); } ?><ul class="studio__stats reveal"><?php foreach ( $stats as $stat ) : if ( empty( $stat['value'] ) && empty( $stat['label'] ) ) { continue; } ?><li><span class="mono stat__num"><?php echo esc_html( $stat['value'] ?? '' ); ?></span><span class="mono stat__cap"><?php echo esc_html( $stat['label'] ?? '' ); ?></span></li><?php endforeach; ?></ul></section>

