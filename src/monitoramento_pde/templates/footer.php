<footer class="content-info" style="width:100%;background-color:#f8f8f8">
  	<div class="container" style="padding-top:15px;padding-bottom:15px;">
		<div class="row rodape-topo">
			<div class="col-sm-3 text-center">
				<div class="row">
					<div class="col-sm-12" >
						<a href="https://gestaourbana.prefeitura.sp.gov.br"><img class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/images/logo_gestao_footer.jpg" alt="link gestão urbana"></a>
					</div>
				</div>
			</div>
			<div class="col-sm-5">
				<div class="row">
					<?php
						wp_nav_menu( 
							array( 
								'theme_location' => 'footer-menu',
								'container_class' => 'col-sm-12'
							)
						);
					?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="row">
					<div class="col-sm-12">
						<p class="redes-sociais">
							Redes Sociais:
							<a href="https://www.facebook.com/smulsp" target="_blank" rel="noopener"><img src="<?php echo bloginfo('template_url'); ?>/images/btn-facebook-27x27.png" /></a>
							<a href="https://twitter.com/smul_sp" target="_blank" rel="noopener"><img src="<?php echo bloginfo('template_url'); ?>/images/btn-twitter-27x27.png" /></a>
							<a href="https://www.instagram.com/smul_sp/" target="_blank" rel="noopener"><img src="<?php echo bloginfo('template_url'); ?>/images/btn-instagram-27x27.png" /></a>
							<a href="https://www.youtube.com/user/pmspsmdu" target="_blank" rel="noopener"><img src="<?php echo bloginfo('template_url'); ?>/images/icon-youtube.png" /></a>
							<a href="https://www.linkedin.com/company/smulsp" target="_blank" rel="noopener"><img src="<?php echo bloginfo('template_url'); ?>/images/icon-linkedin.png" /></a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-3 text-center">
				<div class="row">
					<div class="col-sm-12">
						<a href="https://prefeitura.sp.gov.br" ><img class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/images/logo_prefeitura_sem_smdu_footer.jpg" alt="link prefeitura de são paulo"></a>
					</div>
				</div>
			</div>
			<div class="col-sm-5">
				<div class="row">
					<div class="col-sm-12">
						<p>
							Secretaria Municipal de Urbanismo e Licenciamento (SMUL)<br />
							Prefeitura de São Paulo<br />
							Rua São Bento, 405, Centro - 18º andar<br />
							CEP 01011-100 - São Paulo - SP<br />
							Telefone: (11) 3113 7500
						</p>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="row">
					<div class="col-sm-12">
						<p>Todo o conteúdo do site está disponível sob a licença Creative Commons. Acesse a página <a href="https://gestaourbana.prefeitura.sp.gov.br/desenvolvimento/" style="color:#db2c30">Desenvolvimento</a> e saiba mais.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php dynamic_sidebar('sidebar-footer'); ?>
</footer>
