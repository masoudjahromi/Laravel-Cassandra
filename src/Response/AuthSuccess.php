<?php
namespace Masoudjahromi\LaravelCassandra\Response;

use Cassandra\Response\Response;

class AuthSuccess extends Response {
	public function getData(){
		/**
		 * Indicates that the server is ready to process queries. This message will be
		 * sent by the server either after a STARTUP message if no authentication is
		 * required, or after a successful CREDENTIALS message.
		 */
		return null;
	}
}
