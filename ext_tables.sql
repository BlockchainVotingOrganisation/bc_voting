# Revision 138

#
# Table structure for table 'tx_bcvoting_domain_model_asset'
# Blockchain assets
#
#
CREATE TABLE tx_bcvoting_domain_model_asset (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	name varchar(80) DEFAULT '' NOT NULL,
	divisibility int(11) DEFAULT '0' NOT NULL,
	quantity int(11) DEFAULT '0' NOT NULL,
	asset_id varchar(20) DEFAULT '' NOT NULL,
	icon_url varchar(255) DEFAULT '' NOT NULL,
	ticker varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_bcvoting_domain_model_argument'
#
CREATE TABLE tx_bcvoting_domain_model_argument (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	name varchar(80) DEFAULT '' NOT NULL,
	text text NOT NULL,
	prooption int(11) unsigned DEFAULT '0' NOT NULL,
	project int(11) unsigned DEFAULT '0',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)
	);

#
# Table structure for table 'tx_bcvoting_domain_model_category'
#
CREATE TABLE tx_bcvoting_domain_model_category (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	name varchar(80) DEFAULT '' NOT NULL,
	ulterior tinyint(1) DEFAULT '0' NOT NULL,
	closed tinyint(1) DEFAULT '0' NOT NULL,
	
	description varchar(255) DEFAULT '' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)
);

# Inserts for table 'category'
INSERT INTO `tx_bcvoting_domain_model_category` VALUES (1,61,'Closed Survey','A survey for a closed usergrroup voting results are secret till end of voting period',1474654187,1466423135,1,0,0,0,0,0,0,0,'',0,0,0,0,0,0,0,'a:8:{s:16:\"sys_language_uid\";N;s:6:\"hidden\";N;s:4:\"name\";N;s:11:\"description\";N;s:8:\"ulterior\";N;s:6:\"closed\";N;s:9:\"starttime\";N;s:7:\"endtime\";N;}',1,1),(2,61,'Election','',1466423161,1466423161,1,0,0,0,0,0,0,0,'',0,0,0,0,0,0,0,'a:6:{s:16:\"sys_language_uid\";N;s:6:\"hidden\";N;s:4:\"name\";N;s:11:\"description\";N;s:9:\"starttime\";N;s:7:\"endtime\";N;}',0,0),(3,61,'Petition','',1474654580,1466423171,1,0,0,0,0,0,0,0,'',0,0,0,0,0,0,0,'a:8:{s:16:\"sys_language_uid\";N;s:6:\"hidden\";N;s:4:\"name\";N;s:11:\"description\";N;s:8:\"ulterior\";N;s:6:\"closed\";N;s:9:\"starttime\";N;s:7:\"endtime\";N;}',0,0),(4,61,'Open Survey','An open survey: results are visible while survey period',1474654387,1466489093,1,0,0,0,0,0,0,0,'',0,0,0,0,0,0,0,'a:8:{s:16:\"sys_language_uid\";N;s:6:\"hidden\";N;s:4:\"name\";N;s:11:\"description\";N;s:8:\"ulterior\";N;s:6:\"closed\";N;s:9:\"starttime\";N;s:7:\"endtime\";N;}',0,0),(5,61,'Open Survey (Secret)','',1474654463,1466497894,1,0,0,0,0,0,0,0,'',0,0,0,0,0,0,0,'a:8:{s:16:\"sys_language_uid\";N;s:6:\"hidden\";N;s:4:\"name\";N;s:11:\"description\";N;s:8:\"ulterior\";N;s:6:\"closed\";N;s:9:\"starttime\";N;s:7:\"endtime\";N;}',1,0),(6,61,'Yes/No (Open)','Only available options are \"Yes\" or \"No\".',1474656798,1474654676,1,0,0,0,0,0,0,0,'',0,0,0,0,0,0,0,'a:8:{s:16:\"sys_language_uid\";N;s:6:\"hidden\";N;s:4:\"name\";N;s:11:\"description\";N;s:8:\"ulterior\";N;s:6:\"closed\";N;s:9:\"starttime\";N;s:7:\"endtime\";N;}',1,0),(7,61,'Yes/No (Closed)','',1474656827,1474656718,1,0,0,0,0,0,0,0,'',0,0,0,0,0,0,0,'a:8:{s:16:\"sys_language_uid\";N;s:6:\"hidden\";N;s:4:\"name\";N;s:11:\"description\";N;s:8:\"ulterior\";N;s:6:\"closed\";N;s:9:\"starttime\";N;s:7:\"endtime\";N;}',1,1);


