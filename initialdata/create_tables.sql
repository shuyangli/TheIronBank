use sli8;

create table FM_Film (
	IMDB_ID varchar(20) primary key,
    Poster_URL text,
    Description text,
    Runtime_Min integer,
    MPAA_Rating varchar(10),
    Gross real,
    Release_Year integer,
    Num_Awards integer,
    Title varchar(80),
    Distributor varchar(80)
);

create table FM_Person (
	Person_ID integer primary key auto_increment,
    Person_Name varchar(80),
    Num_Awards integer
);

create table FM_Wrote (
	Person_ID integer references FM_Person(Person_ID),
    IMDB_ID varchar(20) references FM_Film(IMDB_ID),
    primary key (IMDB_ID, Person_ID)
);

create table FM_Acted_In (
	Person_ID integer references FM_Person(Person_ID),
    IMDB_ID varchar(20) references FM_Film(IMDB_ID),
    primary key (IMDB_ID, Person_ID)
);

create table FM_Directed (
	Person_ID integer references FM_Person(Person_ID),
    IMDB_ID varchar(20) references FM_Film(IMDB_ID),
    primary key (IMDB_ID, Person_ID)
);

create table FM_Genre (
	Genre_Name varchar(80) primary key,
    IMDB_ID varchar(20) references FM_Film(IMDB_ID)
);

grant all on sli8.* to 'zwaterso'@'localhost';
grant all on sli8.* to 'lmille14'@'localhost';
grant all on sli8.* to 'kkozak'@'localhost';

select * from FM_Wrote;
select * from FM_Acted_In;
