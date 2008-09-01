ALTER TABLE jos_polls
	ADD fb_poll_type tinyint(1) default NULL COMMENT 'Define the type of FB Poll.';

ALTER TABLE jos_fb_messages
	ADD poll_id int(11) default NULL COMMENT 'Stores the POLL ID associated with it.';

CREATE TABLE jos_fb_poll_result
(
  vid int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL,
  v_option int(11) NOT NULL,
  PRIMARY KEY  (vid)
);