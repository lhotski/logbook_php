<?php
/**
 * Buffered send implementation for http://logbook.me client
 *
 * Using:
 *   $log = new Logbook_Buffer('8e020ea0-6706-47bf-84af-35259576120b');
 *   $log->add_entry(Logbook::SEVERITY_DEBUG, "test", array(
 *	   "message" => "my test message"
 *	 ));
 *
 *   // force send and clean buffer
 *	 $log->commit();
 *
 * TODO: shared buffer needed
 *
 * @author Dmitry Prokudin
 * @version 0.0.1
 */
class Logbook_Buffer extends Logbook {

	/**
	 * Collection of entries
	 * @var array
	 */
	protected $entries = array();

	/**
	 * Limit of buffer length
	 * @var integer
	 */
	public $length_limit = 100;

	/**
	 * Limit of buffer age in seconds
	 * @var integer
	 */
	public $age_limit = 5;

	/**
	 * Add log entry into buffer and send on demand
	 * @param string $severity
	 * @param string $facility
	 * @param array $payload
	 */
	public function add_entry($severity, $facility, $payload) {
		$this->entries[] = $this->build_entry($severity, $facility, $payload);
		$this->send_on_demand();
	}

	/**
	 * Flush buffer
	 */
	public function flush() {
		if (!empty($this->entries)) {
			$this->send_entries($this->entries);
		}
	}

	/**
	 * Get elements number of buffer
	 * @return integer
	 */
	public function length() {
		return count($this->entries);
	}

	/**
	 * Manage buffer: send if full or expired
	 */
	protected function send_on_demand() {
		if ($this->length() >= $this->length_limit
			|| $this->age() >= $this->age_limit)
		{
			$this->send_entries($this->entries);
			$this->entries = array();
		}
	}

	/**
	 * Get buffer age in seconds by first entry
	 * @return integer
	 */
	protected function age() {
		if (empty($this->entries)) {
			return 0;
		}
		$firsttime = strtotime($this->entries[0]["timestamp"]);
		return $firsttime ? time() - $firsttime : 0;
	}

}
