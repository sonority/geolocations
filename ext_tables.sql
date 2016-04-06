#
# Table structure for table 'tx_geolocations_domain_model_location'
#
CREATE TABLE tx_geolocations_domain_model_location (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group varchar(100) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	bodytext mediumtext,
	image int(11) DEFAULT '0' NOT NULL,
	marker int(11) DEFAULT '0' NOT NULL,
	latitude varchar(30) DEFAULT '' NOT NULL,
	longitude varchar(30) DEFAULT '' NOT NULL,
	place_id varchar(100) DEFAULT '' NOT NULL,
	address varchar(80) DEFAULT '' NOT NULL,
	zip varchar(8) DEFAULT '' NOT NULL,
	city varchar(50) DEFAULT '' NOT NULL,
	zone varchar(50) DEFAULT '' NOT NULL,
	country varchar(50) DEFAULT '' NOT NULL,
	www varchar(50) DEFAULT '' NOT NULL,
	email varchar(50) DEFAULT '' NOT NULL,
	phone varchar(50) DEFAULT '' NOT NULL,
	status int(11) DEFAULT '1' NOT NULL,
	datetime int(11) DEFAULT '0' NOT NULL,
	fe_user int(11) DEFAULT '0' NOT NULL,
	categories int(11) unsigned DEFAULT '0' NOT NULL,
	import_id int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),

);

#
# Table structure for table 'tx_geolocations_domain_model_category'
#
CREATE TABLE tx_geolocations_domain_model_category (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	image int(11) DEFAULT '0' NOT NULL,
	marker int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),

);

#
# Table structure for table 'tx_geolocations_location_category_mm'
#
CREATE TABLE tx_geolocations_location_category_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);
