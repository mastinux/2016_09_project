
DROP database IF EXISTS `shares_db`;

create database shares_db;

use shares_db;

create table shares_user(
	first_name varchar(320) not null,
	last_name varchar(320) not null,
	email varchar(320) not null,
	pw varchar(255) not null,
    balance double default 50000 check (balance >= 0),
	primary key (email)
);

create table shares(	
    shares_type enum('buying', 'selling') not null,
    amount integer unsigned not null,
    price double unsigned not null,
	constraint shares_pk primary key (shares_type, price)
);

create table shares_order(
	shares_order_id int not null auto_increment,
	username varchar(320) not null,
    shares_type enum('buying', 'selling') not null,
    amount integer unsigned not null,
    price double unsigned not null,
#    order_datetime datetime not null default current_timestamp,
    foreign key (username) references shares_user(email),
    foreign key (shares_type, price) references shares(shares_type, price),
    primary key (shares_order_id)
#    constraint shares_order_pk primary key (username, shares_type, price, order_datetime)
);

insert into shares_user(first_name, last_name, email, pw) values('u1', 'u1', 'u1@p.it', md5('p1'));
insert into shares_user(first_name, last_name, email, pw) values('u2', 'u2', 'u2@p.it', md5('p2'));
insert into shares_user(first_name, last_name, email, pw) values('Andrea', 'Pantaleo', 'andreapantaleo@gmail.com', md5('asdf'));

insert into shares(shares_type, amount, price) values('buying', 2, 1000);
insert into shares(shares_type, amount, price) values('buying', 10, 960);
insert into shares(shares_type, amount, price) values('buying', 4, 950);
insert into shares(shares_type, amount, price) values('buying', 3, 900);
insert into shares(shares_type, amount, price) values('buying', 8, 800);
insert into shares(shares_type, amount, price) values('selling', 3, 1030);
insert into shares(shares_type, amount, price) values('selling', 11, 1050);
insert into shares(shares_type, amount, price) values('selling', 8, 1100);
insert into shares(shares_type, amount, price) values('selling', 6, 1150);
insert into shares(shares_type, amount, price) values('selling', 15, 1200);
/*
# buying 2 4 2 | 2
update shares set amount = (amount - 2) where price = 800 and shares_type = 'buying';
insert into shares_order(username, shares_type, amount, price) values('u1@p.it', 'buying', 2, 800);
update shares_user set balance = (balance - (2 * 800)) where email = 'u1@p.it';

update shares set amount = (amount - 4) where price = 800 and shares_type = 'buying';
insert into shares_order(username, shares_type, amount, price) values('u2@p.it', 'buying', 4, 800);
update shares_user set balance = (balance - (4 * 800)) where email = 'u2@p.it';

update shares set amount = (amount - 2) where price = 800 and shares_type = 'buying';
insert into shares_order(username, shares_type, amount, price) values('u1@p.it', 'buying', 2, 800);
update shares_user set balance = (balance - (2 * 800)) where email = 'u1@p.it';

update shares set amount = (amount - 2) where price = 900 and shares_type = 'buying';
insert into shares_order(username, shares_type, amount, price) values('u2@p.it', 'buying', 2, 900);
update shares_user set balance = (balance - (2 * 900)) where email = 'u2@p.it';

update shares set amount = (amount - 1) where price = 1200 and shares_type = 'selling';
insert into shares_order(username, shares_type, amount, price) values('u1@p.it', 'selling', 1, 1200);
update shares_user set balance = (balance + (1 * 1200)) where email = 'u1@p.it';

update shares set amount = (amount - 3) where price = 1200 and shares_type = 'selling';
insert into shares_order(username, shares_type, amount, price) values('u2@p.it', 'selling', 3, 1200);
update shares_user set balance = (balance +	 (3 * 1200)) where email = 'u2@p.it';

/*
use shares_db;
select * from shares_user;
select * from shares order by shares_type, price;
select * from shares_order;
