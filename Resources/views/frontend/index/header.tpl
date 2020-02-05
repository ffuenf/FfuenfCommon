{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_javascript_tracking"}
    <script type="text/javascript" src="{$datadogLogsUrl}"></script>
    <script>
      window.DD_LOGS && DD_LOGS.init({
        clientToken: "{$datadogClientToken}",
        forwardErrorsToLogs: true,
        sampleRate: 100
      });
    </script>
    <script type="text/javascript" src="{$datadogRumUrl}"></script>
    <script>
      window.DD_RUM && window.DD_RUM.init({
        clientToken: "{$datadogClientToken}",
        applicationId: "{$datadogApplicationId}",
      });
    </script>
{/block}
