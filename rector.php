<?php declare(strict_types=1);

use Rector\CodeQuality\Rector\ClassMethod\OptionalParametersAfterRequiredRector;
use Rector\CodeQuality\Rector\ClassMethod\ReturnTypeFromStrictScalarReturnExprRector;
use Rector\CodeQuality\Rector\FuncCall\IntvalToTypeCastRector;
use Rector\Config\RectorConfig;
use Rector\Php54\Rector\Array_\LongArrayToShortArrayRector;
use Rector\Php70\Rector\If_\IfToSpaceshipRector;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationBasedOnParentClassMethodRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ArrayShapeFromConstantArrayReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnAnnotationIncorrectNullableRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromReturnNewRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictBoolReturnExprRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNewArrayRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureReturnTypeRector;
use Rector\TypeDeclaration\Rector\FunctionLike\ParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\FunctionLike\ReturnTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\Param\ParamTypeFromStrictTypedPropertyRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return static function ( RectorConfig $rectorConfig ): void {
	$rectorConfig->paths( [
		__DIR__ . '/src',
		__DIR__ . '/tests',
	] );

	//	$rectorConfig->autoloadPaths( [
	//		__DIR__ . '/vendor/php-stubs/wordpress-stubs/',
	//	] );

	$rectorConfig->bootstrapFiles( [
		//__DIR__ . '/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php',
		__DIR__ . '/wordpress/wp-includes/class-wp-error.php',
	] );

	// register a single rule
	// $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class );

//	$rectorConfig->rules( [
//		AddArrayParamDocTypeRector::class,
//		AddClosureReturnTypeRector::class,
//		AddMethodCallBasedStrictParamTypeRector::class,
//		AddReturnTypeDeclarationBasedOnParentClassMethodRector::class,
//		ArrayShapeFromConstantArrayReturnRector::class,
//		IfToSpaceshipRector::class,
//		IntvalToTypeCastRector::class,
//		LongArrayToShortArrayRector::class,
//		OptionalParametersAfterRequiredRector::class,
//		ParamTypeByMethodCallTypeRector::class,
//		ParamTypeFromStrictTypedPropertyRector::class,
//		ParamTypeDeclarationRector::class,
//		ReturnAnnotationIncorrectNullableRector::class,
//		ReturnTypeFromStrictBoolReturnExprRector::class,
//		ReturnTypeFromStrictNewArrayRector::class,
//		ReturnTypeFromStrictScalarReturnExprRector::class,
//		ReturnTypeDeclarationRector::class,
//		ReturnTypeFromReturnNewRector::class,
//		ReturnTypeFromStrictNativeCallRector::class,
//		ReturnTypeFromStrictNewArrayRector::class,
//		TypedPropertyFromStrictConstructorRector::class,
//		NameImportingPostRector::class,
//	] );
//
//	$rectorConfig->ruleWithConfiguration( AddVoidReturnTypeWhereNoReturnRector::class, [
//		AddVoidReturnTypeWhereNoReturnRector::USE_PHPDOC => false,
//	] );

	//$rectorConfig->importNames( true, false );

	// define sets of rules
	$rectorConfig->sets( [
		LevelSetList::UP_TO_PHP_74,
	] );

	// Path to PHPStan with extensions, that PHPStan in Rector uses to determine types
	$rectorConfig->phpstanConfig( __DIR__ . '/tests/phpstan.neon' );
};