#
# Table structure for table 'tx_bcvoting_domain_model_voting'
#
CREATE TABLE tx_bcvoting_domain_model_voting (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	reference varchar(255) DEFAULT '' NOT NULL,
	txid varchar(255) DEFAULT '' NOT NULL,
	secret varchar(255) DEFAULT '' NOT NULL,
	project int(11) unsigned DEFAULT '0' NOT NULL,
	ballot int(11) unsigned DEFAULT '0' NOT NULL,
	
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_bcvoting_domain_model_ballot'
#
CREATE TABLE tx_bcvoting_domain_model_ballot (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	
	asset varchar(25) DEFAULT '' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	logo int(11) unsigned DEFAULT '0' NOT NULL,
	reference varchar(80) DEFAULT '' NOT NULL,
	votes tinyint(2) unsigned DEFAULT '1' NOT NULL,
	project int(11) unsigned DEFAULT '0',
	text text NOT NULL ,
	footer text NOT NULL,
	options int(11) unsigned DEFAULT '0' NOT NULL,
	start int(11) unsigned DEFAULT '0' NOT NULL,
	end int(11) unsigned DEFAULT '0' NOT NULL,
	wallet_address varchar(80) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_bcvoting_domain_model_project'
#
CREATE TABLE tx_bcvoting_domain_model_project (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	password varchar(80) DEFAULT '' NOT NULL,
	stream varchar(30) DEFAULT '' NOT NULL,
	private_key varchar(255) DEFAULT '' NOT NULL,
	public_key varchar(255) DEFAULT '' NOT NULL,
	deposit_btc varchar(80) DEFAULT '' NOT NULL,

	category int(11) unsigned DEFAULT '0' NOT NULL,
	logo int(11) unsigned DEFAULT '0' NOT NULL,
	
	start int(11) unsigned DEFAULT '0' NOT NULL,
	end int(11) unsigned DEFAULT '0' NOT NULL,
	assignments int(11) unsigned DEFAULT '0' NOT NULL,
	ballots int(11) unsigned DEFAULT '0' NOT NULL,
	reference varchar(80) DEFAULT '' NOT NULL,
	anonym tinyint(4) unsigned DEFAULT '0' NOT NULL,
	open tinyint(4) unsigned DEFAULT '0' NOT NULL,
	
	blockchain_name varchar(255) DEFAULT '' NOT NULL,
	rpc_server varchar(255) DEFAULT '' NOT NULL,
	rpc_user varchar(255) DEFAULT '' NOT NULL,
	rpc_password varchar(255) DEFAULT '' NOT NULL,
	rpc_port int(5) DEFAULT '0' NOT NULL,
	infosite varchar(255) DEFAULT '' NOT NULL,
	forum_url varchar(255) DEFAULT '' NOT NULL,
	blockchain_explorer varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	wallet_address varchar(80) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_bcvoting_domain_model_option'
#
CREATE TABLE tx_bcvoting_domain_model_option (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	parent int(11) unsigned DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	wallet_address varchar(80) DEFAULT '' NOT NULL,
	ballot int(11) unsigned DEFAULT '0',
	logo int(11) unsigned DEFAULT '0' NOT NULL,
	color varchar(7) DEFAULT '#000' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_bcvoting_domain_model_assignment'
#
CREATE TABLE tx_bcvoting_domain_model_assignment (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	project int(11) unsigned DEFAULT '0',
	role int(11) unsigned DEFAULT '0',
	user int(11) unsigned DEFAULT '0',
	wallet_address varchar(80) DEFAULT '' NOT NULL,
	votes int(5) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_bcvoting_domain_model_role'
#
CREATE TABLE tx_bcvoting_domain_model_role (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_bcvoting_domain_model_element'
#
CREATE TABLE tx_bcvoting_domain_model_element (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	property varchar(255) DEFAULT '' NOT NULL,
	project int(11) unsigned DEFAULT '0' NOT NULL,
	description text NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)
);