<?php

namespace InstagramAPI\Request;

// use InstagramAPI\Exception\InternalException;
// use InstagramAPI\Exception\SettingsException;
use InstagramAPI\Response;
use InstagramAPI\Utils;

/**
 * Challenge-related functions, such as account verification by code from e-mail and SMS.
 */
class Challenge extends RequestCollection
{
    /**
     * Load data of the challenge.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\ChallengeDataResponse
     */
    public function getChallengeData(
        $apiPath)
    {
        return $this->ig->request(ltrim($apiPath, '/'))
			->setNeedsAuth(false)
            // ->addParam('guid', $this->ig->uuid)
            // ->addParam('device_id', $this->ig->device_id)
			->getDecodedResponse(true);
    }

    /**
     * Select verification method in the verification form (can be 0, 1).
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function selectVerifyMethod(
        $apiPath,
        $choice)
    {
	    $response = $this->buildRequest($apiPath)
			->addPost('choice', $choice)
            // ->getResponse(new Response\GenericResponse());
			->getDecodedResponse(true);

		return $response;
    }

    /**
     * Solve "Confirm this was you" challenge.
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function confirmIdentityChallenge(
        $apiPath)
    {
        return $this->selectVerifyMethod($apiPath, 0);
    }

    /**
     * Send a code received by e-mail or SMS.
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function sendSecurityCode(
        $apiPath,
        $securityCode)
    {
        // Remove all whitespace from the verification code.
        $securityCode = preg_replace('/\s+/', '', $securityCode);

        $response = $this->buildRequest($apiPath)
			->addPost('security_code', $securityCode)
            // ->getResponse(new Response\GenericResponse());
			->getDecodedResponse(true);			

		return $response;
    }

    /**
     * Send the phone number when Instagram requires to.
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function sendPhoneNumber(
        $apiPath,
        $phoneNumber)
    {
	    $response = $this->buildRequest($apiPath)
			->addPost('phone_number', $phone_number)
            // ->getResponse(new Response\GenericResponse());
			->getDecodedResponse(true);			

		return $response;
    }

    /**
     * Send the e-mail address when Instagram requires to.
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function sendEmail(
        $apiPath,
        $email)
    {
        $response = $this->buildRequest($apiPath)
			->addPost('email', $email)
            // ->getResponse(new Response\GenericResponse());
			->getDecodedResponse(true);			

		return $response;
    }

    // public function getCheckpointUndo(
    //     $checkpoint_url)
    // {
    //     return $this->ig->request($checkpoint_url)
	// 		->setNeedsAuth(false)
    //         // ->addParam('guid', $this->ig->uuid)
    //         // ->addParam('device_id', $this->ig->device_id)
    //         // ->getResponse(new Response\GenericResponse());
	// 	    ->getDecodedResponse();
    // }

    /**
     * Reset the current challenge to start over.
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function resetChallenge(
        $apiPath)
    {
		if ($this->ig->account_id)
		{
            return $this->buildRequest($apiPath.'reset/')
                ->addPost('_uid', $this->ig->account_id)
                // ->getResponse(new Response\GenericResponse());
                ->getDecodedResponse(true);
		}
		else
		{
			$resetUrl = str_replace('challenge/', 'challenge/reset/', $apiPath);

            return $this->buildRequest($resetUrl)
                // ->getResponse(new Response\GenericResponse());	
                ->getDecodedResponse(true);
		}
    }

    /**
     * When a consent is required ("Agree with our terms of service"),
     * confirm the first step.
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function agreeConsentFirstStep()
    {
        return $this->buildRequest('consent/existing_user_flow/', [ 'guid', 'device_id' ])
            // ->getResponse(new Response\GenericResponse());
			->getDecodedResponse(true);
    }

    /**
     * Confirm the second step of a consent.
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     * 
     * @see Challenge::agreeConsentFirstStep()
     */
    public function agreeConsentSecondStep()
    {
        return $this->buildRequest('consent/existing_user_flow/', [ 'guid', 'device_id' ])
            ->addPost('_uid', $this->ig->account_id)
            // ->getResponse(new Response\GenericResponse());
			->getDecodedResponse(true);
    }

