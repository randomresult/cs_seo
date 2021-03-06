<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// get extension configurations
$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['cs_seo']);

// SEO Settings
$GLOBALS['TCA']['pages_language_overlay']['columns']['title']['config']['max'] = $extConf['maxTitle'];
$GLOBALS['TCA']['pages_language_overlay']['columns']['nav_title']['config']['max'] = $extConf['maxNavTitle'];
$GLOBALS['TCA']['pages_language_overlay']['columns']['description']['config']['max'] = $extConf['maxDescription'];

// Path segment auto fill
if($extConf['enablePathSegment'] && isset($GLOBALS['TCA']['pages_language_overlay']['columns']['tx_realurl_pathsegment'])) {
    $GLOBALS['TCA']['pages_language_overlay']['columns']['tx_realurl_pathsegment']['config']['eval'] .= ',required';
    $GLOBALS['TCA']['pages_language_overlay']['columns']['tx_realurl_pathsegment']['config']['wizards'] = [
        '_POSITION' => 'bottom',
        'permalinkWizard' => [
            'type' => 'userFunc',
            'userFunc' => 'Clickstorm\\CsSeo\\UserFunc\\PermalinkWizard->render'
        ]
    ];
}

// define new fields
$tempColumns = [
    'tx_csseo_title' => [
        'label' => 'LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.tx_csseo_title',
        'exclude' => 1,
        'config' => [
            'type' => 'input',
            'max' => $extConf['maxTitle'],
            'eval' => 'trim',
            'wizards' => [
                '_POSITION' => 'bottom',
                'previewWizard' => [
                    'type' => 'userFunc',
                    'userFunc' => 'Clickstorm\\CsSeo\\UserFunc\\PreviewWizard->render'
                ]
            ]
        ]
    ],
    'tx_csseo_title_only' => [
        'label' => 'LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.tx_csseo_title_only',
        'exclude' => 1,
        'config' => [
            'type' => 'check',
        ]
    ],
    'tx_csseo_og_title' => [
        'label' => 'LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.tx_csseo_og_title',
        'exclude' => 1,
        'config' => [
            'type' => 'input',
            'max' => '95',
            'eval' => 'trim',
        ]
    ],
    'tx_csseo_og_description' => [
        'label' => 'LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.tx_csseo_og_description',
        'exclude' => 1,
        'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 5,
            'eval' => 'trim',
            'max' => '300',
        ]
    ],
    'tx_csseo_tw_title' => [
        'label' => 'LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.tx_csseo_tw_title',
        'exclude' => 1,
        'config' => [
            'type' => 'input',
            'max' => '70',
            'eval' => 'trim',
        ]
    ],
    'tx_csseo_tw_description' => [
        'label' => 'LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.tx_csseo_tw_description',
        'exclude' => 1,
        'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 5,
            'eval' => 'trim',
            'max' => '200',
        ]
    ],
    'tx_csseo_tw_creator' => [
        'label' => 'LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.tx_csseo_tw_creator',
        'exclude' => 1,
        'config' => [
            'type' => 'input',
            'max' => '40',
            'eval' => 'trim',
        ]
    ],
];

// add new fields
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages_language_overlay', $tempColumns);

// replace description
$GLOBALS['TCA']['pages_language_overlay']['palettes']['metatags']['showitem'] =
    preg_replace('/description(.*,|.*$)/', '', $GLOBALS['TCA']['pages_language_overlay']['palettes']['metatags']['showitem']);

// define new palettes
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'tx_csseo_preview',
    'tx_csseo_title,tx_csseo_title_only,--linebreak--,
    description;LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.description');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'tx_csseo_facebook',
    'tx_csseo_og_title, --linebreak--,
    tx_csseo_og_description');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages_language_overlay',
    'tx_csseo_twitter',
    'tx_csseo_tw_title, --linebreak--,
    tx_csseo_tw_description, --linebreak--,
    tx_csseo_tw_creator');

// add to types
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages_language_overlay',
    '--div--;LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.tab.seo,
    --palette--;LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.palette.tx_csseo_preview;tx_csseo_preview,
    --div--;LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.tab.social,
    --palette--;LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.palette.tx_csseo_facebook;tx_csseo_facebook,
    --palette--;LLL:EXT:cs_seo/Resources/Private/Language/locallang_db.xlf:pages.palette.tx_csseo_twitter;tx_csseo_twitter',
    '1,6',
    'after:lastUpdated'
);