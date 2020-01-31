{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_javascript_tracking"}
    <script type="text/javascript" src="https://www.datadoghq-browser-agent.com/datadog-logs-us.js"></script>
    <script>
        DD_LOGS.init({
            clientToken: "{$datadogClientToken}",
            forwardErrorsToLogs: true
        });
    </script>
    <script type="text/javascript" src="https://www.datadoghq-browser-agent.com/datadog-rum-us.js"></script>
    <script>
      window.DD_RUM && window.DD_RUM.init({
        clientToken: "{$datadogClientToken}",
        applicationId: "{$datadogApplicationId}",
      });
    </script>
{/block}
