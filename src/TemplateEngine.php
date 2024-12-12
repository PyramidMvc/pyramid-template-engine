<?php
/**
 * App          : Pyramid PHP Fremework
 * Author       : Nihat Doğan
 * Email        : info@pyramid.com
 * Website      : https://www.pyramid.com
 * Created Date : 01/01/2025
 * License GPL
 *
 */

namespace Pyramid;


use Pyramid\Response;
use Pyramid\AntiXSS;
use Pyramid\Services\CsrfService;

class TemplateEngine extends Response {

	/**
	 * @return void
	 * Template engine parse ediyoruz
	 */
	public static function parse(): void {

		TemplateEngine::parseHead();
		TemplateEngine::parseHeader();
		TemplateEngine::parseSidebar();
		TemplateEngine::parseTopbar();
		TemplateEngine::parseFooter();

		TemplateEngine::parseInclude();
		TemplateEngine::parseIncludeOnce();
		TemplateEngine::parseRequire();
		TemplateEngine::parseRequire_once();

		TemplateEngine::parseCustomHead();

		TemplateEngine::parseVariables();
		TemplateEngine::parseVariablesHtml();
		TemplateEngine::parseForeach();
		TemplateEngine::parseFor();
		TemplateEngine::parsePhp();
		TemplateEngine::parseClass();
		TemplateEngine::parseEcho();

		TemplateEngine::parseIf();
		TemplateEngine::parseIsset();
		TemplateEngine::parseEmpty();

		TemplateEngine::parseSwitch();
		TemplateEngine::parseWhile();
		TemplateEngine::parseDowhile();
		TemplateEngine::parseHtmlSection();
		TemplateEngine::parseCsrf();

	}