    /**
     * Confirm the third step of a consent.
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     * 
     * @see Challenge::agreeConsentFirstStep()
     */
    public function agreeConsentThirdStep()
    {
        return $this->ig->request('consent/existing_user_flow/', [ 'guid', 'device_id' ])
            ->setNeedsAuth(false)
            ->addPost('current_screen_key', 'tos_and_two_age_button')
            ->addPost('updates', '{"tos_data_policy_consent_state":"2","age_consent_state":"2"}')
            ->addPost('_uid', $this->ig->account_id)
            // ->getResponse(new Response\GenericResponse());
			->getDecodedResponse(true);
    }

    // public function setUnknownMethod($checkpoint_url)
    // {
    //     $request= $this->ig->request(ltrim($checkpoint_url))
    //             ->setNeedsAuth(false)
    //             ->setSignedPost(false)
    //             ->addPost('choice', 1)
    //             ->getResponse(new Response\GenericResponse());
    //             ->getDecodedResponse(true);

	// 	return $request;
    // }

    // public function getWebCheckpoint ($checkpoint_url)
    // {
    //     $request= $this->ig->request(ltrim($checkpoint_url))
    //             ->setVersion(3)
    //             ->setNeedsAuth(false)
    //             ->setSignedPost(false)
    //             // ->getResponse(new Response\GenericResponse());
    //             ->getRawResponse();
    // 			// ->getDecodedResponse(true);

    //         return $request;
    // }

    // public function WebCheckpointAcknowledgeForm ($checkpoint_url)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);
    //     if ($checkpoint_url{0}=='/')
    //     {
    //         $checkpoint_url=substr($checkpoint_url,1); 
    //     }
    //     $request= $this->ig->request($checkpoint_url)
    //         ->setVersion(3)
    //         ->setNeedsAuth(false)
    //         ->setSignedPost(false)
    //         ->addHeader('Referer', 'https://i.instagram.com'.$checkpoint_url)
    //         ->addHeader('X-Requested-With', 'XMLHttpRequest')
    //         ->addHeader('X-CSRFToken', $this->ig->client->getToken())
    //         ->addHeader('X-IG-WWW-Claim', '0')
    //         ->addHeader('X-IG-App-ID', '1217981644879628')
    //         ->addPost('next', 'None')
    //         // ->getResponse(new Response\GenericResponse());
    //         ->getRawResponse();
    //         // ->getDecodedResponse(true);

    //         $setuseragent = $this->ig->client->setUserAgent($useragent);

    //     return $request;
    // }

    // public function WebCheckpointSelectContactPoint ($checkpoint_url)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);
    //     if ($checkpoint_url{0}=='/')
    //     {
    //         $checkpoint_url=substr($checkpoint_url,1); 
    //     }
    //     $request= $this->ig->request($checkpoint_url)
    //         ->setVersion(3)
    //         ->setNeedsAuth(false)
    //         ->setSignedPost(false)
    //         ->addHeader('Referer', 'https://i.instagram.com'.$checkpoint_url)
    //         ->addHeader('X-Requested-With', 'XMLHttpRequest')
    //         ->addHeader('X-CSRFToken', $this->ig->client->getToken())
    //         ->addHeader('X-IG-WWW-Claim', '0')
    //         ->addHeader('X-IG-App-ID', '1217981644879628')
    //         ->addPost('next', 'None')
    //         ->addPost('choice', 1)
    //         // ->getResponse(new Response\GenericResponse());
    //         ->getRawResponse();
    //         // ->getDecodedResponse(true);

    //         $setuseragent = $this->ig->client->setUserAgent($useragent);
            
    //     return $request;
    // }

