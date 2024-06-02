<?php

declare(strict_types=1);

namespace GdprExtensionsCom\GdprExtensionsComBmap\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProcessBmMarkers
{

    /**
     * gdprManagerRepository
     *
     * @var \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\GdprManagerRepository
     */

     protected $gdprManagerRepository = null;

     /**
     * @param \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\GdprManagerRepository $gdprManagerRepository
     */
    public function injectGdprManagerRepository(\GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\GdprManagerRepository $gdprManagerRepository)
    {
        $this->gdprManagerRepository = $gdprManagerRepository;
    }
    public function __construct()
    {
    }

    public function getLocationsforRoodPid(array &$params)
    {
        // dd($this->gdprManagerRepository);
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $bmMarkerQB = $connectionPool->getQueryBuilderForTable(
            'tx_gdprextensionscombmap_domain_model_maplocation'
        );

        $markerResult = $bmMarkerQB->select('*')
            ->from('tx_gdprextensionscombmap_domain_model_maplocation')
            ->executeQuery();
        while ($Location = $markerResult->fetchAssociative()) {
            if (strlen($Location['title']) < 1) {
                continue;
            }
            $params['items'][] = [$Location['title'], $Location['uid']];
       }
        return $params;
    }

}
