<header class="banner">
  <nav class="navbar navbar-default">
    <div class="container">
			<div class="navbar-header" style="width:100%;">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".menu-secoes-toggle">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button> 
				<a class="navbar-brand text-uppercase brand-title" href="<?= esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
				<div class="menu-secoes-toggle collapse navbar-collapse">
				  <a class="navbar-brand navbar-right" href="http://prefeitura.sp.gov.br"><img class="img-responsive" id="navbar-prefeitura" src="../app/uploads/2016/08/prefeitura.png" alt="link prefeitura de são paulo"></a>
				  <!--<a class="navbar-brand navbar-right" href="http://gestaourbana.prefeitura.sp.gov.br"><img class="img-responsive" id="navbar-gestao" src="../app/uploads/2016/08/gestao_urbana.png" alt="link gestão urbana"></a>-->
				</div>
			</div>
			
      <?php
      if (has_nav_menu('secoes')) :
        wp_nav_menu(['theme_location' => 'secoes',
					 'menu_class' => 'nav nav-pills',
					 'container_class' => 'collapse navbar-collapse menu-secoes-toggle',
					 'container_id' => 'container-secoes']);
      endif;
      ?>

    
    </div>
  </nav>
	
	<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-96225593-1', 'auto');
  ga('send', 'pageview');

	</script>
</header>


