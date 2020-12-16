{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_favicons"}
    {$smarty.block.parent}
    {if $preloadFonts_enabled}
        {foreach $ffuenfFontPreload as $font}
            <link crossorigin="anonymous" rel="preload" as="font" type="{$font.type}" href="{include file="string:{$font.url}"}">
        {/foreach}
    {/if}
{/block}
