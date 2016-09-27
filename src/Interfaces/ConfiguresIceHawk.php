<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Interfaces;

use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToReadHandler;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToWriteHandler;

/**
 * Interface ConfiguresIceHawk
 * @package IceHawk\IceHawk\Interfaces
 */
interface ConfiguresIceHawk
{
	/**
	 * @return array|\Traversable|RoutesToReadHandler[]
	 */
	public function getReadRoutes();

	/**
	 * @return array|\Traversable|RoutesToWriteHandler[]
	 */
	public function getWriteRoutes();

	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array;

	public function getRequestInfo() : ProvidesRequestInfo;

	public function getFinalReadResponder() : RespondsFinallyToReadRequest;

	public function getFinalWriteResponder() : RespondsFinallyToWriteRequest;
}
