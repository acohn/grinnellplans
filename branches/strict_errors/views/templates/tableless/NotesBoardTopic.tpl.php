<tr class="<?php echo $this->list_attributes; ?>"><td><?php $this->title_template->display(); ?></td><td><span class="long"><?php echo date(LONG_DATE_FORMAT, $this->updated) ?></span><span class="short"><?php echo date(SHORT_DATE_FORMAT, $this->updated) ?></span></td><td><?php echo $this->posts; ?></td><td><?php echo $this->firstposter_template->display(); ?></td><td><?php echo $this->lastposter_template->display(); ?></td></tr>
