CREATE OR REPLACE FUNCTION sistema.criar_views_sistema()
    RETURNS void
    LANGUAGE 'plpgsql'
    VOLATILE
    PARALLEL UNSAFE
    COST 100
AS $BODY$
begin


	-- View: sistema.regiao_agregacao

-- DROP VIEW sistema.regiao_agregacao;

CREATE OR REPLACE VIEW sistema.regiao_agregacao AS 
 WITH RECURSIVE agg AS (
         SELECT agg.id_regiao_filho,
            agg.id_territorio_filho,
            agg.id_regiao_pai,
            agg.id_territorio_pai
           FROM fonte_dados.regiao_agregacao agg
        ), regiao_agregacao(id_regiao_filho, id_territorio_filho, id_regiao_pai, id_territorio_pai) AS (
         SELECT a.id_regiao_filho,
            a.id_territorio_filho,
            a.id_regiao_pai,
            a.id_territorio_pai,
            true AS agregado
           FROM agg a
        UNION ALL
         SELECT DISTINCT r.id_regiao_filho,
            r.id_territorio_filho,
            a.id_regiao_pai,
            a.id_territorio_pai,
                CASE r.agregado
                    WHEN true THEN false
                    ELSE true
                END AS agregado
           FROM regiao_agregacao r
             JOIN agg a ON r.id_territorio_pai = a.id_territorio_filho AND r.id_regiao_pai = a.id_regiao_filho
          WHERE r.agregado = true
        )
 SELECT DISTINCT regiao_agregacao.id_regiao_filho,
    regiao_agregacao.id_territorio_filho,
    regiao_agregacao.id_regiao_pai,
    regiao_agregacao.id_territorio_pai,
    regiao_agregacao.agregado
   FROM regiao_agregacao;

ALTER TABLE sistema.regiao_agregacao
  OWNER TO postgres;


CREATE OR REPLACE VIEW sistema.config_territorio_variavel AS 
 WITH RECURSIVE config_territorio_variavel(id_variavel, id_territorio, id_territorio_pai, hierarquia, coluna, altura) AS (
         SELECT DISTINCT v.id_variavel,
            COALESCE(t.id_territorio, 4) AS "coalesce",
            t.id_territorio_pai,
            COALESCE(t.hierarquia, 'Administrativa'::text) AS "coalesce",
            c.nome AS coluna,
            1 AS altura
           FROM fonte_dados.territorio t
             JOIN sistema.coluna c ON c.id_territorio = t.id_territorio
             RIGHT JOIN sistema.variavel v ON c.id_fonte_dados = v.id_fonte_dados AND (c.tipo_territorio IS NULL OR c.tipo_territorio = v.tipo_territorio)
        UNION ALL
         SELECT DISTINCT r.id_variavel,
            t.id_territorio,
            t.id_territorio_pai,
            t.hierarquia,
            c.nome AS coluna,
            r.altura + 1 AS altura
           FROM config_territorio_variavel r
             JOIN fonte_dados.territorio t ON t.id_territorio = r.id_territorio_pai
             LEFT JOIN sistema.variavel v ON v.id_variavel = r.id_variavel
             LEFT JOIN sistema.coluna c ON c.id_fonte_dados = v.id_fonte_dados AND c.id_territorio = t.id_territorio AND (c.tipo_territorio IS NULL OR c.tipo_territorio = v.tipo_territorio)
        )
 SELECT DISTINCT config_territorio_variavel.id_variavel,
    config_territorio_variavel.id_territorio,
    config_territorio_variavel.id_territorio_pai,
    config_territorio_variavel.hierarquia,
    config_territorio_variavel.coluna,
    min(config_territorio_variavel.altura) AS altura
   FROM config_territorio_variavel
  GROUP BY config_territorio_variavel.id_variavel, config_territorio_variavel.id_territorio, config_territorio_variavel.id_territorio_pai, config_territorio_variavel.hierarquia, config_territorio_variavel.coluna;

ALTER TABLE sistema.config_territorio_variavel
  OWNER TO postgres;
	
	
	
	-- View: sistema.query_variavel

-- DROP VIEW sistema.query_variavel;

