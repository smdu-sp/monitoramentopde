CREATE OR REPLACE FUNCTION sistema.num_format(IN coluna text,IN tabela text)
    RETURNS text
    LANGUAGE 'sql'
    VOLATILE
    PARALLEL UNSAFE
    COST 100
AS $BODY$
	select coalesce(
			case when data_type = 'integer' or data_type = 'numeric' then quote_ident(coluna)
			else 'cast(replace(' || quote_ident(coluna) || ','','',''.'') as numeric)'
			end
			,coluna) as result
	from information_schema.columns where table_name = $2 and column_name = $1;
$BODY$;