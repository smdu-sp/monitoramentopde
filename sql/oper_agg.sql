CREATE OR REPLACE FUNCTION sistema.oper_agg(IN operacao text)
    RETURNS text
    LANGUAGE 'sql'
    VOLATILE
    PARALLEL UNSAFE
    COST 100
AS $BODY$
	select case $1	when 'soma' then 'sum'
			when 'contagem' then 'count'
			when 'maximo' then 'max'
			when 'minimo' then 'min' 
			when 'media' then 'avg'
			else operacao end as result
$BODY$;