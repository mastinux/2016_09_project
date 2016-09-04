
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
    shares_type enum('demand', 'offer') not null,
    amount integer unsigned not null,
    price double unsigned not null,
	constraint shares_pk primary key (shares_type, price)
);

create table shares_order(
	shares_order_id int not null auto_increment,
	username varchar(320) not null,
    shares_type enum('demand', 'offer') not null,
    amount integer unsigned not null check (amount >= 0),
    price double unsigned not null,
#    order_datetime datetime not null default current_timestamp,
    foreign key (username) references shares_user(email),
    foreign key (shares_type, price) references shares(shares_type, price),
    primary key (shares_order_id)
#    constraint shares_order_pk primary key (username, shares_type, price, order_datetime)
);

insert into shares_user(first_name, last_name, email, pw) values('u1', 'u1', 'u1@p.it', md5('p1'));
insert into shares_user(first_name, last_name, email, pw) values('u2', 'u2', 'u2@p.it', md5('p2'));
insert into shares_user(first_name, last_name, email, pw, balance) values('Andrea', 'Pantaleo', 'andreapantaleo@gmail.com', md5('asdf'), 2920);

insert into shares(shares_type, amount, price) values('demand', 2, 1000);
insert into shares(shares_type, amount, price) values('demand', 10, 960);
insert into shares(shares_type, amount, price) values('demand', 4, 950);
insert into shares(shares_type, amount, price) values('demand', 3, 900);
insert into shares(shares_type, amount, price) values('demand', 8, 800);

insert into shares(shares_type, amount, price) values('offer', 3, 1030);
insert into shares(shares_type, amount, price) values('offer', 11, 1050);
insert into shares(shares_type, amount, price) values('offer', 8, 1100);
insert into shares(shares_type, amount, price) values('offer', 6, 1150);
insert into shares(shares_type, amount, price) values('offer', 15, 1200);

# u1 buys 11 shares
update shares set amount = (amount - 3) where price = 1030 and shares_type = 'offer';
insert into shares_order(username, shares_type, amount, price) values('u1@p.it', 'offer', 3, 1030);
update shares_user set balance = (balance - (3 * 1030)) where email = 'u1@p.it';

update shares set amount = (amount - 8) where price = 1050 and shares_type = 'offer';
insert into shares_order(username, shares_type, amount, price) values('u1@p.it', 'offer', 8, 1050);
update shares_user set balance = (balance - (8 * 1050)) where email = 'u1@p.it';

# u1 sells 1 share
update shares set amount = (amount - 1) where price = 1000 and shares_type = 'demand';
insert into shares_order(username, shares_type, amount, price) values('u1@p.it', 'demand', 1, 1000);
update shares_user set balance = (balance + (1 * 1000)) where email = 'u1@p.it';

# u2 buys 12 shares
update shares set amount = (amount - 3) where price = 1050 and shares_type = 'offer';
insert into shares_order(username, shares_type, amount, price) values('u2@p.it', 'offer', 3, 1050);
update shares_user set balance = (balance - (3 * 1050)) where email = 'u2@p.it';

update shares set amount = (amount - 8) where price = 1100 and shares_type = 'offer';
insert into shares_order(username, shares_type, amount, price) values('u2@p.it', 'offer', 8, 1100);
update shares_user set balance = (balance - (8 * 1100)) where email = 'u2@p.it';

update shares set amount = (amount - 1) where price = 1150 and shares_type = 'offer';
insert into shares_order(username, shares_type, amount, price) values('u2@p.it', 'offer', 1, 1150);
update shares_user set balance = (balance - (1 * 1150)) where email = 'u2@p.it';

# u2 sells 2 share
update shares set amount = (amount - 1) where price = 1000 and shares_type = 'demand';
insert into shares_order(username, shares_type, amount, price) values('u2@p.it', 'demand', 1, 1000);
update shares_user set balance = (balance + (1 * 1000)) where email = 'u2@p.it';

update shares set amount = (amount - 1) where price = 960 and shares_type = 'demand';
insert into shares_order(username, shares_type, amount, price) values('u2@p.it', 'demand', 1, 960);
update shares_user set balance = (balance + (1 * 960)) where email = 'u2@p.it';