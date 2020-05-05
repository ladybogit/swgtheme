<div class="container p-2">
  <form role="search" method="get" class="form search-form" action="/index.php">
<div class="input-group">
	<input type="text" name="s" value="<?php the_search_query();?>" placeholder="Search in this site" class="form-control" required>
      
      <span class="input-group-btn">
        <button type="submit" value="Search" class="btn btn-danger" type="button"><i class="fa fa-search" aria-hidden="true"></i>Search!</button>
      </span>
    </div>
    </form>
</div>
<?php $search_terms = htmlspecialchars( $_GET["s"] ); ?>
