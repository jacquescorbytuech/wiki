<?php

namespace MediaWiki\Extension\Math;

use DataValues\StringValue;
use InvalidArgumentException;
use MediaWiki\Extension\Math\InputCheck\RestbaseChecker;
use MediaWiki\MediaWikiServices;
use ValueValidators\Error;
use ValueValidators\Result;
use ValueValidators\ValueValidator;

/**
 * @author Duc Linh Tran
 * @author Julian Hilbig
 * @author Moritz Schubotz
 */
class MathValidator implements ValueValidator {

	/**
	 * Validates a value with MediaWiki\Extension\Math\InputCheck\RestbaseChecker
	 *
	 * @param StringValue $value The value to validate
	 *
	 * @return Result
	 * @throws InvalidArgumentException if not called with a StringValue
	 */
	public function validate( $value ) {
		global $wgMathUseRestBase;
		if ( !( $value instanceof StringValue ) ) {
			throw new InvalidArgumentException( '$value must be a StringValue' );
		}

		// get input String from value
		$tex = $value->getValue();
		if ( $wgMathUseRestBase ) {
			$checker = new RestbaseChecker( $tex );
		} else {
			$checker = MediaWikiServices::getInstance()
				->getService( 'Math.CheckerFactory' )
				->newMathoidChecker( $tex, 'tex' );
		}
		if ( $checker->isValid() ) {
			return Result::newSuccess();
		}

		// TeX string is not valid
		return Result::newError(
			[
				Error::newError( '', null, 'malformed-value', [ $checker->getError() ] )
			]
		);
	}

	/**
	 * @see ValueValidator::setOptions()
	 *
	 * @param array $options
	 */
	public function setOptions( array $options ) {
		// Do nothing. This method shouldn't even be in the interface.
	}
}
