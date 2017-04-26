<?php
namespace Masoudjahromi\LaravelCassandra\Request;
use Masoudjahromi\LaravelCassandra\Protocol\Frame;

class Prepare extends Request{

	protected $opcode = Frame::OPCODE_PREPARE;
	
	/**
	 * 
	 * @var string
	 */
	protected $_cql;
	
	/**
	 * 
	 * @param string $cql
	 */
	public function __construct($cql) {
		$this->_cql = $cql;
	}
	
	public function getBody(){
		return pack('N', strlen($this->_cql)) . $this->_cql;
	}
}