create table "jos_banner" (
  "bid" serial not null,
  "cid" bigint not null default '0',
  "type" varchar(30) not null default 'banner',
  "name" varchar(255) not null default '',
  "alias" varchar(255) not null default '',
  "imptotal" bigint not null default '0',
  "impmade" bigint not null default '0',
  "clicks" bigint not null default '0',
  "imageurl" varchar(100) not null default '',
  "clickurl" varchar(200) not null default '',
  "date" timestamp default null,
  "showbanner" integer not null default '0',
  "checked_out" integer not null default '0',
  "checked_out_time" timestamp not null default '0001-01-01 00:00:00',
  "editor" varchar(50) default null,
  "custombannercode" text,
  "catid" bigint  not null default '0',
  "description" text not null,
  "sticky" integer  not null default '0',
  "ordering" bigint not null default '0',
  "publish_up" timestamp not null default '0001-01-01 00:00:00',
  "publish_down" timestamp not null default '0001-01-01 00:00:00',
  "tags" text not null,
  "params" text not null,
  constraint jos_banner_pkey primary key  ("bid")
);
  create index  jos_banner_showbanner_index  on jos_banner using btree  ("showbanner");
  create index  jos_banner_catid_index  on jos_banner using btree  ("catid");



CREATE TABLE "jos_bannerclient" (
  "cid" serial NOT NULL,
  "name" varchar(255) NOT NULL default '',
  "contact" varchar(255) NOT NULL default '',
  "email" varchar(255) NOT NULL default '',
  "extrainfo" text NOT NULL,
  "checked_out" integer NOT NULL default '0',
  "checked_out_time" time default NULL,
  "editor" varchar(50) default NULL,
  CONSTRAINT jos_bannerclient_pkey PRIMARY KEY  ("cid")
);


CREATE TABLE "jos_bannertrack" (
  "track_date" date NOT NULL,
  "track_type" bigint  NOT NULL,
  "banner_id" bigint  NOT NULL
);

CREATE TABLE "jos_categories" (
  "id" serial NOT NULL,
  "parent_id" bigint NOT NULL default '0',
  "title" varchar(255) NOT NULL default '',
  "name" varchar(255) NOT NULL default '',
  "alias" varchar(255) NOT NULL default '',
  "image" varchar(255) NOT NULL default '',
  "section" varchar(50) NOT NULL default '',
  "image_position" varchar(30) NOT NULL default '',
  "description" text NOT NULL,
  "published" integer NOT NULL default '0',
  "checked_out" bigint  NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "editor" varchar(50) default NULL,
  "ordering" bigint NOT NULL default '0',
  "access" integer  NOT NULL default '0',
  "count" bigint NOT NULL default '0',
  "params" text NOT NULL,
  CONSTRAINT jos_categories_pkey PRIMARY KEY  ("id")
);
  CREATE INDEX  jos_categories_section ON jos_categories USING btree  ("section","published","access");
  CREATE INDEX  jos_categories_access_index  ON jos_categories USING btree  ("access");
  CREATE INDEX  jos_categories_checked_out_index  ON jos_categories USING btree  ("checked_out");



