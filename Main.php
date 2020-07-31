<?php

namespace IdnoPlugins\ReviewLogin;

class Main extends \Idno\Common\Plugin {
    
    private $userinfo_url = 'https://staging.reviewtalentfeedback.com/api/v1/users/me'; // TODO: change to live value

    function registerPages() {
	
	\Idno\Core\Idno::site()->template()->extendTemplate('account/login', 'review/login/button');
	
    }

    function registerEventHooks()
    {

        // Authenticate
        \Idno\Core\site()->events()->addListener('oauth2/authorised', function(\Idno\Core\Event $event) {

            $data = $event->data();
            
            if (!empty($data['context'])) {
                
                $access_token = $data['access_token'];
                
                Idno::site()->logging()->debug('Review Login Token: ' . $access_token);
                
                $endpoint = \Idno\Core\site()->config()->config['review_login_userinfo_url'] ?? $this->userinfo_url;
                
                $userdata = \Idno\Core\Webservice::get($endpoint, [], [
                    'Authorization: Bearer ' . $access_token
                ]);
                
                Idno::site()->logging()->debug('Review response: ' . var_export($userdata, true));
                
                if (!empty($userdata['content'])) {
                    $userdata = json_decode($userdata['content'], true);
                    if (!empty($userdata['_id'])) {
                        
                        $user = \IdnoPlugins\OAuth2Client\Main::getUser(
                                $data['context'], 
                                $userdata['_id']['$oid'], 
                                $userdata['email'], 
                                $userdata['user_name'], 
                                $userdata['email']);
                        
                        if (!empty($user)) {
                            $event->setResponse($user);
                        }
                        
                        
                    }
                }
            }
        });
    }

}
