<?php
foreach (\App\Models\MenuItem::orderBy('sort_order')->get() as $i) {
    echo $i->label.' -> '.$i->link_type.':'.$i->link_value.PHP_EOL;
}
