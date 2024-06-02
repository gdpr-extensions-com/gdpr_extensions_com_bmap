CREATE TABLE tx_gdprextensionscomyoutube_domain_model_gdprmanager (
	heading text NOT NULL DEFAULT '',
	content text NOT NULL DEFAULT '',
	button_text text NOT NULL DEFAULT '',
	enable_background_image int(11) NOT NULL DEFAULT '0',
	background_image text NOT NULL DEFAULT '',
	background_image_color varchar(255) NOT NULL DEFAULT '',
	button_color varchar(255) NOT NULL DEFAULT '',
	button_text_color varchar(255) NOT NULL DEFAULT '',
	text_color varchar(255) NOT NULL DEFAULT '',
	button_shape varchar(255) NOT NULL DEFAULT '',
	extension_title varchar(255) NOT NULL DEFAULT '',
	extension_key varchar(255) NOT NULL DEFAULT '',
	locations int(11) unsigned NOT NULL DEFAULT '0',
	map_api varchar(255) NOT NULL DEFAULT '',

);

CREATE TABLE tt_content (
	map_marker_image int(11) unsigned DEFAULT '0' NOT NULL,
	gdpr_bm_business_locations varchar(255) NOT NULL DEFAULT ''
);

CREATE TABLE tx_gdprextensionscombmap_domain_model_maplocation (
	map int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(255) NOT NULL DEFAULT '',
	address varchar(255) NOT NULL DEFAULT '',
	lat int(11) NOT NULL DEFAULT '0',
	lon int(11) NOT NULL DEFAULT '0'
);
