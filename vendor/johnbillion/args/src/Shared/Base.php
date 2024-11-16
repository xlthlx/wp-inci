<?php

declare(strict_types=1);

namespace Args\Shared;

use Args\Arrayable\Arrayable;

abstract class Base implements Arrayable {
	public const ORDER_ASC = 'ASC';
	public const ORDER_DESC = 'DESC';

	use \Args\Arrayable\ProvidesFromArray;
	use \Args\Arrayable\ProvidesToArray;

	final public function __construct() {
		if ( $this instanceof \Args\DateQuery\WithArgs ) {
			$this->setDateQuery( new \Args\DateQuery\Query );
		}
		if ( $this instanceof \Args\MetaQuery\WithArgs ) {
			$this->setMetaQuery( new \Args\MetaQuery\Query );
		}
		if ( $this instanceof \Args\TaxQuery\WithArgs ) {
			$this->setTaxQuery( new \Args\TaxQuery\Query );
		}
	}
}
