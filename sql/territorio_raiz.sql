CREATE OR REPLACE FUNCTION sistema.territorio_raiz(IN id_terr integer)
    RETURNS integer
    LANGUAGE 'sql'
    VOLATILE
    PARALLEL UNSAFE
    COST 100
AS $BODY$

	with recursive 
	territorio_raiz(id_territorio,id_territorio_pai) as (
		select t.id_territorio
			   ,t.id_territorio_pai 
		from fonte_dados.territorio t 
		where t.id_territorio  = id_terr

		union all

		select t.id_territorio
			  ,t.id_territorio_pai 
		from territorio_raiz r 
		inner join fonte_dados.territorio t 
			on t.id_territorio = r.id_territorio_pai
	)
	select t_raiz.id_territorio
	from territorio_raiz t_raiz
	where t_raiz.id_territorio_pai is null;

$BODY$;