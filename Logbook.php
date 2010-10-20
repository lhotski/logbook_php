<?php
/**
 * Simple http://logbook.me client implementation
 *
 * Requirements:
 *   cUrl extension
 *
 * Using:
 *   $log = new Logbook('8e020ea0-6706-47bf-84af-35259576120b'); // your API key
 *   $log->send(Logbook::SEVERITY_DEBUG, "test", array("message" => "my test message"));
 *
 * Service errors catching not implemented.
 * Use errno, error, http_status properties
 *
 * @author Dmitry Prokudin
 * @version 0.0.1
 */
class Logbook {

	/**
	 * Url to api
	 */
	const API_URL = "https://logbook.me/entries";

	/**
	 * Severity constants
	 */
	const SEVERITY_FATAL = "fatal";
	const SEVERITY_ERROR = "error";
	const SEVERITY_WARN = "warn";
	const SEVERITY_INFO = "info";
	const SEVERITY_DEBUG = "debug";
	const SEVERITY_UNKNOWN = "unknown";

	/**
	 * Severity scope
	 * @static
	 */
	public static $SEVERITY_TYPES = array(
		self::SEVERITY_FATAL, self::SEVERITY_ERROR,
		self::SEVERITY_WARN, self::SEVERITY_INFO, self::SEVERITY_DEBUG
	);

	/**
	 * Service api key
	 * @var string
	 */
	protected $api_key;

	/**
	 * Send operation timeout
	 * @var integer
	 */
	public $send_timeout = 5;

	/**
	 * Curl errno
	 * @var integer
	 */
	public $errno;

	/**
	 * Curl error
	 * @var string
	 */
	public $error;

	/**
	 * HTTP status after send
	 * @var integer
	 */
	public $http_status;


	/**
	 * Constructor
	 * @param string $api_key private service api key
	 */
	function __construct($api_key) {
		$this->api_key = $api_key;
	}

	/**
	 * Direct send log entry into service
	 * @param string $severity
	 * @param string $facility
	 * @param array $payload
	 */
	public function send($severity, $facility, $payload) {
		$this->send_entries(array(
			$this->build_entry($severity, $facility, $payload)
		));
	}

	/**
	 * Build entry
	 * @param string $severity
	 * @param string $facility
	 * @param array $payload
	 * @return array
	 */
	protected function build_entry($severity, $facility, $payload) {
		// verify severity, using unknown if not in scope
		if (!in_array($severity, self::$SEVERITY_TYPES)) {
			$severity = self::SEVERITY_UNKNOWN;
		}
		return array(
			"severity" => $severity,
			"facility" => $facility,
			"payload" => $payload,
			"timestamp" => date('c', time())
		);
	}

	/**
	 * Send bundle of log entries
	 * @param array $entries
	 */
	protected function send_entries($entries) {

		$request = http_build_query(array(
			"api_key" => $this->api_key,
			"entries" => $entries
		));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_exec($ch);

		$this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->errno = curl_errno($ch);
		$this->error = curl_error($ch);
		curl_close($ch);
	}

}
