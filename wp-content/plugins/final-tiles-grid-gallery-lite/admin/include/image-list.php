<?php

if(isset($gallery))
{
	$active_filters = "";
	if (ftg_fs()->is_plan_or_trial('ultimate'))
		$active_filters = explode('|', $gallery->filters);
}

?>
<?php foreach($imageResults as $image) {
	$sizes = FinalTiles_Gallery::get_image_size_links($image->imageId);
	$thumb = "";
	
	if($image->type == 'image')
		$thumb = array_key_exists("150x150", $sizes) ? $sizes["150x150"] : $image->imagePath;
	else
		$thumb = plugins_url('../images/video.jpg', __FILE__);
	?>
		<div class='item card hidden-<?php echo $image->hidden ?>' data-type='<?php echo $image->type ?>' data-image-id="<?php echo $image->imageId ?>" data-id="<?php echo $image->Id ?>">
			<div class="figure card-image" style="background-image: url('<?php echo $thumb ?>');">
				<?php if($image->type == 'image') : ?>
					<img class="thumb" src="<?php echo plugins_url('../images/square.gif', __FILE__) ?>" />
				<?php else : ?>
					<div class="aspect-ratio">
						<?php print $image->imagePath ?>
					</div>
				<?php endif ?>
				<a href="#<?php echo $image->type ?>-panel-model" class="edit modal-trigger">
					<i class="mdi mdi-pencil"></i>
				</a>

				<?php if($image->source == "gallery") : ?>
					<a href="#" class="remove">
						<i class="fa fa-times"></i>
					</a>
				<?php endif ?>

				<?php if(in_array($image->imagePath, $sizes)) : ?>
					<span class='card-title'><?php echo array_search($image->imagePath, $sizes) ?></span>
				<?php endif ?>
				<?php if($image->hidden == "T") : ?>
					<span class='card-hidden'><i class="mdi mdi-eye-off" title="Hidden image"></i></span>
				<?php endif ?>
				<?php
				if(! empty($image->filters) || isset($image->group))
				{
					echo "<ul class='filters'>";

					if(isset($image->group) && trim($image->group))
					{
						echo "<li><i class=\"mdi mdi-folder-outline\"></i> $image->group</li>";
					}

					if(! empty($image->filters))
					{
						foreach ( explode( '|', $image->filters ) as $f ) {
							if ( in_array( $f, $active_filters ) ) {
								echo "<li><i class='mdi mdi-tag-outline'></i> $f</li>";
							}
						}
					}

					echo "</ul>";

					echo "<input type='hidden' class ='current_image_filter' value=$image->filters>";

				}

				?>
			</div>
			<div class="card-content">
				<p class="truncate">
					<?php echo (isset($image->title) && !empty($image->title)) ? $image->title : $image->description ?>
				</p>

				<input class="copy" type="hidden" name="id" value="<?php echo $image->Id ?>" />
				<input class="copy" type="hidden" name="type" value="<?php echo $image->type ?>" />
				<input class="copy" type="hidden" name="img_id" value="<?php echo $image->imageId ?>" />
				<input class="copy" type="hidden" name="sortOrder" value="<?php echo $image->sortOrder; ?>" />
				<input class="copy" type="hidden" name="filters" value="<?php echo $image->filters ?>" />
				<input class="copy" type="hidden" name="post_id" value="<?php echo isset($image->postId) ? $image->postId : 0 ?>" />
				<select name="img_url" class="select hidden">
					<?php foreach($sizes as $k => $v) : ?>
						<option <?php echo $v == $image->imagePath ? "selected" : "" ?> value="<?php print $v ?>"><?php print $k ?></option>
					<?php endforeach ?>
				</select>
				<input type="hidden" name="target" value="<?php echo $image->target ?>" />
				<input type="hidden" name="group" value="<?php echo $image->group ?>" />
				<input type="hidden" name="zoom" value="<?php echo $image->zoom ?>" />
				<input type="hidden" name="link" value="<?php echo $image->link ?>" />
				<input type="hidden" name="blank" value="<?php echo $image->blank ?>" />
				<input type="hidden" name="hidden" value="<?php echo $image->hidden ?>" />
				<input type="hidden" name="sortOrder" value="<?php echo $image->sortOrder ?>" />
				<pre class="hidden description"><?php echo $image->description ?></pre>
				<pre class="hidden imagepath"><?php echo htmlentities($image->imagePath) ?></pre>
				<input type="hidden" id="img-title" value="<?php echo $image->title ?>">
				<input type="hidden" id="img-alt" value="<?php echo $image->alt ?>">
			</div>
		</div>
<?php } ?>