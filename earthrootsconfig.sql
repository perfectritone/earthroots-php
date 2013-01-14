CREATE TABLE herbs (
	name char(20) not null primary key,
	traits char(100),
	description text,
	history text,
	traditional_uses text
);

CREATE TABLE alternative_names (
	def_name char(20) not null,
	alt_name char(20) not null
);

CREATE TABLE taxonomy (
	name char(20) not null primary key,
	family char(20),
	genus char(20),
	species char(20)
);

CREATE TABLE ailments (
	name char(20) not null,
	ailment char(20) not null,
	effective_weight tinyint unsigned,
	
	primary key(name, ailment)
);

CREATE TABLE active_chemicals (
	name char(20) not null,
	active_component text not null
);

CREATE TABLE actions (
	name char(20) not null,
	ailment_action char(50) not null
);

CREATE TABLE harvest (
	name char(20) not null,
	harvest_when text,
	place text,
	plant_part text,
	method text
);

CREATE TABLE picture (
	name char(20) not null,
	link char(50) not null primary key,
	is_def tinyint unsigned not null
);

CREATE TABLE warnings (
	name char(20) not null primary key,
	toxicity text,
	side_effects text,
	analogues text,
	synergy text,
	more text
);



CREATE TABLE products (
	name char(50) not null primary key,
	category char(50) not null,
	price decimal(4,2) not null
);

CREATE TABLE ingredients (
	name char(50) not null,
	herb char(20) not null
);


CREATE TABLE blog (
  name char(100) not null,
  content text,
  post_when timestamp
);



CREATE TABLE users (
  id int not null auto_increment,
  username varchar(30) NOT NULL UNIQUE,
  password VARCHAR(64) NOT NULL,
  salt VARCHAR(3) NOT NULL,
  PRIMARY KEY(id)
);
