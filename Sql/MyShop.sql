DROP database IF EXISTS myshop;
create database 
		myshop default character set utf8mb4;
use myshop;


create table products (

	id int primary key auto_increment not null,
	title varchar(50) not null,
	author varchar(50) not null,
	image varchar(255) not null,
	price decimal(18.2) not null,
	category int not null,
	content text not NULL,
	pdf varchar(50) NOT NULL, 
	creation_time varchar(50) NOT null,
	discount varchar (3)

);

create table users (

	id int primary key auto_increment not null,
	name varchar(50) not null,
	lastname varchar(50) not null,
	password char (60) not null,
	role varchar(50) not null,
	email varchar(100),
	register_time varchar(50) NOT NULL,
	confirm_email_token varchar(255),
	reset_password_token varchar(255),
	rememberme_token varchar(255)
	
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
	comment_date date,
	approved bit

);

create table rating(

	user int,
	product int,
	rating int(1)

);

create table bought(

	orders int,
	product int,
	quantity int,
	price decimal(18,2)

);

create table slideshow(

	id int primary key auto_increment not null,
	photo varchar(255) not null,
	visible varchar (1) 

);



alter table bought add foreign key (orders) references orders(id);
alter table bought add foreign key (product) references products(id);

alter table products add foreign key (category) references categories(id);
alter table orders add foreign key (user) references users(id);

alter table comments add foreign key (product) references products(id);
alter table comments add foreign key (user) references users(id);

alter table rating add foreign key (product) references products(id);
alter table rating add foreign key (user) references users(id);


