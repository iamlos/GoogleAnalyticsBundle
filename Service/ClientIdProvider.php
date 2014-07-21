<?php


namespace Happyr\Google\AnalyticsBundle\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ClientIdProvider
 *
 * @author Tobias Nyholm
 *
 */
class ClientIdProvider
{
    const COOKIE_NAME = '_ga';

    /**
     * @var \Symfony\Component\HttpFoundation\Request request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Get client id from cookie... if we can
     *
     *
     * @return false|string
     */
    public function getClientId()
    {
        if (false === $clientId = $this->getClientIdFormCookie()) {

            /*
             * We could not find any cookie with a client id. We just have to randomize one
             */
            $clientId = mt_rand(10, 1000) . round(microtime(true));
        }

        return $clientId;
    }

    /**
     * Return the value of a cookie
     *
     * @param string $name
     *
     * @return mixed|false
     */
    protected function getClientIdFormCookie()
    {
        if ($this->request === null) {
            return false;
        }

        $cookies = $this->request->cookies;
        if (!$cookies->has(self::COOKIE_NAME)) {
            return false;
        }
        $value = $cookies->get(self::COOKIE_NAME);

        return $this->extractCookie($value);
    }

    /**
     * Get the client id from the cookie value.
     *
     * The contents of the cookie might be "GA1.2.1110480476.1405690517"
     * The 3rd section is the user id.
     * The 4th section is the session id.
     * The client id is the user id + the session id
     *
     * @param $cookieValue
     *
     * @link http://stackoverflow.com/a/16107194/1526789
     *
     * @return string|bool clientId or boolean false
     */
    protected function extractCookie($cookieValue)
    {
        if (!preg_match('|[^\.]+\.[^\.]+\.([^\.]+\.[^\.]+)|si', $cookieValue, $matches)) {
            //match not found
            return false;
        }

        return $matches[1];
    }
}