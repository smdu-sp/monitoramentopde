<header class="banner">
  <div id="pular-conteudo">
    <ul>
	  <li><a accesskey="1" href="#conteudo-principal">Ir para o conteúdo <span>1</span></a></li>
	  <li><a accesskey="2" href="#container-secoes">Ir para o menu <span>2</span></a></li>
	  <li><a accesskey="4" href="#rodape">Ir para o rodapé <span>4</span></a></li>
    </ul>
  </div>
  <div class="navbar navbar-default">
    <div class="container">
      <nav class="navbar-header" style="width:100%;" aria-label="Cabeçalho">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".menu-secoes-toggle">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button> 
				<a class="navbar-brand text-uppercase brand-title" href="<?= esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
				<div class="menu-secoes-toggle collapse navbar-collapse">
				  <a class="navbar-brand navbar-right" href="https://prefeitura.sp.gov.br"><img class="img-responsive" id="navbar-prefeitura" src="../app/uploads/2016/08/prefeitura.png" alt="Brasão da Cidade de São Paulo (Ir para o site da Prefeitura)"></a>
				  <!--<a class="navbar-brand navbar-right" href="http://gestaourbana.prefeitura.sp.gov.br"><img class="img-responsive" id="navbar-gestao" src="../app/uploads/2016/08/gestao_urbana.png" alt="link gestão urbana"></a>-->
				</div>
      </nav>
			
      <nav id="container-secoes" class="collapse navbar-collapse menu-secoes-toggle" aria-label="Menu Principal">
        <?php
          if (has_nav_menu('secoes')) :
            wp_nav_menu(['theme_location' => 'secoes',
              'menu_class' => 'nav nav-pills',
              'container' => '']);
          endif;
        ?>
      </nav>
    </div>
  </div>
	
	<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-96225593-1', 'auto');
  ga('send', 'pageview');

	</script>
</header>

<noscript class="<?= (is_front_page() || is_page('acoes-prioritarias') || is_page('dados-abertos')) ? 'app-obrigatorio' : 'app-opcional'?>">
  <p>O seu navegador não suporta JavaScript ou este recurso não está ativo no momento.</p>
  <p>Este site possui aplicação web interativa para visualização de dados, gráficos e mapas que necessita do JavaScript para funcionar corretamente.</p>
</noscript>

<div id="conteudo-principal">
