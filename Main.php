<?php

namespace IdnoPlugins\ReviewLogin;

use Idno\Core\Idno;

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
                
                if (empty($access_token)) {
                    throw new \RuntimeException('Missing access token');
                }
                
                Idno::site()->logging()->debug('Review Login Token: ' . $access_token);
                
                $endpoint = \Idno\Core\site()->config()->config['review_login_userinfo_url'] ?? $this->userinfo_url;
                
                /*$userdata = \Idno\Core\Webservice::get($endpoint, [
                ], [
                    'Authorization: Bearer ' . $access_token,
                    'Cache-Control: no-cache'
                ]);*/
                $userdata = file_get_contents($endpoint . '?api_key=' . urlencode($access_token)); // Because doorkeeper doesn't like _something_ about the webservices call
                
                Idno::site()->logging()->debug('Review response: ' . var_export($userdata, true));
                
                if (!empty($userdata)) {
                    $userdata = json_decode($userdata, true);
                    if (!empty($userdata['error'])) {
                        throw new \RuntimeException($userdata['error']);
                    }
                    if (!empty($userdata['id'])) {
                        
                        $user = \IdnoPlugins\OAuth2Client\Main::getUser(
                                $data['context'], 
                                $userdata['id'], 
                                $userdata['email'], 
                                $userdata['name'], 
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
