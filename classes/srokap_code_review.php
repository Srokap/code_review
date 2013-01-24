<?php
/**
 * Main plugin class. 
 * Storage for various handlers.
 * @author Paweł Sroka (srokap@gmail.com)
 */
class srokap_code_review {
	static function boot() {
		require_once elgg_get_config('pluginspath').__CLASS__.'/vendors/Zend/Loader/StandardAutoloader.php';
		$loader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));
		$loader->register();
	}
	
	static function init($foo, $bar = 'abc') {
		self::playground();
	}
	
	/**
	 * Test method
	 * 
	 * @param string $foo param one
	 * @param ElggUser $bar param two
	 * @return null Nothing to return.
	 */
	static function playground($foo, ElggSite $bar) {
		$reflection = new Zend\Code\Reflection\FileReflection(__FILE__);
		var_dump($reflection->getDocBlock()->getContents());
		var_dump($reflection->getDocBlock()->getTag('author')->getContent());
		foreach ($reflection->getClasses() as $class) {
			if ($class instanceof Zend\Code\Reflection\ClassReflection) {
				foreach ($class->getMethods() as $method) {
					if ($method instanceof Zend\Code\Reflection\MethodReflection) {
						var_dump($method->getName());
						$doc = $method->getDocBlock();
						if ($doc) {
							var_dump('DOC', $doc->getContents());
							var_dump($doc, $doc->getTags());
						}
						foreach ($method->getParameters() as $param) {
							if ($param instanceof Zend\Code\Reflection\ParameterReflection) {
								var_dump($param->getPosition());
								var_dump($param->getType());
								var_dump($param->getName());
								if ($param->isOptional()) {
									var_dump($param->getDefaultValue());
								}
							}
						}
						var_dump($method->getBody());
					}
				}
			}
		}
		return null;
	}
	
	static function pagesetup() {
		elgg_register_menu_item('page', array(
			'name' => 'srokap_code_review',
			'href' => 'admin/plugins/review/',
			'text' => elgg_echo('admin:plugins:review'),
			'context' => 'admin',
			'section' => 'configure',
			'priority' => 100,
		));
	}

}