CREATE TABLE "jos_components" (
  "id" serial NOT NULL,
  "name" varchar(50) NOT NULL default '',
  "link" varchar(255) NOT NULL default '',
  "menuid" bigint  NOT NULL default '0',
  "parent" bigint  NOT NULL default '0',
  "admin_menu_link" varchar(255) NOT NULL default '',
  "admin_menu_alt" varchar(255) NOT NULL default '',
  "option" varchar(50) NOT NULL default '',
  "ordering" bigint NOT NULL default '0',
  "admin_menu_img" varchar(255) NOT NULL default '',
  "iscore" integer NOT NULL default '0',
  "params" text NOT NULL,
  "enabled" integer NOT NULL default '1',
  CONSTRAINT jos_components_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX "jos_components_parent_option_index" ON jos_components USING btree ("parent","option");



CREATE TABLE "jos_contact_details" (
  "id" serial NOT NULL,
  "name" varchar(255) NOT NULL default '',
  "alias" varchar(255) NOT NULL default '',
  "con_position" varchar(255) default NULL,
  "address" text,
  "suburb" varchar(100) default NULL,
  "state" varchar(100) default NULL,
  "country" varchar(100) default NULL,
  "postcode" varchar(100) default NULL,
  "telephone" varchar(255) default NULL,
  "fax" varchar(255) default NULL,
  "misc" text,
  "image" varchar(255) default NULL,
  "imagepos" varchar(20) default NULL,
  "email_to" varchar(255) default NULL,
  "default_con" integer  NOT NULL default '0',
  "published" integer  NOT NULL default '0',
  "checked_out" bigint  NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "ordering" bigint NOT NULL default '0',
  "params" text NOT NULL,
  "user_id" bigint NOT NULL default '0',
  "catid" bigint NOT NULL default '0',
  "access" integer  NOT NULL default '0',
  "mobile" varchar(255) NOT NULL default '',
  "webpage" varchar(255) NOT NULL default '',
  CONSTRAINT jos_contact_details_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_contact_details_catid_index  ON jos_contact_details USING btree  ("catid");


CREATE TABLE "jos_content" (
  "id" serial NOT NULL,
  "title" varchar(255) NOT NULL default '',
  "alias" varchar(255) NOT NULL default '',
  "title_alias" varchar(255) NOT NULL default '',
  "introtext" text NOT NULL,
  "fulltext" text NOT NULL,
  "state" integer NOT NULL default '0',
  "sectionid" bigint  NOT NULL default '0',
  "mask" bigint  NOT NULL default '0',
  "catid" bigint  NOT NULL default '0',
  "created" timestamp NOT NULL default '0001-01-01 00:00:00',
  "created_by" bigint  NOT NULL default '0',
  "created_by_alias" varchar(255) NOT NULL default '',
  "modified" timestamp NOT NULL default '0001-01-01 00:00:00',
  "modified_by" bigint  NOT NULL default '0',
  "checked_out" bigint  NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "publish_up" timestamp NOT NULL default '0001-01-01 00:00:00',
  "publish_down" timestamp NOT NULL default '0001-01-01 00:00:00',
  "images" text NOT NULL,
  "urls" text NOT NULL,
  "attribs" text NOT NULL,
  "version" bigint  NOT NULL default '1',
  "parentid" bigint  NOT NULL default '0',
  "ordering" bigint NOT NULL default '0',
  "metakey" text NOT NULL,
  "metadesc" text NOT NULL,
  "access" bigint  NOT NULL default '0',
  "hits" bigint  NOT NULL default '0',
  "metadata" text NOT NULL,
  CONSTRAINT jos_content_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_content_sectionid_index  ON jos_content USING btree  ("sectionid");
  CREATE INDEX  jos_content_access_index  ON jos_content USING btree  ("access");
  CREATE INDEX  jos_content_checked_out_index  ON jos_content USING btree  ("checked_out");
  CREATE INDEX  jos_content_state_index  ON jos_content USING btree  ("state");
  CREATE INDEX  jos_content_catid_index  ON jos_content USING btree  ("catid");
  CREATE INDEX  jos_content_created_by_index  ON jos_content USING btree  ("created_by");

CREATE TABLE "jos_content_frontpage" (
  "content_id" bigint NOT NULL default '0',
  "ordering" bigint NOT NULL default '0',
  CONSTRAINT jos_content_frontpage_pkey PRIMARY KEY  ("content_id")
);


CREATE TABLE "jos_content_rating" (
  "content_id" bigint NOT NULL default '0',
  "rating_sum" bigint  NOT NULL default '0',
  "rating_count" bigint  NOT NULL default '0',
  "lastip" varchar(50) NOT NULL default '',
  CONSTRAINT jos_content_rating_pkey PRIMARY KEY  ("content_id")
);


CREATE TABLE "jos_core_acl_aro" (
  "id" serial NOT NULL,
  "section_value" varchar(240) NOT NULL default '0',
  "value" varchar(240) NOT NULL default '',
  "order_value" bigint NOT NULL default '0',
  "name" varchar(255) NOT NULL default '',
  "hidden" bigint NOT NULL default '0',
  CONSTRAINT jos_core_acl_aro_pkey PRIMARY KEY  ("id"),
  CONSTRAINT jos_core_acl_aro_key UNIQUE ("section_value","value")
) ;
  CREATE INDEX  jos_core_acl_aro_hidden_index  ON jos_core_acl_aro USING btree  ("hidden");




CREATE TABLE "jos_core_acl_aro_groups" (
  "id" serial NOT NULL,
  "parent_id" bigint NOT NULL default '0',
  "name" varchar(255) NOT NULL default '',
  "lft" bigint NOT NULL default '0',
  "rgt" bigint NOT NULL default '0',
  "value" varchar(255) NOT NULL default '',
  CONSTRAINT jos_core_acl_aro_groups_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_core_acl_aro_groups_parent_id_index  ON jos_core_acl_aro_groups USING btree  ("parent_id");
  CREATE INDEX  jos_core_acl_aro_groups_lft_index  ON jos_core_acl_aro_groups USING btree  ("lft","rgt");


CREATE TABLE "jos_core_acl_aro_map" (
  "acl_id" bigint NOT NULL default '0',
  "section_value" varchar(230) NOT NULL default '0',
  "value" varchar(100) NOT NULL,
  CONSTRAINT jos_core_acl_aro_map_pkey PRIMARY KEY  ("acl_id","section_value","value")
);


CREATE TABLE "jos_core_acl_aro_sections" (
  "id" serial NOT NULL,
  "value" varchar(230) NOT NULL default '',
  "order_value" bigint NOT NULL default '0',
  "name" varchar(230) NOT NULL default '',
  "hidden" bigint NOT NULL default '0',
  CONSTRAINT jos_core_acl_aro_sections_pkey PRIMARY KEY  ("id"),
  CONSTRAINT  jos_core_acl_aro_sections_key UNIQUE ("value")
) ;
  CREATE INDEX  jos_core_acl_aro_sections_hidden_index  ON jos_core_acl_aro_sections USING btree  ("hidden");



CREATE TABLE "jos_core_acl_groups_aro_map" (
  "group_id" bigint NOT NULL default '0',
  "section_value" varchar(240) NOT NULL default '',
  "aro_id" bigint NOT NULL default '0',
  CONSTRAINT  jos_core_acl_groups_aro_map_key UNIQUE ("group_id","section_value","aro_id")
);



CREATE TABLE "jos_core_log_items" (
  "time_stamp" date NOT NULL default '0001-01-01',
  "item_table" varchar(50) NOT NULL default '',
  "item_id" bigint  NOT NULL default '0',
  "hits" bigint  NOT NULL default '0'
);


CREATE TABLE "jos_core_log_searches" (
  "search_term" varchar(128) NOT NULL default '',
  "hits" bigint  NOT NULL default '0'
);



CREATE TABLE "jos_groups" (
  "id" integer  NOT NULL default '0',
  "name" varchar(50) NOT NULL default '',
  CONSTRAINT jos_groups_pkey PRIMARY KEY  ("id")
);


CREATE TABLE "jos_menu" (
  "id" serial NOT NULL,
  "menutype" varchar(75) default NULL,
  "name" varchar(255) default NULL,
  "alias" varchar(255) NOT NULL default '',
  "link" text,
  "type" varchar(50) NOT NULL default '',
  "published" integer NOT NULL default '0',
  "parent" bigint  NOT NULL default '0',
  "componentid" bigint  NOT NULL default '0',
  "sublevel" bigint default '0',
  "ordering" bigint default '0',
  "checked_out" bigint  NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "pollid" bigint NOT NULL default '0',
  "browserNav" integer default '0',
  "access" integer  NOT NULL default '0',
  "utaccess" integer  NOT NULL default '0',
  "params" text NOT NULL,
  "lft" bigint  NOT NULL default '0',
  "rgt" bigint  NOT NULL default '0',
  "home" bigint  NOT NULL default '0',
  CONSTRAINT jos_menu_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_menu_componentid_index ON jos_menu USING btree  ("componentid","menutype","published","access");
  CREATE INDEX  jos_menu_menutype_index  ON jos_menu USING btree  ("menutype");


CREATE TABLE "jos_menu_types" (
  "id" serial NOT NULL,
  "menutype" varchar(75) NOT NULL default '',
  "title" varchar(255) NOT NULL default '',
  "description" varchar(255) NOT NULL default '',
  CONSTRAINT jos_menu_types_pkey PRIMARY KEY  ("id"),
  CONSTRAINT  jos_menu_types_key UNIQUE ("menutype")
);



CREATE TABLE "jos_messages" (
  "message_id" serial NOT NULL,
  "user_id_from" bigint  NOT NULL default '0',
  "user_id_to" bigint  NOT NULL default '0',
  "folder_id" bigint  NOT NULL default '0',
  "date_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "state" bigint NOT NULL default '0',
  "priority" bigint  NOT NULL default '0',
  "subject" text NOT NULL,
  "message" text NOT NULL,
  CONSTRAINT jos_messages_pkey PRIMARY KEY  ("message_id")
);
  CREATE INDEX  jos_messages_user_id_to  ON jos_messages USING btree  ("user_id_to","state");


CREATE TABLE "jos_messages_cfg" (
  "user_id" bigint  NOT NULL default '0',
  "cfg_name" varchar(100) NOT NULL default '',
  "cfg_value" varchar(255) NOT NULL default '',
  CONSTRAINT jos_messages_cfg_key UNIQUE ("user_id","cfg_name")
);




CREATE TABLE "jos_migration_backlinks" (
  "itemid" bigint NOT NULL,
  "name" varchar(100) NOT NULL,
  "url" text NOT NULL,
  "sefurl" text NOT NULL,
  "newurl" text NOT NULL,
  CONSTRAINT jos_migration_backlinks_pkey PRIMARY KEY  ("itemid")
);


CREATE TABLE "jos_modules" (
  "id" serial NOT NULL,
  "title" text NOT NULL,
  "content" text NOT NULL,
  "ordering" bigint NOT NULL default '0',
  "position" varchar(50) default NULL,
  "checked_out" bigint  NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "published" integer NOT NULL default '0',
  "module" varchar(50) default NULL,
  "numnews" bigint NOT NULL default '0',
  "access" integer  NOT NULL default '0',
  "showtitle" integer  NOT NULL default '1',
  "params" text NOT NULL,
  "iscore" integer NOT NULL default '0',
  "client_id" integer NOT NULL default '0',
  "control" text NOT NULL,
  CONSTRAINT jos_modules_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_modules_published_index ON jos_modules USING btree  ("published","access");
  CREATE INDEX  jos_modules_module_index ON jos_modules USING btree  ("module","published");


CREATE TABLE "jos_modules_menu" (
  "moduleid" bigint NOT NULL default '0',
  "menuid" bigint NOT NULL default '0',
  CONSTRAINT jos_modules_menu_pkey PRIMARY KEY  ("moduleid","menuid")
);


CREATE TABLE "jos_newsfeeds" (
  "catid" bigint NOT NULL default '0',
  "id" serial NOT NULL,
  "name" text NOT NULL,
  "alias" varchar(255) NOT NULL default '',
  "link" text NOT NULL,
  "filename" varchar(200) default NULL,
  "published" integer NOT NULL default '0',
  "numarticles" bigint  NOT NULL default '1',
  "cache_time" bigint  NOT NULL default '3600',
  "checked_out" integer  NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "ordering" bigint NOT NULL default '0',
  "rtl" integer NOT NULL default '0',
  CONSTRAINT jos_newsfeeds_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_newsfeeds_published_index  ON jos_newsfeeds USING btree  ("published");
  CREATE INDEX  jos_newsfeeds_catid_index  ON jos_newsfeeds USING btree  ("catid");


CREATE TABLE "jos_plugins" (
  "id" serial NOT NULL,
  "name" varchar(100) NOT NULL default '',
  "element" varchar(100) NOT NULL default '',
  "folder" varchar(100) NOT NULL default '',
  "access" integer  NOT NULL default '0',
  "ordering" bigint NOT NULL default '0',
  "published" integer NOT NULL default '0',
  "iscore" integer NOT NULL default '0',
  "client_id" integer NOT NULL default '0',
  "checked_out" bigint  NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "params" text NOT NULL,
  CONSTRAINT jos_plugins_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_plugins_published_index ON jos_plugins USING btree  ("published","client_id","access","folder");


CREATE TABLE "jos_poll_data" (
  "id" serial NOT NULL,
  "pollid" bigint NOT NULL default '0',
  "text" text NOT NULL,
  "hits" bigint NOT NULL default '0',
  CONSTRAINT jos_poll_data_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX jos_poll_data_pollid_index ON jos_poll_data USING btree  ("pollid","text");


CREATE TABLE "jos_poll_date" (
  "id" serial NOT NULL,
  "date" timestamp NOT NULL default '0001-01-01 00:00:00',
  "vote_id" bigint NOT NULL default '0',
  "poll_id" bigint NOT NULL default '0',
  CONSTRAINT jos_poll_date_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_poll_date_poll_id_index  ON jos_poll_date USING btree  ("poll_id");


CREATE TABLE "jos_poll_menu" (
  "pollid" bigint NOT NULL default '0',
  "menuid" bigint NOT NULL default '0',
  CONSTRAINT jos_poll_menu_pkey PRIMARY KEY  ("pollid","menuid")
);


CREATE TABLE "jos_polls" (
  "id" serial NOT NULL,
  "title" varchar(255) NOT NULL default '',
  "alias" varchar(255) NOT NULL default '',
  "voters" bigint NOT NULL default '0',
  "checked_out" bigint NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "published" integer NOT NULL default '0',
  "access" bigint NOT NULL default '0',
  "lag" bigint NOT NULL default '0',
  CONSTRAINT jos_polls_pkey PRIMARY KEY  ("id")
) ;


CREATE TABLE "jos_sections" (
  "id" serial NOT NULL,
  "title" varchar(255) NOT NULL default '',
  "name" varchar(255) NOT NULL default '',
  "alias" varchar(255) NOT NULL default '',
  "image" text NOT NULL,
  "scope" varchar(50) NOT NULL default '',
  "image_position" varchar(30) NOT NULL default '',
  "description" text NOT NULL,
  "published" integer NOT NULL default '0',
  "checked_out" bigint  NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "ordering" bigint NOT NULL default '0',
  "access" integer  NOT NULL default '0',
  "count" bigint NOT NULL default '0',
  "params" text NOT NULL,
  CONSTRAINT jos_sections_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_sections_scope_index  ON jos_sections USING btree  ("scope");


CREATE TABLE "jos_session" (
  "username" varchar(150) default '',
  "time" varchar(14) default '',
  "session_id" varchar(200) NOT NULL default '0',
  "guest" integer default '1',
  "userid" bigint default '0',
  "usertype" varchar(50) default '',
  "gid" integer  NOT NULL default '0',
  "client_id" integer  NOT NULL default '0',
  "data" text,
  CONSTRAINT jos_session_pkey PRIMARY KEY  ("session_id")
);
  CREATE INDEX  jos_session_guest ON jos_session USING btree  ("guest","usertype");
  CREATE INDEX  jos_session_userid_index  ON jos_session USING btree  ("userid");
  CREATE INDEX  jos_session_time_index  ON jos_session USING btree  ("time");



CREATE TABLE "jos_stats_agents" (
  "agent" varchar(255) NOT NULL default '',
  "type" integer  NOT NULL default '0',
  "hits" bigint  NOT NULL default '1'
);


CREATE TABLE "jos_templates_menu" (
  "template" varchar(255) NOT NULL default '',
  "menuid" bigint NOT NULL default '0',
  "client_id" integer NOT NULL default '0',
  CONSTRAINT jos_templates_menu_pkey PRIMARY KEY  ("menuid","client_id","template")
);


CREATE TABLE "jos_users" (
  "id" serial NOT NULL,
  "name" varchar(255) NOT NULL default '',
  "username" varchar(150) NOT NULL default '',
  "email" varchar(100) NOT NULL default '',
  "password" varchar(100) NOT NULL default '',
  "usertype" varchar(25) NOT NULL default '',
  "block" integer NOT NULL default '0',
  "sendEmail" integer default '0',
  "gid" integer  NOT NULL default '1',
  "registerDate" timestamp NOT NULL default '0001-01-01 00:00:00',
  "lastvisitDate" timestamp NOT NULL default '0001-01-01 00:00:00',
  "activation" varchar(100) NOT NULL default '',
  "params" text NOT NULL,
  CONSTRAINT jos_users_pkey PRIMARY KEY  ("id")
) ;
  CREATE INDEX  jos_users_usertype_index  ON jos_users USING btree  ("usertype");
  CREATE INDEX  jos_users_name_index  ON jos_users USING btree  ("name");
  CREATE INDEX  jos_users_gid_index  ON jos_users USING btree  ("gid","block");
  CREATE INDEX  jos_users_username_index  ON jos_users USING btree  ("username");
  CREATE INDEX  jos_users_email_index  ON jos_users USING btree  ("email");


CREATE TABLE "jos_weblinks" (
  "id" serial NOT NULL,
  "catid" bigint NOT NULL default '0',
  "sid" bigint NOT NULL default '0',
  "title" varchar(250) NOT NULL default '',
  "alias" varchar(255) NOT NULL default '',
  "url" varchar(250) NOT NULL default '',
  "description" text NOT NULL,
  "date" timestamp NOT NULL default '0001-01-01 00:00:00',
  "hits" bigint NOT NULL default '0',
  "state" integer NOT NULL default '0',
  "checked_out" bigint NOT NULL default '0',
  "checked_out_time" timestamp NOT NULL default '0001-01-01 00:00:00',
  "ordering" bigint NOT NULL default '0',
  "archived" integer NOT NULL default '0',
  "approved" integer NOT NULL default '1',
  "params" text NOT NULL,
  CONSTRAINT jos_weblinks_pkey PRIMARY KEY  ("id")
);
  CREATE INDEX  jos_weblinks_catid_index ON jos_weblinks USING btree  ("catid","state","archived");