    // public function WebCheckpointReviewContactPointChangeForm ($checkpoint_url)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);
    //     if ($checkpoint_url{0}=='/')
    //     {
    //         $checkpoint_url=substr($checkpoint_url,1); 
    //     }
    //     $request= $this->ig->request($checkpoint_url)
    //         ->setVersion(3)
    //         ->setNeedsAuth(false)
    //         ->setSignedPost(false)
    //         ->addHeader('Referer', 'https://i.instagram.com'.$checkpoint_url)
    //         ->addHeader('X-Requested-With', 'XMLHttpRequest')
    //         ->addHeader('X-CSRFToken', $this->ig->client->getToken())
    //         ->addHeader('X-IG-WWW-Claim', '0')
    //         ->addHeader('X-IG-App-ID', '1217981644879628')
    //         ->addPost('choice', 0)
    //         // ->getResponse(new Response\GenericResponse());
    //         ->getRawResponse();
	// 		// ->getDecodedResponse(true);

    //     $setuseragent = $this->ig->client->setUserAgent($useragent);

    //     return $request;
    // }

    // public function WebCheckpointVerifyEmailCode ($security_code, $checkpoint_url)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);
    //     if ($checkpoint_url{0}=='/')
    //         {
    //             $checkpoint_url=substr($checkpoint_url,1); 
    //         }
    //     $request= $this->ig->request($checkpoint_url)
    //         ->setVersion(3)
    //         ->setNeedsAuth(false)
    //         ->setSignedPost(false)
    //         ->addHeader('Referer', 'https://i.instagram.com'.$checkpoint_url)
    //         ->addHeader('X-Requested-With', 'XMLHttpRequest')
    //         ->addHeader('X-CSRFToken', $this->ig->client->getToken())
    //         ->addHeader('X-IG-WWW-Claim', '0')
    //         ->addHeader('X-IG-App-ID', '1217981644879628')
    //         ->addPost('security_code', $security_code)
    //         ->addPost('next', 'None')
    //         // ->getResponse(new Response\GenericResponse());
    //         ->getRawResponse();
    //         // ->getDecodedResponse(true);

    //     $setuseragent = $this->ig->client->setUserAgent($useragent);

    //     return $request;
    // }

    // public function WebCheckpointReset ($checkpoint_url)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);
    //     if ($checkpoint_url{0}=='/')
    //         {
    //             $checkpoint_url=substr($checkpoint_url,1); 
    //         }
    //     //	inlog ('$this->ig->account_id '.$this->ig->account_id);
    //     $request= $this->ig->request($checkpoint_url.'reset/')
    //             ->setVersion(3)
    //             ->setNeedsAuth(false)
    //             ->setSignedPost(false)
    //             ->addHeader('Referer', 'https://i.instagram.com'.$checkpoint_url)
    //             ->addHeader('X-Requested-With', 'XMLHttpRequest')
    //             ->addHeader('X-CSRFToken', $this->ig->client->getToken())
    //             ->addHeader('X-IG-WWW-Claim', '0')
    //             ->addHeader('X-IG-App-ID', '1217981644879628')
    //             ->addPost('next', 'None')
    // //          ->getResponse(new Response\GenericResponse());
    //             ->getRawResponse();
    // //			->getDecodedResponse(true);

    //     $setuseragent = $this->ig->client->setUserAgent($useragent);
                    
    //     return $request;
    // }
    // public function WebCheckpointSetPhoneNumber ($phone_number, $challenge_context, $checkpoint_url)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);
    //     if ($checkpoint_url{0}=='/')
    //         {
    //             $checkpoint_url=substr($checkpoint_url,1); 
    //         }
    //     //	inlog ('$this->ig->account_id '.$this->ig->account_id);
    //     $request= $this->ig->request($checkpoint_url)
    //             ->setVersion(3)
    //             ->setNeedsAuth(false)
    //             ->setSignedPost(false)
    //             ->addHeader('Referer', 'https://i.instagram.com'.$checkpoint_url)
    //             ->addHeader('X-Requested-With', 'XMLHttpRequest')
    //             ->addHeader('X-CSRFToken', $this->ig->client->getToken())
    //             ->addHeader('X-IG-WWW-Claim', '0')
    //             ->addHeader('X-IG-App-ID', '1217981644879628')
    //             ->addPost('phone_number', $phone_number)
    //             ->addPost('challenge_context', $challenge_context)
    // //          ->getResponse(new Response\GenericResponse());
    //             ->getRawResponse();
    // //			->getDecodedResponse(true);
        
