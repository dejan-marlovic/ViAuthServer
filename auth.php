<?

const CLIENT_KEY = "";

class ViAuth
{
    public function __construct($clientName)
    {
        $this->_authorization = base64_encode($clientName . ':' . CLIENT_KEY);
    }

    public function fetchLastLogin($username)
    {
        $curl = curl_init();
        $url = $this->_apiBase . 'users/fetch_last_login';
        $fields['username'] = $username;
        curl_setopt_array($curl, $this->_generateBody($url, $fields));
        $response = curl_exec($curl);
        http_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $response;
    }

    public function loginWithToken($token)
    {
        $curl = curl_init();
        $url = $this->_apiBase . 'users/login_with_token';
        $fields['token'] = $token;
        curl_setopt_array($curl, $this->_generateBody($url, $fields));
        $response = curl_exec($curl);
        http_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $response;
    }

    public function loginWithUsernamePassword($username, $password)
    {
        $curl = curl_init();
        $url = $this->_apiBase . 'users/login_with_username_password';
        $fields['username'] = $username;
        $fields['password'] = $password;
        curl_setopt_array($curl, $this->_generateBody($url, $fields));
        $response = curl_exec($curl);
        http_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $response;
    }

    public function register($username, $password = null)
    {
        $curl = curl_init();
        $url = $this->_apiBase . 'users/register';
        $fields['username'] = $username;
        $fields['password'] = $password;
        curl_setopt_array($curl, $this->_generateBody($url, $fields));
        $response = curl_exec($curl);
        http_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $response;
    }

    public function resetPassword($username, $password, $token)
    {
        $curl = curl_init();
        $url = $this->_apiBase . 'users/reset_password';
        $fields['username'] = $username;
        $fields['password'] = $password;
        $fields['token'] = $token;
        curl_setopt_array($curl, $this->_generateBody($url, $fields));
        $response = curl_exec($curl);
        http_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $response;
    }

    public function resetToken($username)
    {
        $curl = curl_init();
        $url = $this->_apiBase . 'users/reset_token';
        $fields['username'] = $username;
        curl_setopt_array($curl, $this->_generateBody($url, $fields));
        $response = curl_exec($curl);
        http_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $response;
    }

    public function unregister($username)
    {
        $curl = curl_init();
        $url = $this->_apiBase . 'users/unregister';
        $fields['username'] = $username;
        curl_setopt_array($curl, $this->_generateBody($url, $fields));
        $response = curl_exec($curl);
        http_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $response;
    }

    public function validateToken($token)
    {
        $curl = curl_init();
        $url = $this->_apiBase . 'users/validate_token';
        $fields['token'] = $token;
        curl_setopt_array($curl, $this->_generateBody($url, $fields));
        $response = curl_exec($curl);
        http_response_code(curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $response;
    }

    private function _generateBody($url, $fields)
    {
        return array
        (
            CURLOPT_RETURNTRANSFER => 1,
            /*CURLOPT_HEADER => 1,*/
            CURLOPT_HTTPHEADER => array('Authorization: Basic ' . $this->_authorization, 'Content-Type: application/json'),
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($fields)
        );
    }

    private $_authorization;
    private $_apiBase = 'https://auth.minoch.com/v1/';
}