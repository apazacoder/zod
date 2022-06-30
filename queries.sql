
DROP TABLE wp_zs_horoscopos_mes;
DROP TABLE wp_zs_horoscopos_semana;

create table wp_zs_horoscopos_semana
(
    id       mediumint auto_increment
        primary key,
    fecha timestamp not null,
    texto    varchar(1024)                           not null,
    id_tipo  mediumint                               not null,
    id_signo mediumint                               not null
)
    engine = MyISAM
    collate = utf8mb4_unicode_ci;

create index id_signo
    on wp_zs_horoscopos_semana (id_signo);

create index id_tipo
    on wp_zs_horoscopos_semana (id_tipo);


create table wp_zs_horoscopos_mes
(
    id       mediumint auto_increment
        primary key,
    fecha timestamp not null,
    texto    varchar(1024)                           not null,
    id_tipo  mediumint                               not null,
    id_signo mediumint                               not null
)
    engine = MyISAM
    collate = utf8mb4_unicode_ci;

create index id_signo
    on wp_zs_horoscopos_mes (id_signo);

create index id_tipo
    on wp_zs_horoscopos_mes (id_tipo);



DELETE FROM wp_zs_horoscopos_semana;