	public static function parseCsrf(): void {
		self::$view = preg_replace_callback( @trim( '/@csrf/' ), function ( $variable ) {
			return CsrfService::csrfField();
		}, self::$view );
	}
	public static function parseHead(): void {
		self::$view = preg_replace_callback( @trim( '/@head\((.*)\)/' ), function ( $variable ) {
			return '<?php require(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}

	public static function parseHeader(): void {
		self::$view = preg_replace_callback( @trim( '/@header\((.*)\)/' ), function ( $variable ) {
			return '<?php require(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}

	public static function parseSidebar(): void {
		self::$view = preg_replace_callback( @trim( '/@sidebar\((.*)\)/' ), function ( $variable ) {
			return '<?php require(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}

	public static function parseTopbar(): void {
		self::$view = preg_replace_callback( @trim( '/@topbar\((.*)\)/' ), function ( $variable ) {
			return '<?php require(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}

	public static function parseFooter(): void {
		self::$view = preg_replace_callback( @trim( '/@footer\((.*)\)/' ), function ( $variable ) {
			return '<?php require(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}


	public static function url(): string {
		return sprintf(
			"%s://%s%s",
			isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
			$_SERVER['SERVER_NAME'],
			$_SERVER['REQUEST_URI']
		);
	}

	public static function parseCustomHead(): void {
		self::$view = preg_replace_callback( @trim( '/@stack\((.*?)\)/' ), function ( $variable ) {
			$snc = @trim( $variable[1] );

			preg_match_all( @trim( "/@push\($snc\)\s*?(.*?)@endpush/is" ), @trim( self::$view ), $matches );
			foreach ( $matches[1] as $key => $val ) {
				return $val;
			}
		}, self::$view );

		self::$view = preg_replace_callback( @trim( '/@push\((.*?)\)\s*?(.*?)@endpush/is' ), function ( $variable ) {

			return '';
		}, self::$view );

	}

	public static function parseEcho(): void {
		self::$view = preg_replace_callback( @trim( '/@echo/' ), function ( $variable ) {
			return 'echo';
		}, self::$view );
	}

	public static function parsePhp(): void {
		self::$view = preg_replace_callback( @trim( '/@php/' ), function ( $variable ) {
			return '<?php';
		}, self::$view );
		self::$view = preg_replace_callback( @trim( '/@endphp/' ), function ( $variable ) {
			return '?>';
		}, self::$view );
	}

	public static function parseClass(): void {
		self::$view = preg_replace_callback( @trim( '/@use\((.*?),(.*?)\)/' ), function ( $variable ) {
			return '<?php $' . str_replace( "'", "", @trim( $variable[2] ) ) . '= new ' . str_replace( "'", "", @trim( $variable[1] ) ) . '();?>';
		}, self::$view );

		self::$view = preg_replace_callback( @trim( '/@use\((.*?)\)/' ), function ( $variable ) {
			return '<?php use ' . str_replace( "'", "", @trim( $variable[1] ) ) . ';?>';
		}, self::$view );

	}

	public static function parseVariables(): void {
		self::$view = preg_replace_callback( @trim( '/{{(.*?)}}/' ), function ( $variable ) {
			$antiXss = new AntiXSS();

			return '<?php echo ' . $antiXss->xss_clean( @trim( $variable[1] ) ) . ';?>';
		}, self::$view );
	}

	public static function parseVariablesHtml(): void {
		self::$view = preg_replace_callback( @trim( '/{!!(.*?)!!}/' ), function ( $variable ) {
			return '<?php echo ' . @trim( $variable[1] ) . ';?>';
		}, self::$view );
	}

	public static function parseInclude(): void {
		self::$view = preg_replace_callback( @trim( '/@include\((.*)\)/' ), function ( $variable ) {
			return '<?php include(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}

	public static function parseIncludeOnce(): void {
		self::$view = preg_replace_callback( @trim( '/@include_once\((.*)\)/' ), function ( $variable ) {
			return '<?php include_once(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}

	public static function parseRequire(): void {
		self::$view = preg_replace_callback( @trim( '/@require\((.*)\)/' ), function ( $variable ) {
			return '<?php require(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}

	public static function parseRequire_once(): void {
		self::$view = preg_replace_callback( @trim( '/@require_once\((.*)\)/' ), function ( $variable ) {
			return '<?php require_once(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}

	public static function parseForeach(): void {
		self::$view = preg_replace_callback( @trim( '/@foreach\((.*?) as (.*)\)/' ), function ( $variable ) {

			if ( str_contains( $variable[2], '=>' ) ) {
				[ $key, $value ] = explode( '=>', $variable[2] );

				return '<?php foreach(' . @trim( $variable[1] ) . ' as ' . @trim( $key ) . ' => ' . @trim( $value ) . '): ?>';
			}

			return '<?php foreach(' . @trim( $variable[1] ) . ' as ' . @trim( $variable[2] ) . '): ?>';
		}, self::$view );
		self::$view = preg_replace_callback( @trim( '/@endforeach/' ), function ( $a ) {
			return '<?php endforeach; ?>';
		}, self::$view );


		self::$view = preg_replace_callback( @trim( '/@forelse\((.*?) as (.*)\)/' ), function ( $variable ) {

			if ( str_contains( $variable[2], '=>' ) ) {
				[ $key, $value ] = explode( '=>', $variable[2] );

				return '<?php if( count((array)@trim($variable[1])) > 0): ?><?php foreach(' . @trim( $variable[1] ) . ' as ' . @trim( $key ) . ' => ' . @trim( $value ) . '): ?><?php endforeach; ?>';
			}

			return '<?php if( count((array)@trim($variable[1])) > 0): ?><?php foreach(' . @trim( $variable[1] ) . ' as ' . @trim( $variable[2] ) . '): ?><?php endforeach; ?>';
		}, self::$view );

		self::$view = preg_replace_callback( @trim( '/@empty/' ), function ( $a ) {
			return '<?php else: ?>';
		}, self::$view );

		self::$view = preg_replace_callback( @trim( '/@endforelse/' ), function ( $a ) {
			return '<?php endif;?>';
		}, self::$view );

	}

	public static function parseFor(): void {
		self::$view = preg_replace_callback( @trim( '/@for\((.*?)\)/' ), function ( $variable ) {

			return '<?php for(' . @trim( $variable[1] ) . '): ?>';
		}, self::$view );
		self::$view = preg_replace_callback( @trim( '/@endfor/' ), function ( $a ) {
			return '<?php endfor; ?>';
		}, self::$view );
	}

	public static function parseWhile(): void {
		self::$view = preg_replace_callback( @trim( '/@while\((.*?)\)/' ), function ( $variable ) {
			return '<?php while(' . @trim( $variable[1] ) . '): ?>';
		}, self::$view );
		self::$view = preg_replace_callback( @trim( '/@endwhile/' ), function ( $a ) {
			return '<?php endwhile; ?>';
		}, self::$view );
	}

	public static function parseDowhile(): void {
		self::$view = preg_replace_callback( @trim( '/@do/' ), function ( $a ) {
			return '<?php  do{?>';
		}, self::$view );
		self::$view = preg_replace_callback( @trim( '/@whiledo\((.*?)\)/' ), function ( $variable ) {
			return '<?php }while(' . @trim( $variable[1] ) . ');?>';
		}, self::$view );
	}


	public static function parseIf(): void {
		self::$view = preg_replace_callback( @trim( '/@if\((\w+\([^)]*\))\)/' ), function ( $variable ) {
			/** $variable[1] dinamik fonksiyon çağrısını içerir */
			if ( isset( $variable[1] ) ) {
				return '<?php if(' . trim( $variable[1] ) . '): ?>';
			}

			return '';
			/** Eğer içerik yoksa, boş döner */
		}, self::$view );

		self::$view = preg_replace_callback( @trim( '/@elseif\((.*?)\)/' ), function ( $variable ) {
			if ( isset( $variable[1] ) ) {
				return '<?php elseif(' . trim( $variable[1] ) . '): ?>';
			}

			return '';
			/** Eğer içerik yoksa, boş döner */
		}, self::$view );

		self::$view = preg_replace_callback( '/@else/', function () {
			return '<?php else: ?>';
		}, self::$view );
		self::$view = preg_replace_callback( '/@endif/', function () {
			return '<?php endif;?>';
		}, self::$view );
	}


	public static function parseIsset(): void {
		self::$view = preg_replace_callback( @trim( '/@isset\((.*?)\)/' ), function ( $variable ) {
			return 'isset(' . @trim( $variable[1] ) . ')';
		}, self::$view );
	}

	public static function parseEmpty(): void {
		self::$view = preg_replace_callback( @trim( '/@empty\((.*?)\)/' ), function ( $variable ) {
			return 'empty(' . @trim( $variable[1] ) . ')';
		}, self::$view );
	}


	public static function parseSwitch(): void {
		self::$view = preg_replace_callback( @trim( '/@switch\((.*?)\)/' ), function ( $variable ) {
			return '<?php switch(' . @trim( $variable[1] ) . '){';
		}, self::$view );
		self::$view = preg_replace_callback( @trim( '/@case\((.*?)\)/' ), function ( $variable ) {
			return 'case ' . @trim( $variable[1] ) . ':';
		}, self::$view );
		self::$view = preg_replace_callback( @trim( '/@break/' ), function ( $variable ) {
			return 'break;';
		}, self::$view );
		self::$view = preg_replace_callback( @trim( '/@default/' ), function ( $variable ) {
			return 'default:';
		}, self::$view );
		self::$view = preg_replace_callback( @trim( '/@endswitch/' ), function ( $variable ) {
			return '}?>';
		}, self::$view );
	}


	public static function parseHtmlSection(): void {

		preg_replace_callback( @trim( '/@section\((.*?)\)/' ), function ( $variable ) {
			$select = str_replace( "'", '', @trim( $variable[1] ) );
			preg_match_all( "#<$select" . "[^>]*>(.*?)</$select>#s", self::$view, $matches );
			if ( count( $matches[1] ) < 1 ) {
				self::$view = preg_replace( '#@section\((.*?)\)*@endsection#s', '', self::$view );

				return self::$view;
			} else {
				self::$view = preg_replace_callback( @trim( '/@section\((.*?)\)/' ), function ( $variable ) {
					return '';
				}, self::$view );
				self::$view = preg_replace_callback( @trim( '/@endsection/' ), function ( $variable ) {
					return '';
				}, self::$view );
			}

		}, self::$view );


	}
}