CREATE OR REPLACE VIEW sistema.query_variavel AS 
 SELECT DISTINCT h.id_territorio,
    v.id_variavel,
    (((
        CASE
            WHEN v.acumulativa THEN ((((((('select
cast(date_trunc('::text ||
            CASE
                WHEN v.periodicidade = 'mensal'::text THEN '''month'''::text
                WHEN v.periodicidade = 'anual'::text THEN '''year'''::text
                WHEN v.periodicidade = 'trimestral'::text THEN '''quarter'''::text
                ELSE ''::text
            END) || ',tempo.data) as date) as data
,cast(tempo.id_regiao as numeric) as id_regiao
,tempo.dimensao
,'::text) || v.id_variavel) || ' as id_variavel
,'::text) || campos.campo_territorio) || ' as id_territorio 
,sum(valor) OVER (PARTITION BY tempo.id_regiao,tempo.dimensao,'::text) || campos.campo_territorio) || ' ORDER BY tempo.data ) as valor
from ('::text
            ELSE ''::text
        END || (((((((((((((((((((((((((((((((((((((((((((((((('select '::text || '
'::text) || campos.campo_data) || ' as data '::text) || '
'::text) || ','::text) || campos.campo_regiao) || ' as id_regiao '::text) || '
'::text) || ','::text) || campos.campo_dimensao) || ' as dimensao'::text) || ','::text) || v.id_variavel::text) || ' as id_variavel '::text) || '
'::text) || ','::text) || campos.campo_territorio) || ' as id_territorio'::text) || '
'::text) || ','::text) || sistema.oper_agg(v.operacao_agregacao)) || '('::text) || COALESCE(sistema.num_format(v.coluna_valor, d.nome_tabela), '*'::text)) || ')'::text) || ' as valor'::text) || '
'::text) || 'from fonte_dados.'::text) || quote_ident(d.nome_tabela)) || ' f'::text) || '
'::text) || campos.join_hierarquia) || COALESCE('
'::text || f."where", ''::text)) || '
'::text) || 'group by '::text) ||
        CASE
            WHEN campos.campo_regiao <> 'null'::text THEN campos.campo_regiao
            ELSE ' id_regiao '::text
        END) || '
'::text) || '		,'::text) ||
        CASE
            WHEN campos.campo_data <> 'null'::text THEN campos.campo_data
            ELSE ' data '::text
        END) || '
'::text) || '		,'::text) ||
        CASE
            WHEN campos.campo_dimensao <> 'null'::text THEN campos.campo_dimensao
            ELSE ' dimensao '::text
        END) || '
'::text) || 'order by '::text) ||
        CASE
            WHEN campos.campo_regiao <> 'null'::text THEN campos.campo_regiao
            ELSE ' id_regiao '::text
        END) || '
'::text) || '		,'::text) ||
        CASE
            WHEN campos.campo_data <> 'null'::text THEN campos.campo_data
            ELSE ' data '::text
        END) || '
'::text)) || '		,'::text) ||
        CASE
            WHEN campos.campo_dimensao <> 'null'::text THEN campos.campo_dimensao
            ELSE ' dimensao '::text
        END) ||
        CASE
            WHEN v.acumulativa THEN (((((((((((((((((((' ) var  right join 
(select
	base.id_regiao
	,base.dimensao
	,generate_series (min('::text || campos.campo_data) || ')
                         , max('::text) || campos.campo_data) || ')
                         , interval ''1 '::text) ||
            CASE
                WHEN v.periodicidade = 'mensal'::text THEN 'month'::text
                WHEN v.periodicidade = 'anual'::text THEN 'year'::text
                WHEN v.periodicidade = 'trimestral'::text THEN 'quarter'::text
                ELSE ''::text
            END) || ''')::date AS data
     from fonte_dados.'::text) || quote_ident(d.nome_tabela)) || '
	cross join (select distinct '::text) || campos.campo_regiao) || ' as id_regiao,'::text) || campos.campo_dimensao) || ' as dimensao from fonte_dados.'::text) || quote_ident(d.nome_tabela)) || ' f '::text) || campos.join_hierarquia) || ' '::text) || COALESCE(f."where", ''::text)) || ') base
	group by
	base.id_regiao
	,base.dimensao
	) tempo on tempo.data = var.data and '::text) ||
            CASE
                WHEN NOT v.distribuicao THEN ' tempo.id_regiao = var.id_regiao and '::text
                ELSE ''::text
            END) || ' coalesce(tempo.dimensao,'''') = coalesce(var.dimensao,'''') '::text
            ELSE ''::text
        END AS query
   FROM sistema.variavel v
     JOIN sistema.fonte_dados d ON v.id_fonte_dados = d.id_fonte_dados
     JOIN sistema.config_territorio_variavel h ON h.id_variavel = v.id_variavel
     JOIN sistema.config_territorio_variavel h_base ON h_base.altura = 1 AND h_base.id_variavel = h.id_variavel
     JOIN ( SELECT h_1.id_territorio,
            v_1.id_variavel,
                CASE
                    WHEN v_1.periodicidade IS NULL OR v_1.coluna_data IS NULL THEN ' cast(null as date) '::text
                    ELSE COALESCE(((('cast(date_trunc('''::text || sistema.trunc_peri(quote_ident(v_1.periodicidade))) || ''','::text) || quote_ident(v_1.coluna_data)) || ') as date)'::text, 'null'::text)
                END ||
                CASE
                    WHEN v_1.crescimento THEN
                    CASE
                        WHEN v_1.periodicidade = 'anual'::text THEN '+ interval ''1 year'''::text
                        WHEN v_1.periodicidade = 'trimestral'::text THEN '+ interval ''12 months'''::text
                        WHEN v_1.periodicidade = 'mensal'::text THEN '+ interval ''12 months'''::text
                        ELSE ''::text
                    END
                    ELSE ''::text
                END AS campo_data,
                CASE
                    WHEN v_1.distribuicao = true THEN ' cast(null as numeric) '::text
                    ELSE
                    CASE
                        WHEN h_1.coluna IS NOT NULL THEN COALESCE(sistema.num_format(h_1.coluna, d_1.nome_tabela), h_1.coluna)
                        WHEN h_1.altura > 1 AND h_1.id_territorio <> sistema.territorio_raiz(h_1.id_territorio) THEN ' r.id_regiao_pai '::text
                        ELSE '1'::text
                    END
                END AS campo_regiao,
                CASE
                    WHEN v_1.desabilitar_dim_raiz = true AND h_1.id_territorio_pai IS NULL OR v_1.coluna_dimensao IS NULL THEN 'null'::text
                    ELSE ('trim(both '' '' from '::text || quote_ident(v_1.coluna_dimensao)) || ')'::text
                END AS campo_dimensao,
                CASE
                    WHEN v_1.distribuicao = true THEN sistema.territorio_raiz(h_1.id_territorio)
                    ELSE h_1.id_territorio
                END AS campo_territorio,
                CASE
                    WHEN v_1.distribuicao = false AND h_1.altura > 1 AND h_1.id_territorio <> sistema.territorio_raiz(h_1.id_territorio) THEN (((((((('inner join sistema.regiao_agregacao r '::text || '
'::text) || 'on r.id_regiao_filho = f.'::text) || COALESCE(sistema.num_format(h_base_1.coluna, d_1.nome_tabela), h_base_1.coluna)) || '
'::text) || ' and r.id_territorio_filho = '::text) || quote_literal(h_base_1.id_territorio)) || '
'::text) || ' and r.id_territorio_pai =   '::text) || quote_literal(h_1.id_territorio)
                    ELSE ''::text
                END AS join_hierarquia
           FROM sistema.variavel v_1
             JOIN sistema.fonte_dados d_1 ON v_1.id_fonte_dados = d_1.id_fonte_dados
             JOIN sistema.config_territorio_variavel h_1 ON h_1.id_variavel = v_1.id_variavel
             JOIN sistema.config_territorio_variavel h_base_1 ON h_base_1.altura = 1 AND h_base_1.id_variavel = h_1.id_variavel
             JOIN ( SELECT config_territorio_variavel.id_variavel,
                    min(config_territorio_variavel.id_territorio) AS id_territorio,
                    config_territorio_variavel.hierarquia
                   FROM sistema.config_territorio_variavel
                  WHERE config_territorio_variavel.altura = 1
                  GROUP BY config_territorio_variavel.id_variavel, config_territorio_variavel.hierarquia
                  ORDER BY config_territorio_variavel.id_variavel) h_base_filtro_1 ON h_base_1.id_variavel = h_base_filtro_1.id_variavel AND h_base_1.id_territorio = h_base_filtro_1.id_territorio
             LEFT JOIN LATERAL ( SELECT filtro.id_variavel,
                    'where '::text || string_agg((((((((COALESCE(('
	'::text || filtro.aninhamento) || '
	'::text, ''::text) || ' '::text) || COALESCE(quote_ident(filtro.coluna), ''::text)) || ' '::text) || COALESCE(filtro.operador_comparador, ''::text)) || ' '::text) || COALESCE(
                        CASE
                            WHEN filtro.operador_comparador ~~ '%is%'::text THEN ' null '::text
                            ELSE quote_literal(filtro.valor)
                        END, ''::text)) || ' '::text) || COALESCE('
	'::text || filtro.operador_logico, ''::text), ''::text ORDER BY filtro.id_variavel, filtro.ordem) AS "where"
                   FROM sistema.variavel_filtro filtro
                  GROUP BY filtro.id_variavel) f_1 ON f_1.id_variavel = v_1.id_variavel) campos ON campos.id_variavel = v.id_variavel AND campos.id_territorio = h.id_territorio
     JOIN ( SELECT config_territorio_variavel.id_variavel,
            min(config_territorio_variavel.id_territorio) AS id_territorio,
            config_territorio_variavel.hierarquia
           FROM sistema.config_territorio_variavel
          WHERE config_territorio_variavel.altura = 1
          GROUP BY config_territorio_variavel.id_variavel, config_territorio_variavel.hierarquia
          ORDER BY config_territorio_variavel.id_variavel) h_base_filtro ON h_base.id_variavel = h_base_filtro.id_variavel AND h_base.id_territorio = h_base_filtro.id_territorio
     LEFT JOIN LATERAL ( SELECT filtro.id_variavel,
            'where '::text || string_agg((((((((COALESCE(('
'::text || filtro.aninhamento) || '
'::text, ''::text) || ' '::text) || COALESCE(quote_ident(filtro.coluna), ''::text)) || ' '::text) || COALESCE(filtro.operador_comparador, ''::text)) || ' '::text) || COALESCE(
                CASE
                    WHEN filtro.operador_comparador ~~ '%is%'::text THEN ' null '::text
                    ELSE quote_literal(filtro.valor)
                END, ''::text)) || ' '::text) || COALESCE('
'::text || filtro.operador_logico, ''::text), ''::text ORDER BY filtro.id_variavel, filtro.ordem) AS "where"
           FROM sistema.variavel_filtro filtro
          WHERE NOT (filtro.excluir_regiao_raiz = true AND h.id_territorio = sistema.territorio_raiz(h.id_territorio))
          GROUP BY filtro.id_variavel) f ON f.id_variavel = v.id_variavel
  ORDER BY v.id_variavel, h.id_territorio;

ALTER TABLE sistema.query_variavel
  OWNER TO postgres;

	
	
	-- View: sistema.query_indicador

-- DROP VIEW sistema.query_indicador;

CREATE OR REPLACE VIEW sistema.query_indicador AS 
 SELECT COALESCE(comp.id_indicador_pai, query_indicador.id_indicador) AS id_indicador,
    query_indicador.id_territorio,
    'insert into sistema.indicador_calculo (id_indicador,data,id_regiao,dimensao,id_territorio,valor) '::text ||
        CASE
            WHEN comp.id_indicador_pai IS NOT NULL THEN (((((' select '::text || comp.id_indicador_pai) || ',data,id_regiao,'::text) || COALESCE((''''::text || comp.dimensao) || ''''::text, 'dimensao'::text)) || ' ,id_territorio,valor from ('::text) || query_indicador.query) || ') query_indicador '::text
            ELSE query_indicador.query
        END AS query
   FROM sistema.indicador_composicao comp
     FULL JOIN ( SELECT i_v.id_indicador,
                CASE
                    WHEN col.cont_terr = 0 AND NOT c_ant.id_territorio IS NULL OR i_v.id_variavel IS NULL THEN c_ant.id_territorio
                    ELSE c.id_territorio
                END AS id_territorio,
            ((((((((((((((((((((((((((((((((((((((((('	select'::text || '
'::text) || i_v.id_indicador) || ' as id_indicador'::text) || '
'::text) || ','::text) || 'cast(date_trunc( ' ||
            CASE
                WHEN indic.periodicidade = 'mensal'::text THEN '''month'''::text
                WHEN indic.periodicidade = 'anual'::text THEN '''year'''::text
                WHEN indic.periodicidade = 'trimestral'::text THEN '''quarter'''::text
                ELSE ''::text
            END || ', coalesce('::text) || string_agg(('cast(v'::text || v.id_variavel) || '.data as date)'::text, ','::text ORDER BY i_v.id_indicador, i_v.ordem)) || ')) as date) as data'::text) || '
'::text) || ','::text) || 'coalesce('::text) || string_agg(('cast(v'::text || v.id_variavel) || '.id_regiao as numeric)'::text, ','::text ORDER BY i_v.id_indicador, i_v.ordem)) || ') as id_regiao'::text) || '
'::text) || ','::text) || 'coalesce('::text) || string_agg(('cast(v'::text || v.id_variavel) || '.dimensao as text)'::text, ','::text ORDER BY i_v.id_indicador, i_v.ordem)) || ') as dimensao'::text) || '
'::text) || ','::text) ||
                CASE
                    WHEN col.cont_terr = 0 AND NOT c_ant.id_territorio IS NULL OR i_v.id_variavel IS NULL THEN c_ant.id_territorio
                    ELSE c.id_territorio
                END) || ' as id_territorio'::text) || '
'::text) || ','::text) ||
				' sum( ' ||
                CASE
                    WHEN string_agg(i_v.operador, ' '::text ORDER BY i_v.id_indicador, i_v.ordem) ~~ '%/%'::text THEN (' case when '::text || string_agg(
                    CASE
                        WHEN i_v_ant.operador = '/'::text THEN COALESCE(('cast(coalesce(v'::text || v.id_variavel) || '.valor,0) as numeric) '::text, ''::text)
                        ELSE ''::text
                    END, ' '::text ORDER BY i_v.id_indicador, i_v.ordem)) || ' = 0 then 0 else '::text
                    ELSE ''::text
                END) || 'round('::text) || string_agg((COALESCE(i_v.aninhamento, ''::text) || COALESCE(('cast(coalesce(v'::text || v.id_variavel) || '.valor,0) as numeric) '::text, ''::text)) || COALESCE(i_v.operador, ''::text), ' '::text ORDER BY i_v.id_indicador, i_v.ordem)) || ',4) '::text) ||
                CASE
                    WHEN string_agg(i_v.operador, ' '::text ORDER BY i_v.id_indicador, i_v.ordem) ~~ '%/%'::text THEN ' end '::text
                    ELSE ''::text
                END) || ' ) as valor '::text) || '
'::text) || ' from '::text) || '
'::text) || string_agg(((((
                CASE
                    WHEN i_v.rank > 1 AND NOT (v.coluna_data IS NULL AND v.coluna_dimensao IS NULL AND v.distribuicao = true) THEN (('
'::text || v.tipo_cruzamento) || ' join '::text) || '
'::text
                    WHEN v.coluna_data IS NULL AND v.coluna_dimensao IS NULL AND v.distribuicao = true THEN ' cross join '::text
                    ELSE ''::text
                END || '('::text) || c.query) || ') v'::text) ||
                CASE
                    WHEN i_v.rank_variavel > 1 THEN NULL::integer
                    ELSE v.id_variavel
                END) ||
                CASE
                    WHEN i_v.rank > 1 AND NOT (v.coluna_data IS NULL AND v.coluna_dimensao IS NULL AND v.distribuicao = true) THEN ((((('
'::text || ' on '::text) ||
                    CASE
                        WHEN v.distribuicao = false THEN (((('v'::text || v_ant.id_variavel) || '.id_regiao = '::text) || 'v'::text) || v.id_variavel) || '.id_regiao '::text
                        ELSE ''::text
                    END) ||
                    CASE
                        WHEN v.distribuicao = false AND v.coluna_data IS NOT NULL AND v_ant.coluna_data IS NOT NULL THEN ' and '::text
                        ELSE ''::text
                    END) ||
                    CASE
                        WHEN v.coluna_data IS NOT NULL AND v_ant.coluna_data IS NOT NULL THEN (((('v'::text || v_ant.id_variavel) || '.data = '::text) || 'v'::text) || v.id_variavel) || '.data '::text
                        ELSE ''::text
                    END) ||
                    CASE
                        WHEN (v.distribuicao = false OR v.coluna_data IS NOT NULL AND v_ant.coluna_data IS NOT NULL) AND v.coluna_dimensao IS NOT NULL AND NOT ((v.desabilitar_dim_raiz = true OR v.desabilitar_dim_raiz = true) AND c.id_territorio = sistema.territorio_raiz(c.id_territorio)) THEN ' and '::text
                        ELSE ''::text
                    END) ||
                    CASE
                        WHEN v.coluna_dimensao IS NOT NULL AND NOT ((v.desabilitar_dim_raiz = true OR v.desabilitar_dim_raiz = true) AND c.id_territorio = sistema.territorio_raiz(c.id_territorio)) THEN (((('v'::text || v_ant.id_variavel) || '.dimensao = '::text) || 'v'::text) || v.id_variavel) || '.dimensao '::text
                        ELSE ''::text
                    END
                    ELSE ''::text
                END, ''::text ORDER BY i_v.id_indicador, i_v.rank)) || '
'::text) || ' group by
		coalesce('::text || string_agg(('cast(v'::text  || v.id_variavel) || '.id_regiao as numeric)'::text, ','::text ORDER BY i_v.id_indicador, i_v.ordem) || ')
		 ,cast(date_trunc( ' ||
            CASE
                WHEN indic.periodicidade = 'mensal'::text THEN '''month'''::text
                WHEN indic.periodicidade = 'anual'::text THEN '''year'''::text
                WHEN indic.periodicidade = 'trimestral'::text THEN '''quarter'''::text
                ELSE ''::text
            END || ',coalesce('::text || string_agg(('cast(v'::text || v.id_variavel) || '.data as date)'::text, ','::text ORDER BY i_v.id_indicador, i_v.ordem) || ')) as date)
		,coalesce('::text || string_agg(('cast(v'::text || v.id_variavel) || '.dimensao as text)'::text, ','::text ORDER BY i_v.id_indicador, i_v.ordem) || ')
		order by '::text) || '
'::text) || ' id_regiao'::text) || '
'::text) || ' ,data'::text) || '
'::text) || ' ,dimensao'::text AS query
           FROM ( SELECT rank() OVER (PARTITION BY indicador_x_variavel.id_indicador ORDER BY indicador_x_variavel.ordem) AS rank,
                    rank() OVER (PARTITION BY indicador_x_variavel.id_indicador, indicador_x_variavel.id_variavel ORDER BY indicador_x_variavel.ordem) AS rank_variavel,
                    indicador_x_variavel.id_indicador,
                    indicador_x_variavel.id_variavel,
                    indicador_x_variavel.operador,
                    indicador_x_variavel.ordem,
                    indicador_x_variavel.aninhamento
                   FROM sistema.indicador_x_variavel) i_v
			 INNER JOIN sistema.indicador indic
				ON indic.id_indicador = i_v.id_indicador
             LEFT JOIN sistema.query_variavel c ON c.id_variavel = i_v.id_variavel
             LEFT JOIN sistema.variavel v ON v.id_variavel = i_v.id_variavel
             LEFT JOIN ( SELECT rank() OVER (PARTITION BY indicador_x_variavel.id_indicador ORDER BY indicador_x_variavel.ordem) AS rank,
                    indicador_x_variavel.id_indicador,
                    indicador_x_variavel.id_variavel,
                    indicador_x_variavel.operador,
                    indicador_x_variavel.ordem,
                    indicador_x_variavel.aninhamento
                   FROM sistema.indicador_x_variavel) i_v_ant ON i_v_ant.id_indicador = i_v.id_indicador AND i_v.rank = (i_v_ant.rank + 1)
             LEFT JOIN sistema.variavel v_ant ON v_ant.id_variavel = i_v_ant.id_variavel
             LEFT JOIN ( SELECT COALESCE(coluna.id_fonte_dados, f.id_fonte_dados) AS id_fonte_dados,
                    count(coluna.id_territorio) AS cont_terr
                   FROM sistema.coluna
                     FULL JOIN sistema.fonte_dados f ON f.id_fonte_dados = coluna.id_fonte_dados
                  GROUP BY (COALESCE(coluna.id_fonte_dados, f.id_fonte_dados))) col ON v.id_fonte_dados = col.id_fonte_dados
             LEFT JOIN sistema.query_variavel c_ant ON c_ant.id_variavel = v_ant.id_variavel AND (col.cont_terr = 0 OR i_v.id_variavel IS NULL)
          GROUP BY i_v.id_indicador, indic.periodicidade, (
                CASE
                    WHEN col.cont_terr = 0 AND NOT c_ant.id_territorio IS NULL OR i_v.id_variavel IS NULL THEN c_ant.id_territorio
                    ELSE c.id_territorio
                END)
          ORDER BY i_v.id_indicador) query_indicador ON query_indicador.id_indicador = comp.id_indicador_filho;



ALTER TABLE sistema.query_indicador
  OWNER TO postgres;
	


end;
$BODY$;