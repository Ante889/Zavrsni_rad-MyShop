DROP database IF EXISTS myshop;
create database myshop;
use myshop;

#Products, users, orders,buyers,categories, comments, rating

create table products (

	id int primary key auto_increment not null,
	title varchar(50) not null,
	author varchar(50) not null,
	image varchar(50) not null,
	price decimal(18.2) not null,
	category int not null,
	quantity int not null,
	content text not null

);

create table users (

	id int primary key auto_increment not null,
	name varchar(50) not null,
	lastname varchar(50) not null,
	role varchar(50) not null  DEFAULT 'Users',
	email varchar(50) 
	
);

create table orders(

	id int primary key auto_increment not null,
	status varchar(50) not null,
	amount varchar(50) not null,
	transaction_id varchar(50) not null,
	order_date date not null,
	user int
	
);

create table categories(

	id int primary key auto_increment not null,
	name varchar(50)

);

create table comments(

	user int,
	product int,
	comment text,
	comment_date date

);

create table rating(

	user int,
	product int,
	rating int(1)

);

create table bought(

	user int,
	product int

);



alter table bought add foreign key (user) references users(id);
alter table bought add foreign key (product) references products(id);

alter table products add foreign key (category) references categories(id);
alter table orders add foreign key (user) references users(id);

alter table comments add foreign key (product) references products(id);
alter table comments add foreign key (user) references users(id);

alter table rating add foreign key (product) references products(id);
alter table rating add foreign key (user) references users(id);


