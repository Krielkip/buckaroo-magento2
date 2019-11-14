<?php
/*
*
*
*          ..::..
*     ..::::::::::::..
*   ::'''''':''::'''''::
*   ::..  ..:  :  ....::
*   ::::  :::  :  :   ::
*   ::::  :::  :  ''' ::
*   ::::..:::..::.....::
*     ''::::::::::::''
*          ''::''
*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Creative Commons License.
* It is available through the world-wide-web at this URL:
* http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
* If you are unable to obtain it through the world-wide-web, please send an email
* to servicedesk@tig.nl so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please contact servicedesk@tig.nl for more information.
*
* @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
* @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
*
*/
namespace TIG\Buckaroo\Ui\DataProvider\Modifier;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use TIG\Buckaroo\Ui\Renderer\NotificationRenderer;

/**
 * @see \Magento\ReleaseNotification\Ui\DataProvider\Modifier\Notifications
 */
class Notifications implements ModifierInterface
{
    /** @var CacheInterface $cacheStorage */
    private $cacheStorage;

    /** @var ReadFactory $readFactory */
    private $readFactory;

    /** @var Reader $moduleReader */
    private $moduleReader;

    /** @var SerializerInterface $serializer */
    private $serializer;

    /** @var NotificationRenderer $renderer */
    private $renderer;

    /**
     * @param CacheInterface $cacheStorage
     * @param ReadFactory $readFactory
     * @param Reader $moduleReader
     * @param SerializerInterface $serializer
     * @param NotificationRenderer $render
     */
    public function __construct(
        CacheInterface $cacheStorage,
        ReadFactory $readFactory,
        Reader $moduleReader,
        SerializerInterface $serializer,
        NotificationRenderer $render
    ) {
        $this->cacheStorage = $cacheStorage;
        $this->readFactory = $readFactory;
        $this->moduleReader = $moduleReader;
        $this->serializer = $serializer;
        $this->renderer = $render;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $modalContent = $this->getNotificationContent();

        if ($modalContent) {
            $pages = $modalContent['pages'];
            $pageCount = count($pages);
            $counter = 1;

            foreach ($pages as $page) {
                $meta = $this->buildNotificationMeta($meta, $page, $counter++ == $pageCount);
            }
        } else {
            $meta = $this->hideNotification($meta);
        }

        return $meta;
    }

    /**
     * Builds the notification modal by modifying $meta for the ui component.
     *
     * @param array $meta
     * @param array $page
     * @param bool $isLastPage
     *
     * @return array
     */
    private function buildNotificationMeta(array $meta, array $page, bool $isLastPage): array
    {
        $meta['notification_modal_' . $page['name']]['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => \Magento\Ui\Component\Modal::NAME
        ];

        $meta['notification_modal_' . $page['name']]['children']['notification_fieldset']['children']
        ['notification_text']['arguments']['data']['config'] = [
            'text' => $this->renderer->getNotificationContent($page)
        ];

        if ($isLastPage) {
            $meta['notification_modal_' . $page['name']]['arguments']['data']['config']['options'] = [
                'title' => $this->renderer->getNotificationTitle($page),
                'buttons' => [
                    [
                        'text' => 'Done',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'closeReleaseNotes'
                            ]
                        ],
                        'class' => 'release-notification-button-next'
                    ]
                ],
            ];

            $meta['notification_modal_' . $page['name']]['children']['notification_fieldset']['children']
            ['notification_buttons']['children']['notification_button_next']['arguments']['data']['config'] = [
                'buttonClasses' => 'hide-release-notification'
            ];
        } else {
            $meta['notification_modal_' . $page['name']]['arguments']['data']['config']['options'] = [
                'title' => $this->renderer->getNotificationTitle($page)
            ];
        }

        return $meta;
    }

    /**
     * Sets the modal to not display if no content is available.
     *
     * @param array $meta
     * @return array
     */
    private function hideNotification(array $meta)
    {
        $meta['notification_modal_1']['arguments']['data']['config']['options'] = [
            'autoOpen' => false
        ];

        return $meta;
    }

    /**
     * Returns the json data
     *
     * @return array
     * @throws FileSystemException
     */
    private function getNotificationContent()
    {
        $cacheKey = 'tig-release-notification';
        $modalContent = $this->cacheStorage->load($cacheKey);
        if ($modalContent === false) {
            $readDirectory  = $this->readFactory->create(
                $this->moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_VIEW_DIR, 'TIG_Buckaroo'),
                \Magento\Framework\Filesystem\DriverPool::FILE
            );
            $modalContent = $readDirectory->readFile('release-notification.json');

            $this->cacheStorage->save($modalContent, $cacheKey);
        }

        return $this->serializer->unserialize($modalContent);
    }
}
