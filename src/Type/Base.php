<?php
namespace Masoudjahromi\LaravelCassandra\Type;

abstract class Base{
    
    const CUSTOM = 0x0000;
    const ASCII = 0x0001;
    const BIGINT = 0x0002;
    const BLOB = 0x0003;
    const BOOLEAN = 0x0004;
    const COUNTER = 0x0005;
    const DECIMAL = 0x0006;
    const DOUBLE = 0x0007;
    const FLOAT = 0x0008;
    const INT = 0x0009;
    const TEXT = 0x000A;        // deprecated in Protocol v3
    const TIMESTAMP = 0x000B;
    const UUID = 0x000C;
    const VARCHAR = 0x000D;
    const VARINT = 0x000E;
    const TIMEUUID = 0x000F;
    const INET = 0x0010;
    const COLLECTION_LIST = 0x0020;
    const COLLECTION_MAP = 0x0021;
    const COLLECTION_SET = 0x0022;
    const UDT = 0x0030;
    const TUPLE = 0x0031;
    
    public static $typeClassMap = [
        self::ASCII     => 'Masoudjahromi\LaravelCassandra\Type\Ascii',
        self::VARCHAR   => 'Masoudjahromi\LaravelCassandra\Type\Varchar',
        self::TEXT      => 'Masoudjahromi\LaravelCassandra\Type\Varchar',  // deprecated in Protocol v3
        self::VARINT    => 'Masoudjahromi\LaravelCassandra\Type\Varint',
        self::BIGINT    => 'Masoudjahromi\LaravelCassandra\Type\Bigint',
        self::COUNTER   => 'Masoudjahromi\LaravelCassandra\Type\Counter',
        self::TIMESTAMP => 'Masoudjahromi\LaravelCassandra\Type\Timestamp',
        self::BLOB      => 'Masoudjahromi\LaravelCassandra\Type\Blob',
        self::BOOLEAN   => 'Masoudjahromi\LaravelCassandra\Type\Boolean',
        self::DECIMAL   => 'Masoudjahromi\LaravelCassandra\Type\Decimal',
        self::DOUBLE    => 'Masoudjahromi\LaravelCassandra\Type\Double',
        self::FLOAT     => 'Masoudjahromi\LaravelCassandra\Type\PhpFloat',
        self::INT       => 'Masoudjahromi\LaravelCassandra\Type\PhpInt',
        self::UUID      => 'Masoudjahromi\LaravelCassandra\Type\Uuid',
        self::TIMEUUID  => 'Masoudjahromi\LaravelCassandra\Type\Timeuuid',
        self::INET      => 'Masoudjahromi\LaravelCassandra\Type\Inet',
        self::COLLECTION_LIST => 'Masoudjahromi\LaravelCassandra\Type\CollectionList',
        self::COLLECTION_SET  => 'Masoudjahromi\LaravelCassandra\Type\CollectionSet',
        self::COLLECTION_MAP  => 'Masoudjahromi\LaravelCassandra\Type\CollectionMap',
        self::UDT       => 'Masoudjahromi\LaravelCassandra\Type\UDT',
        self::TUPLE     => 'Masoudjahromi\LaravelCassandra\Type\Tuple',
        self::CUSTOM    => 'Masoudjahromi\LaravelCassandra\Type\Custom',
    ];

    /**
     * 
     * @var array
     */
    protected $_definition;

    /**
     * 
     * @var mixed
     */
    protected $_value;
    
    /**
     * @var string
     */
    protected $_binary;
    
    /**
     * 
     * @param mixed $value
     */
    public function __construct($value = null){
        $this->_value = $value;
    }
    
    /**
     * 
     * @param string $binary
     * @return self
     */
    public function setBinary($binary){
        $this->_binary = $binary;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getBinary(){
        if ($this->_binary === null)
            $this->_binary = static::binary($this->_value, $this->_definition);
        
        return $this->_binary;
    }
    
    /**
     * @return mixed
     */
    public function getValue(){
        if ($this->_value === null && $this->_binary !== null)
            $this->_value = static::parse($this->_binary, $this->_definition);
        
        return $this->_value;
    }
    
    /**
     * @return string
     */
    public function __toString(){
        return (string) $this->_value;
    }
    
    public static function getBinaryByType($dataType, $value){
        if (is_array($dataType)){
            if (!isset($dataType['definition']))
                throw new Exception('Since v0.7, collection types should have "definition" directive.');
            $class = self::$typeClassMap[$dataType['type']];
            return $class::binary($value, $dataType['definition']);
        }
        else{
            $class = self::$typeClassMap[$dataType];
            return $class::binary($value);
        }
    }
    
    /**
     * 
     * @param int|array $dataType
     * @param mixed $value
     * @throws Exception
     * @return Base|null
     */
    public static function getTypeObject($dataType, $value) {
        if ($value === null)
            return null;
        
        if (!is_array($dataType)){
            $class = self::$typeClassMap[$dataType];
            return new $class($value);
        }
        else{
            $class = self::$typeClassMap[$dataType['type']];
            return new $class($value, $dataType['definition']);
        }
    }
}
