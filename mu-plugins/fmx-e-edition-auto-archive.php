<?php
/**
 * Monthly E-Editions auto-archive.
 *
 * /e-editions/ is a stack of issues, each an <h1> date followed by a
 * FlipHTML5 iframe embed, and the client adds new ones at the top every week.
 * Left alone it grows into dozens of flipbooks and crawls on mobile. Once a
 * month this keeps the newest FMX_EE_KEEP issues there and moves the rest to
 * the top of their year page under /e-editions/e-edition-archives/
 * (e.g. 2026-e-editions), creating the year page and its archive link if the
 * year is new.
 *
 * It runs on WP-Cron (pages found by path, not ID), so it moves with the site
 * to any host. On hosts that disable WP-Cron in favor of a system cron
 * (SpinupWP), that cron still triggers it.
 *
 * It refuses to touch anything if the page isn't in the exact
 * <h1> + iframe-wrapper format, and emails the reason instead. Year pages are
 * saved before the main page is trimmed, so a failure part way never loses an
 * issue, and every change leaves a normal WordPress revision.
 *
 * Manual run from WP-CLI:  wp eval 'print_r( fmx_ee_archive_run( true ) );'  (dry run)
 *                          wp eval 'print_r( fmx_ee_archive_run() );'        (for real)
 */

define( 'FMX_EE_KEEP', 4 );
define( 'FMX_EE_MAIN_PATH', 'e-editions' );
define( 'FMX_EE_ARCHIVE_PATH', 'e-editions/e-edition-archives' );
define( 'FMX_EE_NOTIFY', 'david@davidwalkerdesigns.com' );

add_action( 'init', 'fmx_ee_schedule' );
function fmx_ee_schedule() {
	if ( ! wp_next_scheduled( 'fmx_ee_daily_check' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'fmx_ee_daily_check' );
	}
}

// WP-Cron has no monthly interval, so check daily and run once per calendar
// month. A missed day (no traffic, server down) just runs on the next one.
add_action( 'fmx_ee_daily_check', 'fmx_ee_daily_check' );
function fmx_ee_daily_check() {
	$month = wp_date( 'Y-m' );
	if ( get_option( 'fmx_ee_last_run' ) === $month ) {
		return;
	}
	update_option( 'fmx_ee_last_run', $month, false );

	$main    = get_page_by_path( FMX_EE_MAIN_PATH );
	$archive = get_page_by_path( FMX_EE_ARCHIVE_PATH );
	$result  = ( $main && $archive )
		? fmx_ee_archive_pages( $main->ID, $archive->ID, FMX_EE_KEEP )
		: new WP_Error( 'fmx_ee_missing_page', 'Could not find /' . FMX_EE_MAIN_PATH . '/ or /' . FMX_EE_ARCHIVE_PATH . '/.' );

	if ( is_wp_error( $result ) ) {
		wp_mail( FMX_EE_NOTIFY, '[thefmextra.com] E-Edition auto-archive FAILED', $result->get_error_message() . "\n\nNothing was changed." );
	} elseif ( $result['moved'] ) {
		wp_mail( FMX_EE_NOTIFY, '[thefmextra.com] E-Edition auto-archive: moved ' . $result['moved'] . ' issue(s)', implode( "\n", $result['log'] ) );
	}
}

function fmx_ee_archive_run( $dry_run = false ) {
	$main    = get_page_by_path( FMX_EE_MAIN_PATH );
	$archive = get_page_by_path( FMX_EE_ARCHIVE_PATH );
	if ( ! $main || ! $archive ) {
		return new WP_Error( 'fmx_ee_missing_page', 'E-Editions or archive page not found.' );
	}
	return fmx_ee_archive_pages( $main->ID, $archive->ID, FMX_EE_KEEP, $dry_run );
}

/**
 * Split a page into its issues. Returns head/tail (anything before the first
 * and after the last issue, e.g. the "older issues" link) and the issues in
 * page order, or a WP_Error if the content isn't in the expected format.
 */
function fmx_ee_parse( $content ) {
	$pattern = '#<h1[^>]*>(.*?)</h1>\s*<div[^>]*>\s*<iframe\b[^>]*?\bsrc="([^"]+)"[^>]*>\s*</iframe>\s*</div>#is';
	preg_match_all( $pattern, $content, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );

	$h1s     = preg_match_all( '#<h1\b#i', $content );
	$iframes = preg_match_all( '#<iframe\b#i', $content );
	if ( count( $m ) !== $h1s || count( $m ) !== $iframes ) {
		return new WP_Error( 'fmx_ee_format', sprintf( 'Unexpected E-Editions format: %d issues matched, %d <h1>, %d <iframe>. Someone may have added an issue in a different layout.', count( $m ), $h1s, $iframes ) );
	}

	$issues = array();
	$prev   = null;
	foreach ( $m as $match ) {
		list( $html, $offset ) = $match[0];
		if ( null !== $prev && '' !== trim( substr( $content, $prev, $offset - $prev ) ) ) {
			return new WP_Error( 'fmx_ee_format', 'Unexpected content between issues near "' . wp_strip_all_tags( $match[1][0] ) . '".' );
		}
		$label = trim( wp_strip_all_tags( $match[1][0] ) );
		$time  = strtotime( $label );
		if ( ! $time ) {
			return new WP_Error( 'fmx_ee_date', 'Could not read the date in heading "' . $label . '".' );
		}
		$issues[] = array(
			'html'  => $html,
			'label' => $label,
			'time'  => $time,
			'year'  => (int) gmdate( 'Y', $time ),
			'src'   => $match[2][0],
		);
		$prev = $offset + strlen( $html );
	}

	return array(
		'head'   => $m ? substr( $content, 0, $m[0][0][1] ) : $content,
		'tail'   => $m ? substr( $content, $prev ) : '',
		'issues' => $issues,
	);
}

