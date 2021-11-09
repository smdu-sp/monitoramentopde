CREATE OR REPLACE FUNCTION sistema.criar_view_dado_aberto(IN id_fonte integer)
    RETURNS void
    LANGUAGE 'plpgsql'
    VOLATILE
    PARALLEL UNSAFE
    COST 100
AS $BODY$
	
	declare query_dado_abertos cursor for
	select 'create or replace view fonte_dados.vw_' || max(fonte.nome_tabela) || ' as
			select ' || string_agg(column_name,E', \n' ) || ' from fonte_dados.' || max(col.table_name) as query
	from 
	information_schema.columns col
	inner join sistema.fonte_dados fonte
		on fonte.nome_tabela = col.table_name
	where col.table_schema = 'fonte_dados'
	and col.column_name not in 
			(select coluna 
				from sistema.fonte_dados_exclusao_coluna 
				where id_fonte_dados = id_fonte)
	and fonte.id_fonte_dados = id_fonte;
	

begin
    for query_dado_aberto IN query_dado_abertos
	loop
		execute query_dado_aberto.query;
	end loop;
	return;
end
$BODY$;