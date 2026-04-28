<?php
/**
 * Title: Dashboard Preview
 * Slug: angelcamp-platform/dashboard-preview
 * Categories: angelcamp-platform, platform, features
 * Description: Statische KPI- und Verfügbarkeitsansicht als Dashboard-Mockup.
 */
?>
<!-- wp:group {"tagName":"section","className":"angelcamp-section","layout":{"type":"constrained"}} -->
<section class="wp-block-group angelcamp-section"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Operative Übersicht in Echtzeit', 'angelcamp-platform' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"66%"} -->
<div class="wp-block-column" style="flex-basis:66%"><!-- wp:group {"className":"angelcamp-grid","layout":{"type":"constrained"}} -->
<div class="wp-block-group angelcamp-grid"><!-- wp:group {"className":"angelcamp-card"} -->
<div class="wp-block-group angelcamp-card"><p><strong><?php echo esc_html__( 'Buchungen diese Saison', 'angelcamp-platform' ); ?></strong><br>248</p></div>
<!-- /wp:group -->
<!-- wp:group {"className":"angelcamp-card"} --><div class="wp-block-group angelcamp-card"><p><strong><?php echo esc_html__( 'Offene Zahlungen', 'angelcamp-platform' ); ?></strong><br>17</p></div><!-- /wp:group -->
<!-- wp:group {"className":"angelcamp-card"} --><div class="wp-block-group angelcamp-card"><p><strong><?php echo esc_html__( 'Bootsauslastung', 'angelcamp-platform' ); ?></strong><br>78 %</p><div class="angelcamp-progress"><span style="width:78%"></span></div></div><!-- /wp:group -->
<!-- wp:group {"className":"angelcamp-card"} --><div class="wp-block-group angelcamp-card"><p><strong><?php echo esc_html__( 'Umsatz Vorschau', 'angelcamp-platform' ); ?></strong><br>2,8 Mio. NOK</p></div><!-- /wp:group -->
<!-- wp:group {"className":"angelcamp-card"} --><div class="wp-block-group angelcamp-card"><p><strong><?php echo esc_html__( 'Verfügbare Boote heute', 'angelcamp-platform' ); ?></strong><br>11 / 15</p></div><!-- /wp:group -->
<!-- wp:group {"className":"angelcamp-card"} --><div class="wp-block-group angelcamp-card"><p><strong><?php echo esc_html__( 'Wiederkehrende Gäste', 'angelcamp-platform' ); ?></strong><br>42 %</p><div class="angelcamp-progress"><span style="width:42%"></span></div></div><!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"34%"} -->
<div class="wp-block-column" style="flex-basis:34%"><!-- wp:group {"className":"angelcamp-card"} -->
<div class="wp-block-group angelcamp-card"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html__( 'Status heute', 'angelcamp-platform' ); ?></h3>
<!-- /wp:heading -->
<p><span class="angelcamp-chip angelcamp-chip--success"><?php echo esc_html__( 'Bezahlt', 'angelcamp-platform' ); ?></span><span class="angelcamp-chip angelcamp-chip--warning"><?php echo esc_html__( 'Offen', 'angelcamp-platform' ); ?></span><span class="angelcamp-chip angelcamp-chip--neutral"><?php echo esc_html__( 'Service nötig', 'angelcamp-platform' ); ?></span><span class="angelcamp-chip angelcamp-chip--success"><?php echo esc_html__( 'Verfügbar', 'angelcamp-platform' ); ?></span></p>
<ul>
<li><?php echo esc_html__( 'Boot A12 — verfügbar', 'angelcamp-platform' ); ?></li>
<li><?php echo esc_html__( 'Boot C03 — Service morgen', 'angelcamp-platform' ); ?></li>
<li><?php echo esc_html__( 'Haus Fjord 4 — Check-in 16:00', 'angelcamp-platform' ); ?></li>
<li><?php echo esc_html__( 'Haus Nord 2 — Restzahlung offen', 'angelcamp-platform' ); ?></li>
</ul></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