function fmx_ee_archive_pages( $main_id, $archive_id, $keep, $dry_run = false ) {
	// Cron and WP-CLI run with no logged-in user, and KSES would strip the
	// iframes on save. Only our own parsed embeds are being written.
	kses_remove_filters();

	$main   = get_post( $main_id );
	$parsed = fmx_ee_parse( $main->post_content );
	if ( is_wp_error( $parsed ) ) {
		return $parsed;
	}

	// Keep the newest by date, not position, in case one was added out of order.
	$by_date = $parsed['issues'];
	usort( $by_date, function ( $a, $b ) { return $b['time'] - $a['time']; } );
	$cutoff = count( $by_date ) > $keep ? $by_date[ $keep - 1 ]['time'] : 0;

	$kept  = array();
	$moved = array();
	foreach ( $parsed['issues'] as $issue ) {
		if ( $issue['time'] >= $cutoff && count( $kept ) < $keep ) {
			$kept[] = $issue;
		} else {
			$moved[ $issue['year'] ][] = $issue;
		}
	}

	$log = array();
	if ( ! $moved ) {
		return array( 'moved' => 0, 'log' => array( 'Nothing to move: ' . count( $kept ) . ' issue(s) on the main page.' ) );
	}

	krsort( $moved );
	$total = 0;
	foreach ( $moved as $year => $issues ) {
		usort( $issues, function ( $a, $b ) { return $b['time'] - $a['time']; } );
		$year_page = fmx_ee_year_page( $year, $archive_id, $main, $dry_run, $log );
		if ( is_wp_error( $year_page ) ) {
			return $year_page;
		}
		$existing = $year_page ? $year_page->post_content : '';

		// Skip any issue already on the year page (e.g. a rerun after a partial failure).
		$new = array();
		foreach ( $issues as $issue ) {
			if ( false === strpos( $existing, $issue['src'] ) ) {
				$new[] = $issue['html'];
			}
			$log[] = sprintf( '  %s -> %d E-Editions', $issue['label'], $year );
		}
		$total += count( $issues );

		if ( ! $dry_run && $new ) {
			$saved = wp_update_post( wp_slash( array(
				'ID'           => $year_page->ID,
				'post_content' => implode( "\n", $new ) . "\n" . ltrim( $existing ),
			) ), true );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}
	}

	$new_main = $parsed['head'] . implode( "\n", wp_list_pluck( $kept, 'html' ) ) . $parsed['tail'];
	if ( ! $dry_run ) {
		$saved = wp_update_post( wp_slash( array( 'ID' => $main->ID, 'post_content' => $new_main ) ), true );
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}
	}

	array_unshift( $log, sprintf( '%sMoved %d issue(s); kept %d on %s.', $dry_run ? '[DRY RUN] ' : '', $total, count( $kept ), get_permalink( $main ) ) );
	return array( 'moved' => $total, 'log' => $log );
}

/**
 * Find the year page under the archive, or create it (copying the main page's
 * author and byline/date-hiding meta) and add its link to the top of the
 * archive page's year list.
 */
function fmx_ee_year_page( $year, $archive_id, $main, $dry_run, &$log ) {
	global $wpdb;
	$slug = $year . '-e-editions';
	// Direct lookup: WP_Query hides draft/private pages from cron (no user),
	// which would create a duplicate year page.
	$existing = $wpdb->get_var( $wpdb->prepare(
		"SELECT ID FROM $wpdb->posts WHERE post_type = 'page' AND post_parent = %d AND post_name = %s AND post_status NOT IN ('trash', 'auto-draft', 'inherit') LIMIT 1",
		$archive_id,
		$slug
	) );
	if ( $existing ) {
		return get_post( $existing );
	}

	$log[] = "Creating new year page: $year E-Editions";
	if ( $dry_run ) {
		return null;
	}

	$id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => $year . ' E-Editions',
		'post_name'   => $slug,
		'post_parent' => $archive_id,
		'post_author' => $main->post_author,
	), true );
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	foreach ( array( 'suppress_author', 'suppress_date' ) as $key ) {
		$value = get_post_meta( $main->ID, $key, true );
		if ( '' !== $value ) {
			update_post_meta( $id, $key, $value );
		}
	}

	// Slot the link into the newest-first year list: before the first older
	// year, else after the last newer one, else at the top.
	$archive = get_post( $archive_id );
	$content = $archive->post_content;
	$link    = '<h3><a href="' . esc_url( get_permalink( $id ) ) . '">' . $year . '</a></h3>';
	$pos     = 0;
	if ( preg_match_all( '#<h3[^>]*>\s*<a[^>]*>\s*(\d{4})\s*</a>\s*</h3>\s*#i', $content, $links, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
		foreach ( $links as $l ) {
			if ( (int) $l[1][0] < $year ) {
				$pos = $l[0][1];
				break;
			}
			$pos = $l[0][1] + strlen( $l[0][0] );
		}
	}
	wp_update_post( wp_slash( array(
		'ID'           => $archive_id,
		'post_content' => substr( $content, 0, $pos ) . $link . "\n" . substr( $content, $pos ),
	) ) );

	return get_post( $id );
}
