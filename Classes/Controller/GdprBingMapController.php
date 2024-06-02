<?php

declare(strict_types=1);

namespace GdprExtensionsCom\GdprExtensionsComBmap\Controller;


use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "gdpr-extensions-com-bmap" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023
 */

/**
 * GdprBingMapController
 */
class GdprBingMapController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{


    /**
     * gdprManagerRepository
     *
     * @var \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\GdprManagerRepository
     */

    protected $gdprManagerRepository = null;

     /**
     * MapLocationRepository
     *
     * @var \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\MapLocationRepository
     */

     protected $gdprBingMapRepository = null;


    /**
     * ContentObject
     *
     * @var ContentObject
     */
    protected $contentObject = null;

    /**
     * Action initialize
     */
    protected function initializeAction()
    {
        $this->contentObject = $this->configurationManager->getContentObject();

        // intialize the content object
    }

    /**
     * @param \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\GdprManagerRepository $gdprManagerRepository
     */
    public function injectGdprManagerRepository(\GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\GdprManagerRepository $gdprManagerRepository)
    {
        $this->gdprManagerRepository = $gdprManagerRepository;
    }
    
    /**
     * @param \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\MapLocationRepository $gdprBingMapRepository
     */
    public function injectGdprBingMapRepository(\GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\MapLocationRepository $gdprBingMapRepository)
    {
        $this->gdprBingMapRepository = $gdprBingMapRepository;
    }
    /**
     * action index
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_gdprextensionscomyoutube_domain_model_gdprmanager');
        $gdprSettingBingmaps = $queryBuilder
            ->select('*')
            ->from('tx_gdprextensionscomyoutube_domain_model_gdprmanager')->where(
                $queryBuilder->expr()->like('extension_key', $queryBuilder->createNamedParameter('gdpr_extensions_com_bmap'))
            );
        $settings =  $gdprSettingBingmaps->execute()->fetchAssociative();
        $mapLocations = [];
        $maekerUids = explode(',' , $this->contentObject->data['gdpr_bm_business_locations'] );
        foreach ($maekerUids as $markerUid){
                 $marker = $this->gdprBingMapRepository->findByUid((int)$markerUid);
                 $tempArray = [
                        'title' => $marker->getTitle(),
                        'address' => $marker->getAddress(),
                        'lat' => $marker->getLat(),
                        'long' => $marker->getLon(),
                        ];
                 $mapLocations[] = $tempArray;
        }
        $this->view->assign('BingmapsApiKey', $settings['map_api']);
        $this->view->assign('LatLongData', json_encode($mapLocations));
        $this->view->assign('BingMapData', $this->contentObject->data);
        $this->view->assign('BingMapSettings', $settings);
        $this->view->assign('rootPid', $GLOBALS['TSFE']->site->getRootPageId());


        return $this->htmlResponse();
    }
}
