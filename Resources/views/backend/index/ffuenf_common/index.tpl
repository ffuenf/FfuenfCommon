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
    {elseif $environment == 'staging'}
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
                background: url("data:image/png;base64,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") no-repeat right center;
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
