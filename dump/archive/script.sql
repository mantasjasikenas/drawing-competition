#@(#) script.ddl

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS paintings;
DROP TABLE IF EXISTS uploads;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS competitions;

CREATE TABLE competitions
(
	id integer AUTO_INCREMENT,
	topic varchar (255),
	start_date date,
	end_date date,
	creation_date date,
	PRIMARY KEY(id)
);

CREATE TABLE roles
(
	id integer AUTO_INCREMENT,
	name varchar (255) NOT NULL,
	PRIMARY KEY(id)
);
INSERT INTO roles(id, name) VALUES(1, 'Administratorius');
INSERT INTO roles(id, name) VALUES(2, 'Vertintojas');
INSERT INTO roles(id, name) VALUES(3, 'Vartotojas');

-- CREATE TABLE users
-- (
-- 	id integer AUTO_INCREMENT,
-- 	username varchar (255),
-- 	password varchar (255),
-- 	email varchar (255),
-- 	registration_date date,
-- 	birth_date date,
-- 	role integer,
-- 	PRIMARY KEY(id),
-- 	FOREIGN KEY(role) REFERENCES roles (id)
-- );

CREATE TABLE users 
(
	id integer AUTO_INCREMENT,
  	userid varchar(32) DEFAULT NULL,
  	username varchar (30) NOT NULL,
  	password varchar (32) NOT NULL,
  	userlevel tinyint (1) UNSIGNED NOT NULL,
	email varchar (50) NULL,
	birth_date date,
  	timestamp int (11) UNSIGNED NOT NULL,
	PRIMARY KEY(id)
);

INSERT INTO users (`username`, `password`, `userlevel`, `email`, `timestamp`, `birth_date`) VALUES
('Valdytojas', 'fe01ce2a7fbac8fafaed7c982a04e229',  5, 'demo@ktu.lt', 1330553708, '2000-02-22'),
('Administratorius', 'fe01ce2a7fbac8fafaed7c982a04e229', 9, 'demo@ktu.lt', 1698238188, '2005-02-22'),
('Vartotojas', 'fe01ce2a7fbac8fafaed7c982a04e229', 1, 'demo@ktu.lt', 1330553730, '2007-02-22');


CREATE TABLE uploads
(
	id integer AUTO_INCREMENT,
	comment varchar (255),
	creation_date date,
	fk_user integer NOT NULL,
	fk_competition integer NOT NULL,
	PRIMARY KEY(id),
	CONSTRAINT atlieka FOREIGN KEY(fk_user) REFERENCES users (id),
	CONSTRAINT priklauso FOREIGN KEY(fk_competition) REFERENCES competitions (id)
);

CREATE TABLE paintings
(
	id integer AUTO_INCREMENT,
	image longblob,
	fk_upload integer NOT NULL,
	PRIMARY KEY(id),
	CONSTRAINT turi FOREIGN KEY(fk_upload) REFERENCES uploads (id)
);

CREATE TABLE reviews
(
	id integer AUTO_INCREMENT,
	composition int,
	colorfulness int,
	compliance int,
	originality int,
	creation_date date,
	fk_user integer NOT NULL,
	fk_upload integer NOT NULL,
	PRIMARY KEY(id),
	CONSTRAINT palieka FOREIGN KEY(fk_user) REFERENCES users (id),
	CONSTRAINT įvertina FOREIGN KEY(fk_upload) REFERENCES uploads (id)
);
