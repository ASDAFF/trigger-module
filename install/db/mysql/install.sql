create table if not exists f_events
(
	ID int(18) not null auto_increment,
	EVENT_TYPE int(18) not null,
	NAME varchar(255),
	ADDITIONAL_PROPS text,
	CONDITIONS text,
	SORT int not null default 100,
	primary key (ID)
);

create table if not exists f_event_types
(
	ID int(18) not null auto_increment,
	CODE varchar(255),
	NAME varchar(255),
	TYPE varchar(255),
	HANDLER varchar(255),
	SORT int not null default 100,
	primary key (ID)
);

create table if not exists f_event_type_fields
(
	ID int(18) not null auto_increment,	
	NAME varchar(255),
	TYPE int(18),
	FIELD_TYPE varchar(255),
	SORT int not null default 100,
	primary key (ID)
);

create table if not exists f_conditions
(
	ID int(18) not null auto_increment,
	NAME varchar(255),
	EVENT_ID int(18) not null,
	ACTION_ID int(18) not null,
	SORT int not null default 100,
	primary key (ID)
);

create table if not exists f_actions
(
	ID int(18) not null auto_increment,
	NAME varchar(255),
	ACTION_TYPE int(18),
	ADDITIONAL_PROPS text,
	BODY_PARAMS text,
	SORT int not null default 100,
	primary key (ID)
);

create table if not exists f_action_types
(
	ID int(18) not null auto_increment,
	NAME varchar(255),
	CODE varchar(255),
	SORT int not null default 100,
	primary key (ID)
);

create table if not exists f_triggers
(
	ID int(18) not null auto_increment,
	NAME varchar(255),
	EVENT_ID int(18) not null,
	CONDITION_ID int(18) not null,
	ACTION_ID int(18) not null,
	SORT int not null default 100,
	primary key (ID)
);

create table if not exists f_triggers_log
(
	ID int(18) not null auto_increment,
	NAME varchar(255),
	TRIGGER_ID int(18) not null,
	DATE_CREATE datetime,
	CREATED_BY int(18),
	PRIMARY KEY (ID)	
);