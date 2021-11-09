CREATE OR REPLACE FUNCTION sistema.calcular_indicador(IN id_indicador_calculo integer DEFAULT NULL::integer)
    RETURNS void
    LANGUAGE 'plpgsql'
    VOLATILE
    PARALLEL UNSAFE
    COST 100
AS $BODY$
declare query_indicadores cursor for 
	select * 
	from sistema.query_indicador query
		inner join sistema.indicador indic 
			on indic.id_indicador = query.id_indicador
	where (id_indicador_calculo = query.id_indicador or id_indicador_calculo is null) and (indic.ativo = true or indic.homologacao = true);
declare query_variaveis cursor for
	select *
	from sistema.query_variavel query
	inner join sistema.variavel var
		on var.id_variavel = query.id_variavel
	inner join (select count(distinct id_territorio) as distinto, id_indicador 
			from sistema.query_indicador
			where id_indicador = id_indicador_calculo
			group by id_indicador) dist on dist.id_indicador = id_indicador_calculo
	where (query.id_variavel in (select id_variavel from sistema.indicador_x_variavel where id_indicador = id_indicador_calculo) 
		or id_indicador_calculo is null) and not ((var.distribuicao = true and id_territorio != sistema.territorio_raiz(id_territorio)) and
							 (var.distribuicao = true and distinto > 1) );

begin
	delete from sistema.indicador_calculo 
	where id_indicador = id_indicador_calculo or id_indicador_calculo is null;
	
	delete from sistema.variavel_calculo
	where id_variavel in (select id_variavel from sistema.indicador_x_variavel where id_indicador = id_indicador_calculo) or id_indicador_calculo is null;

    for query_variavel IN query_variaveis
	loop
		execute 'insert into sistema.variavel_calculo (data,id_regiao,dimensao,id_variavel,id_territorio,valor) ' || query_variavel.query;
	end loop;

    for query_indicador IN query_indicadores
    loop
        execute query_indicador.query;
    end loop;


end;
$BODY$;