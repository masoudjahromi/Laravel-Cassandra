<?php
namespace Masoudjahromi\LaravelCassandra\Response;
use Cassandra\Response\Response;
use Masoudjahromi\LaravelCassandra\Protocol\Frame;

class Authenticate extends Response {
	public function getData(){
		return unpack('n', $this->getBody())[1];
	}
}
