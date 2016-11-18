<?php
namespace IceHawk\IceHawk\Defaults;

use IceHawk\IceHawk\Constants\HttpMethod;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Routing\Interfaces\RedirectsRoute;

/**
 * Class RequestProxy
 *
 * @package IceHawk\IceHawk\Defaults
 */
final class RequestProxy
{
	/** @var  array|RedirectsRoute[] */
	private $routeRedirects = [];

	public function addRedirect( RedirectsRoute $redirect )
	{
		$this->routeRedirects[] = $redirect;
	}

	public function proxyRequest( ProvidesRequestInfo $request ) : ProvidesRequestInfo
	{
		$uri    = $request->getUri();
		$method = $request->getMethod();

		foreach ( $this->routeRedirects as $redirect )
		{
			if ( $redirect->matches( $uri ) && $method != $redirect->getFinalMethod() )
			{
				$request = $this->createNewRequest( $request, $redirect );
			}
		}

		return $request;
	}

	private function createNewRequest( ProvidesRequestInfo $request, RedirectsRoute $redirect ) : ProvidesRequestInfo
	{
		$requestMethod = $request->getMethod();
		$finalUri      = $redirect->getFinalUri();
		$uriParams     = $redirect->getUriParams();

		$readMethods     = array_intersect( [ $requestMethod, $redirect->getFinalMethod() ], HttpMethod::READ_METHODS );
		$readMethodCount = count( $readMethods );

		array_walk(
			$uriParams,
			function ( $val, $key ) use ( &$finalUri )
			{
				$finalUri = str_replace( ':' . $key, $val, $finalUri );
			}
		);

		$overWrites = [ 'REQUEST_METHOD' => $redirect->getFinalMethod(), 'REQUEST_URI' => $finalUri ];

		if ( $readMethodCount == 1 )
		{
			if ( array_shift( $readMethods ) == $requestMethod )
			{
				$_POST = array_merge( $_GET, $uriParams );
			}
			else
			{
				$_GET = $_POST;

				$overWrites['QUERY_STRING'] = $this->createQueryString( $request, $uriParams );
			}
		}
		elseif ( $readMethodCount == 2 )
		{
			$overWrites['QUERY_STRING'] = $this->createQueryString( $request, $uriParams );
		}
		else
		{
			$_POST = array_merge( $_POST, $uriParams );
		}

		return $request->newWithOverwrites( $overWrites );
	}

	private function createQueryString( ProvidesRequestInfo $request, array $uriParams ) : string
	{
		parse_str( $request->getQueryString(), $queryData );

		$queryData = array_merge( $queryData, $uriParams );

		return http_build_query( $queryData );
	}
}