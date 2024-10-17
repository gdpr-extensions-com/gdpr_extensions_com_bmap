<?php

declare(strict_types=1);

namespace GdprExtensionsCom\GdprExtensionsComBmap\Controller;

use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\MapLocation;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;


/**
 * This file is part of the "gdpr-extensions-com-google_reviewslider" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023
 */

/**
 * GdprManagerController
 */
class GdprManagerController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    protected $gdprManagerRepository = null;
     /**
     * MapLocationRepository
     *
     * @var \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Repository\MapLocationRepository
     */

     protected $gdprBingMapRepository = null;

    /**
     * @var ModuleTemplateFactory
     */
    protected $moduleTemplateFactory;

     /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager = null;

    /**
     * @return void
     */
    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * action index
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();

    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        $extensions = ExtensionManagementUtility::getLoadedExtensionListArray();
        $extensionNames = [];
        
        foreach ($extensions as $extensionKey) {
            if ($packageManager->isPackageAvailable($extensionKey)) {
                $extensionName = $packageManager->getPackage($extensionKey)->getPackageMetaData()->getTitle();
                // Directly assign the name to the key in the associative array.
                $extensionNames[$extensionKey] = $extensionName;
            }
        }
        
        // Filter based on keys, looking for 'gdpr_two_x' in the extensionKey.
        $twoClickSolutions = array_filter($extensionNames, function ($key) {
            return str_contains($key, 'gdpr_two_x') || str_contains($key, 'gdpr_extensions_com');
        }, ARRAY_FILTER_USE_KEY); // Use ARRAY_FILTER_USE_KEY to filter by key.
        


        $gdprDellQb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_gdprextensionscomyoutube_domain_model_gdprmanager');
   
        $gdprDellQb
            ->delete('tx_gdprextensionscomyoutube_domain_model_gdprmanager')
            ->where(
                $gdprDellQb->expr()->notIn(
                    'extension_title',
                    $gdprDellQb->createNamedParameter($twoClickSolutions,Connection::PARAM_STR_ARRAY)
                )
            )
            ->executeStatement();

        $gdprManagers = $this->gdprManagerRepository->findAll();

        $installedTwoClickSol = [];
        foreach ($gdprManagers as $twoClickSol){
            array_push($installedTwoClickSol,$twoClickSol->getExtensionTitle());
        }

        $missingExtensions = array_diff($twoClickSolutions, $installedTwoClickSol);


        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_gdprextensionscomyoutube_domain_model_gdprmanager');
       
        foreach ($missingExtensions as $key => $value) {
            
            $queryBuilder
                ->insert('tx_gdprextensionscomyoutube_domain_model_gdprmanager')
                ->values([
                    'extension_title' => $value,
                    'extension_key' => $key,
                    'heading' => '', // Default empty string
                    'content' => '', // Default empty string
                    'button_text' => '', // Default empty string
                    'enable_background_image' => 0, // Default 0
                    'heading_color' => '', // Default empty string
                    'background_image' => '', // Default empty string
                    'background_image_color' => '', // Default empty string
                    'button_color' => '', // Default empty string
                    'text_color' => '', // Default empty string
                    'button_shape' => '' // Default empty string
                ])
                ->execute();
        }

        $uploadImageUrl = $this->uriBuilder->reset()
            ->uriFor('uploadImage');
        $reviwsSliderData = $this->gdprManagerRepository->findByExtension_key('gdpr_extensions_com_bmap')->toArray();
        return $this->redirect('edit',null,null,['gdprManager' => $reviwsSliderData[0]]);
    }
    /**
     * action show
     *
     * @param \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $gdprManager
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(\GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $gdprManager): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('gdprManager', $gdprManager);
        return $this->htmlResponse();
    }

    /**
     * action new
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function newAction(): \Psr\Http\Message\ResponseInterface
    {
        $uploadImageUrl = $this->uriBuilder->reset()
            ->uriFor('uploadImage');
        $this->view->assign('uploadImageUrl', $uploadImageUrl);

        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $newGdprManager
     */
    public function createAction(\GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $newGdprManager)
    {
        $this->gdprManagerRepository->add($newGdprManager);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $gdprManager
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("gdprManager")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(\GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $gdprManager): \Psr\Http\Message\ResponseInterface
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_gdprextensionscomyoutube_domain_model_gdprmanager');
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $sites = $siteFinder->getAllSites();
        $configurations = [];

        foreach ($sites as $siteKey => $site) {
            $configurations[$siteKey] = $site->getConfiguration();
        }
        $uploadImageUrl = $this->uriBuilder->reset()
            ->uriFor('uploadImage');
        $this->view->assign('uploadImageUrl', $uploadImageUrl);
        $this->view->assign('uploadImageUrl', $uploadImageUrl);
        $this->view->assign('googlemaps', 1);
        $this->view->assign('gdprManager', $gdprManager);
        $this->view->assign('sites', $configurations);
        return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $gdprManager
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateAction(\GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $gdprManager) : \Psr\Http\Message\ResponseInterface
    {
        $locationsData = [];
        if($this->request->hasArgument('tx_gdprextensionscombmap_gdpr_gdprextensionscombmapbingmap')){
            $locationsData = $this->request->getArgument('tx_gdprextensionscombmap_gdpr_gdprextensionscombmapbingmap')['locations'];
        }
        elseif ($this->request->hasArgument('locations')){
            $locationsData = $this->request->getArgument('locations');
        }
        $locationsIds = [];
        foreach ($locationsData as $locationData) {
            if (!$locationData['lat'] || !$locationData['lon']) {
                continue;
            }
            $mapId = $locationData['mapId'];
            if($mapId != ''){
                   array_push($locationsIds , $locationData['mapId']);
                   $location =  $this->gdprBingMapRepository->findByUid($locationData['mapId']);
                   $location->setTitle($locationData['title']);
                   $location->setAddress($locationData['address']);
                   $location->setLat((int)($locationData['lat']*1000000));
                   $location->setLon((int)($locationData['lon']*1000000));
               }
            else{
                $location = new MapLocation();
                $location->setTitle($locationData['title']);
                $location->setAddress($locationData['address']);
                $location->setLat((int)($locationData['lat']*1000000));
                $location->setLon((int)($locationData['lon']*1000000));
                $gdprManager->addLocation($location);
                $this->persistenceManager->persistAll();
                array_push($locationsIds , $location->getUid());
            }
        }
        $gdprLocations = $gdprManager->getLocations();
        if(count($locationsIds) > 0){
            foreach($gdprLocations as $loc){
                if(!in_array($loc->getUid() , $locationsIds )){
                    $gdprManager->removeLocation($loc);
                }
            }
        }
       
        $this->gdprManagerRepository->update($gdprManager);
        $this->persistenceManager->persistAll();
        return  $this->redirect('list');

    }

    /**
     * action delete
     *
     * @param \GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $gdprManager
     */
    public function deleteAction(\GdprExtensionsCom\GdprExtensionsComBmap\Domain\Model\GdprManager $gdprManager)
    {
        $this->gdprManagerRepository->remove($gdprManager);
        $this->redirect('list');
    }

    /**
     * action uploadImage
     *
     */
    public function uploadImageAction()
    {
        $rootPageId = (int)($_GET['rootPageId'] ?? 0);

        if ($rootPageId > 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('gdpr_multilocations');
            $result = $queryBuilder
                ->select('*')
                ->from('gdpr_multilocations')
                ->where(
                    $queryBuilder->expr()->eq('root_pid', $queryBuilder->createNamedParameter($rootPageId, \PDO::PARAM_INT))
                )
                ->executeQuery();

            $locations = [];
            while ($row = $result->fetch()) {
                $locations[] = [
                    'title' => $row['name_of_location'],
                    'apiKey' => $row['dashboard_api_key']
                ];
            }

            // Return the fetched locations
            return $this->jsonResponse(json_encode(['locations' => $locations]));
        }


        $json = file_get_contents('php://input');
        $data = json_decode($json, true); // Decode JSON to associative array

        $actionType = $data['actionType'] ?? null;

        if ($actionType === 'addLocation') {
            // Extract the locations data
            $locations = $data['locations'] ?? [];

            // Get an instance of the QueryBuilder
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('gdpr_multilocations');
            // Assuming all locations have the same rootPid
            $rootPid = isset($locations[0]) ? (int)($locations[0]['rootPageId'] ?? 0) : 0;

            // First, delete existing records for the same rootPageId
            if ($rootPid >= 0) {
                $queryBuilder
                    ->delete('gdpr_multilocations')
                    ->where(
                        $queryBuilder->expr()->eq('root_pid', $queryBuilder->createNamedParameter($rootPid, \PDO::PARAM_INT))
                    )
                    ->executeStatement();
            }

                foreach ($locations as $location) {
                    $nameOfLocation = $location['title'] ?? '';
                    $dashboardApiKey = $location['apiKey'] ?? '';
                    $rootPid = (int)($location['rootPageId'] ?? 0);

                    // Insert the data into the gdpr_multilocations table
                    if($nameOfLocation !== ''){
                        $queryBuilder
                        ->insert('gdpr_multilocations')
                        ->values([
                            'name_of_location' => $nameOfLocation,
                            'dashboard_api_key' => $dashboardApiKey,
                            'root_pid' => $rootPid
                        ])
                        ->executeStatement();
                    }
                }

            return $this->jsonResponse(json_encode(['status' => true, 'message' => 'Changes applied successfully']));
        }
        //

        $forCookieWidget = $this->request->getParsedBody()['forCookie'] ?? $this->request->getQueryParams()['forCookie'] ?? null;
        if($forCookieWidget){
            $cookieWidgetImageValue = $this->request->getParsedBody()['cookieWidgetImageValue'] ?? $this->request->getQueryParams()['cookieWidgetImageValue'] ?? null;
            $cookieWidgetPositionValue = $this->request->getParsedBody()['cookieWidgetPositionValue'] ?? $this->request->getQueryParams()['cookieWidgetPositionValue'] ?? null;

            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_gdprextensionscomyoutube_domain_model_cookiewidget');
            $queryBuilder
                ->delete('tx_gdprextensionscomyoutube_domain_model_cookiewidget')
                ->execute();

            $queryBuilder
                ->insert('tx_gdprextensionscomyoutube_domain_model_cookiewidget')
                ->values([
                    'cookie_widget_image' => $cookieWidgetImageValue,
                    'cookie_widget_position' => $cookieWidgetPositionValue,
                ])
                ->execute();

            return $this->jsonResponse(json_encode(['status' => true]));
        }else{

        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $twoClickFolder = Environment::getPublicPath().'/fileadmin/user_upload/two_click_solution/';
            if (!is_dir($twoClickFolder)) {
                mkdir($twoClickFolder);
            }
            $basePath = 'fileadmin/user_upload/two_click_solution/';


            $originalFileName = basename($_FILES['image']['name']);
            $filePath = $_FILES['image']['tmp_name'];
            $fileHash = md5_file($filePath);
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = $fileHash . '.' . $fileExtension;

            $targetPath = $twoClickFolder . $newFileName;

            if (move_uploaded_file($filePath, $targetPath)) {
                $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

                return $this->jsonResponse(json_encode([
                    'url' => $basePath.$newFileName
                ]));
            }
        }
        }

        return $this->jsonResponse(json_encode(['status' => false]));
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
}
