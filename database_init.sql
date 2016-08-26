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

create table shares(	
    shares_type enum('purchase', 'sales') not null,
    amount integer unsigned not null,
    price double unsigned not null,
	constraint shares_pk primary key (shares_type, price)
);

create table shares_order(
	username varchar(320) not null,
    shares_type enum('purchase', 'sales') not null,
    amount integer unsigned not null,
    price double unsigned not null,
    order_datetime datetime not null default current_timestamp,
    foreign key (username) references shares_user(email),
    foreign key (shares_type, price) references shares(shares_type, price),
    constraint shares_order_pk primary key (username, shares_type, price, order_datetime)
);

insert into shares_user(first_name, last_name, email, pw) values('u1', 'u1', 'u1@p1.it', md5('p1'));
insert into shares_user(first_name, last_name, email, pw) values('u2', 'u2', 'u1@p2.it', md5('p2'));
insert into shares_user(first_name, last_name, email, pw) values('Andrea', 'Pantaleo', 'andreapantaleo@gmail.com', md5('asdf'));

insert into shares(shares_type, amount, price) values('purchase', 2, 1000);
insert into shares(shares_type, amount, price) values('purchase', 10, 960);
insert into shares(shares_type, amount, price) values('purchase', 4, 950);
insert into shares(shares_type, amount, price) values('purchase', 3, 900);
insert into shares(shares_type, amount, price) values('purchase', 8, 800);
insert into shares(shares_type, amount, price) values('sales', 3, 1030);
insert into shares(shares_type, amount, price) values('sales', 11, 1050);
insert into shares(shares_type, amount, price) values('sales', 8, 1100);
insert into shares(shares_type, amount, price) values('sales', 6, 1150);
insert into shares(shares_type, amount, price) values('sales', 15, 1200);

use shares_db;
select * from shares_user;
select * from shares;
select * from shares_order;

#insert into shares_order(username, shares_type, amount, price) values('$username', '$shares_type', '$amount', '$price');