    //             $setuseragent = $this->ig->client->setUserAgent($useragent);
                
    //         return $request;
    // }
    // public function WebCheckpointSetSecurityCode ($security_code, $challenge_context, $checkpoint_url)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);
    //     if ($checkpoint_url{0}=='/')
    //         {
    //             $checkpoint_url=substr($checkpoint_url,1); 
    //         }
    //     //	inlog ('$this->ig->account_id '.$this->ig->account_id);
    //     $request= $this->ig->request($checkpoint_url)
    //             ->setVersion(3)
    //             ->setNeedsAuth(false)
    //             ->setSignedPost(false)
    //             ->addHeader('Referer', 'https://i.instagram.com'.$checkpoint_url)
    //             ->addHeader('X-Requested-With', 'XMLHttpRequest')
    //             ->addHeader('X-CSRFToken', $this->ig->client->getToken())
    //             ->addHeader('X-IG-WWW-Claim', '0')
    //             ->addHeader('X-IG-App-ID', '1217981644879628')
    //             ->addPost('security_code', $security_code)
    //             ->addPost('challenge_context', $challenge_context)
    // //          ->getResponse(new Response\GenericResponse());
    //             ->getRawResponse();
    // //			->getDecodedResponse(true);
        
    //             $setuseragent = $this->ig->client->setUserAgent($useragent);
                
    //         return $request;
    // }
    // public function WebCheckpointGrecaptcha ($gRecaptchaResponse, $checkpoint_url)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);
    //     if ($checkpoint_url{0}=='/')
    //         {
    //             $checkpoint_url=substr($checkpoint_url,1); 
    //         }
    //     //	inlog ('$this->ig->account_id '.$this->ig->account_id);
    //     $request= $this->ig->request($checkpoint_url)
    //             ->setVersion(3)
    //             ->setNeedsAuth(false)
    //             ->setSignedPost(false)
    //             ->addHeader('Referer', 'https://i.instagram.com'.$checkpoint_url)
    //             ->addHeader('X-Requested-With', 'XMLHttpRequest')
    //             ->addHeader('X-CSRFToken', $this->ig->client->getToken())
    //             ->addHeader('X-IG-WWW-Claim', '0')
    //             ->addHeader('X-IG-App-ID', '1217981644879628')
    //             ->addPost('g-recaptcha-response', $gRecaptchaResponse)
    // //          ->getResponse(new Response\GenericResponse());
    //             ->getRawResponse();
    // //			->getDecodedResponse(true);
        
    //             $setuseragent = $this->ig->client->setUserAgent($useragent);
                
    //         return $request;
    // }

