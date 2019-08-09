{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_javascript_tracking"}
    <script type="text/javascript" src="https://www.datadoghq-browser-agent.com/datadog-logs-us.js"></script>
    <script>
        DD_LOGS.init({
            clientToken: "{$datadogFrontendLoggingClientToken}",
            forwardErrorsToLogs: true
        });
    </script>
{/block}
