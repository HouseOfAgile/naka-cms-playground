<?php

namespace HouseOfAgile\NakaCMSBundle\Seo;

/**
 * Implement on any entity that can produce text for <meta> / OGP.
 */
interface MetaTextProviderInterface
{
	/**
	 * Return an **ordered** list of raw HTML strings.
	 * The first non‑empty entry wins.
	 *
	 * @return iterable<string|null>
	 */
	public function getMetaCandidates(): iterable;
}
