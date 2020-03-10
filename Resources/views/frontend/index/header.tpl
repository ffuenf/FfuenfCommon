{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_favicons"}
    {$smarty.block.parent}
    {if $preloadFonts_enabled}
        {foreach $ffuenfFontPreload as $font}
            <link crossorigin="anonymous" rel="preload" as="font" type="{$font.type}" href="{include file="string:{$font.url}"}">
        {/foreach}
    {/if}
{/block}

{block name="frontend_index_header_javascript_tracking"}
    {$smarty.block.parent}
    {if $datadog_frontend_logging_enabled}
        <script type="text/javascript" src="{$datadogLogsUrl}"></script>
        <script>
          window.DD_LOGS && DD_LOGS.init({
            clientToken: "{$datadogClientToken}",
            forwardErrorsToLogs: true,
            sampleRate: 100
          });
        </script>
    {/if}
    {if $datadog_real_user_monitoring_enabled}
        <script type="text/javascript" src="{$datadogRumUrl}"></script>
        <script>
          window.DD_RUM && window.DD_RUM.init({
            clientToken: "{$datadogClientToken}",
            applicationId: "{$datadogApplicationId}",
          });
        </script>
    {/if}
{/block}
