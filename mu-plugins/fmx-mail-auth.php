<?php
/**
 * DKIM-sign the site's outgoing mail and align its return-path.
 *
 * WordPress hands mail to the local sendmail, so it went out unsigned with an
 * envelope sender of the server's system user (e.g. admin_ftp5@<server
 * hostname>), not thefmextra.com — Gmail filed it as spam. Plesk's own DKIM
 * only signs mail arriving over SMTP, so it never touched this. Signing here
 * travels with the site to any host.
 *
 * Needs FMX_DKIM_KEY in wp-config.php: path to the private key, kept outside
 * the web root. The public half is the Cloudflare TXT record
 * wp._domainkey.thefmextra.com. On a host move, copy the key and update the
 * constant; SPF (thefmextra.com TXT) must also allow the new server's IP.
 */
add_action( 'phpmailer_init', 'fmx_mail_auth' );
function fmx_mail_auth( $phpmailer ) {
	$from   = $phpmailer->From;
	$domain = strtolower( substr( strrchr( $from, '@' ), 1 ) );
	if ( 'thefmextra.com' !== $domain ) {
		return;
	}

	// Envelope sender = From, so SPF is checked against thefmextra.com.
	$phpmailer->Sender = $from;

	if ( defined( 'FMX_DKIM_KEY' ) && is_readable( FMX_DKIM_KEY ) ) {
		$phpmailer->DKIM_domain   = 'thefmextra.com';
		$phpmailer->DKIM_selector = 'wp';
		$phpmailer->DKIM_private  = FMX_DKIM_KEY;
		$phpmailer->DKIM_identity = $from;
	}
}
