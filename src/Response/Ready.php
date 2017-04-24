<?php
namespace Masoudjahromi\LaravelCassandra\Response;
use Masoudjahromi\LaravelCassandra\Protocol\Frame;
use Masoudjahromi\LaravelCassandra\Response\Response;

class Ready extends Response {
	public function getData(){
		/**
		 * Indicates that the server is ready to process queries. This message will be
		 * sent by the server either after a STARTUP message if no authentication is
		 * required, or after a successful CREDENTIALS message.
		 */
		return null;
	}
}