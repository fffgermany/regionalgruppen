# drop database regionalgruppen;
create database regionalgruppen;
#create user 'reggru'@'localhost' identified by 'ampelmittwoch';
#grant all privileges on regionalgruppen.* to 'reggru'@'localhost';
use regionalgruppen;
\W

create table ortsgruppe ( 
  id int(11) not null auto_increment primary key,
  name varchar(255),
  lat decimal(11,6),
  lng decimal(11,6),
  admin_id int(11),
  description varchar(350),
  twitter varchar(255),
  facebook varchar(255),
  email varchar(255),
  telnr varchar(200),
  aktiv tinyint not null default 0,
  inserter_id int(11),
  changer_id int(11),
  inserted datetime,
  changed datetime
);

create table user(
  id int(11) not null auto_increment primary key,
  name varchar(255),
  description varchar(300),
  passwort varchar(255) default null,
  email varchar(255),
  ortsgruppe_id int(11), 
  linktoken varchar(255),
  superadmin tinyint not null default 0,
  aktiv tinyint not null default 0,
  inserter_id int(11),
  changer_id int(11),
  inserted datetime,
  changed datetime
);

create table demo(
  id int(11) not null auto_increment primary key,
  ortsgruppe_id int(11),
  ort varchar(255),
  zeit datetime not null,
  teilnehmerzahl int(11),
  beschreibung text(30000),
  aktiv tinyint not null default 0,
  inserter_id int(11),
  changer_id int(11),
  inserted datetime,
  changed datetime
);

create table demopropaganda(
  id int(11) not null auto_increment primary key,
  demo int(11) not null,
  ortsgruppe_id int(11) not null,
  name varchar(255),
  content varchar(300),
  aktiv tinyint not null default 0,
  inserter_id int(11),
  changer_id int(11),
  inserted datetime,
  changed datetime
);

create table logging(
  id int(11) not null auto_increment primary key,
  admin_id int(11) not null,
  zeit datetime not null,
  action varchar(255),
  wert text(30000),
  index(admin_id)
);
