<div class="container p-2">
	<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label for="search-field" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'swgtheme' ); ?></label>
		<div class="input-group">
			<input type="search" 
				id="search-field" 
				name="s" 
				value="<?php echo get_search_query(); ?>" 
				placeholder="<?php esc_attr_e( 'Search in this site', 'swgtheme' ); ?>" 
				class="form-control" 
				required>
			<span class="input-group-btn">
				<button type="submit" class="btn btn-danger">
					<i class="fa fa-search" aria-hidden="true"></i>
					<span><?php esc_html_e( 'Search', 'swgtheme' ); ?></span>
				</button>
			</span>
		</div>
	</form>
</div>
