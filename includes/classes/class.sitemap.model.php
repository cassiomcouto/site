<?php

interface iISC_SITEMAP_MODEL {

	/**
	*
	* @return string
	*/
	public function getHeading ();

	/**
	*
	* @param int $limit
	* @param int $offset
	* @return ISC_SITEMAP_NODE
	*/
	public function getTree ($limit = null, $offset = null);

	/**
	*
	* @return int
	*/
	public function countAll ();

	/**
	*
	* return string
	*/
	public function getSubsectionUrl ();
}


class ISC_SITEMAP_MODEL {

}
