<?php

namespace IdnoPlugins\ReviewLogin;

class Main extends \Idno\Common\Plugin {

    function registerPages() {
	
	\Idno\Core\Idno::site()->template()->extendTemplate('account/login', 'review/login/button');
	
    }


}
