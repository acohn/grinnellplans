<tr class="<?php echo $this->list_attributes; ?>"><td class="title"><?php $this->title_template->display(); ?></td><td class="updated"><span class="long"><?php echo date(LONG_DATE_FORMAT, $this->updated) ?></span><span class="short"><?php echo date(SHORT_DATE_FORMAT, $this->updated) ?></span></td><td class="num_posts"><?php echo $this->posts; ?></td><td class="author"><?php echo $this->firstposter_template->display(); ?></td><td class="recent_poster"><?php echo $this->lastposter_template->display(); ?></td></tr>
