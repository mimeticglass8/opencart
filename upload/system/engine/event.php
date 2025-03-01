<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2022, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/
namespace Opencart\System\Engine;
/**
* Class Event
*
* https://github.com/opencart/opencart/wiki/Events-(script-notifications)-2.2.x.x
*/
class Event {
	/**
	 * @var \Opencart\System\Engine\Registry
	 */
	protected $registry;
	/**
	 * @var array
	 */
	protected array $data = [];
	
	/**
	 * Constructor
	 *
	 * @param	object	$route
 	*/
	public function __construct(\Opencart\System\Engine\Registry $registry) {
		$this->registry = $registry;
	}
	
	/**
	 * 
	 *
	 * @param	string	$trigger
	 * @param	object	$action
	 * @param	int		$priority
 	*/	
	public function register(string $trigger, \Opencart\System\Engine\Action $action, int $priority = 0): void {
		$this->data[] = [
			'trigger'  => $trigger,
			'action'   => $action,
			'priority' => $priority
		];
		
		$sort_order = [];

		foreach ($this->data as $key => $value) {
			$sort_order[$key] = $value['priority'];
		}

		array_multisort($sort_order, SORT_ASC, $this->data);	
	}
	
	/**
	 * 
	 *
	 * @param	string	$event
	 * @param	array	$args
	 *
	 * @return   mixed
 	*/		
	public function trigger(string $event, array $args = []) {
		foreach ($this->data as $value) {
			if (preg_match('/^' . str_replace(['\*', '\?'], ['.*', '.'], preg_quote($value['trigger'], '/')) . '/', $event)) {
				$value['action']->execute($this->registry, $args);
			}
		}

		return '';
	}
	
	/**
	 * unregister
	 *
	 * @param	string	$trigger
	 * @param	string	$route
 	*/	
	public function unregister(string $trigger, string $route): void {
		foreach ($this->data as $key => $value) {
			if ($trigger == $value['trigger'] && $value['action']->getId() == $route) {
				unset($this->data[$key]);
			}
		}			
	}
	
	/**
	 * clear
	 *
	 * @param	string	$trigger
 	*/		
	public function clear(string $trigger): void {
		foreach ($this->data as $key => $value) {
			if ($trigger == $value['trigger']) {
				unset($this->data[$key]);
			}
		}
	}	
}
