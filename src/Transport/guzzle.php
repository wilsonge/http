<?php
/**
 * Part of the Joomla Framework Http Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Transport;

use Joomla\Http\TransportInterface;
use Joomla\Http\Response;
use Joomla\Uri\UriInterface;
use GuzzleHttp\Client;

/**
 * HTTP transport class for using PHP streams.
 *
 * @since  1.0
 */
class Guzzle implements TransportInterface
{
	/**
	 * @var    array  The client options.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @param   array  $options  Client options object.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function __construct($options = array())
	{
		// Verify that fopen() is available.
		if (!self::isSupported())
		{
			throw new \RuntimeException('Cannot use a guzzle transport when fopen() is not available.');
		}

		$this->options = $options;
	}

	/**
	 * Send a request to the server and return a JHttpResponse object with the response.
	 *
	 * @param   string        $method     The HTTP method for sending the request.
	 * @param   UriInterface  $uri        The URI to the resource to request.
	 * @param   mixed         $data       Either an associative array or a string to be sent with the request.
	 * @param   array         $headers    An array of request headers to send with the request.
	 * @param   integer       $timeout    Read timeout in seconds.
	 * @param   string        $userAgent  The optional user agent string to send with the request.
	 *
	 * @return  Response
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function request($method, UriInterface $uri, $data = null, array $headers = null, $timeout = null, $userAgent = null)
	{
		if ($userAgent)
		{
			$this->options['defaults']['headers']['user-agent'] = $userAgent;
		}

		if ($timeout)
		{
			$this->options['defaults']['timeout'] = $timeout;
		}
	
		$client = new Client($this->options);
		$request = $client->createRequest($method, $uri->toString());
		$response = $client->send($request);
	
		return $this->getResponse($response);
	}

	/**
	 * Method to get a response object from a server response.
	 *
	 * @param   \GuzzleHttp\Message\ResponseInterface  $guzzleResponse  The complete guzzle repsonse instance.
	 *
	 * @return  Response
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException
	 */
	private function getResponse($guzzleResponse)
	{
		// Create the response object.
		$return = new Response;

		$return->body    = $guzzleResponse->getBody();
		$return->headers = $guzzleResponse->getHeaders();
		$return->code    = $guzzleResponse->getStatusCode();

		return $return;
	}

	/**
	 * Method to check if http transport stream available for use
	 *
	 * @return  boolean  True if available else false
	 *
	 * @since   1.0
	 */
	public static function isSupported()
	{
		return true;
	}
}
