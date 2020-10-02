{extends file='parent:backend/index/parent.tpl'}

{block name='backend/base/header/title'}{if $environment}{$environment|upper} {/if}Shopware {$SHOPWARE_VERSION} {$SHOPWARE_VERSION_TEXT} (Rev. {$SHOPWARE_REVISION}) - Backend (c) shopware AG{/block}

{block name="backend/base/header/css" append}
    {if $environment == 'development'}
        {$gradients = '165deg, #93ff99 45%, #dff1f6 95%, #c6eaf6 100%'}
        {$bordercolor = '#008a20'}
        {$background = '#00ac28'}
        {$textcolor = '#FFFFFF'}
        {$textshadow = '#008a20'}
        {$separator = $bordercolor}
        {$filter = 'hue-rotate(280deg)'}
    {elseif $environment == 'staging' || $environment == 'testing'}
        {$gradients = '165deg, #fffc7d 45%, #dff1f6 95%, #c6eaf6 100%'}
        {$bordercolor = '#e4df00'}
        {$background = '#fff900'}
        {$textcolor = '#b2b200'}
        {$textshadow = 'none'}
        {$separator = $bordercolor}
        {$filter = 'hue-rotate(215deg) brightness(1.65)'}
    {elseif $environment == 'production'}
        {$gradients = '165deg, #ffa59e 45%, #dff1f6 95%, #c6eaf6 100%'}
        {$bordercolor = '#FC3A1D'}
        {$background = '#cc0000'}
        {$textcolor = '#FFFFFF'}
        {$textshadow = '#2a2a2a'}
        {$separator = $bordercolor}
        {$filter = 'hue-rotate(150deg) saturate(1.5)'}
    {/if}
    {if $gradients}
        <style>
            body {
                background: center center no-repeat, linear-gradient({$gradients}) !important;
            }
            .shopware-menu {
                border-color: {$bordercolor} !important;
                background: {$background};
            }
            .shopware-menu .x-box-inner {
                background: url("data:image/png;base64,{$base64logo}") no-repeat right center;
            }

            .shopware-menu .x-box-inner .x-btn .x-btn-icon,
            .shopware-menu .x-box-inner .x-form-item-body,
            .shopware-menu .x-box-inner .x-main-logo-container {
                -webkit-filter: {$filter};
                filter: {$filter};
            }
            .shopware-menu .x-btn-default-toolbar-small button .x-btn-inner {
                text-shadow: {$textshadow};
                color: {$textcolor};
            }

            .shopware-menu .x-box-inner .x-toolbar-separator-horizontal {
                border-left: 1px solid {$separator};
            }
        </style>
    {/if}
{/block}
