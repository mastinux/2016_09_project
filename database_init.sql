DROP database IF EXISTS `shares_db`;

create database shares_db;

use shares_db;

create table shares_user(
	first_name varchar(320) not null,
	last_name varchar(320) not null,
	email varchar(320) not null,
	pw varchar(255) not null,
    balance double default 50000,
	primary key (email)
);

insert into shares_user(first_name, last_name, email, pw) values('Andrea', 'Pantaleo', 'andreapantaleo@gmail.com', md5('asdf'));

select * from shares_user;