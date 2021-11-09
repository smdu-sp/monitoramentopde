CREATE OR REPLACE FUNCTION sistema.trunc_peri(IN periodicidade text)
    RETURNS text
    LANGUAGE 'sql'
    VOLATILE
    PARALLEL UNSAFE
    COST 100
AS $BODY$
	select case $1	when 'diario' then 'day'
			when 'semanal' then 'week'
			when 'mensal' then 'month'
			when 'trimestral' then 'quarter'
			when 'anual' then 'year' end as result
$BODY$;