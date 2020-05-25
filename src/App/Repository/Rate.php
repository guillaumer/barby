<?php

namespace App\Repository;

/**
 * Class Document
 *
 * @package App\Repository
 */
class Rate extends Repository {

	/**
	 * @return string
	 */
	public function getTableName()
	{
		return 'rates';
	}

	public function findSince($market, $cfrom, $cto, $since)
    {
        $sql = 'SELECT last, created FROM %1$s WHERE market = "%2$s" AND cfrom = "%3$s" AND cto = "%4$s" AND created > %5$d';
        return $this->db->fetchAll(sprintf($sql, $this->getTableName(), $market, $cfrom, $cto, $since));
    }

    public function findFxSince($cfrom, $cto, $since)
    {
        $sql = 'SELECT * FROM %1$s WHERE market = "%2$s" AND cfrom = "%3$s" AND cto = "%4$s" AND created <= %5$d LIMIT 1';
        return $this->db->fetchAssoc(sprintf($sql, $this->getTableName(), 'fx', $cfrom, $cto, $since));
    }

}
