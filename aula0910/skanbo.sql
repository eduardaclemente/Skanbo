create database skanbo;

use skanbo;

create table if not exists cliente (
	cpf_cliente char(11) not null primary key,
	nome varchar(25) not null);

create table if not exists prestador (
	cnpj char(14) not null primary key,
	nomeFantasia varchar(25) not null,
	especialidade varchar(50) not null);

create table if not exists agendamento (
	id_agendamento int(6) auto_increment not null primary key,
	data datetime not null,
	cpf_cliente char(11) not null,
	cnpj char(14) not null,
	categoria varchar(12) not null,
	foreign key (cnpj) references prestador(cnpj),
	foreign key (cpf_cliente) references cliente(cpf_cliente),
	unique key (data, cnpj));

create view vProxAgendamento as 
	select id_agendamento, date_format(data, "%e/%c/%Y") as data_c, 
	 date_format(data, "%k:%i") as hora, cliente.nome as cliente, 
	 prestador.nomeFantasia as prestador, categoria, timestampdiff(HOUR, curtime(), data) as dif 
	 from agendamento inner join cliente on agendamento.cpf_cliente = cliente.cpf_cliente 
	 inner join prestador on agendamento.cnpj = prestador.cnpj 
	 where data > now() 
	 order by data, prestador;	 

create view vClientePorNome as
 select * from cliente order by nome;

create view vPrestadorPorNome as 
 select * from prestador order by nomeFantasia;

create view vCategorias as 
 select distinct categoria from agendamento order by categoria; 

delimiter $$

create procedure spConsultaPorId (IN id INT(6))
	begin
  select cliente.nome as cliente, prestador.nomeFantasia as prestador 
   from agendamento inner join cliente on agendamento.cpf_cliente = cliente.cpf_cliente 
   inner join prestador on agendamento.cnpj = prestador.cnpj where id_agendamento = id;	 
	end $$

create procedure spIncluiCliente (IN cli varchar(25), OUT char(11))
	begin
	 insert into cliente (nome) values (cli);
	 select cpf_cliente from cliente where nome = cli;
	end $$

create procedure spIncluiPrestador (IN cnpj char(14), IN nomeFantasia varchar(25), IN especialidade varchar(50))
	begin
	 insert into prestador (cnpj, nomeFantasia, especialidade) values (cnpj, nomeFantasia, especialidade);
	end $$

create procedure spIncluiAgendamento (IN data varchar(20), IN cliente char(11), 
	IN prestador char(14), IN categoria varchar(12))
 begin
  insert into agendamento (data, cpf_cliente, cnpj, categoria) 
  	values (str_to_date(data,'%Y-%m-%d %H:%i'), cliente, prestador, categoria);
 end $$

create procedure spCancelaAgendamento (IN id INT(6))
 begin
  delete from agendamento where id_agendamento = id;
 end $$ 

create procedure spAlteraAgendamento (IN id INT(6), IN data_c varchar(20))
	begin
	 update agendamento set data = str_to_date(data_c,'%Y-%m-%d %H:%i') where id_agendamento = id;
	end $$

delimiter ; 

call spIncluiCliente('Latussa Natividade', '69628473000', @id);
call spIncluiCliente('Guigliermo Vilas', '98680318000', @id);
call spIncluiCliente('Diorone Nolasco', '16953556013', @id);
call spIncluiCliente('Elvispresley Barreira', '78601527035', @id);
call spIncluiCliente('Cristhaldo Paranhos', '70792585020', @id);
call spIncluiCliente('Dhesiani Schultz', '16871546006', @id);
call spIncluiCliente('Julesio Calisto', '51649557094', @id);
call spIncluiCliente('Leotrice Paranhos', '38210961012', @id);
call spIncluiCliente('Inizele Filgueira', '16480083059', @id);
call spIncluiCliente('Loraidy do Amparo', '22300379007', @id);

call spIncluiPrestador('36037184000101', 'Mãos Divinas', 'Manicure&Pedicure');
call spIncluiPrestador('67255634000166', 'Haja Luz', 'Eletricista');
call spIncluiPrestador('19838746000105', 'Limpa e Brilha', 'Diarista');
call spIncluiPrestador('15622430000112', 'Desentope Alma', 'Desentupidor');
call spIncluiPrestador('29697258000170', 'MasterChef', 'Cozinheira');
call spIncluiPrestador('02051561000145', 'Perereirão Construção', 'Pedreiro');
call spIncluiPrestador('08433172000160', 'The Rock', 'Personal Trainer');
call spIncluiPrestador('13568611000182', 'Márcia Sensitiva', 'Psicológa');

call spIncluiAgendamento('2023-10-9 10:30', 3, '08433172000160', 'Em domícilio');
call spIncluiAgendamento('2023-10-16 8:20', 6, '13568611000182', 'Presencial no Estabelecimento');
call spIncluiAgendamento('2023-10-17 8:00', 1, '08433172000160', 'Escolha do Cliente');
call spIncluiAgendamento('2023-10-18 14:00', 1, '19838746000105', 'Em domícilio');
call spIncluiAgendamento('2023-10-19 9:00', 4, '08433172000160', 'Escolha do Cliente');
call spIncluiAgendamento('2023-10-20 9:20', 8, '13568611000182', 'Presencial no Estabelecimento');
call spIncluiAgendamento('2023-10-22 9:20', 9, '02051561000145', 'Em domícilio');
call spIncluiAgendamento('2023-10-23 9:40', 10, '13568611000182', 'Presencial no Estabelecimento');
call spIncluiAgendamento('2023-10-24 14:00', 5, '36037184000101', 'Em domícilio');
call spIncluiAgendamento('2023-10-26 14:20', 7, '67255634000166', 'Em domícilio');
call spIncluiAgendamento('2023-10-28 14:40', 2, '15622430000112', 'Escolha do Cliente');
call spIncluiAgendamento('2023-10-30 14:40', 3, '19838746000105', 'Em domícilio');