    /**
     * When Instagram requires to reset a password - send the new one.
     * 
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function sendNewPassword(
        $apiPath,
        $password,
        $publicKey,
        $keyId,
        $version)
    {
        $enc_password1 = Utils::generateEncPassword($password, $publicKey, $keyId, $version);
        $enc_password2 = Utils::generateEncPassword($password, $publicKey, $keyId, $version);

        $time = time();
        $enc_password1 = '#PWD_INSTAGRAM_BROWSER:0:' . $time . ':' . $password;
        $enc_password2 = '#PWD_INSTAGRAM_BROWSER:0:' . $time . ':' . $password;

        $userAgent = $this->ig->device->getUserAgent();
        // FIXME: This is bad
        $newUserAgent = 'Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
        $this->ig->client->setUserAgent($newUserAgent);

        $response = $this->ig->request(ltrim($apiPath, '/'))
            ->setVersion(3)
            ->setNeedsAuth(false)
            ->setSignedPost(false)
            ->addHeader('Referer', 'https://i.instagram.com'.ltrim($apiPath, '/'))
            ->addHeader('X-Requested-With', 'XMLHttpRequest')
            ->addHeader('X-CSRFToken', $this->ig->client->getToken())
            ->addHeader('X-IG-WWW-Claim', '0')
            ->addHeader('X-IG-App-ID', '1217981644879628')
            ->addPost('enc_new_password1', $enc_password1)
            ->addPost('enc_new_password2', $enc_password2)
            ->addPost('next', 'None')
            // ->getResponse(new Response\GenericResponse());
            ->getRawResponse();
            // ->getDecodedResponse(true);

        $this->ig->client->setUserAgent($userAgent);
        
        return $response;
    }

    // public function SetNewPasswordOld ($old_password,$new_password,$chk_url,$rollout_hash='55a1a813af90-hot')
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);
    //     $request = $this->ig->request('accounts/password/change/')
	// 		->setVersion(3)
    //         ->setNeedsAuth(false)
	// 		->setSignedPost(false)
	// 		->addHeader('Referer', 'https://i.instagram.com'.$chk_url)
	// 		->addHeader('X-Requested-With', 'XMLHttpRequest')
	// 		->addHeader('X-CSRFToken', $this->ig->client->getToken())
	// 		->addHeader('X-Instagram-AJAX', $rollout_hash)
	// 		->addHeader('X-IG-WWW-Claim', '0')
	// 		->addHeader('X-IG-App-ID', '1217981644879628')
	// 		->addPost('old_password', $old_password)
	// 		->addPost('new_password1', $new_password)
	// 		->addPost('new_password2', $new_password)
	// 		->addPost('enc_old_password', '')
	// 		->addPost('enc_new_password1', '')
	// 		->addPost('enc_new_password2', '')
    //         // ->getResponse(new Response\GenericResponse());
    //         // ->getRawResponse();
	// 		->getDecodedResponse(true);

	// 	$setuseragent = $this->ig->client->setUserAgent($useragent);		
	// 	return $request;
    // }

    // public function PreSendRecoveryEmailWeb ()
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $new_useragent='Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0';
        
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);

    //     //inlog ('$this->ig->account_id '.$this->ig->account_id);
    //     $request= $this->ig->request('accounts/password/reset/')
    //         ->setVersion(4)
    //         ->setNeedsAuth(false)
    //         ->setSignedPost(false)
    //         ->addHeader('Referer', 'https://www.instagram.com/accounts/password/reset/')
    //         ->addHeader('X-Requested-With', 'XMLHttpRequest')
    //         ->addHeader('X-CSRFToken', $this->ig->client->getToken())
    //         ->addHeader('X-IG-WWW-Claim', '0')
    //         ->addHeader('X-IG-App-ID', '936619743392459')
    //         // ->getResponse(new Response\GenericResponse());
    //         ->getRawResponse();
	// 		// ->getDecodedResponse(true);
    
    //         $setuseragent = $this->ig->client->setUserAgent($useragent);
            
    //     return $request;
    // }

    // public function sendRecoveryEmailWeb($username)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $new_useragent='Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0';
        
    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);

    //     $request= $this->ig->request('accounts/account_recovery_send_ajax/')
	// 		->setVersion(4)
    //         ->setNeedsAuth(false)
	// 		->setSignedPost(false)
	// 		->addHeader('Referer', 'https://www.instagram.com/accounts/password/reset/')
	// 		->addHeader('X-Requested-With', 'XMLHttpRequest')
	// 		->addHeader('X-CSRFToken', $this->ig->client->getToken())
	// 		->addHeader('X-IG-WWW-Claim', '0')
	// 		->addHeader('X-IG-App-ID', '936619743392459')
	// 		->addPost('email_or_username', $username)
	// 		->addPost('recaptcha_challenge_field', '')
	// 		->addPost('flow', '')
    //         // ->getResponse(new Response\GenericResponse());
	// 		->getRawResponse();
    //         // ->getDecodedResponse(true);
	
	// 		$setuseragent = $this->ig->client->setUserAgent($useragent);
			
	// 	return $request;
    // }

    // public function sendHelpSupportWeb ($mail)
    // {
    //     $useragent=$this->ig->device->getUserAgent();
    //     $new_useragent='Mozilla/5.0 (Linux; Android 8.0.0; Custom Phone Build/OPR6.170623.017; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.125 Mobile Safari/537.36'.$useragent;
    //     $new_useragent='Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0';

    //     $setuseragent = $this->ig->client->setUserAgent($new_useragent);

    //     //	inlog ('$this->ig->account_id '.$this->ig->account_id);
    //     $request= $this->ig->request('ajax/help/contact/submit/page')
	// 		->setVersion(4)
    //         ->setNeedsAuth(false)
	// 		->setSignedPost(false)
	// 		->addHeader('Referer', 'https://help.instagram.com/contact/151081798582137')
	// 		->addHeader('X-Requested-With', 'XMLHttpRequest')
	// 		->addHeader('X-CSRFToken', $this->ig->client->getToken())
	// 		->addHeader('X-IG-WWW-Claim', '0')
	// 		->addHeader('X-IG-App-ID', '936619743392459')
	// 		->addPost('jazoest', '2913')
	// 		->addPost('lsd', 'AVouCIHzMG4')
	// 		->addPost('issue2', 'cant_sign_up_for_an_account')
	// 		->addPost('problems', $mail)
	// 		->addPost('details', 'no code send')
	// 		->addPost('support_form_id', '151081798582137')
	// 		->addPost('support_form_hidden_fields', '{"261033887423161":false,"382689188461041":false,"359890017438092":false,"1641092579483969":true,"1551831115092703":true,"150094612274230":true}')
	// 		->addPost('support_form_fact_false_fields', '[]')
	// 		->addPost('__user', '0')
	// 		->addPost('__a', '1')
	// 		->addPost('__dyn', '7xe6Fo4OQ1PyWwHBWo5O12wAxu13wqovzEy58ogbUuw9-3K4o1j8hwem0nCq1ewcG0KEswaq1xwEw7BKdwl8G0jx0Fwww4aw9O1TwoU2swdq0Ho2ew4pw')
	// 		->addPost('__csr=', '')
	// 		->addPost('__req', '7')
	// 		->addPost('__beoa', '0')
	// 		->addPost('__pc', 'PHASED:DEFAULT')
	// 		->addPost('dpr', '1')
	// 		->addPost('__ccg', 'EXCELLENT')
	// 		->addPost('__rev', '1003138699')
	// 		->addPost('__s', ':60q83w:v41p9p')
	// 		->addPost('__hsi', '6909850954511803036-0')
	// 		->addPost('__comet_req', '0')
	// 		->addPost('__spin_r', '1003138699')
	// 		->addPost('__spin_b', 'trunk')
	// 		->addPost('__spin_t', '1608825045')
    //         // ->getResponse(new Response\GenericResponse());
	// 		->getRawResponse();
	// 		// ->getDecodedResponse(true);

    //     $setuseragent = $this->ig->client->setUserAgent($useragent);

	// 	return $request;
    // }

    protected function buildRequest(
        $apiPath,
        $excludeFields = [])
    {
        $request = $this->ig->request(ltrim($apiPath, '/'))
            ->setNeedsAuth(false);

        $fields = [
            '_uuid' => $this->ig->uuid,
            'guid' => $this->ig->uuid,
            'device_id' => $this->ig->device_id,
            '_csrftoken' => $this->ig->client->getToken(),
        ];

        foreach ($fields as $field => $value) {
            if (in_array($field, $excludeFields)) continue;

            $request->addPost($field, $value);
        }

        return $request;
    }

}
