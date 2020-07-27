<?php

$buttons = \IdnoPlugins\OAuth2Client\Entities\OAuth2Client::get(['label' => 'reView']);

if (!empty($buttons[0])) {
    ?>
    
<div class="row">
    
    <div class="col-md-6 col-md-offset-3 well text-center">
	<div class="col-md-10 col-md-offset-1">

	    
	    <?php
	    
	    echo $buttons[0]->draw();
	    
	    ?>
	    
	</div>
    </div>
    
</div>
    
    <?php
}